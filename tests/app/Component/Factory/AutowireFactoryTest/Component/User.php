<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\Factory\AutowireFactoryTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;

#[Component(name: 'component-factory-autowire-factory-test-user')]
final class User extends LivewireComponent
{
}
