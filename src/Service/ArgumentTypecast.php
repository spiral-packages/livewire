<?php

declare(strict_types=1);

namespace Spiral\Livewire\Service;

use Spiral\Livewire\Exception\InvalidTypeException;

class ArgumentTypecast
{
    /**
     * @throws InvalidTypeException
     * @throws \JsonException
     */
    public function cast(array $arguments, \ReflectionMethod $method): array
    {
        foreach ($arguments as $name => $value) {
            foreach ($method->getParameters() as $parameter) {
                if ($name === $parameter->getName()) {
                    if (($type = $this->getType($parameter)) === null) {
                        continue;
                    }

                    if ($value === null && $parameter->allowsNull()) {
                        continue;
                    }

                    $arguments[$name] = match (true) {
                        $type->getName() === 'bool' => \filter_var($value, \FILTER_VALIDATE_BOOL),
                        $type->getName() === 'int' => (int) $value,
                        $type->getName() === 'float' => (float) $value,
                        $type->getName() === 'array' && !empty($value) =>
                            \json_decode($value, true, 512, JSON_THROW_ON_ERROR),
                        default => $value
                    };
                }
            }
        }

        return $arguments;
    }

    /**
     * @throws InvalidTypeException
     */
    private function getType(\ReflectionParameter $parameter): ?\ReflectionNamedType
    {
        if (!$parameter->hasType()) {
            return null;
        }

        $type = $parameter->getType();

        if ($type instanceof \ReflectionIntersectionType) {
            throw new InvalidTypeException(\sprintf('Invalid type for the `%s` property.', $parameter->getName()));
        }

        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $type) {
                if ($type instanceof \ReflectionNamedType && $type->isBuiltin()) {
                    return $type;
                }
            }
        }

        if ($type instanceof \ReflectionNamedType && $type->isBuiltin()) {
            return $type;
        }

        return null;
    }
}
