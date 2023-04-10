<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Validation\Symfony;

use PHPUnit\Framework\TestCase;
use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Validation\Symfony\SymfonyValidator;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

final class SymfonyValidatorTest extends TestCase
{
    /**
     * @dataProvider pathDataProvider
     */
    public function testFormatPath(string $propertyPath, string $expected): void
    {
        $validator = new SymfonyValidator(
            $this->createMock(ConstraintValidatorFactoryInterface::class),
            $this->createMock(ReaderInterface::class),
            $this->createMock(DataAccessorInterface::class),
        );

        $ref = new \ReflectionMethod($validator, 'formatPath');

        $this->assertSame($expected, $ref->invoke($validator, $propertyPath));
    }

    public function pathDataProvider(): \Traversable
    {
        yield ['name', 'name'];
        yield ['[first_name]', 'first_name'];
        yield ['contact.email', 'contact.email'];
        yield ['contact[email]', 'contact.email'];
        yield ['[0][first_name]', '0.first_name'];
        yield ['children[0].firstName', 'children.0.firstName'];
    }
}
