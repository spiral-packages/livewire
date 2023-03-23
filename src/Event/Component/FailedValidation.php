<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Filters\Exception\ValidationException;
use Spiral\Livewire\Component\LivewireComponent;

final class FailedValidation
{
    public function __construct(
        public readonly LivewireComponent $component,
        public readonly ValidationException $exception
    ) {
    }
}
