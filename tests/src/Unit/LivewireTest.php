<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Livewire\Livewire;
use Spiral\Livewire\Tests\Functional\TestCase;

final class LivewireTest extends TestCase
{
    #[DataProvider('nameDataProvider')]
    public function testHashComponentName(string $name, string $expected): void
    {
        $this->assertSame($expected, Livewire::hashComponentName($name));
    }

    public static function nameDataProvider(): \Traversable
    {
        yield ['foo', 'foo'];
        yield ['foo-bar', 'foo-bar'];
        yield ['foo\\bar', 'foo-bar'];
        yield ['foo\\bar\\baz', 'foo-bar-baz'];
        yield [\stdClass::class, '09a15e9660c1ebc6f429d818825ce0c6'];
        yield [\Traversable::class, '968ed9d83ff246cbf99b8b208892ce3c'];
    }
}
