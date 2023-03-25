<?php

declare(strict_types=1);

namespace Spiral\Livewire\Validation;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Validation\ValidationException;

interface ValidatorInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(LivewireComponent $component): void;

    /**
     * @throws ValidationException
     */
    public function validateProperty(\ReflectionProperty $property, mixed $value, LivewireComponent $component): void;
}
