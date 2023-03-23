<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Request;

interface InitialHydrationMiddleware
{
    public function initialHydrate(LivewireComponent $component, Request $request): void;
}
