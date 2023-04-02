<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Exception\Component\ModelNotWritableException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class DataAccessor implements DataAccessorInterface
{
    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly ReaderInterface $reader
    ) {
    }

    public function getData(LivewireComponent $component): array
    {
        $data = [];
        foreach ((new \ReflectionClass($component))->getProperties() as $property) {
            if (null !== $this->reader->firstPropertyMetadata($property, Model::class)) {
                $value = $this->propertyAccessor->isReadable($component, $property->getName())
                    ? $this->propertyAccessor->getValue($component, $property->getName())
                    : null;

                $data[$property->getName()] = match (true) {
                    \is_array($value) => $this->getArrayData($value),
                    \is_object($value) => $this->getObjectData($value),
                    default => $value
                };
            }
        }

        return $data;
    }

    /**
     * @param non-empty-string $propertyPath
     *
     * @throws ModelNotWritableException
     */
    public function setValue(LivewireComponent $component, string $propertyPath, mixed $value): void
    {
        $model = $component;
        /** @var array<non-empty-string> $pathParts */
        $pathParts = explode('.', $propertyPath);

        $nextValueArrayKey = false;
        foreach ($pathParts as $key => $part) {
            if ($nextValueArrayKey) {
                $pathParts[$key] = '['.$part.']';
            }
            $nextValueArrayKey = false;
            $model = $this->getModelValue($model, $part);
            if (\is_array($model)) {
                $nextValueArrayKey = true;
            }
        }

        $propertyPath = '';
        foreach ($pathParts as $part) {
            $propertyPath .= str_starts_with($part, '[') || '' === $propertyPath ? $part : '.'.$part;
        }

        // Set the value of the target property.
        if (!$this->propertyAccessor->isWritable($component, $propertyPath)) {
            throw new ModelNotWritableException(sprintf(
                'Unable to set component data. Model `%s` not found on component: `%s`.',
                $propertyPath,
                $component->getComponentName()
            ));
        }

        $this->propertyAccessor->setValue($component, $propertyPath, $value);
    }

    /**
     * @param non-empty-string $propertyPath
     */
    public function getValue(LivewireComponent $component, string $propertyPath, mixed $default = null): mixed
    {
        $model = $component;
        /** @var non-empty-string $part */
        foreach (explode('.', $propertyPath) as $part) {
            $model = $this->getModelValue($model, $part);
        }

        return $model;
    }

    /**
     * @param non-empty-string $property
     */
    public function hasModel(LivewireComponent $component, string $property): bool
    {
        foreach ((new \ReflectionClass($component))->getProperties() as $reflection) {
            $model = $this->reader->firstPropertyMetadata($reflection, Model::class);

            if ((null !== $model) && $property === $reflection->getName()) {
                return true;
            }
        }

        return false;
    }

    private function getObjectData(object $object): array
    {
        $data = [];
        foreach ((new \ReflectionClass($object))->getProperties() as $property) {
            $value = $this->propertyAccessor->isReadable($object, $property->getName())
                ? $this->propertyAccessor->getValue($object, $property->getName())
                : null;

            $data[$property->getName()] = match (true) {
                \is_array($value) => $this->getArrayData($value),
                \is_object($value) => $this->getObjectData($value),
                default => $value
            };
        }

        return $data;
    }

    private function getArrayData(array $array): array
    {
        $data = [];
        foreach ($array as $key => $arrayValue) {
            $data[$key] = match (true) {
                \is_array($arrayValue) => $this->getArrayData($arrayValue),
                \is_object($arrayValue) => $this->getObjectData($arrayValue),
                default => $arrayValue
            };
        }

        return $data;
    }

    /**
     * @param non-empty-string $propertyPath
     */
    public function getModelValue(object|array $model, string $propertyPath, mixed $default = null): mixed
    {
        if (\is_array($model)) {
            $propertyPath = '['.$propertyPath.']';
        }

        if (!$this->propertyAccessor->isReadable($model, $propertyPath)) {
            return $default;
        }

        return $this->propertyAccessor->getValue($model, $propertyPath);
    }
}
