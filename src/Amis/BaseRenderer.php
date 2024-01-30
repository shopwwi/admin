<?php

namespace Shopwwi\Admin\Amis;

class BaseRenderer
{
    public string $type;

    public static function make()
    {
        return new static();
    }

    public function __call($name, $arguments)
    {
        $this->$name = array_shift($arguments);

        return $this;
    }

    public function set($name, $value)
    {
        $this->$name = $value;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function toJson()
    {
        return json_encode($this);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
