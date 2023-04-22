<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Adbar\Dot;
use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Exception\Component\ModelNotWritableException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

final class DataAccessor implements DataAccessorInterface
{
    public function __construct(
        private readonly Serializer $serializer,
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
        $data = $this->serializer->normalize(data: $component, context: [AbstractNormalizer::ATTRIBUTES => $models]);

        return $data;
    }

    /**
     * @param non-empty-string $propertyPath
     *
     * @throws ModelNotWritableException
     */
    public function setValue(LivewireComponent $component, string $propertyPath, mixed $value): void
    {
        $data = new Dot($this->getData($component));
        $data->set($propertyPath, $value);

        $this->serializer->denormalize(
            data: $data->all(),
            type: $component::class,
            context: [AbstractNormalizer::OBJECT_TO_POPULATE => $component]
        );
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
}
