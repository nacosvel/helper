<?php

use Nacosvel\Utils\Concerns\HigherOrderTapProxy;

if (!function_exists('tap')) {
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @template TValue
     *
     * @param TValue                         $value
     * @param (callable(TValue): mixed)|null $callback
     *
     * @return ($callback is null ? HigherOrderTapProxy : TValue)
     */
    function tap($value, callable $callback = null)
    {
        if (is_null($callback)) {
            return new HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }
}

if (!function_exists('with')) {
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @template TValue
     * @template TReturn
     *
     * @param TValue                             $value
     * @param (callable(TValue): (TReturn))|null $callback
     *
     * @return ($callback is null ? TValue : TReturn)
     */
    function with($value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}
