<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\HydrationMiddleware;

final class HydrationMiddlewareRegistry implements HydrationMiddlewareRegistryInterface
{
    /**
     * @var HydrationMiddleware[]
     */
    private array $middleware = [];

    public function add(HydrationMiddleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return HydrationMiddleware[]
     */
    public function all(): array
    {
        return $this->middleware;
    }
}
