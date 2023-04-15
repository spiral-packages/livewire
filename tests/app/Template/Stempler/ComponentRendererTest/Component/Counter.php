<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Template\Stempler\ComponentRendererTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;

#[Component(
    name: 'template-stempler-component-renderer-test-counter',
    template: 'template/stempler/component-renderer-test/components/counter'
)]
final class Counter extends LivewireComponent
{
    #[Model]
    public int $count = 1;

    public function mount(?int $start = null): void
    {
        if ($start !== null) {
            $this->count = $start;
        }
    }

    public function increment(): void
    {
        $this->count++;
    }
}
