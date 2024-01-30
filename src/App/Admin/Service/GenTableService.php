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



use Shopwwi\Admin\App\Admin\Models\GenTable;
use Shopwwi\Admin\App\Admin\Models\InFomaTionSchema;
use Shopwwi\Admin\App\Admin\Models\InFomaTionSchemaColumn;

class GenTableService
{
    /**
     * 获取数据库表
     * @param $pageSize
     * @param $request
     * @return mixed
     */
    public static function getTables($pageSize,$request)
    {
        $database = config("database.connections.".config('database.default').".database");
        $exists = GenTable::get();
        $exists = array_column($exists->toarray(),'name');
        return InFomaTionSchema::select(
            'TABLE_NAME as tableName',
            'TABLE_COMMENT as tableComment',
            'CREATE_TIME as createTime',
            'UPDATE_TIME as updateTime'
        )->where(function ($q)use ($database,$request,$exists){
            $q->where('table_schema',"$database");
            if (!empty($request->input('tableName'))){
                $q->where('TABLE_NAME','like','%'.$request->input('tableName').'%');
            }

            if (!empty($request->input('tableComment'))){
                $q->where('TABLE_COMMENT','like','%'.$request->input('tableComment').'%');
            }
            $q->whereNotIn('TABLE_NAME',$exists);
        })->paginate($pageSize,'*','page',$request->input('page',1));
    }

    /**
     * 获得表
     * @param $table
     * @return mixed
     * @throws CreateException
     */
    public static function getTableInfo($table)
    {
        $database = config("database.connections.".config('database.default').".database");
        //获得表注释
        $table = InFomaTionSchema::select(
            'TABLE_NAME as tableName',
            'TABLE_COMMENT as tableComment',
            'CREATE_TIME as createTime',
            'UPDATE_TIME as updateTime'
        )->where(function ($q)use ($database,$table){
            $q->where('table_schema',"$database");
            $q->where('table_name',"$table");
        })->first();
        if (empty($table)){
            throw new \Exception('数据表不存在','400');
        }

        $table_comment = $table->tableComment;
        return $table_comment;
    }

    /**
     * 获得表字段
     * @param $table
     * @return array
     */
    public static function getColumns($table)
    {
        $database = config("database.connections.".config('database.default').".database");
        //获取所有字段
        $columns = InFomaTionSchemaColumn::where(function ($q)use ($table,$database){
            $q->where('table_name' ,"$table");
            $q->where('table_schema' ,"$database");
        })
            ->select(
                'COLUMN_NAME as columnName',
                'DATA_TYPE as dataType',
                'COLUMN_COMMENT as columnComment',
                'COLUMN_KEY as columnKey',
                'EXTRA as extra',
                'IS_NULLABLE as isNullable',
                'ORDINAL_POSITION as ordinalPosition',
            )
            ->get();
        if (empty($columns)){
            throw new \Exception('数据表不存在','400');
        }
        return $columns;
    }

    /**
     * 获得某表特定字段
     * @param $table
     * @param $column
     * @return array
     * @throws \Exception
     */
    public static function getColumnByName($table,$column)
    {
        $database = config("database.connections.".config('database.default').".database");
        //获取所有字段
        $column = InFomaTionSchemaColumn::where(function ($q)use ($table,$database,$column){
            $q->where('table_name' ,"$table");
            $q->where('table_schema' ,"$database");
            $q->where('column_name',$column);
        })->first(
            [
                'COLUMN_NAME as columnName',
                'DATA_TYPE as dataType',
                'COLUMN_COMMENT as columnComment',
            ]
        );
        if (empty($column)){
            throw new \Exception('数据表或字段不存在','400');
        }
        return $column;
    }
    /**
     * 驼峰命名
     * @param $table
     * @return string
     */
    public static function getModelName($table,$to = '_')
    {
        $model_name = '';
        $names = explode($to,$table);
        foreach ($names as $name){
            $model_name.=ucfirst($name);
        }
        return $model_name;
    }

    /**
     * 语言包小驼峰
     * @param $table
     * @return string
     */
    public static function getLangName($table)
    {
        $lang_name = '';
        $names = explode('_',$table);
        foreach ($names as $key=>$name){
            $lang_name.=$key==0?$name:ucfirst($name);
        }
        return $lang_name;
    }
}