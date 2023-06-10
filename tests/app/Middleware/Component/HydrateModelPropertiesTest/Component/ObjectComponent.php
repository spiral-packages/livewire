<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\User;

final class ObjectComponent extends LivewireComponent
{
    #[Model]
    public User $data;
}
