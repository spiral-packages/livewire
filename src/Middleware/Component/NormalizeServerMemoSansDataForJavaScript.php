<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

use Spiral\Livewire\Component\LivewireComponent;
use Spiral\Livewire\Response;

final class NormalizeServerMemoSansDataForJavaScript extends AbstractDataNormalizerForJavaScript implements DehydrationMiddleware, InitialDehydrationMiddleware
{
    public function dehydrate(LivewireComponent $component, Response $response): void
    {
        $this->normalize($response);
    }

    public function initialDehydrate(LivewireComponent $component, Response $response): void
    {
        $this->normalize($response);
    }

    private function normalize(Response $response): void
    {
        foreach ($response->memo as $key => $val) {
            if ('data' === $key) {
                continue;
            }

            if (\is_array($val)) {
                $response->memo[$key] = $this->reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($val);
            }
        }
    }
}
