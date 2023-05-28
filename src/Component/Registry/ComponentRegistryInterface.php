<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\ComponentNotFoundException;

/**
 * @psalm-import-type TComponentName from \Spiral\Livewire\Component\LivewireComponent
 */
interface ComponentRegistryInterface
{
    /**
     * @param non-empty-string $name
     */
    public function add(string $name, \Closure $component): void;

    /**
     * @param TComponentName $componentName
     *
     * @throws ComponentNotFoundException
     */
    public function get(string $componentName): LivewireComponent;
}
