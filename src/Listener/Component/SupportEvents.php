<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Spiral\Livewire\Event\Component\ComponentDehydrate;
use Spiral\Livewire\Event\Component\ComponentDehydrateInitial;

final class SupportEvents
{
    public function onComponentDehydrateInitial(ComponentDehydrateInitial $event): void
    {
        $event->response->effects['listeners'] = $event->component->getEventsBeingListenedFor();
    }

    public function onComponentDehydrate(ComponentDehydrate $event): void
    {
        $emits = $event->component->getEventQueue();
        $dispatches = $event->component->getDispatchQueue();

        if ($emits) {
            $event->response->effects['emits'] = $emits;
        }

        if ($dispatches) {
            $event->response->effects['dispatches'] = $dispatches;
        }
    }
}
