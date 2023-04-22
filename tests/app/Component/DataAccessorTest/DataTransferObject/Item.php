<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject;

final class Item
{
    public string $name;
    public float $price;

    public static function create(string $name, float $price): self
    {
        $self = new self();
        $self->name = $name;
        $self->price = $price;

        return $self;
    }
}
