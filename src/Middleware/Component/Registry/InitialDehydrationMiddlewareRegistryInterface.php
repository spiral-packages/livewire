<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component\Registry;

use Spiral\Livewire\Middleware\Component\InitialDehydrationMiddleware;

interface InitialDehydrationMiddlewareRegistryInterface
{
    public function add(InitialDehydrationMiddleware $middleware): void;

    /**
     * @return InitialDehydrationMiddleware[]
     */
    public function all(): array;
}
