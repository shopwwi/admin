<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员收获地址
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

use Shopwwi\Admin\App\User\Models\UserAddress;
use Shopwwi\WebmanAuth\Facade\Auth;

trait UserAddressTraits
{
    /**
     * @return void
     * @throws Exception
     */
    public static function bootUserAddressTraits()
    {
        static::updating(function ($address){
            if($address->address_default){ //其它全部设置为非默认
                UserAddress::where('user_id',$address->user_id)->where('id','!=',$address->id)->update(['address_default'=>0]);
            }
        });

        static::creating(function ($address){
            if($address->address_default){ //其它全部设置为非默认
                UserAddress::where('user_id',$address->user_id)->update(['address_default'=>0]);
            }
        });
    }
}
