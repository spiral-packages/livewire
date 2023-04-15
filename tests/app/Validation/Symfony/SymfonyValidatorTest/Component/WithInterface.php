<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Validation\Symfony\SymfonyValidatorTest\Component;

use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Validation\ShouldBeValidated;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Component(name: 'validation-symfony-symfony-validator-test-with-interface')]
final class WithInterface extends LivewireComponent implements ShouldBeValidated
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
            'name' => new NotBlank(),
            'email' => [new NotBlank(message: 'Email is empty!!!'), new Email()],
            'password' => [new NotBlank(), new EqualTo(propertyPath: 'repeatedPassword')],
            'age' => [new NotBlank(), new GreaterThan(value: 0)],
            'address' => new Collection([
                'city' => new NotBlank(),
                'street' => new NotBlank(),
            ]),
        ];
    }
}
