<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Bootloader\Security\EncrypterBootloader;
use Spiral\Livewire\Controller\LivewireController;
use Spiral\Livewire\WireTrait;
use Spiral\MarshallerBridge\Bootloader\MarshallerBootloader;
use Spiral\Router\Loader\Configurator\RoutingConfigurator;
use Spiral\Tokenizer\Bootloader\TokenizerListenerBootloader;
use Spiral\Views\Bootloader\ViewsBootloader;

final class LivewireBootloader extends Bootloader
{
    use WireTrait;

    protected const DEPENDENCIES = [
        TokenizerListenerBootloader::class,
        EncrypterBootloader::class,
        ConfigBootloader::class,
        ComponentBootloader::class,
        ComponentMiddlewareBootloader::class,
        ListenerBootloader::class,
        MarshallerBootloader::class,
    ];

    public function init(ViewsBootloader $views, DirectoriesInterface $dirs): void
    {
        $views->addDirectory('livewire', rtrim($dirs->get('vendor'), '/').'/spiral-packages/livewire/views');
    }

    public function boot(RoutingConfigurator $routing): void
    {
        $routing
            ->add(name: 'livewire.message', pattern: '/livewire/message/<component>')
            ->action(LivewireController::class, 'message')
            ->methods('POST');
    }
}
