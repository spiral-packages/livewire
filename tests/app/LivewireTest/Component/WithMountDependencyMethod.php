<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\LivewireTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\LivewireTest\Service;
use Spiral\Views\ViewInterface;

#[Component(name: 'livewire-test-with-mount-dependency-method')]
final class WithMountDependencyMethod extends LivewireComponent
{
    private string $result = 'foo';

    public function mount(int $start, Service $service): void
    {
        $this->result = \sprintf(
            'changed by mount method. Initial value: %s. Changed by service value: %s',
            $start,
            $service->modifyInt($start)
        );
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
