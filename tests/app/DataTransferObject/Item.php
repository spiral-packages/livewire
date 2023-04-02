<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\DataTransferObject;

final class Item
{
    public function __construct(
        public string $name,
        public float $price
    ) {
    }
}
