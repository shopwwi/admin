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

use Shopwwi\WebmanAuth\Facade\Auth;

trait SysUserTraits
{

    public static function bootSysUserTraits()
    {
        static::updating(function ($sysUser){
            if ($sysUser->isDirty('password')){
                $sysUser->password = Auth::bcrypt($sysUser->password);
            }
        });

        static::creating(function ($sysUser){
            $has = static::where('username',$sysUser->username)->first();
            if($has){
                throw new \Exception('用户名已存在');
            }
            $sysUser->password = Auth::bcrypt($sysUser->password);
        });
    }
}
