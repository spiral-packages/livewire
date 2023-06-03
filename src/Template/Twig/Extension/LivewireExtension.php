<?php

declare(strict_types=1);

namespace Spiral\Livewire\Template\Twig\Extension;

use Spiral\Boot\Environment\DebugMode;
use Spiral\Core\FactoryInterface;
use Spiral\Livewire\Livewire;
use Spiral\Livewire\Template\Twig\NodeVisitor\LivewireNodeVisitor;
use Spiral\Views\ViewsInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LivewireExtension extends AbstractExtension
{
    public function __construct(
        private readonly FactoryInterface $factory
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('livewire_styles', [$this, 'livewireStyles'], ['is_safe' => ['html']]),
            new TwigFunction('livewire_scripts', [$this, 'livewireScripts'], ['is_safe' => ['html']]),
            new TwigFunction('livewire', [$this, 'livewire'], ['is_safe' => ['html']]),
        ];
    }

    public function livewireStyles(): string
    {
        return $this->factory
            ->make(ViewsInterface::class)
            ->render('livewire:styles', [
                'debug' => $this->factory->make(DebugMode::class)->isEnabled(),
            ]);
    }

    public function livewireScripts(): string
    {
        return $this->factory
            ->make(ViewsInterface::class)
            ->render('livewire:scripts', [
                'debug' => $this->factory->make(DebugMode::class)->isEnabled(),
                'jsonEncodedOptions' => '',
                'appUrl' => '',
            ]);
    }

    /**
     * @param non-empty-string $name
     */
    public function livewire(string $name, mixed ...$parameters): string
    {
        return $this->factory
            ->make(Livewire::class)
            ->initialRequest($name, $parameters);
    }

    public function getNodeVisitors(): array
    {
        return [
            $this->factory->make(LivewireNodeVisitor::class),
        ];
    }
}
