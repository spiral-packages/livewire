<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Adbar\Dot;
use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Attribute\Model;
use Spiral\Marshaller\MarshallerInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

final class DataAccessor implements DataAccessorInterface
{
    public function __construct(
        private readonly MarshallerInterface $marshaller,
        private readonly ReaderInterface $reader
    ) {
    }

    public function getData(LivewireComponent $component): array
    {
        $models = [];
        foreach ((new \ReflectionClass($component))->getProperties() as $property) {
            if (null !== $this->reader->firstPropertyMetadata($property, Model::class)) {
                $models[] = $property->getName();
            }
        }

        /** @var array $data */
        $data = \array_filter(
            $this->marshaller->marshal($component),
            static fn (string $key) => \in_array($key, $models, true),
            \ARRAY_FILTER_USE_KEY
        );

        return $data;
    }

    /**
     * @param non-empty-string $propertyPath
     */
    public function setValue(LivewireComponent $component, string $propertyPath, mixed $value): void
    {
        $data = new Dot($this->getData($component));
        $data->set($propertyPath, $value);

        $this->marshaller->unmarshal($data->all(), $component);
    }

    /**
     * @param non-empty-string $propertyPath
     */
    public function getValue(LivewireComponent $component, string $propertyPath, mixed $default = null): mixed
    {
        $data = new Dot($this->getData($component));

        return $data->get($propertyPath, $default);
    }

    /**
     * @param non-empty-string $propertyPath
     */
    public function hasModel(LivewireComponent $component, string $propertyPath): bool
    {
        $path = new PropertyPath($propertyPath);

        foreach ((new \ReflectionClass($component))->getProperties() as $reflection) {
            $model = $this->reader->firstPropertyMetadata($reflection, Model::class);

            if ((null !== $model) && $path->getElement(0) === $reflection->getName()) {
                return true;
            }
        }

        return false;
    }
}
