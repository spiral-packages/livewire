<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Functional\Bootloader;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Core\Container\Autowire;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\InterceptableCore;
use Spiral\Livewire\Bootloader\ComponentBootloader;
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
use Spiral\Livewire\Config\LivewireConfig;
use Spiral\Livewire\Interceptor\Boot\BootInvoker;
use Spiral\Livewire\Interceptor\Boot\Core as BootCore;
use Spiral\Livewire\Interceptor\Mount\Core as MountCore;
use Spiral\Livewire\Interceptor\Mount\CycleInterceptor;
use Spiral\Livewire\Interceptor\Mount\MountInvoker;
use Spiral\Livewire\Interceptor\Mount\TypecasterInterceptor;
use Spiral\Livewire\Tests\Functional\TestCase;

final class ComponentBootloaderTest extends TestCase
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

    public function testMountInvokerShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(MountInvoker::class, MountInvoker::class);
    }

    public function testBootInvokerShouldBeBoundAsSingleton(): void
    {
        $this->assertContainerBoundAsSingleton(BootInvoker::class, BootInvoker::class);
    }

    public function testProcessorsShouldBeRegistered(): void
    {
        $this->assertInstanceOf(
            AttributeProcessor::class,
            $this->getContainer()->get(ComponentProcessorRegistry::class)->getProcessors()[0]
        );
    }

    #[DataProvider('interceptorsDataProvider')]
    public function testInitMountInvoker(array $interceptors): void
    {
        $bootloader = $this->getContainer()->get(ComponentBootloader::class);
        $initMountInvokerRef = new \ReflectionMethod($bootloader, 'initMountInvoker');


        $invoker = $initMountInvokerRef->invoke(
            $bootloader,
            $this->getContainer()->get(MountCore::class),
            new LivewireConfig(['interceptors' => ['mount' => $interceptors]]),
            $this->getContainer(),
            $this->getContainer()->get(EventDispatcherInterface::class)
        );

        $coreRef = new \ReflectionProperty($invoker, 'core');

        $expected = new InterceptableCore(
            $this->getContainer()->get(MountCore::class),
            $this->getContainer()->get(EventDispatcherInterface::class)
        );
        foreach ($interceptors as $interceptor) {
            if (!$interceptor instanceof CoreInterceptorInterface) {
                $interceptor = $this->getContainer()->get($interceptor);
            }
            $expected->addInterceptor($interceptor);
        }

        $this->assertEquals($expected, $coreRef->getValue($invoker));
    }

    #[DataProvider('interceptorsDataProvider')]
    public function testInitBootInvoker(array $interceptors): void
    {
        $bootloader = $this->getContainer()->get(ComponentBootloader::class);
        $initBootInvokerRef = new \ReflectionMethod($bootloader, 'initBootInvoker');


        $invoker = $initBootInvokerRef->invoke(
            $bootloader,
            $this->getContainer()->get(BootCore::class),
            new LivewireConfig(['interceptors' => ['boot' => $interceptors]]),
            $this->getContainer(),
            $this->getContainer()->get(EventDispatcherInterface::class)
        );

        $coreRef = new \ReflectionProperty($invoker, 'core');

        $expected = new InterceptableCore(
            $this->getContainer()->get(BootCore::class),
            $this->getContainer()->get(EventDispatcherInterface::class)
        );
        foreach ($interceptors as $interceptor) {
            if (!$interceptor instanceof CoreInterceptorInterface) {
                $interceptor = $this->getContainer()->get($interceptor);
            }
            $expected->addInterceptor($interceptor);
        }

        $this->assertEquals($expected, $coreRef->getValue($invoker));
    }

    public static function interceptorsDataProvider(): \Traversable
    {
        yield [[]];
        yield [[
            CycleInterceptor::class
        ]];
        yield [[
            CycleInterceptor::class,
            TypecasterInterceptor::class
        ]];
        yield [[
            CycleInterceptor::class,
            new Autowire(TypecasterInterceptor::class),
            new class () implements CoreInterceptorInterface {
                public function process(string $controller, string $action, array $parameters, CoreInterface $core): mixed
                {
                }
            }
        ]];
    }
}
