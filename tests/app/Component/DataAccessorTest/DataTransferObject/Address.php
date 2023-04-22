<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject;

final class Address
{
    public string $street;
    public string $city;
    public string $state;

    public static function create(string $street, string $city, string $state): self
    {
        $self = new self();
        $self->street = $street;
        $self->city = $city;
        $self->state = $state;

        return $self;
    }
}
