<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Validation\Symfony;

use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Exception\Validation\ValidationException;
use Spiral\Livewire\Tests\Functional\TestCase;
use Spiral\Livewire\Validation\Symfony\SymfonyValidator;

final class SymfonyValidatorTest extends TestCase
{
    protected const VALIDATOR = 'symfony';

    public function testValidationWithInterface(): void
    {
        $validator = $this->getContainer()->get(SymfonyValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-symfony-symfony-validator-test-with-interface');

        try {
            $validator->validate($component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('This value should not be blank.', $errors['name'][0]);
        $this->assertSame('Email is empty!!!', $errors['email'][0]);
        $this->assertSame('This value should not be blank.', $errors['password'][0]);
        $this->assertSame('This value should not be blank.', $errors['age'][0]);
        $this->assertSame('This field is missing.', $errors['address.city'][0]);
        $this->assertSame('This field is missing.', $errors['address.street'][0]);
    }

    public function testValidationWithAttributes(): void
    {
        $validator = $this->getContainer()->get(SymfonyValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-symfony-symfony-validator-test-with-attributes');

        try {
            $validator->validate($component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('This value should not be blank.', $errors['name'][0]);
        $this->assertSame('Email is empty!!!', $errors['email'][0]);
        $this->assertSame('This value should not be blank.', $errors['password'][0]);
        $this->assertSame('This value should not be blank.', $errors['age'][0]);
        $this->assertSame('This field is missing.', $errors['address.city'][0]);
        $this->assertSame('This field is missing.', $errors['address.street'][0]);
    }

    public function testValidationPropertyWithInterface(): void
    {
        /** @var SymfonyValidator $validator */
        $validator = $this->getContainer()->get(SymfonyValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-symfony-symfony-validator-test-with-interface');

        try {
            $validator->validateProperty('name', null, $component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('This value should not be blank.', $errors['name'][0]);
    }

    public function testValidationPropertyWithAttribute(): void
    {
        /** @var SymfonyValidator $validator */
        $validator = $this->getContainer()->get(SymfonyValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-symfony-symfony-validator-test-with-attributes');

        try {
            $validator->validateProperty('name', null, $component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('This value should not be blank.', $errors['name'][0]);
    }
}
