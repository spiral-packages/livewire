<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\ComponentNotFoundException;

final class ComponentRegistry implements ComponentRegistryInterface
{
    /**
     * @var array<non-empty-string, LivewireComponent>
     */
    private array $components = [];

    public function add(LivewireComponent $component): void
    {
        if (!$this->hasComponent($component->getName())) {
            $this->components[$component->getName()] = $component;
        }
    }

    /**
     * @param non-empty-string $componentName
     *
     * @throws ComponentNotFoundException
     */
    public function get(string $componentName): LivewireComponent
    {
        if (!$this->hasComponent($componentName)) {
            throw new ComponentNotFoundException(sprintf('Unable to find component: `%s`.', $componentName));
        }

        return $this->components[$componentName];
    }

    /**
     * @param non-empty-string $componentName
     */
    public function hasComponent(string $componentName): bool
    {
        return isset($this->components[$componentName]);
    }
}
