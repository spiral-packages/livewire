<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\Component\User;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Address;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Item;
use Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Order;
use Spiral\Livewire\Tests\Functional\TestCase;
use Spiral\Marshaller\Meta\Marshal;
use Spiral\Marshaller\Meta\MarshalArray;

final class DataAccessorTest extends TestCase
{
    #[DataProvider('getValueDataProvider')]
    public function testGetValue(LivewireComponent $component, string $propertyPath, mixed $expectedValue): void
    {
        $accessor = $this->getContainer()->get(DataAccessorInterface::class);

        $this->assertSame($expectedValue, $accessor->getValue($component, $propertyPath));
    }

    #[DataProvider('setValueDataProvider')]
    public function testSetValue(LivewireComponent $component, string $propertyPath, mixed $value): void
    {
        $accessor = $this->getContainer()->get(DataAccessorInterface::class);

        $accessor->setValue($component, $propertyPath, $value);

        $this->assertSame($value, $accessor->getValue($component, $propertyPath));
    }

    public function testGetData(): void
    {
        $accessor = $this->getContainer()->get(DataAccessorInterface::class);
        $orders = [
            Order::create(
                id: 1,
                items: [
                    Item::create(name: 'Widget', price: 19.99),
                    Item::create(name: 'Gizmo', price: 9.99),
                ]
            ),
            Order::create(
                id: 2,
                items: [
                    Item::create(name: 'Thingamajig', price: 14.99),
                    Item::create(name: 'Doodad', price: 5.99),
                ]
            ),
        ];

        $user = new User();
        $user->id = 1;
        $user->name = 'John';
        $user->email = 'john@localhost';
        $user->address = Address::create('123 Main St', 'Anytown', 'CA');
        $user->phoneNumbers = ['555-1234', '555-5678'];
        $user->orders = $orders;

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

    #[DataProvider('pathsDataProvider')]
    public function testHasModel(string $path, bool $expected): void
    {
        $component = new class () extends LivewireComponent {
            #[Model]
            public array $data;
        };

        $accessor = $this->getContainer()->get(DataAccessorInterface::class);

        $this->assertSame($expected, $accessor->hasModel($component, $path));
    }

    public static function getValueDataProvider(): \Traversable
    {
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                public string $name = 'foo';
            },
            'name',
            'foo',
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Marshal]
                #[Model]
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
                #[Model]
                public array $prices = [10, 100.0];
            },
            'prices',
            [10, 100.0],
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Marshal]
                #[Model]
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
                #[Model]
                public Address $address;

                public function __construct() {
                    $this->address = Address::create('', 'New York', '');
                }
            },
            'address.city',
            'New York',
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                public array $form = ['name' => 'John'];
            },
            'form.name',
            'John',
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                #[MarshalArray(of: Order::class)]
                public array $orders = [];

                public function __construct()
                {
                    $this->orders = [
                        Order::create(id: 1, items: [Item::create(name: 'Test', price: 100.3)]),
                        Order::create(id: 2, items: [Item::create(name: 'Test 2', price: 80.2)]),
                    ];
                }
            },
            'orders.1.items.0.name',
            'Test 2',
        ];
    }

    public static function setValueDataProvider(): \Traversable
    {
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                public string $name;
            },
            'name',
            'foo',
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                #[Marshal]
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
                #[Model]
                public array $prices;
            },
            'prices',
            [10, 100.0],
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                #[Marshal]
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
                #[Model]
                public Address $address;
            },
            'address.city',
            'New York',
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                public array $form = [];
            },
            'form.name',
            'John',
        ];
        yield [
            new class extends LivewireComponent
            {
                #[Model]
                public array $formData = [];
            },
            'formData.main.email',
            'foo@gmail.com',
        ];
        yield [
            new class extends LivewireComponent
            {
                /**
                 * @var \Spiral\Livewire\Tests\App\Component\DataAccessorTest\DataTransferObject\Order[]
                 */
                #[Model]
                public array $orders = [];

                public function __construct()
                {
                    $this->orders = [
                        Order::create(id: 1, items: [Item::create(name: 'Test', price: 100.3)]),
                        Order::create(id: 2, items: [Item::create(name: 'Foo', price: 80.2)]),
                    ];
                }
            },
            'orders.1.items.0.name',
            'Test 2',
        ];
    }

    public static function pathsDataProvider(): \Traversable
    {
        yield ['data', true];
        yield ['data.foo', true];
        yield ['data[foo]', true];
        yield ['foo', false];
        yield ['foo.bar', false];
        yield ['foo[bar]', false];
    }
}
