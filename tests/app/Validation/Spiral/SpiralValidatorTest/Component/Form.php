<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Validation\Spiral\SpiralValidatorTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Validation\ShouldBeValidated;

#[Component(name: 'validation-spiral-spiral-validator-test-form')]
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

    public function validationRules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'age' => 'required'
        ];
    }
}
