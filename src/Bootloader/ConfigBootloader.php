<?php

declare(strict_types=1);

namespace Spiral\Livewire\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Events\Config\EventListener;
use Spiral\Livewire\Component\Registry\Processor\AttributeProcessor;
use Spiral\Livewire\Config\LivewireConfig;
use Spiral\Livewire\Event\Component\ComponentDehydrate;
use Spiral\Livewire\Event\Component\ComponentDehydrateInitial;
use Spiral\Livewire\Event\Component\ComponentDehydrateSubsequent;
use Spiral\Livewire\Event\Component\ComponentHydrateInitial;
use Spiral\Livewire\Event\Component\ComponentHydrateSubsequent;
use Spiral\Livewire\Event\Component\FlushState;
use Spiral\Livewire\Listener\Component as Listener;
use Spiral\Livewire\Middleware\Component as Middleware;

final class ConfigBootloader extends Bootloader
{
    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(): void
    {
        $this->initDefaultConfig();
    }

    private function initDefaultConfig(): void
    {
        $this->config->setDefaults(
            LivewireConfig::CONFIG,
            [
                'listeners' => [
                    ComponentHydrateInitial::class => new EventListener(
                        listener: Listener\SupportLocales::class,
                        method: 'onComponentHydrateInitial',
                        priority: 10
                    ),
                    ComponentHydrateSubsequent::class => [
                        new EventListener(
                            listener: Listener\SupportLocales::class,
                            method: 'onComponentHydrateSubsequent',
                            priority: 10
                        ),
                        new EventListener(
                            listener: Listener\SupportChildren::class,
                            method: 'onComponentHydrateSubsequent',
                            priority: 20
                        ),
                    ],
                    ComponentDehydrateInitial::class => [
                        new EventListener(
                            listener: Listener\SupportEvents::class,
                            method: 'onComponentDehydrateInitial',
                            priority: 10
                        ),
                        new EventListener(
                            listener: Listener\SupportRootElementTracking::class,
                            method: '__invoke',
                            priority: 15
                        ),
                        new EventListener(
                            listener: Listener\SupportLocales::class,
                            method: 'onComponentDehydrateInitial',
                            priority: 20
                        ),
                    ],
                    ComponentDehydrate::class => [
                        new EventListener(
                            listener: Listener\SupportEvents::class,
                            method: 'onComponentDehydrate',
                            priority: 10
                        ),
                        new EventListener(
                            listener: Listener\SupportStacks::class,
                            method: 'onComponentDehydrate',
                            priority: 20
                        ),
                        new EventListener(
                            listener: Listener\SupportChildren::class,
                            method: 'onComponentDehydrate',
                            priority: 30
                        ),
                    ],
                    ComponentDehydrateSubsequent::class => [
                        new EventListener(
                            listener: Listener\SupportStacks::class,
                            method: 'onComponentDehydrateSubsequent',
                            priority: 10
                        ),
                    ],
                    FlushState::class => new EventListener(
                        listener: Listener\SupportStacks::class,
                        method: 'onFlushState',
                        priority: 10
                    ),
                ],
                'initial_hydration_middleware' => [
                    Middleware\CallHydrationHooks::class,
                ],
                'hydration_middleware' => [
                    Middleware\SecureHydrationWithChecksum::class,
                    Middleware\HashDataPropertiesForDirtyDetection::class,
                    Middleware\HydratePublicProperties::class,
                    Middleware\CallPropertyHydrationHooks::class,
                    Middleware\CallHydrationHooks::class,
                    Middleware\PerformDataBindingUpdates::class,
                    Middleware\PerformActionCalls::class,
                    Middleware\PerformEventEmissions::class,
                ],
                'initial_dehydration_middleware' => [
                    Middleware\SecureHydrationWithChecksum::class,
                    Middleware\NormalizeServerMemoSansDataForJavaScript::class,
                    Middleware\HydratePublicProperties::class,
                    Middleware\CallPropertyHydrationHooks::class,
                    Middleware\CallHydrationHooks::class,
                    Middleware\RenderView::class,
                    Middleware\NormalizeComponentPropertiesForJavaScript::class,
                ],
                'dehydration_middleware' => [
                    Middleware\SecureHydrationWithChecksum::class,
                    Middleware\NormalizeServerMemoSansDataForJavaScript::class,
                    Middleware\HashDataPropertiesForDirtyDetection::class,
                    Middleware\HydratePublicProperties::class,
                    Middleware\CallPropertyHydrationHooks::class,
                    Middleware\CallHydrationHooks::class,
                    Middleware\RenderView::class,
                    Middleware\NormalizeComponentPropertiesForJavaScript::class,
                ],
                'processors' => [
                    AttributeProcessor::class,
                ],
                'disable_browser_cache' => true,
            ]
        );
    }
}
