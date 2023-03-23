<?php

/**
 * This code is partially extracted from package illuminate/support.
 *
 * @see https://github.com/illuminate/support for the canonical source repository.
 *
 * @license https://github.com/illuminate/support/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Spiral\Livewire;

final class Str
{
    /**
     * Replace the given value in the given string.
     */
    public static function replace(string|iterable $search, string|iterable $replace, string|iterable $subject): string
    {
        if ($search instanceof \Traversable) {
            $search = iterator_to_array($search);
        }

        if ($replace instanceof \Traversable) {
            $replace = iterator_to_array($replace);
        }

        if ($subject instanceof \Traversable) {
            $subject = iterator_to_array($subject);
        }

        return str_replace($search, $replace, $subject);
    }

    /**
     * Convert a value to studly caps case.
     */
    public static function studly(string $value): string
    {
        $words = explode(' ', self::replace(['-', '_'], ' ', $value));

        $studlyWords = array_map(static fn (string $word): string => ucfirst($word), $words);

        return implode('', $studlyWords);
    }

    /**
     * Convert a string to kebab case.
     */
    public static function kebab(string $value): string
    {
        return self::snake($value, '-');
    }

    /**
     * Convert a string to snake case.
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        /** @psalm-suppress NoValue */
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return $value;
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     */
    public static function before(string|\Stringable $subject, string|\Stringable $search): string
    {
        if (!\is_string($subject)) {
            $subject = (string) $subject;
        }

        if (!\is_string($search)) {
            $search = (string) $search;
        }

        if ('' === $search) {
            return $subject;
        }

        $result = strstr($subject, $search, true);

        return false === $result ? $subject : $result;
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     */
    public static function after(string|\Stringable $subject, string|\Stringable $search): string
    {
        if (!\is_string($subject)) {
            $subject = (string) $subject;
        }

        if (!\is_string($search)) {
            $search = (string) $search;
        }

        return '' === $search ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     */
    public static function afterLast(string|\Stringable $subject, string|\Stringable $search): string
    {
        if (!\is_string($subject)) {
            $subject = (string) $subject;
        }

        if (!\is_string($search)) {
            $search = (string) $search;
        }

        $position = strrpos($subject, $search);

        if (false === $position) {
            return $subject;
        }

        return substr($subject, $position + \strlen($search));
    }

    /**
     * @param positive-int $length
     *
     * @throws \Exception
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = \strlen($string)) < $length) {
            $size = $length - $len;

            /** @var positive-int $bytesSize */
            $bytesSize = (int) ceil($size / 3) * 3;

            $bytes = random_bytes($bytesSize);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
