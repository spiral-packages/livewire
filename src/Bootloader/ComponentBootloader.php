<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\FactoryInterface;
use Spiral\Core\InterceptableCore;
use Spiral\Livewire\Component\ActionHandler;
use Spiral\Livewire\Component\ActionHandlerInterface;
use Spiral\Livewire\Component\ChecksumManager;
use Spiral\Livewire\Component\ChecksumManagerInterface;
use Spiral\Livewire\Component\DataAccessor;
use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\Factory\AutowireFactory;
use Spiral\Livewire\Component\Factory\FactoryInterface as ComponentFactory;
use Spiral\Livewire\Component\PropertyHasher;
use Spiral\Livewire\Component\PropertyHasherInterface;
use Spiral\Livewire\Component\Registry\ComponentProcessorRegistry;
use Spiral\Livewire\Component\Registry\ComponentRegistry;
use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Component\Registry\Processor\ProcessorInterface;
use Spiral\Livewire\Component\Renderer;
use Spiral\Livewire\Component\RendererInterface;
use Spiral\Livewire\Config\LivewireConfig;
use Spiral\Livewire\Interceptor\Boot\BootInvoker;
use Spiral\Livewire\Interceptor\Boot\Core as BootCore;
use Spiral\Livewire\Interceptor\Mount\Core as MountCore;
use Spiral\Livewire\Interceptor\Mount\MountInvoker;
use Spiral\Livewire\WireTrait;

final class ComponentBootloader extends Bootloader
{
    use WireTrait;

    protected const SINGLETONS = [
        ComponentRegistryInterface::class => ComponentRegistry::class,
        ComponentProcessorRegistry::class => ComponentProcessorRegistry::class,
        ComponentFactory::class => AutowireFactory::class,
        ChecksumManagerInterface::class => ChecksumManager::class,
        PropertyHasherInterface::class => PropertyHasher::class,
        RendererInterface::class => Renderer::class,
        DataAccessorInterface::class => DataAccessor::class,
        ActionHandlerInterface::class => ActionHandler::class,
        MountInvoker::class => [self::class, 'initMountInvoker'],
        BootInvoker::class => [self::class, 'initBootInvoker']
    ];

    public function boot(
        LivewireConfig $config,
        FactoryInterface $factory,
        AbstractKernel $kernel,
        ComponentProcessorRegistry $registry,
    ): void {
        $this->registerComponentProcessors($config, $factory, $kernel, $registry);
    }

    private function registerComponentProcessors(
        LivewireConfig $config,
        FactoryInterface $factory,
        AbstractKernel $kernel,
        ComponentProcessorRegistry $registry
    ): void {
        foreach ($config->getProcessors() as $processor) {
            $processor = $this->wire($processor, $factory);

            \assert($processor instanceof ProcessorInterface);
            $registry->addProcessor($processor);
        }

        $kernel->bootstrapped(static function (ComponentProcessorRegistry $registry): void {
            $registry->process();
        });
    }

    private function initMountInvoker(
        MountCore $core,
        LivewireConfig $config,
        FactoryInterface $factory,
        ?EventDispatcherInterface $dispatcher = null,
    ): MountInvoker {
        $core = new InterceptableCore($core, $dispatcher);

        foreach ($config->getMountInterceptors() as $interceptor) {
            $interceptor = $this->wire($interceptor, $factory);

            \assert($interceptor instanceof CoreInterceptorInterface);
            $core->addInterceptor($interceptor);
        }

        return new MountInvoker($core);
    }

    private function initBootInvoker(
        BootCore $core,
        LivewireConfig $config,
        FactoryInterface $factory,
        ?EventDispatcherInterface $dispatcher = null,
    ): BootInvoker {
        $core = new InterceptableCore($core, $dispatcher);

        foreach ($config->getBootInterceptors() as $interceptor) {
            $interceptor = $this->wire($interceptor, $factory);

            \assert($interceptor instanceof CoreInterceptorInterface);
            $core->addInterceptor($interceptor);
        }

        return new BootInvoker($core);
    }
}
