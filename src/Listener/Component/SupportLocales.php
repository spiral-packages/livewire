<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Spiral\Livewire\Event\Component\ComponentDehydrateInitial;
use Spiral\Livewire\Event\Component\ComponentHydrateInitial;
use Spiral\Livewire\Event\Component\ComponentHydrateSubsequent;
use Spiral\Translator\TranslatorInterface;

final class SupportLocales
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function onComponentHydrateInitial(ComponentHydrateInitial $event): void
    {
        $event->request->fingerprint['locale'] = $this->translator->getLocale();
    }

    public function onComponentDehydrateInitial(ComponentDehydrateInitial $event): void
    {
        $event->response->fingerprint['locale'] = $this->translator->getLocale();
    }

    public function onComponentHydrateSubsequent(ComponentHydrateSubsequent $event): void
    {
        if ($locale = ($event->request->fingerprint['locale'] ?? null)) {
            if (\method_exists($this->translator, 'setLocale')) {
                $this->translator->setLocale($locale);
            }
        }
    }
}
