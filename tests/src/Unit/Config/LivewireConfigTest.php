<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit\Config;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Spiral\Core\Container\Autowire;
use Spiral\Events\Config\EventListener;
use Spiral\Livewire\Config\LivewireConfig;
use Spiral\Livewire\Event\Component\ComponentHydrateInitial;
use Spiral\Livewire\Interceptor\Mount\CycleInterceptor;
use Spiral\Livewire\Interceptor\Mount\TypecasterInterceptor;
use Spiral\Livewire\Listener\Component\SupportLocales;
use Spiral\Livewire\Service\ArgumentTypecast;

final class LivewireConfigTest extends TestCase
{
    #[DataProvider('listenersDataProvider')]
    public function testGetListeners(mixed $listeners, array $expected): void
    {
        $config = new LivewireConfig(['listeners' => $listeners]);

        $this->assertEquals($expected, $config->getListeners());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetMountInterceptors(mixed $interceptors, array $expected): void
    {
        $config = new LivewireConfig(['interceptors' => ['mount' => $interceptors]]);

        $this->assertEquals($expected, $config->getMountInterceptors());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetBootInterceptors(mixed $interceptors, array $expected): void
    {
        $config = new LivewireConfig(['interceptors' => ['boot' => $interceptors]]);

        $this->assertEquals($expected, $config->getBootInterceptors());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetInitialHydrationMiddleware(mixed $middleware, array $expected): void
    {
        $config = new LivewireConfig(['initial_hydration_middleware' => $middleware]);

        $this->assertEquals($expected, $config->getInitialHydrationMiddleware());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetHydrationMiddleware(mixed $middleware, array $expected): void
    {
        $config = new LivewireConfig(['hydration_middleware' => $middleware]);

        $this->assertEquals($expected, $config->getHydrationMiddleware());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetInitialDehydrationMiddleware(mixed $middleware, array $expected): void
    {
        $config = new LivewireConfig(['initial_dehydration_middleware' => $middleware]);

        $this->assertEquals($expected, $config->getInitialDehydrationMiddleware());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetDehydrationMiddleware(mixed $middleware, array $expected): void
    {
        $config = new LivewireConfig(['dehydration_middleware' => $middleware]);

        $this->assertEquals($expected, $config->getDehydrationMiddleware());
    }

    #[DataProvider('canWireDataProvider')]
    public function testGetProcessors(mixed $processors, array $expected): void
    {
        $config = new LivewireConfig(['processors' => $processors]);

        $this->assertEquals($expected, $config->getProcessors());
    }

    public function testIsBrowserCacheDisabled(): void
    {
        $config = new LivewireConfig();
        $this->assertTrue($config->isBrowserCacheDisabled());

        $config = new LivewireConfig(['disable_browser_cache' => false]);

        $this->assertFalse($config->isBrowserCacheDisabled());
    }

    public static function listenersDataProvider(): \Traversable
    {
        yield [null, []];
        yield [[], []];
        yield [
            [
                ComponentHydrateInitial::class => new EventListener(
                    listener: SupportLocales::class,
                    method: 'onComponentHydrateInitial',
                    priority: 10
                )
            ],
            [
                ComponentHydrateInitial::class => new EventListener(
                    listener: SupportLocales::class,
                    method: 'onComponentHydrateInitial',
                    priority: 10
                )
            ]
        ];
        yield [
            [
                ComponentHydrateInitial::class => [
                    new EventListener(
                        listener: SupportLocales::class,
                        method: 'onComponentHydrateInitial',
                        priority: 10
                    ),
                    new EventListener(
                        listener: SupportLocales::class,
                        method: 'onComponentHydrateInitial',
                        priority: 20
                    )
                ]
            ],
            [
                ComponentHydrateInitial::class => [
                    new EventListener(
                        listener: SupportLocales::class,
                        method: 'onComponentHydrateInitial',
                        priority: 10
                    ),
                    new EventListener(
                        listener: SupportLocales::class,
                        method: 'onComponentHydrateInitial',
                        priority: 20
                    )
                ]
            ]
        ];
    }

    public static function canWireDataProvider(): \Traversable
    {
        yield [null, []];
        yield [[], []];
        yield [[CycleInterceptor::class], [CycleInterceptor::class]];
        yield [
            [CycleInterceptor::class, TypecasterInterceptor::class],
            [CycleInterceptor::class, TypecasterInterceptor::class]
        ];
        yield [
            [
                CycleInterceptor::class,
                new Autowire(TypecasterInterceptor::class),
                new TypecasterInterceptor(new ArgumentTypecast())
            ],
            [
                CycleInterceptor::class,
                new Autowire(TypecasterInterceptor::class),
                new TypecasterInterceptor(new ArgumentTypecast())
            ]
        ];
    }
}
