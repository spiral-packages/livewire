<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\ChecksumManagerInterface;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Exception\Component\CorruptComponentPayloadException;
use Spiral\Livewire\Request;
use Spiral\Livewire\Response;

/**
 * Make sure the data coming back to hydrate a component hasn't been tampered with.
 */
final class SecureHydrationWithChecksum implements HydrationMiddleware, DehydrationMiddleware, InitialDehydrationMiddleware
{
    public function __construct(
        private readonly ChecksumManagerInterface $checksumManager
    ) {
    }

    /**
     * @throws CorruptComponentPayloadException
     */
    public function hydrate(LivewireComponent $component, Request $request): void
    {
        $checksum = $request->memo['checksum'];

        unset($request->memo['checksum']);

        if (!$this->checksumManager->check($checksum, $request->fingerprint, $request->memo)) {
            throw new CorruptComponentPayloadException(sprintf(
                'Livewire encountered corrupt data when trying to hydrate the %s component.',
                $component->getComponentName()
            ));
        }
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $response->memo['checksum'] = $this->checksumManager->generate($response->fingerprint, $response->memo);
    }

    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $response->memo['checksum'] = $this->checksumManager->generate($response->fingerprint, $response->memo);
    }
}
