<?php

namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\Admin\App\Admin\Models\SysRole;
use Shopwwi\Admin\App\Admin\Models\SysUserSector;
use Shopwwi\LaravelCache\Cache;

class SysSectorService
{
    /**
     * 获取所有部门
     */
    public static function getList()
    {
        $list = Cache::rememberForever('shopwwiAdminSector', function () {
            return SysUserSector::orderBy('sort','asc')->orderBy('id','asc')->select('id','name','pid','sort','leader','status')->get();
        });
        return $list;
    }

    /**
     * 获取已有权限部门列表
     * @param $admin
     * @param $role
     * @return mixed|void
     */
    public static function getUseList($admin,$role = null)
    {
        $ids = $admin->sector_ids ?? [];
        if($role == null){
            $role = SysRole::where('id',$admin->role_id)->first();
        }
        $list = self::getList();
        if($role == null || ($role != null && $role->scope == 1) || $role->scope == 2){ //角色为空或只查看自己数据时或只查看本部门数据
              return $list->whereIn('id',$ids);
        }elseif ($role->scope == 3){
            foreach ($ids as $id){
                self::getSectorAndLevels($list,$id,$ids);
            }
            return $list->whereIn('id',$ids);
        }else{
            return $list;
        }
    }

    /**
     * 获取下级部门
     * @param $sectorList
     * @param $id
     * @param $sectorIds
     * @return void
     */
    public static function getSectorAndLevels($sectorList,$id,&$sectorIds)
    {
        if(empty($sectorList)) return;
        foreach ($sectorList as $item){
            if($item->pid == $id){
                $sectorIds[] = $item->id;
                self::getSectorAndLevels($sectorList,$item->id,$sectorIds);
            }
        }
    }

    /**
     * 清理部门缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiAdminSector");
    }
}