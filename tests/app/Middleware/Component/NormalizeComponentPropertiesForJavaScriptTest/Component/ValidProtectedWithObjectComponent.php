<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Component;

use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Tests\App\Middleware\Component\NormalizeComponentPropertiesForJavaScriptTest\Entity\User;
use Spiral\Marshaller\Meta\Marshal;

final class ValidProtectedWithObjectComponent extends LivewireComponent
{
    #[Marshal]
    #[Model]
    protected array $data = [
        'foo',
        'bar',
        'baz'
    ];

    #[Marshal]
    #[Model]
    protected User $user;

    public function __construct()
    {
        $this->user = new User(1, 'foo');
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
