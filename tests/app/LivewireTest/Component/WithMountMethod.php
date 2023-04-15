<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\LivewireTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Views\ViewInterface;

#[Component(name: 'livewire-test-with-mount-method')]
final class WithMountMethod extends LivewireComponent
{
    private string $result = 'foo';

    public function mount(string $foo): void
    {
        $this->result = \sprintf('changed by mount method. Param: %s', $foo);
    }

    public function render(): ViewInterface
    {
        return new class ($this->result) implements ViewInterface {
            public function __construct(
                private readonly string $result
            ) {
            }

            public function render(array $data = []): string
            {
                return $this->result;
            }
        };
    }
}
