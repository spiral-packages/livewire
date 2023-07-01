<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject;

use Spiral\Marshaller\Meta\MarshalArray;

final class Order
{
    public int $id;

    #[MarshalArray(of: Item::class)]
    public array $items;

    public static function create(int $id, array $items): self
    {
        $self = new self();
        $self->id = $id;
        $self->items = $items;

        return $self;
    }
}
