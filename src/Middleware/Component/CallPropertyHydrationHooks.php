<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Event\Component\PropertyDehydrate;
use Spiral\Livewire\Event\Component\PropertyHydrate;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

final class CallPropertyHydrationHooks implements HydrationMiddleware, DehydrationMiddleware, InitialDehydrationMiddleware
{
    public function __construct(
        private readonly ResolverInterface $resolver,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function hydrate(LivewireComponent $component, Request $request): void
    {
        foreach ($component->getPublicPropertiesDefinedBySubClass() as $property => $value) {
            $this->dispatcher->dispatch(new PropertyHydrate($property, $value, $component, $request));

            // Call magic hydrateProperty methods on the component.
            // If the method doesn't exist, the __call will eat it.
            $studlyProperty = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $property)));
            $method = 'hydrate'.$studlyProperty;

            if (method_exists($component, $method)) {
                $component->{$method}(...$this->resolver->resolveArguments(
                    new \ReflectionMethod($component, $method),
                    ['value' => $value, 'request' => $request]
                ));
            }
        }
    }

    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $this->callDehydrateHooks($component, $response);
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $this->callDehydrateHooks($component, $response);
    }

    private function callDehydrateHooks(LivewireComponent $component, Response $response): void
    {
        foreach ($component->getPublicPropertiesDefinedBySubClass() as $property => $value) {
            $studlyProperty = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $property)));
            $method = 'dehydrate'.$studlyProperty;

            if (method_exists($component, $method)) {
                $component->{$method}(...$this->resolver->resolveArguments(
                    new \ReflectionMethod($component, $method),
                    ['value' => $value, 'response' => $response]
                ));
            }

            $this->dispatcher->dispatch(new PropertyDehydrate($property, $value, $component, $response));
        }
    }
}
