<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Template\Stempler;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Livewire\Tests\Functional\TestCase;

final class ComponentRendererTest extends TestCase
{
    protected const TEMPLATE_ENGINE = 'stempler';

    public function testRenderWithoutDefaultValue(): void
    {
        $request = new ServerRequest('GET', 'foo');

        $this->getContainer()->bind(ServerRequestInterface::class, $request);

        $this->assertViewContains('counter.dark.php', [], '<h1>1</h1>');
    }

    public function testRenderWithDefaultValue(): void
    {
        $request = new ServerRequest('GET', 'foo');

        $this->getContainer()->bind(ServerRequestInterface::class, $request);

        $this->assertViewContains('counter_with_default_value.dark.php', [], '<h1>5</h1>');
    }

    public function testRenderWithDefaultValueAndInjectedDependency(): void
    {
        $request = new ServerRequest('GET', 'foo');

        $this->getContainer()->bind(ServerRequestInterface::class, $request);

        $this->assertViewContains('with_mount_dependency.dark.php', [], '<h1>6</h1>');
    }
}
