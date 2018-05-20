<?php

namespace ksmz\nana;

/** @mixin Fetch */
class Sink
{
    public static $faucets = [];

    public static function registerClient($name, $fetch = null)
    {
        if ($fetch instanceof Fetch) {
            static::$faucets[$name] = $fetch;
        } elseif ($fetch instanceof \Closure) {
            static::$faucets[$name] = $fetch();
        } else {
            static::$faucets[$name] = new Fetch();
        }
    }

    public static function client($faucet)
    {
        return static::$faucets[$faucet];
    }

    public static function __callStatic($name, $arguments)
    {
        return static::client('default')->{$name}(...$arguments);
    }
}
