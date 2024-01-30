<?php

namespace Shopwwi\Admin\Libraries;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ArrayCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if($value != null){ //保留数字属性
            return \json_decode('['.str_replace('_',',',trim($value,'_')).']') ;
        }
        return [];
        // TODO: Implement get() method.
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $list = is_array($value) ? $value : (is_string($value) ? explode(',', $value) : []);
        return count($list) > 0 ? '_'.implode('_',$list).'_':null;
    }
}