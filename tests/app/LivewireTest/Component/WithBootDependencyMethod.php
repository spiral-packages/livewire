<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\LivewireTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\LivewireTest\Service;
use Spiral\Views\ViewInterface;

#[Component(name: 'livewire-test-with-boot-dependency-method')]
final class WithBootDependencyMethod extends LivewireComponent
{
    private string $result = 'foo';

    public function boot(Service $service): void
    {
        $this->result = $service->modify('changed by boot method');
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
