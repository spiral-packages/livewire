<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Entity\User;

final class ValidPublicWithObjectComponent extends LivewireComponent
{
    #[Model]
    public array $data = [
        'foo',
        'bar',
        'baz'
    ];

    #[Model]
    public User $user;

    public function __construct()
    {
        $this->user = new User(1, 'foo');
    }
}
