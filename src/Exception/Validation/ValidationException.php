<?php

declare(strict_types=1);

namespace Spiral\Livewire\Exception\Validation;

use Spiral\Livewire\Exception\LivewireException;

final class ValidationException extends LivewireException
{
    public function __construct(
        private readonly array $errors
    ) {
        parent::__construct('The given data was invalid!');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
