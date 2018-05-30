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
     * @var string
     */
    protected static $defaultSink = 'default';

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
    public static function faucet(?string $name = null): Fetch
    {
        $name = $name ?? static::getDefaultSink();

        if (! \array_key_exists($name, static::$configs)) {
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
     * @param string $sink
     */
    public static function setDefaultSink(string $sink)
    {
        static::$defaultSink = $sink;
    }

    /**
     * @return string
     */
    public static function getDefaultSink()
    {
        return static::$defaultSink;
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
