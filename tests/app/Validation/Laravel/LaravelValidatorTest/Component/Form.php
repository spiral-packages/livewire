<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Validation\Laravel\LaravelValidatorTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Validation\ShouldBeValidated;

#[Component(name: 'validation-laravel-laravel-validator-test-form')]
final class Form extends LivewireComponent implements ShouldBeValidated
{
    #[Model]
    public string $name;

    #[Model]
    public string $email;

    #[Model]
    public string $password;

    #[Model]
    public string $repeatedPassword;

    #[Model]
    public int $age;

    #[Model]
    public array $address = [];

    public function validationRules(): array
    {
        return [
            'name' => 'min:6',
            'email' => 'email',
            'password' => 'required',
            'age' => 'required',
            'address.city' => 'required',
            'address.street' => 'required'
        ];
    }
}
