<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

final class RenderView implements InitialDehydrationMiddleware, DehydrationMiddleware
{
    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $this->render($component, $response);
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $this->render($component, $response);
    }

    private function render(LivewireComponent $component, Response $response): void
    {
        $html = $component->output();

        $response->effects['html'] = $html;
    }
}
