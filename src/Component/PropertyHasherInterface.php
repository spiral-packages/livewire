<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

interface PropertyHasherInterface
{
    /**
     * @param non-empty-string $componentId
     */
    public function getHashes(string $componentId): array;

    /**
     * @param non-empty-string $componentId
     * @param non-empty-string $propertyName
     */
    public function hash(string $componentId, string $propertyName, mixed $value): void;

    /**
     * @param non-empty-string|int $hash
     */
    public function isEquals(int|string $hash, mixed $value): bool;
}
