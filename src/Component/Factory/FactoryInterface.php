<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Factory;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;

interface FactoryInterface
{
    /**
     * @param non-empty-string $name
     * @param class-string<LivewireComponent> $component
     */
    public function create(string $name, string $component, Component $componentAttr): \Closure;
}
