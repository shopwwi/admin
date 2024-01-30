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
namespace Shopwwi\Admin\App\Admin\Traits;

use Shopwwi\Admin\App\Admin\Models\SysDictData;

trait SysDictTypeTraits
{

    public static function bootSysDictTypeTraits()
    {
        static::creating(function ($dictType){
            if(!empty($dictType->type)){
                $has = static::where('type',$dictType->type)->first();
                if($has){
                    throw new \Exception('类型已存在');
                }
            }
        });


        static::updating(function ($dictType){
            // getOriginal(); getOriginal('name'); // 获得原数据 isDirty();isDirty('name); 是否更改 getChanges 发生了变化得属性
            $changes = $dictType->getChanges();
            if(isset($changes->type)){
                if($dictType->allow_delete == '0'){
                    $dictType->type = $dictType->getOriginal('type');
                }else{
                    $has = static::where('type',$dictType->type)->first();
                    if($has){
                        throw new \Exception('类型已存在');
                    }
                    $type = self::findOrFail($dictType->id);
                    $data = SysDictData::where('type',$type->type)->get();
                    foreach ($data as $datum){
                        $datum->type = $dictType->type;
                        $datum->save();
                    }
                }
            }
            if($dictType->getOriginal('allow_delete') == 0){
                $dictType->allow_delete = 0;
            }
        });

        static::deleting(function ($dictType){
            if ($dictType->allow_delete=='0'){
                throw new \Exception('禁止删除');
            }
            $data = SysDictData::where('type',$dictType->type)->get();
            $ids = $data->pluck('id');
            if (!empty($ids)){
                SysDictData::destroy($ids);
            }
        });
    }
}
