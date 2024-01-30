<?php

namespace Shopwwi\Admin\Libraries;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class NumberCast implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        return $value != null ? (double) $value : $value;
        // TODO: Implement get() method.
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
        // TODO: Implement set() method.
    }
}