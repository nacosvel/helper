<?php

namespace Nacosvel\Helper\Concerns;

trait ArrayTrait
{
    /**
     * The tests whether at least one element in the array passes the test implemented by the provided function.
     *
     * @param array    $array
     * @param callable $callback
     *
     * @return bool
     */
    public static function some(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * The method tests whether all elements in the array pass the test implemented by the provided function.
     *
     * @param array    $array
     * @param callable $callback
     *
     * @return bool
     */
    public static function every(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively replicate an iterable structure, optionally initializing each level with a callback.
     *
     * This method traverses the input iterable (array or object implementing Traversable) and recursively
     * replicates its structure. For nested iterables, the same replication logic is applied.
     *
     * @param iterable      $data   The input iterable to replicate.
     * @param callable|null $target Optional callback to initialize the resulting structure at each level.
     *                              Should return an empty array, object, or other container suitable for the current level.
     *                              If not provided, a default empty array is used.
     *
     * @return iterable The replicated iterable structure, preserving keys and nesting of the original data.
     *
     * @example
     * ```
     * $data = [
     *     'a' => [1, 2],
     *     'b' => [3, 4],
     * ];
     *
     * $replicatedWithCallback = replicate($data, fn() => new ArrayObject());
     * // $replicatedWithCallback behaves the same, but allows customizing the container type for each level
     * ```
     */
    public static function replicate(iterable $data, callable $target = null): iterable
    {
        $result = is_callable($target) ? call_user_func($target) : [];

        foreach ($data as $key => $value) {
            if (is_iterable($value)) {
                $result[$key] = self::replicate($value, $target);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Transform an array using a callback function, similar to a combination of array_map and array_filter.
     *
     * This method allows you to modify the values, keys, or both, and optionally skip elements by returning false.
     *
     * @param array    $array    The input array to transform.
     * @param callable $callback The callback function applied to each element. The callback signature depends on the $mode:
     *                           - default (0): function($value): mixed  — transforms the value.
     *                           - ARRAY_FILTER_USE_KEY (2): function($key): mixed — transforms the key.
     *                           - ARRAY_FILTER_USE_BOTH (1): function($value, $key): array|false — returns an associative array of key => value pairs to merge, or false to skip.
     * @param int      $mode     The transformation mode (default 0):
     *                           - 0: transform values only (default behavior)
     *                           - ARRAY_FILTER_USE_BOTH: transform both key and value
     *                           - ARRAY_FILTER_USE_KEY: transform keys only
     *
     * @return array The transformed array after applying the callback.
     *
     * @example
     * ```
     * $data = ['a' => 1, 'b' => 2];
     * $result = transform($data, fn($v) => $v * 2);
     * // $result = ['a' => 2, 'b' => 4]
     *
     * $result = transform($data, fn($k) => strtoupper($k), ARRAY_FILTER_USE_KEY);
     * // $result = ['A' => 1, 'B' => 2]
     *
     * $result = transform($data, fn($v, $k) => [$k => $v * 10], ARRAY_FILTER_USE_BOTH);
     * // $result = ['a' => 10, 'b' => 20]
     * ```
     */
    public static function transform(array $array, callable $callback, int $mode = 0): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = match ($mode) {
                ARRAY_FILTER_USE_KEY => $key = $callback($key),
                ARRAY_FILTER_USE_BOTH => $callback($value, $key),
                default => $value = $callback($value),
            };

            if ($assoc === false) {
                continue;
            }

            if ($mode === ARRAY_FILTER_USE_BOTH) {
                foreach ($assoc as $mapKey => $mapValue) {
                    $result[$mapKey] = $mapValue;
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Recursively flatten an iterable into a single-level array with customizable key paths.
     *
     * Supports arrays and Traversable objects. Nested keys are concatenated with a separator
     * to indicate their original path. Recursion depth can be limited.
     *
     * @param iterable $iterable     The input array or Traversable object to flatten.
     * @param int      $depth        Maximum depth to flatten. Defaults to PHP_INT_MAX.
     * @param string   $separator    Separator used to join nested keys. Defaults to '.'.
     * @param bool     $preserveKeys Whether to preserve original keys at each level. Default true.
     *
     * @return array The flattened array with concatenated keys.
     *
     * @example
     * ```
     * $data = [
     *     'a' => [1, 2],
     *     'b' => ['x' => 3, 'y' => ['z' => 4]],
     * ];
     *
     * $flattened = flatten($data);
     * // [
     * //     'a.0' => 1,
     * //     'a.1' => 2,
     * //     'b.x' => 3,
     * //     'b.y.z' => 4
     * // ]
     *
     * $flattenedCustom = flatten($data, 2, '->', false);
     * // [
     * //     'a->0' => 1,
     * //     'a->1' => 2,
     * //     'b->x' => 3,
     * //     'b->y' => ['z' => 4]
     * // ]
     * ```
     */
    public static function flatten(iterable $iterable, int $depth = PHP_INT_MAX, string $separator = '.', bool $preserveKeys = true, string $prefix = null): array
    {
        $result = [];

        foreach ($iterable as $key => $value) {
            $currentKey = $preserveKeys ? (string)$key : count($result);
            $fullKey    = is_null($prefix) ? $currentKey : $prefix . $separator . $currentKey;

            if (is_iterable($value) && $depth > 1) {
                $result += static::flatten($value, $depth - 1, $separator, $preserveKeys, $fullKey);
            } else {
                $result[$fullKey] = $value;
            }
        }

        return $result;
    }
}
