<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Livewire\Twig\Extension\LivewireExtension;
use Spiral\Twig\Bootloader\TwigBootloader as TwigBridge;

final class TwigBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        TwigBridge::class,
    ];

    public function init(TwigBridge $twig): void
    {
        $twig->addExtension(LivewireExtension::class);
    }
}
