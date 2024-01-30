<?php
/**
 *-------------------------------------------------------------------------s*
 *
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\Admin\App\Admin\Service;

class GenTablePreviewService
{
    /**
     * 创建控制器
     * @param  $table
     * @return array|false|string|string[]
     * @throws \Exception
     */
    public static function createController($table){

        //模块没有默认为Admin
        try{
            //小驼峰
            $lang = GentableService::getLangName($table->name);
            //存放地址
            $columns = $table->columns;
            //获取模板文件
            $controller_stub = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleController.stub');
            //数据库字段
            $fields = '';

            foreach ($columns as $column){
                if(in_array($column->name,['created_at','updated_at','deleted_at','sort','updated_user_id','created_user_id','id'])){
                    switch ($column->name){
                        case 'updated_user_id':
                        case 'created_user_id':
                        $fields .= "          shopwwiAmisFields(trans('field.$column->name',[],'messages'),'$column->name')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->showFilter(),".PHP_EOL;
                            break;
                        case 'created_at':
                            $fields .= "          shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),".PHP_EOL;
                            break;
                        case 'updated_at':
                            $fields .= "          shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),".PHP_EOL;
                            break;
                        case 'deleted_at':
                            $fields .= "          shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),".PHP_EOL;
                            break;
                        case 'id':
                            $fields .= "          shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),".PHP_EOL;
                            break;
                        default:
                            $fields .= "          shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),".PHP_EOL;
                            break;
                    }
                }else{
                    $fields.="          shopwwiAmisFields(trans('field.$column->name',[],'$lang'),'$column->name')";
                    if(empty($column->is_list)){
                        $fields.= "->showOnIndex(0)";
                    }
                    if(empty($column->is_show)){
                        $fields.= "->showOnDetail(0)";
                    }
                    if(empty($column->is_insert)){
                        $fields.= "->showOnCreation(0)";
                    }
                    if(empty($column->is_edit)){
                        $fields.= "->showOnUpdate(0)";
                    }
                    if(!empty($column->is_required)){
                        $fields.= "->rules('required')";
                    }
                    $fields.= ",".PHP_EOL;
                }

            }

            $controllerName = explode("\\",$table->business_name);
            $controller = str_replace(
                [
                    '{namespace}',
                    '{title}',
                    '{controller}',
                    '{model}',
                    '{fields}',
                    '{author}',
                    '{lang}'
                ],
                [
                    $table->business_name,
                    $table->comment,
                    $table->class_name.'Controller',
                    $table->module_name.'\\'.$table->class_name,
                    $fields,
                    $table->function_author,
                    $lang
                ],
                $controller_stub
            );
            return $controller;
        }catch (\Exception $exception){
            throw $exception;
        }
    }

    /**
     * 创建语言包
     * @param  $table
     * @return array|false|string|string[]
     */
    public static function createLang($table)
    {
        $table_comment = $table->comment;
        $columns = $table->columns;

        //字段以及注释
        $fields = '';
        foreach ($columns as $column){
            if(!in_array($column->name,['created_at','updated_at','deleted_at','created_user_id','updated_user_id'])) {
                $fields.="      '$column->name' => '$column->comment',".PHP_EOL;
            }
        }
        $lang_stub = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleLang.stub');

        $lang = str_replace(
            [
                '{comment}',
                '{field}',
            ],
            [
                $table_comment,
                $fields
            ],
            $lang_stub
        );

        return $lang;
    }

    /**
     * 创建模型
     * @param $table
     * @return array|false|string|string[]
     */
    public static function createModel($table)
    {
        $model = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleModel.stub');
        $tips = ''.PHP_EOL;
        $columns = $table->columns;
        foreach ($columns as $column){
            $type = 'string';
            if(in_array($column->column_type,['bigint','int','char'])){
                $type = 'integer';
            }
            $tips.= ' * @property '. $type .' $'.$column->name.' '.$column->comment.PHP_EOL;
        }
        $tips.= ' */';
        $model = str_replace(
            [
                '{namespace}',
                '{tips}',
                '{title}',
                '{model}',
                '{table}',
                '{author}'
            ],
            [
                $table->module_name,
                $tips,
                $table->comment,
                $table->class_name,
                $table->name,
                $table->function_author
            ],
            $model
        );

        return $model;
    }

    /**
     * 创建前端语言包
     * @param $table
     * @return array|false|string|string[]
     */
    public static function createVueLang($table)
    {
        //获取字段及注释
        $columns = $table->columns;

        //字段以及注释
        $fields = '';
        foreach ($columns as $column){
            if(!in_array($column->name,['created_at','updated_at','deleted_at','created_user_id','updated_user_id'])) {
                $fields .= '      "' . $column->name . '" : "' . $column->comment . '",' . PHP_EOL;
            }
        }
        //小驼峰
        $lang_name = GenTableService::getLangName($table->name);

        $lang = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleVueLang.stub');

        $lang = str_replace(
            [
                '{table}',
                '{column}',
                '{comment}'
            ],
            [
                $lang_name,
                $fields,
                $table->comment,
            ],
            $lang
        );
        return $lang;
    }

    /**
     * 创建前端api接口
     * @param $table
     * @return array|false|string|string[]
     */
    public static function createVueRoute($table)
    {
        //驼峰
        $model = GenTableService::getModelName($table->gen_path,'/');

        //字段以及注释
        $routePath = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleVueRoute.stub');

        $route = str_replace(
            [
                '{comment}',
                '{model}',
                '{url}',
            ],
            [
                $table->comment,
                $model,
                $table->gen_path,
            ],
            $routePath
        );
        return $route;
    }

    /**
     * 创建弹框
     * @param $table
     * @return array|false|string|string[]
     */
    public static function createVueModule($table)
    {
        //驼峰
        $model = GenTableService::getModelName($table->name);
        $apiPathName = GenTableService::getModelName($table->gen_path,'/');

        //小驼峰
        $lang_name = GenTableService::getLangName($table->name);

        //字段以及注释
        $columns = $table->columns;
        $fields = self::getColumnInputType($columns,$lang_name);

        $path = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleVueModel.stub');
        return str_replace(
            [
                '{column}',
                '{model}',
                '{path}',
                '{module}',
                '{lang}'
            ],
            [
                $fields,
                $model,
                $table->gen_path,
                $apiPathName,
                $lang_name
            ],
            $path
        );
    }

    public static function createVueList($table)
    {
        //驼峰
        $model = GenTableService::getModelName($table->name);
        $apiPathName = GenTableService::getModelName($table->gen_path,'/');

        //小驼峰
        $lang_name = GenTableService::getLangName($table->name);

        //字段以及注释
        $columns = $table->columns;
        $search = self::getColumnInputType($columns,$lang_name,2);
        $fields = '';
        foreach ($columns as $column){
            if(empty($column->is_list)) continue;
            $langName = "t('$lang_name.column.$column->name')";
            if(in_array($column->name,['created_at','updated_at','deleted_at','created_user_id','updated_user_id','sort','id'])){
                $langName = "t('column.$column->name')";
            }
            $render = $column->dict_type ? ",render (row) {return h(NSpace, {},()=> [ h(NBadge,{dot:true,value:1,type:row.$column->name === '1'? 'success':'error'}),h('span', {},dictKey('$column->dict_type',row.$column->name)?.label)])}":"";
            $fields.="{ title: $langName, key: '$column->name', sorter: true, width: 100 $render},".PHP_EOL;
        }
        $path = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleView.stub');
        return str_replace(
            [
                '{comment}',
                '{search}',
                '{field}',
                '{model}',
                '{path}',
                '{lang}'
            ],
            [
                $table->comment,
                $search,
                $fields,
                $apiPathName,
                $table->gen_path,
                $lang_name
            ],
            $path
        );
    }

    public static function createVueRecoveryList($table)
    {
        //驼峰
        $model = GenTableService::getModelName($table->name);
        $apiPathName = GenTableService::getModelName($table->gen_path,'/');

        //小驼峰
        $lang_name = GenTableService::getLangName($table->name);

        //字段以及注释
        $columns = $table->columns;
        $search = self::getColumnInputType($columns,$lang_name,2);
        $fields = '';
        foreach ($columns as $column){
            if(empty($column->is_list)) continue;
            $langName = "t('$lang_name.column.$column->name')";
            if(in_array($column->name,['created_at','updated_at','deleted_at','created_user_id','updated_user_id','sort','id'])){
                $langName = "t('column.$column->name')";
            }
            $render = $column->dict_type ? ",render (row) {return h(NSpace, {},()=> [ h(NBadge,{dot:true,value:1,type:row.$column->name === '1'? 'success':'error'}),h('span', {},dictKey('$column->dict_type',row.$column->name)?.label)])}":"";
            $fields.="{ title: $langName, key: '$column->name', sorter: true, width: 100 $render},".PHP_EOL;
        }
        $path = file_get_contents(dirname(__DIR__) . '/Stubs/ExampleViewRecovery.stub');
        return str_replace(
            [
                '{comment}',
                '{search}',
                '{field}',
                '{model}',
                '{path}',
                '{lang}'
            ],
            [
                $table->comment,
                $search,
                $fields,
                $apiPathName,
                $table->gen_path,
                $lang_name
            ],
            $path
        );
    }

    /**
     * 返回
     * @param $columns
     * @param $lang_name
     * @param int $type 1为新增编辑 2为查询
     * @return string
     */
    public static function getColumnInputType($columns, $lang_name, int $type = 1)
    {
        $fields = '';
        foreach ($columns as $column){
            if($type == 1){
                if(empty($column->is_edit) || empty($column->is_insert)) continue;
            }else{
                if(empty($column->is_query)) continue;
            }

            $required = !empty($column->is_required)?'true':'false';
            $number = $column->column_type == 'int' || $column->column_type == 'bigint' ? "type:'number',":'';
            $fields.="  {".PHP_EOL;
            $columnName = $column->name;
            if($type != 1) {
                if ($column->query_type == 'BETWEEN') {
                    $columnName = $column->name . "_between";
                }
                if ($column->query_type == 'LIKE') {
                    $columnName = $column->name . "_like";
                }
            }
            $langName = "t('$lang_name.column.$column->name')";
            if(in_array($column->name,['created_at','updated_at','deleted_at','created_user_id','updated_user_id','sort','id'])){
                $langName = "t('column.$column->name')";
            }
            $fields.="      field: '$columnName',".PHP_EOL;
            switch ($column->html_type){
                case 'textarea':
                    $fields.="      component: 'NInput',".PHP_EOL;
                    $fields.="      label: $langName,".PHP_EOL;
                    if($required == 'true' && $type == 1) $fields.="      labelMessage: t('explain.input'),".PHP_EOL;
                    if($type == 1) $fields.="      defaultValue: form.value.$column->name,".PHP_EOL;
                    if($type == 1) $fields.="      giProps: {span: '2 s:1'},".PHP_EOL;
                    $fields.="      componentProps: {".PHP_EOL;
                    $fields.="          type:'textarea',".PHP_EOL;
                    $fields.="          placeholder: t('placeholder.input', {name: $langName}),".PHP_EOL;
                    $fields.="      },".PHP_EOL;
                    if($type == 1) $fields.="      rules: [{required: $required,$number message: t('rules.required', {name: $langName}), trigger: ['blur']}]".PHP_EOL;
                    break;
                case 'select':
                    $options = $column->dict_type ? "dictKeys('$column->dict_type')":"[{label:'',value:''}]";
                    $fields.="      component: 'NSelect',".PHP_EOL;
                    $fields.="      label: $langName,".PHP_EOL;
                    if($required == 'true' && $type == 1) $fields.="      labelMessage: t('explain.select'),".PHP_EOL;
                    if($type == 1) $fields.="      defaultValue: form.value.$column->name,".PHP_EOL;
                    if($type == 1) $fields.="      giProps: {span: '2 s:1'},".PHP_EOL;
                    $fields.="      componentProps: {".PHP_EOL;
                    $fields.="          options: $options,".PHP_EOL;
                    $fields.="          placeholder: t('placeholder.select', {name: $langName}),".PHP_EOL;
                    $fields.="      },".PHP_EOL;
                    if($type == 1) $fields.="      rules: [{required: $required,$number message: t('rules.required', {name: $langName}), trigger: ['blur']}]".PHP_EOL;
                    break;
                case 'radio':
                    $component = $type == 1?'NRadioGroup':'NSelect';
                    $options = $column->dict_type ? "dictKeys('$column->dict_type')":"[{label:'',value:''}]";
                    $fields.="      component: '$component',".PHP_EOL;
                    $fields.="      label: $langName,".PHP_EOL;
                    if($required == 'true' && $type == 1) $fields.="      labelMessage: t('explain.select'),".PHP_EOL;
                    if($type == 1) $fields.="      defaultValue: form.value.$column->name,".PHP_EOL;
                    if($type == 1) $fields.="      giProps: {span: '2 s:1'},".PHP_EOL;
                    $fields.="      componentProps: {".PHP_EOL;
                    $fields.="          options: $options,".PHP_EOL;
                    $fields.="          placeholder: t('placeholder.select', {name: $langName}),".PHP_EOL;
                    $fields.="      },".PHP_EOL;
                    if($type == 1) $fields.="      rules: [{required: $required,$number message: t('rules.required', {name: $langName}), trigger: ['blur']}]".PHP_EOL;
                    break;
                case 'checkbox':
                    $options = $column->dict_type ? "dictKeys('$column->dict_type')":"[{label:'',value:''}]";
                    $component = $type == 1?'NCheckboxGroup':'NSelect';
                    $fields.="      component: '$component',".PHP_EOL;
                    $fields.="      label: $langName,".PHP_EOL;
                    if($required == 'true' && $type == 1) $fields.="      labelMessage: t('explain.select'),".PHP_EOL;
                    if($type == 1) $fields.="      defaultValue: form.value.$column->name,".PHP_EOL;
                    if($type == 1) $fields.="      giProps: {span: '2 s:1'},".PHP_EOL;
                    $fields.="      componentProps: {".PHP_EOL;
                    $fields.="          options: $options,".PHP_EOL;
                    $fields.="          placeholder: t('placeholder.select', {name: $langName}),".PHP_EOL;
                    $fields.="      },".PHP_EOL;
                    if($type == 1) $fields.="      rules: [{required: $required,$number message: t('rules.required', {name: $langName}), trigger: ['blur']}]".PHP_EOL;
                    break;
                case 'date':
                    $fields.="      component: 'NDatePicker',".PHP_EOL;
                    $fields.="      label: $langName,".PHP_EOL;
                    if($required == 'true' && $type == 1) $fields.="      labelMessage: t('explain.select'),".PHP_EOL;
                    if($type == 1) $fields.="      defaultValue: form.value.$column->name,".PHP_EOL;
                    if($type == 1) $fields.="      giProps: {span: '2 s:1'},".PHP_EOL;
                    $fields.="      componentProps: {".PHP_EOL;
                    $fields.="          type: 'datetimerange',".PHP_EOL;
                    $fields.="          clearable: true,".PHP_EOL;
                    $fields.="          valueFormat: 'yyyy-MM-dd HH:mm:ss'".PHP_EOL;
                    $fields.="      },".PHP_EOL;
                    if($type == 1) $fields.="      rules: [{required: $required,$number message: t('rules.required', {name: $langName}), trigger: ['blur']}]".PHP_EOL;
                    break;
                default:
                    $fields.="      component: 'NInput',".PHP_EOL;
                    $fields.="      label: $langName,".PHP_EOL;
                    if($required == 'true' && $type == 1) $fields.="      labelMessage: t('explain.input'),".PHP_EOL;
                    if($type == 1) $fields.="      defaultValue: form.value.$column->name,".PHP_EOL;
                    if($type == 1) $fields.="      giProps: {span: '2 s:1'},".PHP_EOL;
                    $fields.="      componentProps: {".PHP_EOL;
                    $fields.="          placeholder: t('placeholder.input', {name: $langName})".PHP_EOL;
                    $fields.="      },".PHP_EOL;
                    if($type == 1) $fields.="      rules: [{required: $required,$number message: t('rules.required', {name: $langName}), trigger: ['blur']}]".PHP_EOL;
                    break;
            }
            $fields.="  },".PHP_EOL;
        }
        return $fields;
    }
}