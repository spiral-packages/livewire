<?php

declare(strict_types=1);

namespace Spiral\Livewire\Validation\Laravel;

use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Validation\ValidationException;
use Spiral\Livewire\Validation\ShouldBeValidated;
use Spiral\Livewire\Validation\ValidatorInterface;
use Spiral\Validation\Laravel\LaravelValidation;

final class LaravelValidator implements ValidatorInterface
{
    public function __construct(
        private readonly LaravelValidation $validation,
        private readonly DataAccessorInterface $dataAccessor
    ) {
    }

    public function validate(LivewireComponent $component): void
    {
        if (!$component instanceof ShouldBeValidated) {
            return;
        }

        $validator = $this->validation->validate(
            $this->dataAccessor->getData($component),
            $component->validationRules(),
            $component->toArray()['validationContext']
        );

        if (!$validator->isValid()) {
            throw new ValidationException($validator->getErrors());
        }
    }

    public function validateProperty(string $property, mixed $value, LivewireComponent $component): void
    {
        if (!$component instanceof ShouldBeValidated) {
            return;
        }

        $validator = $this->validation->validate(
            [$property => $value],
            array_filter(
                $component->validationRules(),
                static fn (string $key): bool => $key === $property,
                \ARRAY_FILTER_USE_KEY
            ),
            $component->toArray()['validationContext']
        );

        if (!$validator->isValid()) {
            throw new ValidationException($validator->getErrors());
        }
    }
}
