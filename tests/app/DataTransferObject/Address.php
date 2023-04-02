<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\DataTransferObject;

final class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $state
    ) {
    }
}
