<?php

namespace Shopwwi\Admin\Libraries;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ArrayDotCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if($value != null){
            return explode(',,,',trim($value,',,,'));
        }
        return [];
        // TODO: Implement get() method.
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $list = is_array($value) ? $value : (is_string($value) ? explode(',', $value) : []);
        return count($list) > 0 ? ',,,'.implode(',,,',$list).',,,':null;
        // TODO: Implement set() method.
    }
}