<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员等级组
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

trait UserGradeGroupTraits
{
    /**
     * @return void
     * @throws Exception
     */
    public static function bootUserGradeGroupTraits()
    {
        static::deleting(function ($userGradeGroup){
            if($userGradeGroup->is_default){
                throw new \Exception('默认等级不允许删除');
            }
        });

        static::creating(function ($userGradeGroup){
            $has = static::where('is_default',1)->first();
            if($has == null){
                $userGradeGroup->is_default = 1;
            }
        });
    }
}