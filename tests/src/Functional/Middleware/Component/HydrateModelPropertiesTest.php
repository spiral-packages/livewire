<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Middleware\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Middleware\Component\HydrateModelProperties;
use Spiral\Livewire\Request;
use Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\Address;
use Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\Component\ArrayComponent;
use Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\Component\ObjectComponent;
use Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\User;
use Spiral\Livewire\Tests\Functional\TestCase;

final class HydrateModelPropertiesTest extends TestCase
{
    #[DataProvider('componentsDataProvider')]
    public function testHydrate(LivewireComponent $component, array $data, mixed $expected): void
    {
        /** @var HydrateModelProperties $middleware */
        $middleware = $this->getContainer()->get(HydrateModelProperties::class);
        $middleware->hydrate($component, new Request([
            'fingerprint' => [],
            'updates' => [],
            'serverMemo' => ['data' => ['data' => $data]]
        ]));

        $this->assertEquals($expected, $component->data);
    }

    public static function componentsDataProvider(): \Traversable
    {
        yield [
            new ArrayComponent(),
            [
                'foo',
                'bar' => 'baz',
                'baz' => ['nested' => 12345, 'bool' => true, 'null' => null]
            ],
            [
                'foo',
                'bar' => 'baz',
                'baz' => ['nested' => 12345, 'bool' => true, 'null' => null]
            ]
        ];
        yield [
            new ObjectComponent(),
            [

                'id' => 3,
                'name' => 'John',
                'address' => ['id' => 5, 'city' => 'Portland', 'street' => '5th Avenue']
            ],
            new User(3, 'John', new Address(5, 'Portland', '5th Avenue'))
        ];
    }
}
