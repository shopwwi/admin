<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysCache;

class SysCacheSeeder
{
    public static function run()
    {
        SysCache::query()->truncate();
        SysCache::query()->insert([
            ['key'=>'shopwwiSysMenu','name'=>'平台菜单','desc'=>'平台菜单缓存用于后台的菜单展示,缓存可用于减少性能的消耗','model' => 'Shopwwi\Admin\App\Admin\Service\SysMenuService@clear'],
            ['key'=>'shopwwiSysConfig','name'=>'系统配置','desc'=>'系统配置通常用于对常量的设置，可以快速的获取数据','model' => 'Shopwwi\Admin\App\Admin\Service\SysConfigService@clear'],
            ['key'=>'shopwwiSysDict','name'=>'数据字典','desc'=>'数据字典用于对相关字段或定义的类型来进行语言的解释','model' => 'Shopwwi\Admin\App\Admin\Service\DictTypeService@clear'],
            ['key'=>'shopwwiUserMenu','name'=>'会员中心菜单','desc'=>'用于会员中心的菜单','model' => 'Shopwwi\Admin\App\User\Service\UserMenuService@clear'],
            ['key'=>'shopwwiSensitiveWord','name'=>'敏感词','desc'=>'用于敏感词列表的缓存','model' => 'Shopwwi\Admin\Logic\SensitiveUseLogic@clear'],
            ['key'=>'shopwwiAdminSector','name'=>'平台部门','desc'=>'用于平台部门数据的缓存','model' => 'Shopwwi\Admin\App\Admin\Service\SysSectorService@clear'],
            ['key'=>'shopwwiSysNavigation','name'=>'系统导航','desc'=>'用于对应用的前台导航缓存','model' => 'Shopwwi\Admin\App\Admin\Service\SysNavigationService@clear'],
        ]);
    }
}