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
        if (!$this->propertyAccessor->isReadable($component, $propertyPath)) {
            return $default;
        }

        return $this->propertyAccessor->getValue($component, $propertyPath);
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
}
