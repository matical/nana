<?php

namespace ksmz\nana;

use ksmz\nana\Exceptions\NonExistentClientException;
use ksmz\nana\Exceptions\ClientAlreadyRegisteredException;

/** @mixin Fetch */
class Sink
{
    /**
     * Active instances of Fetch.
     *
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
     * Register a new faucet into the sink.
     *
     * @param string      $name
     * @param array|Fetch $client
     *
     * @throws \ksmz\nana\Exceptions\ClientAlreadyRegisteredException
     */
    public static function register(string $name, $client): void
    {
        if (\array_key_exists($name, static::$faucets) || \array_key_exists($name, static::$configs)) {
            throw new ClientAlreadyRegisteredException("[{$name}] is already exists in the sink.");
        }

        if ($client instanceof Fetch) {
            static::$faucets[$name] = $client;
        }

        static::$configs[$name] = $client;
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

        // Store in local cache once resolved.
        return static::$faucets[$name] = static::fetch($name);
    }

    /**
     * Attempt to get an existing instance from the local cache.
     *
     * @param string $name
     * @return \ksmz\nana\Fetch
     *
     * @throws \ksmz\nana\Exceptions\NonExistentClientException
     */
    public static function fetch($name): Fetch
    {
        return static::$faucets[$name] ?? static::resolve($name);
    }

    /**
     * Create a new instance based on the registered config.
     *
     * @param string $name
     * @return \ksmz\nana\Fetch
     *
     * @throws \ksmz\nana\Exceptions\NonExistentClientException
     */
    protected static function resolve($name): Fetch
    {
        // Since there is no existing instance available, we will check if a config waiting for registration
        if (! \array_key_exists($name, static::$configs)) {
            throw new NonExistentClientException("[$name] has yet to be registered.");
        }

        return new Fetch(static::$configs[$name]);
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
