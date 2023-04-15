<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\LivewireTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Views\ViewInterface;

#[Component(name: 'livewire-test-without-methods')]
final class WithoutMethods extends LivewireComponent
{
    public function render(): ViewInterface
    {
        return new class () implements ViewInterface {
            public function render(array $data = []): string
            {
                return 'rendered-string';
            }
        };
    }
}
