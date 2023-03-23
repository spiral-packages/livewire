<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

final class NormalizeComponentPropertiesForJavaScript extends AbstractDataNormalizerForJavaScript implements InitialDehydrationMiddleware, DehydrationMiddleware
{
    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $this->normalize($component);
    }

    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $this->normalize($component);
    }

    private function normalize(LivewireComponent $component): void
    {
        foreach ($component->getPublicPropertiesDefinedBySubClass() as $key => $value) {
            if (\is_array($value)) {
                $component->$key = $this->reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
            }
        }
    }
}
