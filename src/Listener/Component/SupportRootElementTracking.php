<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Spiral\Livewire\Event\Component\ComponentDehydrateInitial;

final class SupportRootElementTracking
{
    public function __invoke(ComponentDehydrateInitial $event): void
    {
        if (empty($event->response->effects['html'])) {
            return;
        }

        $event->response->effects['html'] = $this->addComponentEndingMarker(
            $event->response->effects['html'],
            $event->component->getComponentId()
        );
    }

    private function addComponentEndingMarker(string $html, string $id): string
    {
        return $html."\n<!-- Livewire Component wire-end:".$id.' -->';
    }
}
