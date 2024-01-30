<?php

namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\Admin\App\Admin\Models\SysNotice;
use Shopwwi\LaravelCache\Cache;

class SysNoticeService
{
    /**
     * 获取公告列表
     * @param $position
     * @param $limit
     * @param $cache
     * @return mixed
     */
    public static function getList($position,$limit,$cache = true){
        if($cache){
            return Cache::remember('ShopwwiSysNotice'.$position.$limit,72000,function () use ($limit, $position) {
                return SysNotice::where('position',$position)->take($limit)->get();
            });
        }else{
            return  SysNotice::where('position',$position)->take($limit)->get();
        }
    }
}