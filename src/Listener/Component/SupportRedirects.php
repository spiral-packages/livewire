<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Spiral\Livewire\Event\Component\ComponentDehydrate;

final class SupportRedirects
{
    public function onComponentDehydrate(ComponentDehydrate $event): void
    {
        $event->response->effects['redirect'] = $event->component->toArray()['redirectTo'];
    }
}
