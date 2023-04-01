<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

use Spiral\Livewire\Exception\Component\BadMethodCallException;
use Spiral\Livewire\Exception\Component\ModelNotFoundException;
use Spiral\Livewire\Exception\Component\ModelNotWritableException;

interface ActionHandlerInterface
{
    /**
     * @param non-empty-string $method
     *
     * @throws BadMethodCallException
     * @throws ModelNotWritableException
     */
    public function callMethod(
        LivewireComponent $component,
        string $method,
        array $params = [],
        callable $captureReturnValueCallback = null
    ): void;

    /**
     * @param non-empty-string $name
     *
     * @throws ModelNotWritableException
     * @throws ModelNotFoundException
     */
    public function syncInput(
        LivewireComponent $component,
        string $name,
        string|float|array|bool $value,
        bool $rehash = true
    ): void;
}
