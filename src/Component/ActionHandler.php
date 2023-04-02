<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Component\Enum\ComponentMethod;
use Spiral\Livewire\Event\Component\ComponentCalledMethod;
use Spiral\Livewire\Event\Component\ComponentCallingMethod;
use Spiral\Livewire\Event\Component\ComponentUpdated;
use Spiral\Livewire\Event\Component\ComponentUpdating;
use Spiral\Livewire\Exception\Component\BadMethodCallException;
use Spiral\Livewire\Exception\Component\ModelNotFoundException;
use Spiral\Livewire\Exception\Component\ModelNotWritableException;
use Spiral\Livewire\Str;

final class ActionHandler implements ActionHandlerInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ResolverInterface $resolver,
        private readonly PropertyHasherInterface $hasher,
        private readonly DataAccessorInterface $dataAccessor
    ) {
    }

    /**
     * @param non-empty-string $method
     *
     * @throws BadMethodCallException
     * @throws ModelNotWritableException
     */
    public function callMethod(
        LivewireComponent $component,
        string $method,
        array $params = [],
        callable $captureReturnValueCallback = null
    ): void {
        if (null !== ComponentMethod::tryFrom($method)) {
            $this->callComponentStateMethod($component, ComponentMethod::from($method), $params);

            return;
        }

        $methodRef = $this->getComponentMethod($component, $method);

        /** @var ComponentCallingMethod $event */
        $event = $this->dispatcher->dispatch(new ComponentCallingMethod($component, $methodRef, $params));

        if ($event->shouldSkipCalling) {
            return;
        }

        $returned = $methodRef->invoke($component, ...$this->resolver->resolveArguments($methodRef, $params));

        $this->dispatcher->dispatch(new ComponentCalledMethod($component, $methodRef, $params));

        $captureReturnValueCallback && $captureReturnValueCallback($returned);
    }

    /**
     * @param non-empty-string $name
     *
     * @throws ModelNotWritableException
     * @throws ModelNotFoundException
     */
    public function syncInput(
        LivewireComponent $component,
        string $name,
        string|float|array|bool $value,
        bool $rehash = true
    ): void {
        $property = Str::before($name, '.');

        $component->updating($name, $value);

        $this->dispatcher->dispatch(new ComponentUpdating($component, $name, $value));

        if (!$this->dataAccessor->hasModel($component, $property)) {
            throw new ModelNotFoundException(sprintf(
                'Unable to set component data. Model `%s` not found on component: `%s`.',
                $property,
                $component->getComponentName()
            ));
        }

        $this->dataAccessor->setValue($component, $name, $value);

        $rehash && $this->hasher->hash($component->getComponentId(), $name, $value);

        $component->updated($name, $value);

        $this->dispatcher->dispatch(new ComponentUpdated($component, $name, $value));
    }

    /**
     * @throws ModelNotWritableException
     */
    private function callComponentStateMethod(
        LivewireComponent $component,
        ComponentMethod $method,
        array $params
    ): void {
        switch ($method) {
            case ComponentMethod::Sync:
            case ComponentMethod::Set:
                $prop = array_shift($params);
                $head = reset($params);
                $this->syncInput($component, $prop, $head, ComponentMethod::Sync === $method);

                return;
            case ComponentMethod::Toggle:
                $prop = array_shift($params);

                $this->syncInput($component, $prop, !$this->dataAccessor->getValue($component, $prop), false);

                return;
        }
    }

    /**
     * @param non-empty-string $method
     *
     * @throws BadMethodCallException
     */
    private function getComponentMethod(LivewireComponent $component, string $method): \ReflectionMethod
    {
        $ref = new \ReflectionClass($component);

        if ($ref->hasMethod($method)) {
            $ref = new \ReflectionMethod($component, $method);

            if ($ref->isPublic()) {
                return $ref;
            }
        }

        throw new BadMethodCallException(sprintf(
            'Unable to call component method. Public method `%s` not found on component: `%s`.',
            $method,
            $component->getComponentName()
        ));
    }
}
