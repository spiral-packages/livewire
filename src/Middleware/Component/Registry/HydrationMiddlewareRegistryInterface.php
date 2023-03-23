<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\HydrationMiddleware;

interface HydrationMiddlewareRegistryInterface
{
    public function add(HydrationMiddleware $middleware): void;

    /**
     * @return HydrationMiddleware[]
     */
    public function all(): array;
}
