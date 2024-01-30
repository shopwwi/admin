<?php

namespace Shopwwi\Admin\Libraries\Amis;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use Shopwwi\Admin\Amis\TableColumn;
use Shopwwi\Admin\Amis\TextControl;

class AmisFields
{
    public $name;
    public $attribute;
    public $value;
    public $creationRules = [];
    public $updateRules = [];
    public $rules = [];
    public $showOnIndex = 1; // 0为不显示不查询 1为显示并查询 2为不显示查询 3为显示不查询用于外加字段 4为回收站专用
    public $showOnDetail = 1; // 0为不显示不查询 1为显示并查询 2为不显示并查询 3为显示不查询用于外加字段
    public $showOnCreation = 1; // 0为不显示不查询 1为显示并查询 2为不显示并查询 3为显示不查询用于外加字段
    public $showOnUpdate = 1; // 0为不显示不查询 1为显示并查询 2为不显示并查询 3为显示不查询用于外加字段 4为显示不操作
    public $showFilter = 0; // 0为不筛选 1为显示

    public $tableColumn;
    public $createColumn;
    public $updateColumn;
    public $showColumn;
    public $filterColumn;


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
        $this->tableColumn = TableColumn::make()->label($this->name)->name($this->attribute);
        $this->createColumn = TextControl::make()->name($this->attribute)->label($this->name)->xs(12)->sm(6)->placeholder(trans('form.input',['attribute'=>$this->name],'messages'));
        $this->updateColumn = TextControl::make()->name($this->attribute)->label($this->name)->xs(12)->sm(6)->placeholder(trans('form.input',['attribute'=>$this->name],'messages'));
        $this->showColumn = TextControl::make()->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        $this->filterColumn = TextControl::make()->name($this->attribute.'_like')->label($this->name)->placeholder(trans('form.input',['attribute'=>$this->name],'messages'));
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
     * 是否显示在table中
     * @param bool $open
     * @return $this
     */
    public function showOnIndex($open = 1)
    {
        $this->showOnIndex = $open;
        return $this;
    }

    /**
     * 是否在详情页显示
     * @param bool $open
     * @return $this
     */
    public function showOnDetail($open = 1)
    {
        $this->showOnDetail = $open;
        return $this;
    }


    /**
     * 是否在编辑显示
     * @param bool $open
     * @return $this
     */
    public function showOnUpdate($open = 1)
    {
        $this->showOnUpdate = $open;
        return $this;
    }

    /**
     * 是否在新增显示
     * @param bool $open
     * @return $this
     */
    public function showOnCreation($open = 1)
    {
        $this->showOnCreation = $open;
        return $this;
    }

    /**
     * 是否筛选
     * @param $open
     * @return $this
     */
    public function showFilter($open = 1)
    {
        $this->showFilter = $open;
        $this->tableColumn->searchable($this->filterColumn);
        return $this;
    }

    public function value($null = null)
    {
        $this->value = $null;
        return $this;
    }

    public function tableColumn($map = [])
    {
        foreach ($map as $key=>$val){
            $this->tableColumn->$key($val);
        }
        if(isset($map['column'])) $this->tableColumn = $map['column'];
        return $this;
    }

    public function column($type = 'input-text',$map = [])
    {
        $this->createColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        $this->updateColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        $this->showColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        $this->createColumn = $this->setLanguage($this->createColumn,$type);
        $this->updateColumn = $this->setLanguage($this->updateColumn,$type);
        foreach ($map as $key=>$val){
            $this->createColumn->$key($val);
            $this->updateColumn->$key($val);
            $this->showColumn->$key($val);
        }
        return $this;
    }

    /**
     * 新增字段
     * @param $type
     * @param $map
     * @return $this
     */
    public function createColumn($type = 'input-text',$map = [])
    {
        $this->createColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        $this->createColumn = $this->setLanguage($this->createColumn,$type);
        foreach ($map as $key=>$val){
            $this->createColumn->$key($val);
        }
        return $this;
    }

    /**
     * 修改字段
     * @param $type
     * @param $map
     * @return $this
     */
    public function updateColumn($type = 'input-text',$map = [])
    {
        $this->updateColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        $this->updateColumn = $this->setLanguage($this->updateColumn,$type);
        foreach ($map as $key=>$val){
            $this->updateColumn->$key($val);
        }
        return $this;
    }

    /**
     * 展示字段
     * @param $type
     * @param $map
     * @return $this
     */
    public function showColumn($type = 'input-text',$map = [])
    {
        $this->showColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name)->xs(12)->sm(6);
        foreach ($map as $key=>$val){
            $this->showColumn->$key($val);
        }
        return $this;
    }

    /**
     * 筛选展示
     * @param $type
     * @param $map
     * @return $this
     */
    public function filterColumn($type = 'input-text',$map = [])
    {
        $this->filterColumn = shopwwiAmis($type)->name($this->attribute)->label($this->name);
        $this->filterColumn = $this->setLanguage($this->filterColumn,$type);
        foreach ($map as $key=>$val){
            $this->filterColumn->$key($val);
        }
        $this->tableColumn->searchable($this->filterColumn);
        return $this;
    }

    protected function setLanguage($object,$type){
        if(in_array($type,['input-text','textarea','input-number'])){
            $object->placeholder(trans('form.input',['attribute'=>$this->name],'messages'));
        }
        if(in_array($type,['select','radio'])){
            $object->placeholder(trans('form.select',['attribute'=>$this->name],'messages'));
        }
        return $object;
    }
}