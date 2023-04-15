<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject;

final class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $state
    ) {
    }
}