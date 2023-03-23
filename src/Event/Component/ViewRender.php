<?php

declare(strict_types=1);

namespace Spiral\Livewire\Event\Component;

use Spiral\Views\ViewInterface;

final class ViewRender
{
    public function __construct(
        public readonly ViewInterface $view,
        public readonly string $output
    ) {
    }
}
