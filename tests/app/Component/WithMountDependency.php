<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Modifier;

#[Component(name: 'with-mount-dependency', template: 'components/counter')]
final class WithMountDependency extends LivewireComponent
{
    #[Model]
    public int $count;

    public function mount(int $start, Modifier $modifier): void
    {
        $this->count = $modifier->modify($start);
    }

    public function increment(): void
    {
        $this->count++;
    }
}
