<?php

namespace ksmz\nana;

use ksmz\nana\Exceptions\ClientAlreadyRegisteredException;

/** @mixin Fetch */
class Sink
{
    /**
     * @var array
     */
    public static $faucets = [];

    /**
     * @param string                         $name
     * @param \ksmz\nana\Fetch|\Closure|null $fetch
     *
     * @throws \ksmz\nana\Exceptions\ClientAlreadyRegisteredException
     */
    public static function registerFaucet(string $name = 'default', $fetch = null)
    {
        if (array_key_exists($name, static::$faucets)) {
            throw new ClientAlreadyRegisteredException("'{$name}' is already registered with the sink.");
        }

        if ($fetch instanceof Fetch) {
            static::$faucets[$name] = $fetch;
        } elseif ($fetch instanceof \Closure) {
            static::$faucets[$name] = $fetch();
        } else {
            static::$faucets[$name] = new Fetch();
        }
    }

    /**
     * @param $faucet
     * @return \ksmz\nana\Fetch
     */
    public static function faucet($faucet = 'default')
    {
        return static::$faucets[$faucet];
    }

    /**
     * @param $name
     * @param $arguments
     * @return \ksmz\nana\Fetch|mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::faucet('default')->{$name}(...$arguments);
    }
}
