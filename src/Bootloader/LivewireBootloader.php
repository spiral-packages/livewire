<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Bootloader\Security\EncrypterBootloader;
use Spiral\Core\FactoryInterface;
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
use Spiral\Livewire\Controller\LivewireController;
use Spiral\Livewire\WireTrait;
use Spiral\Router\Loader\Configurator\RoutingConfigurator;
use Spiral\Serializer\Symfony\Bootloader\SerializerBootloader;
use Spiral\Tokenizer\Bootloader\TokenizerListenerBootloader;
use Spiral\Views\Bootloader\ViewsBootloader;

final class LivewireBootloader extends Bootloader
{
    use WireTrait;

    protected const DEPENDENCIES = [
        TokenizerListenerBootloader::class,
        EncrypterBootloader::class,
        ConfigBootloader::class,
        ComponentMiddlewareBootloader::class,
        ListenerBootloader::class,
        SerializerBootloader::class,
    ];

    protected const SINGLETONS = [
        ComponentRegistryInterface::class => ComponentRegistry::class,
        ComponentProcessorRegistry::class => ComponentProcessorRegistry::class,
        ComponentFactory::class => AutowireFactory::class,
        ChecksumManagerInterface::class => ChecksumManager::class,
        PropertyHasherInterface::class => PropertyHasher::class,
        RendererInterface::class => Renderer::class,
        DataAccessorInterface::class => DataAccessor::class,
        ActionHandlerInterface::class => ActionHandler::class,
    ];

    public function init(ViewsBootloader $views, DirectoriesInterface $dirs): void
    {
        $views->addDirectory('livewire', rtrim($dirs->get('vendor'), '/').'/spiral-packages/livewire/views');
    }

    public function boot(
        LivewireConfig $config,
        FactoryInterface $factory,
        AbstractKernel $kernel,
        ComponentProcessorRegistry $registry,
        RoutingConfigurator $routing
    ): void {
        $this->registerComponentProcessors($config, $factory, $kernel, $registry);

        $routing
            ->add(name: 'livewire.message', pattern: '/livewire/message/<component>')
            ->action(LivewireController::class, 'message')
            ->methods('POST');
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
}
