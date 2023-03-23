<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

final class ComponentDehydrate
{
    public function __construct(
        public readonly LivewireComponent $component,
        public readonly Response $response
    ) {
    }
}
