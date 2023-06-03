<?php

declare(strict_types=1);

namespace Spiral\Livewire\Config;

use Spiral\Core\Container\Autowire;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\InjectableConfig;
use Spiral\Events\Config\EventListener;
use Spiral\Livewire\Component\Registry\Processor\ProcessorInterface;
use Spiral\Livewire\Middleware\Component\DehydrationMiddleware;
use Spiral\Livewire\Middleware\Component\HydrationMiddleware;
use Spiral\Livewire\Middleware\Component\InitialDehydrationMiddleware;
use Spiral\Livewire\Middleware\Component\InitialHydrationMiddleware;

/**
 * @psalm-type TInitialHydrationMiddleware = InitialHydrationMiddleware|class-string<InitialHydrationMiddleware>|Autowire<InitialHydrationMiddleware>
 * @psalm-type THydrationMiddleware = HydrationMiddleware|class-string<HydrationMiddleware>|Autowire<HydrationMiddleware>
 * @psalm-type TInitialDehydrationMiddleware = InitialDehydrationMiddleware|class-string<InitialDehydrationMiddleware>|Autowire<InitialDehydrationMiddleware>
 * @psalm-type TDehydrationMiddleware = DehydrationMiddleware|class-string<DehydrationMiddleware>|Autowire<DehydrationMiddleware>
 * @psalm-type TProcessor = ProcessorInterface|class-string<ProcessorInterface>|Autowire<ProcessorInterface>
 * @psalm-type TInterceptor = CoreInterceptorInterface|class-string<CoreInterceptorInterface>|Autowire<CoreInterceptorInterface>
 *
 * @property array{
 *     listeners: array<class-string, EventListener|EventListener[]>,
 *     interceptors: array{mount: TInterceptor[], boot: TInterceptor[]},
 *     initial_hydration_middleware: TInitialHydrationMiddleware[],
 *     hydration_middleware: THydrationMiddleware[],
 *     initial_dehydration_middleware: TInitialDehydrationMiddleware[],
 *     dehydration_middleware: TDehydrationMiddleware[],
 *     processors: TProcessor[],
 *     disable_browser_cache: bool
 * } $config
 */
final class LivewireConfig extends InjectableConfig
{
    public const CONFIG = 'livewire';

    protected array $config = [
        'listeners' => [],
        'interceptors' => [
            'mount' => [],
            'boot' => []
        ],
        'initial_hydration_middleware' => [],
        'hydration_middleware' => [],
        'initial_dehydration_middleware' => [],
        'dehydration_middleware' => [],
        'processors' => [],
        'disable_browser_cache' => true,
    ];

    /**
     * @return array<class-string, EventListener|EventListener[]>
     */
    public function getListeners(): array
    {
        return $this->config['listeners'] ?? [];
    }

    /**
     * @return TInterceptor[]
     */
    public function getMountInterceptors(): array
    {
        return $this->config['interceptors']['mount'] ?? [];
    }

    /**
     * @return TInterceptor[]
     */
    public function getBootInterceptors(): array
    {
        return $this->config['interceptors']['boot'] ?? [];
    }

    /**
     * @return TInitialHydrationMiddleware[]
     */
    public function getInitialHydrationMiddleware(): array
    {
        return $this->config['initial_hydration_middleware'] ?? [];
    }

    /**
     * @return THydrationMiddleware[]
     */
    public function getHydrationMiddleware(): array
    {
        return $this->config['hydration_middleware'] ?? [];
    }

    /**
     * @return TInitialDehydrationMiddleware[]
     */
    public function getInitialDehydrationMiddleware(): array
    {
        return $this->config['initial_dehydration_middleware'] ?? [];
    }

    /**
     * @return TDehydrationMiddleware[]
     */
    public function getDehydrationMiddleware(): array
    {
        return $this->config['dehydration_middleware'] ?? [];
    }

    /**
     * @return TProcessor[]
     */
    public function getProcessors(): array
    {
        return $this->config['processors'] ?? [];
    }

    public function isBrowserCacheDisabled(): bool
    {
        return $this->config['disable_browser_cache'];
    }
}
