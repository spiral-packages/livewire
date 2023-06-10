<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Middleware\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Middleware\Component\HydrateModelProperties;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

final class HydrateModelPropertiesTest extends TestCase
{
    public function testHydrate(): void
    {
        $component = new class () extends LivewireComponent {};

        $request = new Request([
            'serverMemo' => ['data' => ['foo' => 'bar']],
            'fingerprint' => [],
            'updates' => []
        ]);

        $accessor = $this->createMock(DataAccessorInterface::class);
        $accessor
            ->expects($this->once())
            ->method('setValue')
            ->with($component, 'foo', 'bar');

        $middleware = new HydrateModelProperties($accessor);

        $middleware->hydrate($component, $request);
    }

    public function testHydrateEmpty(): void
    {
        $component = new class () extends LivewireComponent {};

        $request = new Request([
            'serverMemo' => [],
            'fingerprint' => [],
            'updates' => []
        ]);

        $accessor = $this->createMock(DataAccessorInterface::class);
        $accessor
            ->expects($this->never())
            ->method('setValue');

        $middleware = new HydrateModelProperties($accessor);

        $middleware->hydrate($component, $request);
    }

    public function testInitialDehydrate(): void
    {
        $component = new class () extends LivewireComponent {};

        $accessor = $this->createMock(DataAccessorInterface::class);
        $accessor
            ->expects($this->once())
            ->method('getData')
            ->with($component)
            ->willReturn(['foo', 'bar']);

        $middleware = new HydrateModelProperties($accessor);

        $response = new Response([]);

        $middleware->initialDehydrate($component, $response);

        $this->assertEquals(['foo', 'bar'], $response->memo['data']);
    }

    public function testDehydrate(): void
    {
        $component = new class () extends LivewireComponent {};

        $accessor = $this->createMock(DataAccessorInterface::class);
        $accessor
            ->expects($this->once())
            ->method('getData')
            ->with($component)
            ->willReturn(['foo', 'bar']);

        $middleware = new HydrateModelProperties($accessor);

        $response = new Response([]);

        $middleware->dehydrate($component, $response);

        $this->assertEquals(['foo', 'bar'], $response->memo['data']);
    }
}
