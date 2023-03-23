<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\InitialHydrationMiddleware;

interface InitialHydrationMiddlewareRegistryInterface
{
    public function add(InitialHydrationMiddleware $middleware): void;

    /**
     * @return InitialHydrationMiddleware[]
     */
    public function all(): array;
}
