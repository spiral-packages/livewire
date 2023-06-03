<?php

declare(strict_types=1);

namespace Spiral\Livewire;

use Spiral\Http\Request\InputManager;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Component\Registry\ComponentRegistryInterface;
use Spiral\Livewire\Exception\Component\ComponentNotFoundException;
use Spiral\Livewire\Exception\Component\RenderException;
use Spiral\Livewire\Exception\InvalidTypeException;
use Spiral\Livewire\Exception\RootTagMissingFromViewException;
use Spiral\Livewire\Interceptor\Boot\BootInvoker;
use Spiral\Livewire\Interceptor\Mount\MountInvoker;
use Spiral\Livewire\Middleware\Component\Registry\DehydrationMiddlewareRegistryInterface;
use Spiral\Livewire\Middleware\Component\Registry\HydrationMiddlewareRegistryInterface;
use Spiral\Livewire\Middleware\Component\Registry\InitialDehydrationMiddlewareRegistryInterface;
use Spiral\Livewire\Middleware\Component\Registry\InitialHydrationMiddlewareRegistryInterface;

/**
 * @psalm-import-type TComponentName from LivewireComponent
 */
final class Livewire
{
    public function __construct(
        private readonly ComponentRegistryInterface $componentRegistry,
        private readonly InitialHydrationMiddlewareRegistryInterface $initialHydrationMiddlewareRegistry,
        private readonly HydrationMiddlewareRegistryInterface $hydrationMiddlewareRegistry,
        private readonly InitialDehydrationMiddlewareRegistryInterface $initialDehydrationMiddlewareRegistry,
        private readonly DehydrationMiddlewareRegistryInterface $dehydrationMiddlewareRegistry,
        private readonly BootInvoker $bootInvoker,
        private readonly MountInvoker $mountInvoker,
        private readonly InputManager $input
    ) {
    }

    /**
     * @psalm-param TComponentName $componentName
     *
     * @throws \JsonException
     * @throws \ReflectionException
     * @throws RenderException
     * @throws ComponentNotFoundException
     * @throws RootTagMissingFromViewException
     * @throws InvalidTypeException
     */
    public function initialRequest(string $componentName, array $params): string
    {
        $component = $this->componentRegistry->get($componentName);

        $this->bootInvoker->invoke($component);

        $request = new Request([
            'fingerprint' => [
                'id' => $component->getComponentId(),
                'name' => $component->getComponentName(),
                'path' => $this->input->path(),
                'method' => $this->input->method(),
            ],
            'updates' => [],
            'serverMemo' => [
                'errors' => [],
            ],
        ]);

        $this->initialHydrate($component, $request);

        $this->mountInvoker->invoke($component, $params);

        $component->renderToView();
        $response = new Response($request->fingerprint, $request->memo);
        $this->initialDehydrate($component, $response);

        $response->embedThyselfInHtml();

        return $response->toInitialResponse();
    }

    /**
     * @param TComponentName $componentName
     *
     * @throws ComponentNotFoundException
     * @throws RenderException
     * @throws RootTagMissingFromViewException
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function subsequentRequest(string $componentName, Request $request): array
    {
        $component = $this->componentRegistry->get($componentName);
        $this->bootInvoker->invoke($component);

        $this->hydrate($component, $request);

        $component->renderToView();
        $response = new Response(fingerprint: $request->fingerprint, memo: $request->memo);

        $this->dehydrate($component, $response);

        return $response->toSubsequentResponse($request);
    }

    private function initialHydrate(LivewireComponent $component, Request $request): void
    {
        foreach ($this->initialHydrationMiddlewareRegistry->all() as $middleware) {
            $middleware->initialHydrate($component, $request);
        }
    }

    private function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        // The array is being reversed here, so the middleware dehydrate phase order of execution is
        // the inverse of hydrate. This makes the middlewares behave like layers in a shell.
        foreach (\array_reverse($this->initialDehydrationMiddlewareRegistry->all()) as $middleware) {
            $middleware->initialDehydrate($component, $response);
        }
    }

    private function hydrate(LivewireComponent $component, Request $request): void
    {
        foreach ($this->hydrationMiddlewareRegistry->all() as $middleware) {
            $middleware->hydrate($component, $request);
        }
    }

    private function dehydrate(LivewireComponent $component, Response $response): void
    {
        // The array is being reversed here, so the middleware dehydrate phase order of execution is
        // the inverse of hydrate. This makes the middlewares behave like layers in a shell.
        foreach (\array_reverse($this->dehydrationMiddlewareRegistry->all()) as $middleware) {
            $middleware->dehydrate($component, $response);
        }
    }
}
