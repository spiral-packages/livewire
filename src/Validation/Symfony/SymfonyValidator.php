<?php

declare(strict_types=1);

namespace Spiral\Livewire\Validation\Symfony;

use Adbar\Dot;
use Spiral\Attributes\ReaderInterface;
use Spiral\Livewire\Attribute\Model;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Validation\ValidationException;
use Spiral\Livewire\Str;
use Spiral\Livewire\Validation\ShouldBeValidated;
use Spiral\Livewire\Validation\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

final class SymfonyValidator implements ValidatorInterface
{
    public function __construct(
        private readonly SymfonyValidatorInterface $validator,
        private readonly ReaderInterface $reader,
        private readonly DataAccessorInterface $dataAccessor
    ) {
    }

    public function validate(LivewireComponent $component): void
    {
        if (!$component instanceof ShouldBeValidated && !$this->hasConstraints($component)) {
            return;
        }

        $violations = $this->validator->validate(
            $component instanceof ShouldBeValidated ? $this->dataAccessor->getData($component) : $component,
            $component instanceof ShouldBeValidated
                ? new Collection(fields: $component->validationRules(), allowExtraFields: true)
                : null,
            $component->toArray()['validationContext']
        );

        if ($violations->count() > 0) {
            throw new ValidationException($this->wrapErrors($violations));
        }
    }

    public function validateProperty(string $property, mixed $value, LivewireComponent $component): void
    {
        if (!$component instanceof ShouldBeValidated && !$this->hasConstraints($component)) {
            return;
        }

        if ($component instanceof ShouldBeValidated) {
            $dot = new Dot();
            $violations = $this->validator->validate(
                $dot->set($property, $value)->all(),
                new Collection(fields: $component->validationRules(), allowExtraFields: true),
                $component->toArray()['validationContext']
            );
        } else {
            $propertyValue = $this->dataAccessor->getValue($component, Str::before($property, '.'));
            $dot = new Dot($propertyValue);

            $violations = $this->validator->validatePropertyValue(
                $component,
                Str::before($property, '.'),
                str_contains($property, '.') ? $dot->set(Str::after($property, '.'), $value)->all() : $value,
                $component->toArray()['validationContext']
            );
        }

        if ($violations->count() > 0) {
            $errors = $this->wrapErrors($violations);
            if (isset($errors[$property])) {
                throw new ValidationException([$property => $errors[$property]]);
            }
        }
    }

    private function hasConstraints(LivewireComponent $component): bool
    {
        foreach ((new \ReflectionClass($component))->getProperties() as $property) {
            $propertyConstraints = $this->getPropertyConstraints($property);
            if ($propertyConstraints !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return iterable<array-key, Constraint>
     */
    private function getPropertyConstraints(\ReflectionProperty $property): iterable
    {
        if (null !== $this->reader->firstPropertyMetadata($property, Model::class)) {
            return $this->reader->getPropertyMetadata($property, Constraint::class);
        }
        return [];
    }

    private function wrapErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$this->formatPath($violation->getPropertyPath())][] = (string) $violation->getMessage();
        }

        return $errors;
    }

    private function formatPath(string $propertyPath): string
    {
        $path = new PropertyPath($propertyPath);

        return \implode('.', $path->getElements());
    }
}
