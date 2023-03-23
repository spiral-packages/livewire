<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Livewire\Component\LivewireComponent;

final class ActionReturned
{
    public function __construct(
        public readonly LivewireComponent $component,
        public readonly string $method,
        public readonly mixed $returned,
        public readonly string $id
    ) {
    }
}
