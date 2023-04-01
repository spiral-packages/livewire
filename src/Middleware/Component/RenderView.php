<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Component\RendererInterface;
use Spiral\Livewire\Response;

final class RenderView implements InitialDehydrationMiddleware, DehydrationMiddleware
{
    public function __construct(
        private readonly RendererInterface $renderer
    ) {
    }

    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $response->effects['html'] = $this->renderer->render($component);
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $response->effects['html'] = $this->renderer->render($component);
    }
}
