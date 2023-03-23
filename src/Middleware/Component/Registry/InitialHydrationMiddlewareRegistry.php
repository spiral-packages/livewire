<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\InitialHydrationMiddleware;

final class InitialHydrationMiddlewareRegistry implements InitialHydrationMiddlewareRegistryInterface
{
    /**
     * @var InitialHydrationMiddleware[]
     */
    private array $middleware = [];

    public function add(InitialHydrationMiddleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return InitialHydrationMiddleware[]
     */
    public function all(): array
    {
        return $this->middleware;
    }
}
