<?php

namespace ksmz\nana;

/** @mixin Fetch */
class Nana
{
    public static function __callStatic($name, $arguments)
    {
        return Fetch::new()->{$name}(...$arguments);
    }
}
