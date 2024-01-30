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


trait SysDictDataTraits
{

    public static function bootSysDictDataTraits()
    {
//        static::creating(function ($dictType){
//
//        });

        static::updating(function ($dictData){
            if($dictData->getOriginal('allow_delete') == '0'){
                $dictData->allow_delete = 0;
            }
            if($dictData->isDirty('value')){
                if($dictData->allow_delete == '0'){
                    $dictData->value = $dictData->getOriginal('value');
                }
            }
        });

        static::deleting(function ($dictType){
            if ($dictType->allow_delete == '0'){
                throw new \Exception('不允许删除');
            }
        });
    }
}
