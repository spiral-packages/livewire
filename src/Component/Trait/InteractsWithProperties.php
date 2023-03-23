<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Trait;

use Spiral\Livewire\Dot;

trait InteractsWithProperties
{
    public function getPublicPropertiesDefinedBySubClass(): array
    {
        $publicProperties = array_filter((new \ReflectionObject($this))->getProperties(), static function ($property) {
            return $property->isPublic() && !$property->isStatic();
        });

        $data = [];
        foreach ($publicProperties as $property) {
            if (self::class !== $property->getDeclaringClass()->getName()) {
                $data[$property->getName()] = $this->getInitializedPropertyValue($property);
            }
        }

        return $data;
    }

    public function getInitializedPropertyValue(\ReflectionProperty $property): mixed
    {
        if (!$property->isInitialized($this)) {
            return null;
        }

        return $property->getValue($this);
    }

    public function hasProperty(string $property): bool
    {
        return property_exists(
            $this,
            $this->beforeFirstDot($property)
        );
    }

    public function getPropertyValue(string $name): mixed
    {
        $value = $this->{$this->beforeFirstDot($name)};

        if ($this->containsDots($name)) {
            return Dot::get($value, $this->afterFirstDot($name));
        }

        return $value;
    }

    public function setProtectedPropertyValue(string $name, mixed $value): mixed
    {
        return $this->{$name} = $value;
    }

    public function containsDots(string $subject): bool
    {
        return str_contains($subject, '.');
    }

    public function beforeFirstDot(string $subject): string
    {
        $result = explode('.', $subject);

        return reset($result);
    }

    public function afterFirstDot(string $subject): string
    {
        $result = explode('.', $subject);
        unset($result[0]);

        return implode('.', $result);
    }

    public function propertyIsPublicAndNotDefinedOnBaseClass(string $propertyName): bool
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

    public function fill(mixed $values): void
    {
        $publicProperties = array_keys($this->getPublicPropertiesDefinedBySubClass());

        if ($values instanceof \JsonSerializable) {
            $values = $values->jsonSerialize();
        }

        foreach ($values as $key => $value) {
            if (\in_array($this->beforeFirstDot($key), $publicProperties, true)) {
                Dot::set($this, $key, $value);
            }
        }
    }
}
