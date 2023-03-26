<?php

declare(strict_types=1);

namespace Spiral\Livewire\Validation;

interface ShouldBeValidated
{
    /**
     * Get validation rules.
     */
    public function validationRules(): array;
}
