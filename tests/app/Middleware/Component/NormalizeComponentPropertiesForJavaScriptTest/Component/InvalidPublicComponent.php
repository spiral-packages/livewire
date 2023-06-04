<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;

final class InvalidPublicComponent extends LivewireComponent
{
    #[Model]
    public array $data = [
        3 => 'foo',
        0 => 'bar',
        4 => 'baz'
    ];
}
