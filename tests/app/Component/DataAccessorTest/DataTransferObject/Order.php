<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject;

final class Order
{
    public function __construct(
        public int $id,
        public array $items
    ) {
    }
}
