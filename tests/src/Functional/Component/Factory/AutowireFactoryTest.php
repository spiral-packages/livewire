<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Component\Factory;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\Factory\AutowireFactory;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Component\Factory\AutowireFactoryTest\Component\User;
use Spiral\Livewire\Tests\Functional\TestCase;

final class AutowireFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new AutowireFactory();

        $this->assertInstanceOf(LivewireComponent::class, $factory->create(
            'foo',
            User::class,
            new Component('foo')
        )($this->getContainer()));
    }
}
