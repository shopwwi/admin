<?php

namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\Admin\App\Admin\Models\SysArea;
use Shopwwi\LaravelCache\Cache;

class SysAreaService
{

    /**
     * 获取地区缓存
     * @return mixed
     */
    public static function getList(){
        $list = Cache::rememberForever('shopwwiSysArea', function () {
            return SysArea::orderBy('id','asc')->get();
        });
        return $list;
    }

    /**
     * 清理地区缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiSysArea");
        Cache::forget("shopwwiSysAreaDeep0");
    }

    public static function getFirstAreaList(){
        $list = Cache::rememberForever('shopwwiSysAreaDeep0', function () {
            return SysArea::orderBy('id','asc')->where('deep',0)->get();
        });
        return $list;
    }
}