<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Events\ListenerFactoryInterface;
use Spiral\Events\ListenerRegistryInterface;
use Spiral\League\Event\Bootloader\EventBootloader;
use Spiral\Livewire\Config\LivewireConfig;

final class ListenerBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        EventBootloader::class,
    ];

    public function boot(
        ListenerRegistryInterface $listenerRegistry,
        ListenerFactoryInterface $factory,
        LivewireConfig $config
    ): void {
        foreach ($config->getListeners() as $event => $listeners) {
            if (!\is_array($listeners)) {
                $listeners = [$listeners];
            }
            foreach ($listeners as $listener) {
                /** @psalm-suppress ArgumentTypeCoercion */
                $listenerRegistry->addListener(
                    $event,
                    $factory->create($listener->listener, $listener->method),
                    $listener->priority
                );
            }
        }
    }
}
