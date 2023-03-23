<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\DehydrationMiddleware;

final class DehydrationMiddlewareRegistry implements DehydrationMiddlewareRegistryInterface
{
    /**
     * @var DehydrationMiddleware[]
     */
    private array $middleware = [];

    public function add(DehydrationMiddleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return DehydrationMiddleware[]
     */
    public function all(): array
    {
        return $this->middleware;
    }
}
