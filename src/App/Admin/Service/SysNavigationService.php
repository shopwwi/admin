<?php

namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\LaravelCache\Cache;
use Shopwwi\Admin\App\Admin\Models\SysNavigation;

class SysNavigationService
{
    /**
     * 获取所有菜单
     */
    public static function getList()
    {
        return Cache::rememberForever('shopwwiSysNavigation', function () {
            return SysNavigation::orderBy('sort','asc')->orderBy('id','asc')->get();
        });
    }

    /**
     * 清理菜单缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiSysNavigation");
    }

}