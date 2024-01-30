<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员处理
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

use Shopwwi\WebmanAuth\Facade\Auth;

trait UserTraits
{
    /**
     * @return void
     * @throws Exception
     */
    public static function bootUserTraits()
    {
        static::updating(function ($user){
            if($user->isDirty('username')){
                $user->username = strtolower($user->username);
                $has = static::where('username',$user->username)->first();
                if($has){
                    throw new \Exception('账号已存在');
                }
            }
            if ($user->isDirty('password')){
                $user->password = Auth::bcrypt($user->password);
            }
            if ($user->isDirty('pay_pwd')){
                $user->pay_pwd = Auth::bcrypt($user->pay_pwd);
            }
            if ($user->isDirty('label')){
                $user->label = implode(',',$user->label);
            }
        });

        static::creating(function ($user){
            $user->username = strtolower($user->username);
            $has = static::where('username',$user->username)->first();
            if($has){
                throw new \Exception('账号已存在');
            }
            if(isset($user->pay_pwd)){
                $user->pay_pwd = Auth::bcrypt($user->pay_pwd);
            }
            if(isset($user->label)){
                $user->label = implode(',',$user->label);
            }
            $user->password = Auth::bcrypt($user->password);
        });
    }
}
