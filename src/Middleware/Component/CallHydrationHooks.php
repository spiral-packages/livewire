<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Event\Component\ComponentBooted;
use Spiral\Livewire\Event\Component\ComponentDehydrate;
use Spiral\Livewire\Event\Component\ComponentDehydrateInitial;
use Spiral\Livewire\Event\Component\ComponentDehydrateSubsequent;
use Spiral\Livewire\Event\Component\ComponentHydrate;
use Spiral\Livewire\Event\Component\ComponentHydrateInitial;
use Spiral\Livewire\Event\Component\ComponentHydrateSubsequent;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

final class CallHydrationHooks implements HydrationMiddleware, DehydrationMiddleware, InitialDehydrationMiddleware, InitialHydrationMiddleware
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function hydrate(LivewireComponent $component, Request $request): void
    {
        $this->dispatcher->dispatch(new ComponentHydrate($component, $request));
        $this->dispatcher->dispatch(new ComponentHydrateSubsequent($component, $request));

        $component->hydrate($request);

        $this->dispatcher->dispatch(new ComponentBooted($component, $request));
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $component->dehydrate($response);

        $this->dispatcher->dispatch(new ComponentDehydrate($component, $response));
        $this->dispatcher->dispatch(new ComponentDehydrateSubsequent($component, $response));
    }

    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $component->dehydrate($response);

        $this->dispatcher->dispatch(new ComponentDehydrate($component, $response));
        $this->dispatcher->dispatch(new ComponentDehydrateInitial($component, $response));
    }

    public function initialHydrate(LivewireComponent $component, Request $request): void
    {
        $this->dispatcher->dispatch(new ComponentHydrate($component, $request));
        $this->dispatcher->dispatch(new ComponentHydrateInitial($component, $request));
    }
}
