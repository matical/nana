<?php

if (! function_exists('pat')) {
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param mixed         $value
     * @param callable|null $callback
     * @return mixed
     */
    function pat($value, $callback = null)
    {
        $callback($value);

        return $value;
    }
}
