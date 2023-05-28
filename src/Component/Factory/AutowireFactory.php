<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component\Factory;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Component\PropertyHasherInterface;
use Spiral\Livewire\Str;
use Spiral\Router\RouterInterface;
use Spiral\Views\ViewsInterface;

final class AutowireFactory implements FactoryInterface
{
    /**
     * @param non-empty-string $name
     * @param class-string<LivewireComponent> $component
     */
    public function create(string $name, string $component, Component $componentAttr): \Closure
    {
        return static function (ContainerInterface $container) use (
            $name,
            $component,
            $componentAttr
        ): LivewireComponent {
            $componentRef = new \ReflectionClass($component);

            $component = $container->get($componentRef->getName());
            \assert($component instanceof LivewireComponent);

            $componentRef->getMethod('configure')->invoke(
                $component,
                $name,
                $componentAttr->template ?? Str::kebab($componentRef->getName()),
                $container->get(ViewsInterface::class),
                $container->get(ResolverInterface::class),
                $container->get(PropertyHasherInterface::class),
                $container->get(EventDispatcherInterface::class),
                $container->get(RouterInterface::class)
            );

            return $component;
        };
    }
}
