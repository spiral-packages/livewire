<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject;

final class Order
{
    public int $id;
    public array $items;

    public static function create(int $id, array $items): self
    {
        $self = new self();
        $self->id = $id;
        $self->items = $items;

        return $self;
    }
}
