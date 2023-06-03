<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Interceptor\Boot;

use PHPUnit\Framework\TestCase;
use Spiral\Core\CoreInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Interceptor\Boot\BootInvoker;

final class BootInvokerTest extends TestCase
{
    public function testInvokeWithoutBootMethod(): void
    {
        $component = new class () extends LivewireComponent {};

        $core = $this->createMock(CoreInterface::class);
        $core
            ->expects($this->never())
            ->method('callAction');

        $invoker = new BootInvoker($core);
        $invoker->invoke($component);
    }

    public function testInvokeWithBootMethod(): void
    {
        $component = new class () extends LivewireComponent {
            public function boot(): void
            {
            }
        };

        $core = $this->createMock(CoreInterface::class);
        $core
            ->expects($this->once())
            ->method('callAction');

        $invoker = new BootInvoker($core);
        $invoker->invoke($component);
    }
}
