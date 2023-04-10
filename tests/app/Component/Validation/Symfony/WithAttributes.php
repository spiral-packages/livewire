<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\App\Component\Validation\Symfony;

use Spiral\Core\Container;
use Spiral\Livewire\Attribute\Component;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Views\Engine\Native\NativeView;
use Spiral\Views\ViewSource;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Component(name: 'symfony-with-attributes')]
final class WithAttributes extends LivewireComponent
{
    #[NotBlank]
    #[Model]
    public string $name;

    #[Email]
    #[NotBlank(message: 'Email is empty!!!')]
    #[Model]
    public string $email;

    #[NotBlank]
    #[EqualTo(propertyPath: 'repeatedPassword')]
    #[Model]
    public string $password;

    #[Model]
    public string $repeatedPassword;

    #[NotBlank]
    #[GreaterThan(value: 0)]
    #[Model]
    public int $age;

    #[Collection(
        fields: [
            'city' => new NotBlank(),
            'street' => new NotBlank()
        ]
    )]
    #[Model]
    public array $address = [];

    public function __construct()
    {
        $this->preRenderedView = new NativeView(new ViewSource('', '', ''), new Container());
    }
}
