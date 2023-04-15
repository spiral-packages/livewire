<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Template\Twig\NodeVisitor\LivewireNodeVisitorTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;

#[Component(
    name: 'template-twig-node-visitor-livewire-node-visitor-test-counter',
    template: 'template/twig/node-visitor/livewire-node-visitor-test/components/counter'
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
