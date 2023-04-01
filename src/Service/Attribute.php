<?php

declare(strict_types=1);

namespace Spiral\Livewire\Service;

use Spiral\Livewire\Exception\RootTagMissingFromViewException;

final class Attribute
{
    /**
     * Embeds the given attributes and their data into the the root tag
     * of the specified HTML string.
     *
     * @param non-empty-string $dom  the source HTML
     * @param array            $data the attribute data to embed
     *
     * @return string the HTML with the attributes embedded in the root tag
     *
     * @throws RootTagMissingFromViewException when the component template contains more than one root tag
     * @throws \JsonException                  when escaping of data failed
     */
    public static function addAttributesToRootTagOfHtml(string $dom, array $data): string
    {
        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[sprintf('wire:%s', $key)] = self::escapeStringForHtml($value);
        }
        $attributes = array_map(
            static fn (string $value, string $key): string => sprintf('%s="%s"', $key, $value),
            $attributes,
            array_keys($attributes)
        );

        preg_match('/<([a-zA-Z0-9\-]*)/', $dom, $matches, \PREG_OFFSET_CAPTURE);

        if (\count($matches) < 1) {
            throw new RootTagMissingFromViewException(
                'Livewire encountered a missing root tag when trying to render a component.'
            );
        }

        $tagName = $matches[1][0];
        $tagNameLength = \strlen($tagName);
        $firstCharacterPositionInTagName = $matches[1][1];

        return substr_replace(
            $dom,
            ' '.implode(' ', $attributes),
            $firstCharacterPositionInTagName + $tagNameLength,
            0
        );
    }

    /**
     * Escapes a string for use in HTML code.
     *
     * @param mixed $subject the string to escape
     *
     * @return string the escaped string
     *
     * @throws \JsonException when encoding of json in subject string failed
     */
    protected static function escapeStringForHtml(mixed $subject): string
    {
        if (\is_string($subject) || is_numeric($subject)) {
            return htmlspecialchars((string) $subject);
        }

        return htmlspecialchars(json_encode($subject, \JSON_THROW_ON_ERROR));
    }
}
