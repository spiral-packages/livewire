<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Validation\Laravel;

use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Exception\Validation\ValidationException;
use Spiral\Livewire\Tests\Functional\TestCase;
use Spiral\Livewire\Validation\Spiral\SpiralValidator;

final class SpiralValidatorTest extends TestCase
{
    protected const VALIDATOR = 'spiral';

    public function testValidation(): void
    {
        $validator = $this->getContainer()->get(SpiralValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-spiral-spiral-validator-test-form');

        try {
            $validator->validate($component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('This value is required.', $errors['name'][0]);
        $this->assertSame('Must be a valid email address.', $errors['email'][0]);
        $this->assertSame('This value is required.', $errors['password'][0]);
        $this->assertSame('This value is required.', $errors['age'][0]);
    }

    public function testValidationProperty(): void
    {
        $validator = $this->getContainer()->get(SpiralValidator::class);
        $component = $this->getContainer()
            ->get(ComponentRegistryInterface::class)
            ->get('validation-spiral-spiral-validator-test-form');

        try {
            $validator->validateProperty('name', null, $component);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }

        $this->assertSame('This value is required.', $errors['name'][0]);
    }
}
