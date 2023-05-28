<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Bootloader;

use Spiral\Boot\DirectoriesInterface;
use Spiral\Livewire\Component\ActionHandler;
use Spiral\Livewire\Component\ActionHandlerInterface;
use Spiral\Livewire\Component\ChecksumManager;
use Spiral\Livewire\Component\ChecksumManagerInterface;
use Spiral\Livewire\Component\DataAccessor;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\Factory\AutowireFactory;
use Spiral\Livewire\Component\Factory\FactoryInterface;
use Spiral\Livewire\Component\PropertyHasher;
use Spiral\Livewire\Component\PropertyHasherInterface;
use Spiral\Livewire\Component\Registry\ComponentProcessorRegistry;
use Spiral\Livewire\Component\Registry\ComponentRegistry;
use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Component\Registry\Processor\AttributeProcessor;
use Spiral\Livewire\Component\Renderer;
use Spiral\Livewire\Component\RendererInterface;
use Spiral\Livewire\Tests\Functional\TestCase;
use Spiral\Views\Config\ViewsConfig;

final class LivewireBootloaderTest extends TestCase
{
    public function testComponentRegistryShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(ComponentRegistryInterface::class, ComponentRegistry::class);
    }

    public function testComponentProcessorRegistryShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(ComponentProcessorRegistry::class, ComponentProcessorRegistry::class);
    }

    public function testComponentFactoryShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(FactoryInterface::class, AutowireFactory::class);
    }

    public function testChecksumManagerShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(ChecksumManagerInterface::class, ChecksumManager::class);
    }

    public function testPropertyHasherShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(PropertyHasherInterface::class, PropertyHasher::class);
    }

    public function testRendererShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(RendererInterface::class, Renderer::class);
    }

    public function testDataAccessorShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(DataAccessorInterface::class, DataAccessor::class);
    }

    public function testActionHandlerShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(ActionHandlerInterface::class, ActionHandler::class);
    }

    public function testViewsShouldBeRegistered(): void
    {
        $container = $this->getApp()->getContainer();
        $dirs = $container->get(DirectoriesInterface::class);

        $config = $this->getConfig(ViewsConfig::CONFIG);
        $this->assertSame(
            \rtrim($dirs->get('vendor'), '/') . '/spiral-packages/livewire/views',
            $config['namespaces']['livewire'][0]
        );
    }

    public function testProcessorsShouldBeRegistered(): void
    {
        $this->assertInstanceOf(
            AttributeProcessor::class,
            $this->getContainer()->get(ComponentProcessorRegistry::class)->getProcessors()[0]
        );
    }
}
