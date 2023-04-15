<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\LivewireTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Views\ViewInterface;

#[Component(name: 'livewire-test-with-boot-method')]
final class WithBootMethod extends LivewireComponent
{
    private string $result = 'foo';

    public function boot(): void
    {
        $this->result = 'changed by boot method';
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
