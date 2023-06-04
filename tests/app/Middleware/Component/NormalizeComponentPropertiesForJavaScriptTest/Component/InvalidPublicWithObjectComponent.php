<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Entity\User;

final class InvalidPublicWithObjectComponent extends LivewireComponent
{
    #[Model]
    public array $data = [
        3 => 'foo',
        0 => 'bar',
        4 => 'baz'
    ];

    #[Model]
    public User $user;

    public function __construct()
    {
        $this->user = new User(1, 'foo');
    }
}
