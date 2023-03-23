<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Spiral\Livewire\Event\Component\ComponentDehydrate;
use Spiral\Livewire\Event\Component\ComponentHydrateSubsequent;

final class SupportChildren
{
    public function onComponentDehydrate(ComponentDehydrate $event): void
    {
        $event->response->memo['children'] = $event->component->getRenderedChildren();
    }

    public function onComponentHydrateSubsequent(ComponentHydrateSubsequent $event): void
    {
        $event->component->setPreviouslyRenderedChildren($event->request->memo['children']);
    }
}
