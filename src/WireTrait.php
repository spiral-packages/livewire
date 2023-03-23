<?php

declare(strict_types=1);

namespace Spiral\Livewire;

use Spiral\Core\Container\Autowire;
use Spiral\Core\FactoryInterface;

trait WireTrait
{
    private function wire(mixed $alias, FactoryInterface $factory): mixed
    {
        return match (true) {
            \is_string($alias) => $factory->make($alias),
            $alias instanceof Autowire => $alias->resolve($factory),
            default => $alias
        };
    }
}
