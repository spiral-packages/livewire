<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\DataAccessorInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\PublicPropertyTypeNotAllowedException;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

final class HydrateModelProperties implements HydrationMiddleware, DehydrationMiddleware, InitialDehydrationMiddleware
{
    public function __construct(
        private readonly DataAccessorInterface $dataAccessor
    ) {
    }

    public function hydrate(LivewireComponent $component, Request $request): void
    {
        foreach ($request->memo['data'] ?? [] as $property => $value) {
            $this->dataAccessor->setValue($component, $property, $value);
        }
    }

    /**
     * @throws PublicPropertyTypeNotAllowedException
     */
    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $this->dehydrateProperties($component, $response);
    }

    /**
     * @throws PublicPropertyTypeNotAllowedException
     */
    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $this->dehydrateProperties($component, $response);
    }

    /**
     * @throws PublicPropertyTypeNotAllowedException
     */
    private function dehydrateProperties(LivewireComponent $component, Response $response): void
    {
        $response->memo['data'] = $this->dataAccessor->getData($component);
    }
}
