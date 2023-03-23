<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\InitialDehydrationMiddleware;

final class InitialDehydrationMiddlewareRegistry implements InitialDehydrationMiddlewareRegistryInterface
{
    /**
     * @var InitialDehydrationMiddleware[]
     */
    private array $middleware = [];

    public function add(InitialDehydrationMiddleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return InitialDehydrationMiddleware[]
     */
    public function all(): array
    {
        return $this->middleware;
    }
}
