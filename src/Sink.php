<?php

namespace ksmz\nana;

use ksmz\nana\Exceptions\NonExistentClientException;
use ksmz\nana\Exceptions\ClientAlreadyRegisteredException;

/** @mixin Fetch */
class Sink
{
    /**
     * @var array
     */
    public static $faucets = [];

    /**
     * @var array
     */
    public static $configs = [];

    /**
     * @param string $name
     * @param array  $config
     *
     * @throws \ksmz\nana\Exceptions\ClientAlreadyRegisteredException
     */
    public static function register(string $name, $config)
    {
        if (\array_key_exists($name, static::$faucets)) {
            throw new ClientAlreadyRegisteredException("[{$name}] is already exists in the sink.");
        }

        static::$configs[$name] = $config;
    }

    /**
     * @param string $name
     * @return \ksmz\nana\Fetch
     *
     * @throws \ksmz\nana\Exceptions\NonExistentClientException
     */
    public static function faucet(string $name = 'default'): Fetch
    {
        if (! \array_key_exists('default', static::$configs)) {
            throw new NonExistentClientException("[$name] has yet to be registered.");
        }

        return static::$faucets[$name] = static::resolve($name);
    }

    /**
     * @param string $name
     * @return \ksmz\nana\Fetch|mixed
     */
    public static function fetch($name)
    {
        return static::$faucets[$name] ?? static::resolve($name);
    }

    /**
     * @param string $name
     * @return \ksmz\nana\Fetch
     */
    protected static function resolve($name)
    {
        $config = static::$configs[$name];

        return new Fetch($config);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return \ksmz\nana\Fetch|mixed
     *
     * @throws \ksmz\nana\Exceptions\NonExistentClientException
     */
    public static function __callStatic($name, $arguments)
    {
        return static::faucet()->{$name}(...$arguments);
    }
}
