<?php

namespace Shopwwi\Admin\Libraries;

use Shopwwi\Admin\App\Admin\Service\SysConfigService;

class ShopwwiConfig
{
    protected static $config = [];

    public function __construct()
    {
      //  if(!static::$config){
            static::$config = SysConfigService::getList(true);
      //  }
    }

    public function get(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::$config;
        }
        $keyArray = explode('.', $key);
        $value = static::$config;
        $found = true;
        foreach ($keyArray as $index) {
            if (!isset($value[$index])) {
                $found = false;
                break;
            }
            $value = $value[$index];
        }
        if ($found) {
            return $value;
        }
        return $default;
    }
}