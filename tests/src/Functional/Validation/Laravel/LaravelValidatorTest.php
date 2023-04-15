<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Validation\Laravel;

use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Exception\Validation\ValidationException;
use Spiral\Livewire\Tests\Functional\TestCase;
use Spiral\Livewire\Validation\Laravel\LaravelValidator;

final class LaravelValidatorTest extends TestCase
{
    protected const VALIDATOR = 'laravel';

    public function testValidation(): void
    {
        $validator = $this->getContainer()->get(LaravelValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-laravel-laravel-validator-test-form');

        try {
            $validator->validate($component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('validation.min.string', $errors['name'][0]);
        $this->assertSame('validation.email', $errors['email'][0]);
        $this->assertSame('validation.required', $errors['password'][0]);
        $this->assertSame('validation.required', $errors['age'][0]);
        $this->assertSame('validation.required', $errors['address.city'][0]);
        $this->assertSame('validation.required', $errors['address.street'][0]);
    }

    public function testValidationPropertyWithInterface(): void
    {
        $validator = $this->getContainer()->get(LaravelValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-laravel-laravel-validator-test-form');

        try {
            $validator->validateProperty('name', null, $component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('validation.min.string', $errors['name'][0]);
    }

    public function testValidationNestedProperty(): void
    {
        $validator = $this->getContainer()->get(LaravelValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-laravel-laravel-validator-test-form');

        try {
            $validator->validateProperty('address.street', null, $component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('validation.required', $errors['address.street'][0]);
    }
}
