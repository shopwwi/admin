<?php

namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\LaravelCache\Cache;
use Shopwwi\Admin\App\Admin\Models\SysHelpClass;

class SysHelpService
{
    /**
     * 获取帮助分类及下属文章
     * @param $position
     * @param $classNum
     * @param $articleNum
     * @param $cache
     * @return mixed
     */
    public static function getHelpClassAndArticleByPosition($position,$classNum,$articleNum,$cache = false)
    {
        if($cache){
            return Cache::remember('ShopwwiHelpClassAndArticle'.$position.$classNum.$articleNum,72000,function () use ($articleNum, $classNum, $position) {
                return SysHelpClass::where('position',$position)->take($classNum)->get()->each(function ($item) use ($articleNum) {
                    $item->load(['helps'=>function ($q) use ($articleNum) {
                        if($articleNum > 0){
                            $q->take($articleNum);
                        }
                    }]);
                });
            });
        }else{
            return  SysHelpClass::where('position',$position)->take($classNum)->get()->each(function ($item) use ($articleNum) {
                $item->load(['helps'=>function ($q) use ($articleNum) {
                    if($articleNum > 0){
                        $q->take($articleNum);
                    }
                }]);
            });
        }
    }

    /**
     * 获取分类列表
     * @param $position
     * @param $cache
     * @return mixed
     */
    public static function getHelpClassByPosition($position,$cache = false){
        if($cache){
            return Cache::remember('ShopwwiHelpClassByPosition'.$position,72000,function () use ($position) {
                return SysHelpClass::where('position',$position)->get();
            });
        }else{
            return  SysHelpClass::where('position',$position)->get();
        }
    }
}