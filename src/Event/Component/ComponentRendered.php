<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Views\ViewInterface;

final class ComponentRendered
{
    public function __construct(
        public readonly LivewireComponent $component,
        public readonly ViewInterface $view
    ) {
    }
}
