<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\ComponentNotFoundException;

interface ComponentRegistryInterface
{
    public function add(LivewireComponent $component): void;

    /**
     * @param non-empty-string $componentName
     *
     * @throws ComponentNotFoundException
     */
    public function get(string $componentName): LivewireComponent;
}
