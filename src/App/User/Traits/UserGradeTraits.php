<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户等级事件
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

namespace Shopwwi\Admin\App\User\Traits;

use Shopwwi\Admin\App\User\Service\GradeService;

trait UserGradeTraits
{
    /**
     * @return void
     */
    public static function bootUserGradeTraits()
    {
        static::deleting(function ($userGrade){
            if($userGrade->is_default){
                throw new \Exception('默认等级不允许删除');
            }
            GradeService::clear();
        });

        static::creating(function ($userGrade){
            if($userGrade->is_default == 1){
                static::where('group_id',$userGrade->group_id)->update(['is_default'=>0]);
            }
            $has = static::where('level',$userGrade->level)->where('group_id',$userGrade->group_id)->first();
            if($has != null){
                throw  new \Exception('数字等级已存在，换个试试');
            }
            GradeService::clear();
        });
        static::updating(function ($model){
            $has = static::where('level',$model->level)->where('group_id',$model->group_id)->where('id','<>',$model->id)->first();
            if($has != null){
                throw  new \Exception('数字等级已存在，换个试试');
            }
            GradeService::clear();
        });
    }
}