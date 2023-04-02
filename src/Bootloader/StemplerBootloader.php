<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Livewire\Livewire;
use Spiral\Livewire\Template\Stempler\ComponentRenderer;
use Spiral\Livewire\Template\Stempler\LivewireDirective;
use Spiral\Livewire\Template\Stempler\NodeVisitor;
use Spiral\Stempler\Bootloader\StemplerBootloader as StemplerBridge;
use Spiral\Stempler\Builder;
use Spiral\Stempler\StemplerEngine;
use Spiral\Views\ViewContext;
use Spiral\Views\ViewManager;

final class StemplerBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        StemplerBridge::class,
    ];

    public function init(StemplerBridge $stempler): void
    {
        $stempler->addDirective(LivewireDirective::class);
        $stempler->addVisitor(NodeVisitor::class, Builder::STAGE_COMPILE);
    }

    public function boot(ViewManager $views, Livewire $livewire): void
    {
        foreach ($views->getEngines() as $engine) {
            if ($engine instanceof StemplerEngine) {
                $engine->getBuilder(new ViewContext())->getCompiler()->addRenderer(new ComponentRenderer($livewire));
            }
        }
    }
}
