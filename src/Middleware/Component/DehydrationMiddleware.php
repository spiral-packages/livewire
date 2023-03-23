<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

interface DehydrationMiddleware
{
    public function dehydrate(LivewireComponent $component, Response $response): void;
}
