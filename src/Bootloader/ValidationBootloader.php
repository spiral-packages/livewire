<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Psr\Container\ContainerInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\FactoryInterface;
use Spiral\Livewire\Validation\Laravel\LaravelValidator;
use Spiral\Livewire\Validation\Spiral\SpiralValidator;
use Spiral\Livewire\Validation\Symfony\SymfonyValidator;
use Spiral\Livewire\Validation\ValidatorInterface;
use Spiral\Translator\TranslatorInterface;
use Spiral\Validation\Bootloader\ValidationBootloader as SpiralValidation;
use Spiral\Validation\Laravel\LaravelValidation;
use Spiral\Validation\Symfony\Validation as SymfonyValidation;
use Spiral\Validation\ValidationInterface;
use Spiral\Validator\Validation;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

final class ValidationBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        SpiralValidation::class,
    ];

    protected const SINGLETONS = [
        ValidatorInterface::class => [self::class, 'initValidator'],
        SymfonyValidator::class => [self::class, 'initSymfonyValidator']
    ];

    public function initValidator(ContainerInterface $container, ValidationInterface $validation): ValidatorInterface
    {
        return match ($validation::class) {
            Validation::class => $container->get(SpiralValidator::class),
            LaravelValidation::class => $container->get(LaravelValidator::class),
            SymfonyValidation::class => $container->get(SymfonyValidator::class)
        };
    }

    private function initSymfonyValidator(
        ConstraintValidatorFactoryInterface $constraintValidatorFactory,
        ContainerInterface $container,
        FactoryInterface $factory
    ): SymfonyValidator {
        $validator = \Symfony\Component\Validator\Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->setConstraintValidatorFactory($constraintValidatorFactory);

        if ($container->has(TranslatorInterface::class)) {
            $validator->setTranslator($container->get(TranslatorInterface::class));
        }

        return $factory->make(SymfonyValidator::class, ['validator' => $validator->getValidator()]);
    }
}
