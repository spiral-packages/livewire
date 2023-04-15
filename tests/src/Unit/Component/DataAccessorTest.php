<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Component;

use PHPUnit\Framework\TestCase;
use Spiral\Attributes\AttributeReader;
use Spiral\Livewire\Bootloader\LivewireBootloader;
use Spiral\Livewire\Component\DataAccessor;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\Component\User;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Address;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Item;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Order;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class DataAccessorTest extends TestCase
{
    private PropertyAccessorInterface $propertyAccessor;

    protected function setUp(): void
    {
        $ref = new \ReflectionMethod(LivewireBootloader::class, 'initPropertyAccessor');
        $this->propertyAccessor = $ref->invoke(new LivewireBootloader());
    }

    /**
     * @dataProvider getValueDataProvider
     */
    public function testGetValue(LivewireComponent $component, string $propertyPath, mixed $expectedValue): void
    {
        $accessor = new DataAccessor($this->propertyAccessor, new AttributeReader());

        $this->assertSame($expectedValue, $accessor->getValue($component, $propertyPath));
    }

    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValue(LivewireComponent $component, string $propertyPath, mixed $value): void
    {
        $accessor = new DataAccessor($this->propertyAccessor, new AttributeReader());

        $accessor->setValue($component, $propertyPath, $value);

        $this->assertSame($value, $accessor->getValue($component, $propertyPath));
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
            ),
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
                '555-5678',
            ],
            'orders' => [
                [
                    'id' => 1,
                    'items' => [
                        [
                            'name' => 'Widget',
                            'price' => 19.99,
                        ],
                        [
                            'name' => 'Gizmo',
                            'price' => 9.99,
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'items' => [
                        [
                            'name' => 'Thingamajig',
                            'price' => 14.99,
                        ],
                        [
                            'name' => 'Doodad',
                            'price' => 5.99,
                        ],
                    ],
                ],
            ],
        ], $accessor->getData($user));
    }

    public function getValueDataProvider(): \Traversable
    {
        yield [
            new class extends LivewireComponent
            {
                public string $name = 'foo';
            },
            'name',
            'foo',
        ];
        yield [
            new class extends LivewireComponent
            {
                private string $name = 'foo';

                public function getName(): string
                {
                    return $this->name;
                }
            },
            'name',
            'foo',
        ];
        yield [
            new class extends LivewireComponent
            {
                public array $prices = [10, 100.0];
            },
            'prices',
            [10, 100.0],
        ];
        yield [
            new class extends LivewireComponent
            {
                private array $prices = [10, 100.0];

                public function getPrices(): array
                {
                    return $this->prices;
                }
            },
            'prices',
            [10, 100.0],
        ];
        yield [
            new class extends LivewireComponent
            {
                public Address $address;

                public function __construct() {
                    $this->address = new Address('', 'New York', '');
                }
            },
            'address.city',
            'New York',
        ];
        yield [
            new class extends LivewireComponent
            {
                public array $form = ['name' => 'John'];
            },
            'form.name',
            'John',
        ];
        yield [
            new class extends LivewireComponent
            {
                public array $orders = [];

                public function __construct()
                {
                    $this->orders = [
                        new Order(id: 1, items: [new Item(name: 'Test', price: 100.3)]),
                        new Order(id: 2, items: [new Item(name: 'Test 2', price: 80.2)]),
                    ];
                }
            },
            'orders.1.items.0.name',
            'Test 2',
        ];
    }

    public function setValueDataProvider(): \Traversable
    {
        yield [
            new class extends LivewireComponent
            {
                public string $name;
            },
            'name',
            'foo',
        ];
        yield [
            new class extends LivewireComponent
            {
                private string $name;

                public function setName(string $name): void
                {
                    $this->name = $name;
                }

                public function getName(): string
                {
                    return $this->name;
                }
            },
            'name',
            'foo',
        ];
        yield [
            new class extends LivewireComponent
            {
                public array $prices;
            },
            'prices',
            [10, 100.0],
        ];
        yield [
            new class extends LivewireComponent
            {
                private array $prices;

                public function setPrices(array $prices): void
                {
                    $this->prices = $prices;
                }

                public function getPrices(): array
                {
                    return $this->prices;
                }
            },
            'prices',
            [10, 100.0],
        ];
        yield [
            new class extends LivewireComponent
            {
                public Address $address;

                public function __construct() {
                    $this->address = new Address('', '', '');
                }
            },
            'address.city',
            'New York',
        ];
        yield [
            new class extends LivewireComponent
            {
                public array $form = [];
            },
            'form.name',
            'John',
        ];
        yield [
            new class extends LivewireComponent
            {
                public array $orders = [];

                public function __construct()
                {
                    $this->orders = [
                        new Order(id: 1, items: [new Item(name: 'Test', price: 100.3)]),
                        new Order(id: 2, items: [new Item(name: 'Foo', price: 80.2)]),
                    ];
                }
            },
            'orders.1.items.0.name',
            'Test 2',
        ];
    }
}
