<?php

declare(strict_types=1);

namespace Spiral\Livewire\Interceptor\Boot;

use Spiral\Core\CoreInterface;
use Spiral\Livewire\Component\LivewireComponent;

final class BootInvoker
{
    public function __construct(
        private readonly CoreInterface $core
    ) {
    }

    public function invoke(LivewireComponent $component): void
    {
        if (\method_exists($component, 'boot')) {
            $ref = new \ReflectionMethod($component, 'boot');

            $this->core->callAction($component::class, 'boot', [
                'component' => $component,
                'reflection' => $ref
            ]);
        }
    }
}
