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

use Shopwwi\Admin\App\Admin\Models\SysDictType;
use Shopwwi\Admin\App\Admin\Models\SysMenu;
use Shopwwi\LaravelCache\Cache;

class SysMenuService
{
    /**
     * 获取所有菜单
     */
    public static function getMenusList()
    {
        $list = Cache::rememberForever('shopwwiAdminMenu', function () {
            return SysMenu::orderBy('sort','asc')->orderBy('id','asc')->get();
        });
        return $list;
    }

    public static function getAmisMenusList()
    {
        $list = Cache::rememberForever('shopwwiAdminAmisMenu', function () {
            return SysMenu::orderBy('sort','asc')->orderBy('id','asc')->where('status',1)->get(['id','name','pid','path','is_frame','is_cache','menu_type','visible','icon','key']);
        });
        return $list;
    }

    /**
     * 清理菜单缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiAdminMenu");
        Cache::forget("shopwwiAdminAmisMenu");
    }
}