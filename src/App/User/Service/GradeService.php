<?php

namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\App\User\Models\UserGrade;
use Shopwwi\LaravelCache\Cache;

class GradeService
{
    public static function getIndexAmis()
    {
        return
            shopwwiAmis('crud')->perPage(15)
                ->perPageField('limit')
                ->bulkActions()->syncLocation(false)
                ->headerToolbar([
                    'bulkActions',
                    shopwwiAmis('reload')->align('right'),
                ])
                ->api(shopwwiUserUrl('grade?_format=json'))
                ->columns([
                    shopwwiAmis()->name('created_at')->label(trans('field.created_at',[],'messages')),
                    shopwwiAmis()->name('growth')->label(trans('field.growth',[],'userGrowthLog'))->classNameExpr("<%= data.growth > 0 ? 'text-danger' : 'text-success' %>"),
                    shopwwiAmis()->name('description')->label(trans('field.description',[],'userGrowthLog')),
                ]);
    }

    /**
     * 获取等级缓存
     */
    public static function getGradeList(){
        return Cache::rememberForever('shopwwiUserGrade', function () {
            return UserGrade::with('group')->get();
        });
    }

    /**
     * 清理地区缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiUserGrade");
        Cache::forget("shopwwiUserGradeByGroup");
    }


    /**
     * 获取等级组组合缓存
     */
    public static function getGradeGroupByList(){
        return Cache::rememberForever('shopwwiUserGradeByGroup', function () {
            $groupList = self::getGradeList();
            $groupNewList = collect();
            foreach ($groupList as $item){
                if($item->status != 1) continue;
                if($groupNewList->get((string) $item->group_id)){
                    $newItem = $groupNewList->get((string) $item->group_id);
                }else{
                    $newItem = new \stdClass();
                    $newItem->group = $item->group;
                    $newItem->children = collect();
                }
                $item->setHidden(['created_user_id', 'created_at', 'updated_user_id', 'updated_at', 'deleted_at','group']);
                $newItem->children->push($item);
                $groupNewList->put((string)$item->group_id,$newItem);
            }
            return $groupNewList;
        });
    }

}