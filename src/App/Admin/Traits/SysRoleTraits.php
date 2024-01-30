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

trait SysRoleTraits
{

    public static function bootSysRoleTraits()
    {
        static::creating(function ($role){

        });


        static::updating(function ($role){

        });

        static::updated(function ($role){
           $role->menu()->sync(request()->menu_id);
        });

        static::deleting(function ($role){
            if($role->id === 1){
                throw new \Exception('编号为1的数据不允许删除');
            }
        });

    }
}
