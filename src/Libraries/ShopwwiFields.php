<?php

namespace Shopwwi\Admin\Libraries;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ShopwwiFields
{
    public $name;
    public $attribute;
    public $value;
    public $creationRules = [];
    public $updateRules = [];
    public $rules = [];
    public $showOnIndex = true;
    public $showOnDetail = true;
    public $showOnCreation = true;
    public $showOnUpdate = true;


    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @return void
     */
    public function __construct($name, $attribute = null)
    {
        $this->name = $name;
        $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($name));
    }

    /**
     * Set the validation rules for the field.
     *
     * @param  callable|array|string  $rules
     * @return $this
     */
    public function rules($rules)
    {
        $this->rules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  callable|array|string  $rules
     * @return $this
     */
    public function creationRules($rules)
    {
        $this->creationRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  callable|array|string  $rules
     * @return $this
     */
    public function updateRules($rules)
    {
        $this->updateRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;
        return $this;
    }

    /**
     * @param bool $open
     * @return $this
     */
    public function showOnIndex($open = true)
    {
        $this->showOnIndex = $open;
        return $this;
    }

    /**
     * 是否在详情页显示
     * @param bool $open
     * @return $this
     */
    public function showOnDetail($open = true)
    {
        $this->showOnDetail = $open;
        return $this;
    }

    /**
     * 是否在编辑显示
     * @param bool $open
     * @return $this
     */
    public function showOnUpdate($open = true)
    {
        $this->showOnUpdate = $open;
        return $this;
    }

    /**
     * 是否在新增显示
     * @param bool $open
     * @return $this
     */
    public function showOnCreation($open = true)
    {
        $this->showOnCreation = $open;
        return $this;
    }

    public function value($null = null)
    {
        $this->value = $null;
        return $this;
    }
}