<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Template\Twig\NodeVisitor;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Livewire\Tests\Functional\TestCase;

final class LivewireNodeVisitorTest extends TestCase
{
    public function testRenderWithoutDefaultValue(): void
    {
        $request = new ServerRequest('GET', 'foo');

        $this->getContainer()->bind(ServerRequestInterface::class, $request);

        $this->assertViewContains('counter.twig', [], '<h1>1</h1>');
    }

    public function testRenderWithDefaultValue(): void
    {
        $request = new ServerRequest('GET', 'foo');

        $this->getContainer()->bind(ServerRequestInterface::class, $request);

        $this->assertViewContains('counter_with_default_value.twig', [], '<h1>5</h1>');
    }

    public function testRenderWithDefaultValueAndInjectedDependency(): void
    {
        $request = new ServerRequest('GET', 'foo');

        $this->getContainer()->bind(ServerRequestInterface::class, $request);

        $this->assertViewContains('with_mount_dependency.twig', [], '<h1>6</h1>');
    }
}
