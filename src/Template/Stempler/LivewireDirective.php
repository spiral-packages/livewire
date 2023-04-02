<?php

declare(strict_types=1);

namespace Spiral\Livewire\Template\Stempler;

use Spiral\Boot\Environment\DebugMode;
use Spiral\Core\FactoryInterface;
use Spiral\Stempler\Directive\AbstractDirective;
use Spiral\Stempler\Node\Dynamic\Directive;
use Spiral\Views\ViewsInterface;

final class LivewireDirective extends AbstractDirective
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly DebugMode $debugMode,
    ) {
        parent::__construct();
    }

    public function renderLivewireStyles(Directive $directive): string
    {
        return $this->factory
            ->make(ViewsInterface::class)
            ->render('livewire:styles', [
                'debug' => $this->debugMode->isEnabled(),
            ]);
    }

    public function renderLivewireScripts(Directive $directive): string
    {
        return $this->factory
            ->make(ViewsInterface::class)
            ->render('livewire:scripts', [
                'debug' => $this->debugMode->isEnabled(),
            ]);
    }
}
