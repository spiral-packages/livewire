<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Attribute\Model;
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
            $model = $this->reader->firstPropertyMetadata($property, Model::class);

            if (null !== $model) {
                $data[$property->getName()] = $this->propertyAccessor->isReadable($component, $property->getName())
                    ? $this->propertyAccessor->getValue($component, $property->getName())
                    : null;
            }
        }

        return $data;
    }
}
