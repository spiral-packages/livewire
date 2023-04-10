<?php

declare(strict_types=1);

namespace Spiral\Livewire\Controller;

use Psr\Http\Message\ResponseInterface;
use Spiral\Http\Request\InputManager;
use Spiral\Http\ResponseWrapper;
use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Livewire;
use Spiral\Livewire\Request;

/**
 * @psalm-import-type TComponentName from LivewireComponent
 */
final class LivewireController
{
    public function __construct(
        private readonly ResponseWrapper $response
    ) {
    }

    /**
     * @param TComponentName $component
     */
    public function message(string $component, Livewire $livewire, InputManager $input): ResponseInterface
    {
        return $this->response->json($livewire->subsequentRequest(
            $component,
            new Request($input->data->all())
        ));
    }
}
