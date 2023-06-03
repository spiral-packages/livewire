<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Spiral\Livewire\Event\Component\ComponentDehydrate;
use Spiral\Livewire\Event\Component\ComponentDehydrateSubsequent;
use Spiral\Livewire\Event\Component\FlushState;

final class SupportStacks
{
    public array $forStack = [];

    public function onComponentDehydrate(ComponentDehydrate $event): void
    {
        $this->forStack[$event->component->getComponentName()] = array_merge(
            $this->forStack[$event->component->getComponentName()] ?? [],
            $event->component->toArray()['forStack']
        );
    }

    public function onComponentDehydrateSubsequent(ComponentDehydrateSubsequent $event): void
    {
        if (\count($this->forStack[$event->component->getComponentName()] ?? [])) {
            $event->response->effects['forStack'] = $this->forStack[$event->component->getComponentName()];
        }
    }

    /**
     * TODO fire event
     */
    public function onFlushState(FlushState $event): void
    {
        $this->forStack[$event->component->getComponentName()] = [];
    }
}
