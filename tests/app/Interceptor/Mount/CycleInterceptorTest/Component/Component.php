<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Interceptor\Mount\CycleInterceptorTest\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Interceptor\Mount\CycleInterceptorTest\Entity\User;

final class Component extends LivewireComponent
{
    public function mount(User $user): void
    {
    }
}
