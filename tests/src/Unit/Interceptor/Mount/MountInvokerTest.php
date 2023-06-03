<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Interceptor\Mount;

use PHPUnit\Framework\TestCase;
use Spiral\Core\CoreInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Interceptor\Mount\MountInvoker;

final class MountInvokerTest extends TestCase
{
    public function testInvokeWithoutMountMethod(): void
    {
        $component = new class () extends LivewireComponent {};

        $core = $this->createMock(CoreInterface::class);
        $core
            ->expects($this->never())
            ->method('callAction');

        $invoker = new MountInvoker($core);
        $invoker->invoke($component, []);
    }

    public function testInvokeWithMountMethod(): void
    {
        $component = new class () extends LivewireComponent {
            public function mount(): void
            {
            }
        };

        $core = $this->createMock(CoreInterface::class);
        $core
            ->expects($this->once())
            ->method('callAction');

        $invoker = new MountInvoker($core);
        $invoker->invoke($component, []);
    }
}
