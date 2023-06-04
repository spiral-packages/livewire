<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Middleware\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Livewire\Middleware\Component\NormalizeComponentPropertiesForJavaScript;
use Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;
use Spiral\Livewire\Tests\Functional\TestCase;

final class NormalizeComponentPropertiesForJavaScriptTest extends TestCase
{
    #[DataProvider('componentsDataProvider')]
    public function testNormalize(string $component, array $expected): void
    {
        $obj = new $component();
        $ref = new \ReflectionProperty($obj, 'data');

        $normalizer = $this->getContainer()->get(NormalizeComponentPropertiesForJavaScript::class);
        (new \ReflectionMethod($normalizer, 'normalize'))->invoke($normalizer, $obj);

        $this->assertSame($expected, $ref->getValue($obj));
    }

    public static function componentsDataProvider(): \Traversable
    {
        yield [Component\ValidPublicComponent::class, ['foo', 'bar', 'baz']];
        yield [Component\ValidPublicWithObjectComponent::class, ['foo', 'bar', 'baz']];
        yield [Component\ValidProtectedComponent::class, ['foo', 'bar', 'baz']];
        yield [Component\ValidProtectedWithObjectComponent::class, ['foo', 'bar', 'baz']];
        yield [Component\ValidPrivateComponent::class, ['foo', 'bar', 'baz']];
        yield [Component\ValidPrivateWithObjectComponent::class, ['foo', 'bar', 'baz']];
        yield [Component\InvalidPublicComponent::class, [0 => 'bar', 3 => 'foo', 4 => 'baz']];
        yield [Component\InvalidPublicWithObjectComponent::class, [0 => 'bar', 3 => 'foo', 4 => 'baz']];
        yield [Component\InvalidProtectedComponent::class, [0 => 'bar', 3 => 'foo', 4 => 'baz']];
        yield [Component\InvalidProtectedWithObjectComponent::class, [0 => 'bar', 3 => 'foo', 4 => 'baz']];
        yield [Component\InvalidPrivateComponent::class, [0 => 'bar', 3 => 'foo', 4 => 'baz']];
        yield [Component\InvalidPrivateWithObjectComponent::class, [0 => 'bar', 3 => 'foo', 4 => 'baz']];
    }
}
