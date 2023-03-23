<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Livewire\Component\LivewireComponent;

final class FlushState
{
    public function __construct(
        public readonly LivewireComponent $component
    ) {
    }
}
