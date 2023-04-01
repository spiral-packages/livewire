<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Spiral\Livewire\Exception\Component\ModelNotWritableException;

interface DataAccessorInterface
{
    public function getData(LivewireComponent $component): array;

    /**
     * @param non-empty-string $propertyPath
     */
    public function getValue(LivewireComponent $component, string $propertyPath, mixed $default = null): mixed;

    /**
     * @param non-empty-string $propertyPath
     *
     * @throws ModelNotWritableException
     */
    public function setValue(LivewireComponent $component, string $propertyPath, mixed $value): void;

    /**
     * @param non-empty-string $property
     */
    public function hasModel(LivewireComponent $component, string $property): bool;
}
