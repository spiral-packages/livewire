<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

final class PropertyDehydrate
{
    public function __construct(
        public readonly string $property,
        public readonly mixed $value,
        public readonly LivewireComponent $component,
        public readonly Response $response
    ) {
    }
}
