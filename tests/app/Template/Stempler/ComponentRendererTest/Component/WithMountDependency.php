<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Template\Stempler\ComponentRendererTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Template\Stempler\ComponentRendererTest\Modifier;

#[Component(
    name: 'template-stempler-component-renderer-test-with-mount-dependency',
    template: 'template/stempler/component-renderer-test/components/counter'
)]
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
