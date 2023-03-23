<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Component\PropertyHasherInterface;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

final class HashDataPropertiesForDirtyDetection implements HydrationMiddleware, DehydrationMiddleware
{
    public function __construct(
        private readonly PropertyHasherInterface $hasher
    ) {
    }

    public function hydrate(LivewireComponent $component, Request $request): void
    {
        $data = $request->memo['data'] ?? [];

        foreach ($data as $key => $value) {
            $this->hasher->hash($component->getId(), $key, $value);
        }
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $data = $response->memo['data'] ?? [];

        $dirtyProps = [];
        foreach ($this->hasher->getHashes($component->getId()) as $key => $hash) {
            if (!$this->hasher->isEquals($hash, $data[$key] ?? null)) {
                $dirtyProps[] = $key;
            }
        }

        $response->effects['dirty'] = $dirtyProps;
    }
}
