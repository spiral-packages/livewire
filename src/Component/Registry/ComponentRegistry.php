<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\ComponentNotFoundException;

/**
 * @psalm-import-type TComponentName from \Spiral\Livewire\Component\LivewireComponent
 */
final class ComponentRegistry implements ComponentRegistryInterface
{
    /**
     * @var array<TComponentName, LivewireComponent>
     */
    private array $components = [];

    public function add(LivewireComponent $component): void
    {
        if (!$this->hasComponent($component->getComponentName())) {
            $this->components[$component->getComponentName()] = $component;
        }
    }

    /**
     * @param TComponentName $componentName
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
     * @param TComponentName $componentName
     */
    public function hasComponent(string $componentName): bool
    {
        return isset($this->components[$componentName]);
    }
}
