<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\DehydrationMiddleware;

interface DehydrationMiddlewareRegistryInterface
{
    public function add(DehydrationMiddleware $middleware): void;

    /**
     * @return DehydrationMiddleware[]
     */
    public function all(): array;
}
