<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

final class PropertyHasher implements PropertyHasherInterface
{
    private array $hashes = [];

    /**
     * @param non-empty-string $componentId
     */
    public function getHashes(string $componentId): array
    {
        return $this->hashes[$componentId] ?? [];
    }

    /**
     * @param non-empty-string $componentId
     * @param non-empty-string $propertyName
     */
    public function hash(string $componentId, string $propertyName, mixed $value): void
    {
        $this->hashes[$componentId][$propertyName] = $this->generateHash($value);
    }

    /**
     * @param non-empty-string|int $hash
     */
    public function isEquals(int|string $hash, mixed $value): bool
    {
        return $this->generateHash($value) === $value;
    }

    private function generateHash(mixed $value): int|string
    {
        if (null !== $value && !\is_string($value) && !is_numeric($value) && !\is_bool($value)) {
            if (\is_array($value)) {
                return json_encode($value);
            }
            $value = method_exists($value, '__toString')
                ? (string) $value
                : json_encode($value);
        }

        // Using crc32 because it's fast, and this doesn't have to be secure.
        return crc32((string) $value ?? '');
    }
}
