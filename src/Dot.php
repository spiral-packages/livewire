<?php

/**
 * This code is partially extracted from package illuminate/collections.
 *
 * @see https://github.com/illuminate/collections for the canonical source repository.
 *
 * @license https://github.com/illuminate/collections/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Spiral\Livewire;

final class Dot
{
    public static function set(
        mixed &$target,
        array|string $key,
        mixed $value,
        bool $overwrite = true
    ): object|array {
        $segments = \is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if ((\is_array($target) || $target instanceof \ArrayAccess) === false) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    self::set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (\is_array($target) || $target instanceof \ArrayAccess) {
            if ($segments) {
                if (!self::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                self::set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !self::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (\is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                self::set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                self::set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }

    public static function get(object|array $target, null|string|array $key, mixed $default = null): mixed
    {
        if (null === $key) {
            return $target;
        }

        $key = \is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (null === $segment) {
                return $target;
            }

            if ('*' === $segment) {
                if (!is_iterable($target)) {
                    return $default instanceof \Closure ? $default() : $default;
                }

                $result = [];
                foreach ($target as $item) {
                    $result[] = self::get($item, $key);
                }

                return \in_array('*', $key, true) ? self::collapse($result) : $result;
            }

            if ((\is_array($target) || $target instanceof \ArrayAccess) && self::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (\is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return $default instanceof \Closure ? $default() : $default;
            }
        }

        return $target;
    }

    /**
     * @internal
     */
    private static function exists(array|object $array, string|float $key): bool
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        if (\is_float($key)) {
            $key = (string) $key;
        }

        return \array_key_exists($key, $array);
    }

    /**
     * @internal
     */
    private static function collapse(array $array): array
    {
        $results = [];
        foreach ($array as $values) {
            if (!\is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }
}
