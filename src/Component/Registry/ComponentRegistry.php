<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Registry;

use Psr\Container\ContainerInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\ComponentNotFoundException;

/**
 * @psalm-import-type TComponentName from \Spiral\Livewire\Component\LivewireComponent
 */
final class ComponentRegistry implements ComponentRegistryInterface
{
    /**
     * @var array<TComponentName, \Closure>
     */
    private array $components = [];

    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @param non-empty-string $name
     */
    public function add(string $name, \Closure $component): void
    {
        if (!$this->hasComponent($name)) {
            $this->components[$name] = $component;
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

        return $this->components[$componentName]($this->container);
    }

    /**
     * @param TComponentName $componentName
     */
    public function hasComponent(string $componentName): bool
    {
        return isset($this->components[$componentName]);
    }
}
