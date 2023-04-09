<?php

declare(strict_types=1);

namespace Spiral\Livewire;

use Spiral\Livewire\Exception\RootTagMissingFromViewException;
use Spiral\Livewire\Service\Attribute;

/**
 * @psalm-import-type TFingerprint from Request
 */
final class Response
{
    /**
     * @param TFingerprint $fingerprint
     */
    public function __construct(
        public array $fingerprint,
        public array $memo = [],
        public array $effects = []
    ) {
    }

    /**
     * @throws RootTagMissingFromViewException
     * @throws \JsonException
     */
    public function embedThyselfInHtml(): void
    {
        if (!$html = $this->effects['html'] ?? null) {
            return;
        }

        $this->effects['html'] = Attribute::addAttributesToRootTagOfHtml($html, [
            'initial-data' => $this->toArrayWithoutHtml(),
        ]);
    }

    /**
     * @return non-empty-string
     *
     * @throws RootTagMissingFromViewException
     * @throws \JsonException
     */
    public function toInitialResponse(): string
    {
        $this->embedIdInHtml();

        return $this->effects['html'];
    }

    /**
     * Embeds the id of the component in the HTML tag
     * (<div wire:id="xxxxx">).
     *
     * @throws RootTagMissingFromViewException
     * @throws \JsonException
     */
    public function embedIdInHtml(): void
    {
        if (!$html = $this->effects['html'] ?? null) {
            return;
        }

        $this->effects['html'] = Attribute::addAttributesToRootTagOfHtml($html, [
            'id' => $this->fingerprint['id'],
        ]);
    }

    public function toArrayWithoutHtml(): array
    {
        return [
            'fingerprint' => $this->fingerprint,
            'effects' => \array_diff_key($this->effects, ['html' => null]),
            'serverMemo' => $this->memo,
        ];
    }

    /**
     * @throws RootTagMissingFromViewException
     * @throws \JsonException
     */
    public function toSubsequentResponse(Request $request): array
    {
        $this->embedIdInHtml();

        $dirtyMemo = [];

        // Only send along the memos that have changed to not bloat the payload.
        foreach ($this->memo as $key => $newValue) {
            // If the memo key is not in the request, add it.
            if (!isset($request->memo[$key])) {
                $dirtyMemo[$key] = $newValue;

                continue;
            }

            // If the memo values are the same, skip adding them.
            if ($request->memo[$key] === $newValue) {
                continue;
            }

            $dirtyMemo[$key] = $newValue;
        }

        // If 'data' is present in the response memo, diff it one level deep.
        if (isset($dirtyMemo['data'], $request->memo['data'])) {
            foreach ($dirtyMemo['data'] as $key => $value) {
                if (!isset($request->memo['data'][$key])) {
                    continue;
                }

                if ($value === $request->memo['data'][$key]) {
                    unset($dirtyMemo['data'][$key]);
                }
            }
        }

        // Make sure any data marked as "dirty" is present in the resulting data payload.
        foreach ($this->effects['dirty'] ?? [] as $property) {
            $parts = explode('.', $property);
            $property = reset($parts);

            $dirtyMemo['data'][$property] = $this->memo['data'][$property];
        }

        return [
            'effects' => $this->effects,
            'serverMemo' => $dirtyMemo,
        ];
    }
}
