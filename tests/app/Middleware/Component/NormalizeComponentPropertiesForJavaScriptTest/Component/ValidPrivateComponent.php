<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Marshaller\Meta\Marshal;

final class ValidPrivateComponent extends LivewireComponent
{
    #[Marshal]
    #[Model]
    private array $data = [
        'foo',
        'bar',
        'baz'
    ];

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
