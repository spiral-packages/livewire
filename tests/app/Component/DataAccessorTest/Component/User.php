<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\DataAccessorTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Address;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Order;
use Spiral\Marshaller\Meta\MarshalArray;

#[Component(name: 'component-data-accessor-test-user')]
final class User extends LivewireComponent
{
    #[Model]
    public int $id;

    #[Model]
    public string $name;

    #[Model]
    public string $email;

    #[Model]
    public Address $address;

    #[Model]
    public array $phoneNumbers = [];

    #[Model]
    #[MarshalArray(of: Order::class)]
    public array $orders = [];

    public string $publicNonModelProperty = 'foo';
    private string $privateNonModelProperty = 'bar';
}
