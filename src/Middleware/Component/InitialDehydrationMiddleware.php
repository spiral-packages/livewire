<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

interface InitialDehydrationMiddleware
{
    public function initialDehydrate(LivewireComponent $component, Response $response): void;
}
