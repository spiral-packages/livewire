<?php

declare(strict_types=1);

namespace Spiral\Livewire\Middleware\Component;

abstract class AbstractDataNormalizerForJavaScript
{
    protected function reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder(mixed $value): array
    {
        // Make sure string keys are last (but not ordered) and numeric keys are ordered.
        // JSON.parse will do this on the frontend, so we'll get ahead of it.

        if (!\is_array($value)) {
            return $value;
        }

        $itemsWithNumericKeys = array_filter($value, static fn ($key) => is_numeric($key), \ARRAY_FILTER_USE_KEY);
        ksort($itemsWithNumericKeys);

        $itemsWithStringKeys = array_filter($value, static fn ($key) => !is_numeric($key), \ARRAY_FILTER_USE_KEY);

        // array_merge will reindex in some cases so we stick to array_replace
        $normalizedData = array_replace($itemsWithNumericKeys, $itemsWithStringKeys);

        $output = array_map(function (mixed $value): array {
            return $this->reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
        }, $normalizedData);

        return $output;
    }
}
