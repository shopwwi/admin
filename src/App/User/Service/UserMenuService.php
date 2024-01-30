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
namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\LaravelCache\Cache;
use Shopwwi\Admin\App\User\Models\UserMenu;

class UserMenuService
{
    /**
     * 获取所有菜单
     */
    public static function getMenusList()
    {
        $list = Cache::rememberForever('shopwwiUserMenu', function () {
            return UserMenu::orderBy('sort','asc')->orderBy('id','asc')->get();
        });
        return $list;
    }

    /**
     * 清理菜单缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiUserMenu");
    }
}