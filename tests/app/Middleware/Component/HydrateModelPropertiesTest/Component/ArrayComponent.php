<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\HydrateModelPropertiesTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;

final class ArrayComponent extends LivewireComponent
{
    #[Model]
    public array $data;
}
