<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Validation\Symfony;

use PHPUnit\Framework\TestCase;
use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Validation\Symfony\SymfonyValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SymfonyValidatorTest extends TestCase
{
    /**
     * @dataProvider pathDataProvider
     */
    public function testFormatPath(string $propertyPath, string $expected): void
    {
        $validator = new SymfonyValidator(
            $this->createMock(ValidatorInterface::class),
            $this->createMock(ReaderInterface::class),
            $this->createMock(DataAccessorInterface::class),
        );

        $ref = new \ReflectionMethod($validator, 'formatPath');

        $this->assertSame($expected, $ref->invoke($validator, $propertyPath));
    }

    public function testComponentShouldNotBeValidated(): void
    {
        $sfValidator = $this->createMock(ValidatorInterface::class);
        $sfValidator->expects($this->never())->method('validate');

        $validator = new SymfonyValidator(
            $sfValidator,
            $this->createMock(ReaderInterface::class),
            $this->createMock(DataAccessorInterface::class),
        );

        $validator->validate(new class () extends LivewireComponent {});
    }

    public static function pathDataProvider(): \Traversable
    {
        yield ['name', 'name'];
        yield ['[first_name]', 'first_name'];
        yield ['contact.email', 'contact.email'];
        yield ['contact[email]', 'contact.email'];
        yield ['[0][first_name]', '0.first_name'];
        yield ['children[0].firstName', 'children.0.firstName'];
    }
}
