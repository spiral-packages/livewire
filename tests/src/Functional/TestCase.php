<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional;

use Spiral\Bootloader\Http\RouterBootloader;
use Spiral\Core\ContainerScope;
use Spiral\Cycle\Bootloader\CycleOrmBootloader;
use Spiral\Livewire\Bootloader\LivewireBootloader;
use Spiral\Livewire\Bootloader\StemplerBootloader;
use Spiral\Livewire\Bootloader\TwigBootloader;
use Spiral\Livewire\Bootloader\ValidationBootloader;
use Spiral\Nyholm\Bootloader\NyholmBootloader;
use Spiral\Validation\Laravel\Bootloader\ValidatorBootloader as LaravelValidatorBootloader;
use Spiral\Validation\Symfony\Bootloader\ValidatorBootloader as SymfonyValidatorBootloader;
use Spiral\Validator\Bootloader\ValidatorBootloader as SpiralValidatorBootloader;

class TestCase extends \Spiral\Testing\TestCase
{
    protected const TEMPLATE_ENGINE = 'twig';
    protected const VALIDATOR = 'spiral';

    protected function setUp(): void
    {
        parent::setUp();

        // Bind container to ContainerScope
        (new \ReflectionClass(ContainerScope::class))->setStaticPropertyValue('container', $this->getContainer());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanUpRuntimeDirectory();

        (new \ReflectionClass(ContainerScope::class))->setStaticPropertyValue('container', null);
    }

    public function rootDirectory(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function defineDirectories(string $root): array
    {
        return \array_merge(
            ['views' => $root . '/views'],
            parent::defineDirectories($root)
        );
    }

    public function defineBootloaders(): array
    {
        $bootloaders = [
            LivewireBootloader::class,
            RouterBootloader::class,
            NyholmBootloader::class,
            CycleOrmBootloader::class
        ];

        $bootloaders[] = match (static::TEMPLATE_ENGINE) {
            'twig' => TwigBootloader::class,
            'stempler' => StemplerBootloader::class
        };

        if (static::VALIDATOR !== 'none') {
            $bootloaders[] = match (static::VALIDATOR) {
                'spiral' => SpiralValidatorBootloader::class,
                'symfony' => SymfonyValidatorBootloader::class,
                'laravel' => LaravelValidatorBootloader::class
            };
            $bootloaders[] = ValidationBootloader::class;
        }

        return $bootloaders;
    }
}
