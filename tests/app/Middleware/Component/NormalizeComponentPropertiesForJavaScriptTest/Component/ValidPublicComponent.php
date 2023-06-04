<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;

final class ValidPublicComponent extends LivewireComponent
{
    #[Model]
    public array $data = [
        'foo',
        'bar',
        'baz'
    ];
}
