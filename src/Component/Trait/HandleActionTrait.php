<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Trait;

use Spiral\Livewire\Dot;
use Spiral\Livewire\Event\Component\ComponentCalledMethod;
use Spiral\Livewire\Event\Component\ComponentCallingMethod;
use Spiral\Livewire\Event\Component\ComponentUpdated;
use Spiral\Livewire\Event\Component\ComponentUpdating;
use Spiral\Livewire\Exception\Component\BadMethodCallException;
use Spiral\Livewire\Exception\Component\MissingFileUploadsTraitException;
use Spiral\Livewire\Exception\Component\NonPublicComponentMethodCall;
use Spiral\Livewire\Exception\Component\PublicPropertyNotFoundException;
use Spiral\Livewire\Str;

trait HandleActionTrait
{
    /**
     * @param non-empty-string $name
     *
     * @throws \Throwable
     * @throws PublicPropertyNotFoundException
     */
    public function syncInput(string $name, mixed $value, bool $rehash = true): void
    {
        $propertyName = Str::before($name, '.');

        $this->callBeforeAndAfterSyncHooks($name, $value, function ($name, $value) use ($propertyName, $rehash) {
            if (!$this->propertyIsPublicAndNotDefinedOnBaseClass($propertyName)) {
                throw new PublicPropertyNotFoundException(sprintf(
                    'Unable to set component data. Public property `%s` not found on component: `%s`.',
                    $propertyName,
                    $this->getName()
                ));
            }

            if (str_contains($name, '.')) {
                // Strip away model name.
                $keyName = Str::after($name, '.');
                // Get model attribute to be filled.
                $targetKey = Str::before($keyName, '.');

                // Get existing data from model property.
                $results = [];
                $results[$targetKey] = Dot::get($this->{$propertyName}, $targetKey, []);

                // Merge in new data.
                Dot::set($results, $keyName, $value);

                // Re-assign data to model.
                Dot::set($this->{$propertyName}, $targetKey, $results[$targetKey]);
            } else {
                $this->{$name} = $value;
            }

            $rehash && $this->livewireHasher->hash($this->getId(), $name, $value);
        });
    }

    /**
     * @param non-empty-string $name
     */
    protected function callBeforeAndAfterSyncHooks(string $name, mixed $value, callable $callback): void
    {
        $propertyName = Str::before(Str::studly($name), '.');
        $keyAfterFirstDot = str_contains($name, '.') ? Str::after($name, '.') : null;
        $keyAfterLastDot = str_contains($name, '.') ? Str::afterLast($name, '.') : null;

        $beforeMethod = 'updating'.$propertyName;
        $afterMethod = 'updated'.$propertyName;

        $beforeNestedMethod = str_contains($name, '.')
            ? 'updating'.Str::studly(Str::replace('.', '_', $name))
            : false;

        $afterNestedMethod = str_contains($name, '.')
            ? 'updated'.Str::studly(Str::replace('.', '_', $name))
            : false;

        $this->updating($name, $value);

        if (method_exists($this, $beforeMethod)) {
            $this->{$beforeMethod}($value, $keyAfterFirstDot);
        }

        if ($beforeNestedMethod && method_exists($this, $beforeNestedMethod)) {
            $this->{$beforeNestedMethod}($value, $keyAfterLastDot);
        }

        $this->livewireDispatcher->dispatch(new ComponentUpdating($this, $name, $value));

        $callback($name, $value);

        $this->updated($name, $value);

        if (method_exists($this, $afterMethod)) {
            $this->{$afterMethod}($value, $keyAfterFirstDot);
        }

        if ($afterNestedMethod && method_exists($this, $afterNestedMethod)) {
            $this->{$afterNestedMethod}($value, $keyAfterLastDot);
        }

        $this->livewireDispatcher->dispatch(new ComponentUpdated($this, $name, $value));
    }

    /**
     * @throws BadMethodCallException
     * @throws MissingFileUploadsTraitException
     * @throws NonPublicComponentMethodCall
     */
    public function callMethod(string $method, array $params = [], callable $captureReturnValueCallback = null): void
    {
        $method = trim($method);

        switch ($method) {
            case '$sync':
                $prop = array_shift($params);
                $head = reset($params);
                $this->syncInput($prop, $head);

                return;
            case '$set':
                $prop = array_shift($params);
                $head = reset($params);
                $this->syncInput($prop, $head, false);

                return;
            case '$toggle':
                $prop = array_shift($params);

                if (str_contains($prop, '.')) {
                    $propertyName = Str::before($prop, '.');
                    $targetKey = Str::after($prop, '.');
                    $currentValue = Dot::get($this->{$propertyName}, $targetKey);
                } else {
                    $currentValue = $this->{$prop};
                }

                $this->syncInput($prop, !$currentValue, false);

                return;

            case '$refresh':
                return;
        }

        if (!method_exists($this, $method)) {
            if ('startUpload' === $method) {
                throw new MissingFileUploadsTraitException(sprintf(
                    'Cannot handle file upload without `%s` trait on the `%s` component.',
                    WithFileUploads::class,
                    $this->getName()
                ));
            }

            throw new BadMethodCallException(sprintf(
                'Unable to call component method. Public method `%s` not found on component: `%s`.',
                $method,
                $this->getName()
            ));
        }

        if (!$this->methodIsPublicAndNotDefinedOnBaseClass($method)) {
            throw new NonPublicComponentMethodCall(sprintf('Component method not found: `%s`.', $method));
        }

        /** @var ComponentCallingMethod $event */
        $event = $this->livewireDispatcher->dispatch(new ComponentCallingMethod($this, $method, $params));

        if ($event->shouldSkipCalling) {
            return;
        }

        $returned = $this->{$method}(
            $this->livewireResolver->resolveArguments(new \ReflectionMethod($this, $method), $params)
        );

        $this->livewireDispatcher->dispatch(new ComponentCalledMethod($this, $method, $params));

        $captureReturnValueCallback && $captureReturnValueCallback($returned);
    }

    protected function methodIsPublicAndNotDefinedOnBaseClass(string $methodName): bool
    {
        $ref = new \ReflectionClass($this);

        $methods = array_filter(
            $ref->getMethods(\ReflectionMethod::IS_PUBLIC),
            static function (string $method) use ($ref): bool {
                // The "render" method is a special case. This method might be called by event listeners or other ways.
                if ('render' === $method) {
                    return false;
                }

                return self::class !== $ref->getName();
            }
        );

        foreach ($methods as $method) {
            if ($method->getName() === $methodName) {
                return true;
            }
        }

        return false;
    }

    protected function propertyIsPublicAndNotDefinedOnBaseClass(string $propertyName): bool
    {
        $properties = array_filter(
            (new \ReflectionObject($this))->getProperties(\ReflectionMethod::IS_PUBLIC),
            static fn (\ReflectionProperty $property): bool => self::class === $property->class
        );

        foreach ($properties as $property) {
            if ($property->getName() === $propertyName) {
                return false;
            }
        }

        return true;
    }
}
