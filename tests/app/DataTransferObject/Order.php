<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\DataTransferObject;

final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly array $items
    ) {
    }
}
