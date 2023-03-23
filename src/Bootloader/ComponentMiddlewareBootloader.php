<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\FactoryInterface;
use Spiral\Livewire\Config\LivewireConfig;
use Spiral\Livewire\Middleware\Component\DehydrationMiddleware;
use Spiral\Livewire\Middleware\Component\HydrationMiddleware;
use Spiral\Livewire\Middleware\Component\InitialDehydrationMiddleware;
use Spiral\Livewire\Middleware\Component\InitialHydrationMiddleware;
use Spiral\Livewire\Middleware\Component\Registry\DehydrationMiddlewareRegistry;
use Spiral\Livewire\Middleware\Component\Registry\DehydrationMiddlewareRegistryInterface;
use Spiral\Livewire\Middleware\Component\Registry\HydrationMiddlewareRegistry;
use Spiral\Livewire\Middleware\Component\Registry\HydrationMiddlewareRegistryInterface;
use Spiral\Livewire\Middleware\Component\Registry\InitialDehydrationMiddlewareRegistry;
use Spiral\Livewire\Middleware\Component\Registry\InitialDehydrationMiddlewareRegistryInterface;
use Spiral\Livewire\Middleware\Component\Registry\InitialHydrationMiddlewareRegistry;
use Spiral\Livewire\Middleware\Component\Registry\InitialHydrationMiddlewareRegistryInterface;
use Spiral\Livewire\WireTrait;

final class ComponentMiddlewareBootloader extends Bootloader
{
    use WireTrait;

    protected const SINGLETONS = [
        InitialHydrationMiddlewareRegistryInterface::class => [self::class, 'initInitialHydrationMiddlewareRegistry'],
        HydrationMiddlewareRegistryInterface::class => [self::class, 'initHydrationMiddlewareRegistry'],
        InitialDehydrationMiddlewareRegistryInterface::class => [self::class, 'initInitialDehydrationMiddlewareRegistry'],
        DehydrationMiddlewareRegistryInterface::class => [self::class, 'initDehydrationMiddlewareRegistry'],
    ];

    private function initInitialHydrationMiddlewareRegistry(
        LivewireConfig $config,
        FactoryInterface $factory
    ): InitialHydrationMiddlewareRegistry {
        $registry = new InitialHydrationMiddlewareRegistry();

        foreach ($config->getInitialHydrationMiddleware() as $middleware) {
            $middleware = $this->wire($middleware, $factory);

            \assert($middleware instanceof InitialHydrationMiddleware);
            $registry->add($middleware);
        }

        return $registry;
    }

    private function initHydrationMiddlewareRegistry(
        LivewireConfig $config,
        FactoryInterface $factory
    ): HydrationMiddlewareRegistry {
        $registry = new HydrationMiddlewareRegistry();

        foreach ($config->getHydrationMiddleware() as $middleware) {
            $middleware = $this->wire($middleware, $factory);

            \assert($middleware instanceof HydrationMiddleware);
            $registry->add($middleware);
        }

        return $registry;
    }

    private function initInitialDehydrationMiddlewareRegistry(
        LivewireConfig $config,
        FactoryInterface $factory
    ): InitialDehydrationMiddlewareRegistry {
        $registry = new InitialDehydrationMiddlewareRegistry();

        foreach ($config->getInitialDehydrationMiddleware() as $middleware) {
            $middleware = $this->wire($middleware, $factory);

            \assert($middleware instanceof InitialDehydrationMiddleware);
            $registry->add($middleware);
        }

        return $registry;
    }

    private function initDehydrationMiddlewareRegistry(
        LivewireConfig $config,
        FactoryInterface $factory
    ): DehydrationMiddlewareRegistry {
        $registry = new DehydrationMiddlewareRegistry();

        foreach ($config->getDehydrationMiddleware() as $middleware) {
            $middleware = $this->wire($middleware, $factory);

            \assert($middleware instanceof DehydrationMiddleware);
            $registry->add($middleware);
        }

        return $registry;
    }
}
