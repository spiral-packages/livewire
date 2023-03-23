<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Livewire\Component\LivewireComponent;

final class ComponentUpdated
{
    public function __construct(
        public readonly LivewireComponent $component,
        public readonly string $name,
        public readonly mixed $value
    ) {
    }
}
