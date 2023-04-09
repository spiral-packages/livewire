<?php

declare(strict_types=1);

namespace Spiral\Livewire\Listener\Component;

use Psr\Container\ContainerInterface;
use Spiral\Livewire\Event\Component\ComponentDehydrateInitial;
use Spiral\Livewire\Event\Component\ComponentHydrateInitial;
use Spiral\Livewire\Event\Component\ComponentHydrateSubsequent;
use Spiral\Translator\TranslatorInterface;

final class SupportLocales
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    public function onComponentHydrateInitial(ComponentHydrateInitial $event): void
    {
        if (!$translator = $this->getTranslator()) {
            return;
        }

        $event->request->fingerprint['locale'] = $translator->getLocale();
    }

    public function onComponentDehydrateInitial(ComponentDehydrateInitial $event): void
    {
        if (!$translator = $this->getTranslator()) {
            return;
        }

        $event->response->fingerprint['locale'] = $translator->getLocale();
    }

    public function onComponentHydrateSubsequent(ComponentHydrateSubsequent $event): void
    {
        if (!$translator = $this->getTranslator()) {
            return;
        }

        if (($locale = ($event->request->fingerprint['locale'] ?? null)) && \method_exists($translator, 'setLocale')) {
            $translator->setLocale($locale);
        }
    }

    private function getTranslator(): ?TranslatorInterface
    {
        if (!$this->container->has(TranslatorInterface::class)) {
            return null;
        }

        return $this->container->get(TranslatorInterface::class);
    }
}
