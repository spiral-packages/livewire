<?php

declare(strict_types=1);

namespace Spiral\Livewire\Component;

interface RendererInterface
{
    /**
     * @return ?non-empty-string
     */
    public function render(LivewireComponent $component): ?string;
}
