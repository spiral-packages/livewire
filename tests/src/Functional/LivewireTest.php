<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Livewire\Livewire;

final class LivewireTest extends TestCase
{
    public function testInitialRequestMethodsShouldNotBeCalledIfNotExists(): void
    {
        /** @var Livewire $livewire */
        $livewire = $this->getContainer()->get(Livewire::class);

        $this->getContainer()->bind(ServerRequestInterface::class, new ServerRequest('GET', '/'));

        $this->assertStringContainsString(
            'rendered-string',
            $livewire->initialRequest('livewire-test-without-methods', [])
        );
    }

    public function testInitialRequestBootShouldBeCalled(): void
    {
        /** @var Livewire $livewire */
        $livewire = $this->getContainer()->get(Livewire::class);

        $this->getContainer()->bind(ServerRequestInterface::class, new ServerRequest('GET', '/'));

        $this->assertStringContainsString(
            'changed by boot method',
            $livewire->initialRequest('livewire-test-with-boot-method', [])
        );
    }

    public function testInitialRequestBootWithDependencyShouldBeCalled(): void
    {
        /** @var Livewire $livewire */
        $livewire = $this->getContainer()->get(Livewire::class);

        $this->getContainer()->bind(ServerRequestInterface::class, new ServerRequest('GET', '/'));

        $this->assertStringContainsString(
            'changed by boot method modified by service',
            $livewire->initialRequest('livewire-test-with-boot-dependency-method', [])
        );
    }

    public function testInitialRequestMountShouldBeCalled(): void
    {
        /** @var Livewire $livewire */
        $livewire = $this->getContainer()->get(Livewire::class);

        $this->getContainer()->bind(ServerRequestInterface::class, new ServerRequest('GET', '/'));

        $this->assertStringContainsString(
            'changed by mount method. Param: bar',
            $livewire->initialRequest('livewire-test-with-mount-method', ['foo' => 'bar'])
        );
    }

    public function testInitialRequestMountWithDependencyShouldBeCalled(): void
    {
        /** @var Livewire $livewire */
        $livewire = $this->getContainer()->get(Livewire::class);

        $this->getContainer()->bind(ServerRequestInterface::class, new ServerRequest('GET', '/'));

        $this->assertStringContainsString(
            'changed by mount method. Initial value: 1. Changed by service value: 2',
            $livewire->initialRequest('livewire-test-with-mount-dependency-method', ['start' => 1])
        );
    }
}
