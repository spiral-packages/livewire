<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Component;

use PHPUnit\Framework\TestCase;
use Spiral\Attributes\AttributeReader;
use Spiral\Livewire\Bootloader\LivewireBootloader;
use Spiral\Livewire\Component\DataAccessor;
use Spiral\Livewire\Tests\App\Component\User;
use Spiral\Livewire\Tests\App\DataTransferObject\Address;
use Spiral\Livewire\Tests\App\DataTransferObject\Item;
use Spiral\Livewire\Tests\App\DataTransferObject\Order;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class DataAccessorTest extends TestCase
{
    private PropertyAccessorInterface $propertyAccessor;

    protected function setUp(): void
    {
        $ref = new \ReflectionMethod(LivewireBootloader::class, 'initPropertyAccessor');
        $this->propertyAccessor = $ref->invoke(new LivewireBootloader());
    }

    public function testGetData(): void
    {
        $accessor = new DataAccessor($this->propertyAccessor, new AttributeReader());
        $orders = [
            new Order(
                id: 1,
                items: [
                    new Item(name: 'Widget', price: 19.99),
                    new Item(name: 'Gizmo', price: 9.99),
                ]
            ),
            new Order(
                id: 2,
                items: [
                    new Item(name: 'Thingamajig', price: 14.99),
                    new Item(name: 'Doodad', price: 5.99),
                ]
            )
        ];

        $user = new User();
        $user->id = 1;
        $user->name = 'John';
        $user->email = 'john@localhost';
        $this->propertyAccessor->setValue(
            $user,
            'address',
            new Address('123 Main St', 'Anytown', 'CA')
        );
        $this->propertyAccessor->setValue($user, 'phoneNumbers', ['555-1234', '555-5678']);
        $this->propertyAccessor->setValue($user, 'orders', $orders);

        $this->assertSame([
            'id' => 1,
            'name' => 'John',
            'email' => 'john@localhost',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
            ],
            'phoneNumbers' => [
                '555-1234',
                '555-5678'
            ],
            'orders' => [
                [
                    'id' => 1,
                    'items' => [
                        [
                            'name' => 'Widget',
                            'price' => 19.99
                        ],
                        [
                            'name' => 'Gizmo',
                            'price' => 9.99
                        ]
                    ]
                ],
                [
                    'id' => 2,
                    'items' => [
                        [
                            'name' => 'Thingamajig',
                            'price' => 14.99
                        ],
                        [
                            'name' => 'Doodad',
                            'price' => 5.99
                        ]
                    ]
                ]
            ]
        ], $accessor->getData($user));
    }
}
