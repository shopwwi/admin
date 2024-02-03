<?php

namespace Shopwwi\Admin\Libraries;

class Storage
{

    protected static $_instance = null;


    public static function instance()
    {
        if (!static::$_instance) {
            static::$_instance = new \Shopwwi\WebmanFilesystem\Storage(shopwwiConfig('filesystem'));
        }
        return static::$_instance;
    }
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}