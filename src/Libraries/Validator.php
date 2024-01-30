<?php

namespace Shopwwi\Admin\Libraries;

class Validator
{
    protected static $_instance = null;


    public static function instance()
    {
        if (!static::$_instance) {
            static::$_instance = new ValidatorFactory();
        }
        return static::$_instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return static::instance()->{$method}(... $arguments);
    }
}