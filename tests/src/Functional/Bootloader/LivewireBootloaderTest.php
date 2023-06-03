<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Bootloader;

use Spiral\Boot\DirectoriesInterface;
use Spiral\Livewire\Tests\Functional\TestCase;
use Spiral\Views\Config\ViewsConfig;

final class LivewireBootloaderTest extends TestCase
{
    public function testViewsShouldBeRegistered(): void
    {
        $container = $this->getApp()->getContainer();
        $dirs = $container->get(DirectoriesInterface::class);

        $config = $this->getConfig(ViewsConfig::CONFIG);
        $this->assertSame(
            \rtrim($dirs->get('vendor'), '/') . '/spiral-packages/livewire/views',
            $config['namespaces']['livewire'][0]
        );
    }
}
