<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\DataTransferObject;

final class Item
{
    public function __construct(
        public readonly string $name,
        public readonly float $price
    ) {
    }
}
