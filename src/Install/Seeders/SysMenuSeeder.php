<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysMenu;

class SysMenuSeeder
{
    public static function run()
    {
        SysMenu::query()->truncate();
        $list = self::getData();
        $sort = 1;
        foreach ($list as $item){
            if(isset($item['children'])){
                $level = $item['children'];
                unset($item['children']);
            }
            if(!isset($item['sort'])){
                $item['sort'] = $sort;
            }
            $first = SysMenu::firstOrCreate(['key'=>$item['key']],$item);
            if(isset($level) && count($level) > 0) {
                self::setMenu($level, $first->id);
            }
            $sort++;
        }
    }

    /**
     * 循环处理下级菜单
     * @param $data
     * @param $pid
     */
    protected static function setMenu($data,$pid){
        $sort = 1;
        foreach ($data as $item){
            $level = $item['children'] ?? null;
            if(!empty($level)){
                unset($item['children']);
            }
            $item['pid'] = $pid;
            if(!isset($item['sort'])){
                $item['sort'] = $sort;
            }
            $first = SysMenu::firstOrCreate(['key'=>$item['key']],$item);
            if(!empty($level)) {
                self::setMenu($level, $first->id);
            }
            $sort++;
        }
    }

    protected static function getController($path){
        $controller = "Shopwwi\Admin\App\Admin\Controllers";

        if(is_array($path)){
            $arr = [];
            foreach ($path as $v){
                $arr[] = $controller.$v;
            }
            return implode(',',$arr);
        }
        return $controller.$path;
    }

    protected static function getData(){
        return [
            ['name'=>'首页','key'=>'index','menu_type'=>'C','component'=>'/','path'=>'/','icon'=>'ri-home-smile-fill','is_cache'=>0],
            ['name'=>'设置','key'=>'setting','menu_type' => 'M','icon' => 'ri-settings-4-fill','is_cache'=>1
                ,'children' => [
                    ['name'=>'基础设置','key'=>'settingSite','menu_type' => 'M','icon' => 'ri-settings-2-line','is_cache'=>1
                        ,'children' => [
                            ['name'=>'站点设置','key'=>'settingSiteBase','menu_type' => 'M','is_cache'=>1 ,'children' => [
                                ['name'=>'基础设置','key'=>'settingSiteBaseSite','menu_type' => 'C','component' => '/setting/site/base','path'=>'/system/config/site','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@site')],
                                ['name'=>'规则设置','key'=>'settingSiteBaseRule','menu_type' => 'C','component' => '/setting/site/rule','path'=>'/system/config/rule','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@rule')],
                                ['name'=>'图片设置','key'=>'settingSiteBasePic','menu_type' => 'C','component' => '/setting/site/pic','path'=>'/system/config/pic','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@pic')],
                            ]],
                            ['name'=>'导航设置','key'=>'settingSiteNavigation','menu_type' => 'C','component'=>'/setting/site/navigation','path'=>'/system/navigation','is_cache'=>1,'children' => [
                                ['name'=>'导航新增','key'=>'settingSiteNavigationCreate','menu_type' => 'F','perms' => self::getController(['\System\SysNavigationController@create','\System\SysNavigationController@store'])],
                                ['name'=>'导航修改','key'=>'settingSiteNavigationEdit','menu_type' => 'F','perms' => self::getController(['\System\SysNavigationController@edit','\System\SysNavigationController@update'])],
                                ['name'=>'导航删除','key'=>'settingSiteNavigationDestroy','menu_type' => 'F','perms' => self::getController('\System\SysNavigationController@destroy')],
                            ],'perms' => self::getController(['\System\SysNavigationController@index','\System\SysNavigationController@show'])],
                            ['name'=>'地区设置','key'=>'settingSiteArea','menu_type' => 'C','component'=>'/setting/site/area','path'=>'/system/area','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'地区新增','key'=>'settingSiteAreaCreate','menu_type' => 'F','perms' => self::getController(['\System\SysAreaController@create','\System\SysAreaController@store'])],
                                    ['name'=>'地区修改','key'=>'settingSiteAreaEdit','menu_type' => 'F','perms' => self::getController(['\System\SysAreaController@edit','\System\SysAreaController@update'])],
                                    ['name'=>'地区删除','key'=>'settingSiteAreaDestroy','menu_type' => 'F','perms' => self::getController('\System\SysAreaController@destroy')],
                                    ['name'=>'地区回收站','key'=>'settingSiteAreaRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/area/recovery','path'=>'/system/area/recovery','perms' => self::getController(['\System\SysAreaController@recovery','\System\SysAreaController@restore','\System\SysAreaController@erasure'])]
                                ],'perms' => self::getController(['\System\SysAreaController@index','\System\SysAreaController@show'])
                            ],
                            ['name'=>'运费模板','key'=>'settingSiteFreight','menu_type' => 'C','component'=>'/setting/site/freight','path'=>'/system/freight','is_cache' => 1
                                ,'children' => [
                                    ['name'=>'模板新增','key'=>'settingSiteFreightCreate','menu_type' => 'F','perms' => self::getController(['\System\SysFreightTemplateController@create','\System\SysFreightTemplateController@store'])],
                                    ['name'=>'模板修改','key'=>'settingSiteFreightEdit','menu_type' => 'F','perms' => self::getController(['\System\SysFreightTemplateController@edit','\System\SysFreightTemplateController@update'])],
                                    ['name'=>'模板删除','key'=>'settingSiteFreightDestroy','menu_type' => 'F','perms' => self::getController('\System\SysFreightTemplateController@destroy')],
                                    ['name'=>'模板复制','key'=>'settingSiteFreightCopy','menu_type' => 'F','perms' => self::getController('\System\SysFreightTemplateController@copy')]
                                ],'perms' => self::getController(['\System\SysFreightTemplateController@index','\System\SysFreightTemplateController@show'])
                            ],
                            ['name'=>'物流设置','key'=>'settingSiteShip','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'物流公司','key'=>'settingSiteShipIndex','menu_type' => 'C','component' =>'/setting/site/ship','path'=>'/system/ship','is_cache'=>1,'perms' => self::getController('\System\SysShipCompanyController@index')],
                                    ['name'=>'物流新增','key'=>'settingSiteShipCreate','menu_type' => 'F','perms' => self::getController(['\System\SysShipCompanyController@create','\System\SysShipCompanyController@store'])],
                                    ['name'=>'物流修改','key'=>'settingSiteShipEdit','menu_type' => 'F','perms' => self::getController(['\System\SysShipCompanyController@edit','\System\SysShipCompanyController@update'])],
                                    ['name'=>'物流详情','key'=>'settingSiteShipShow','menu_type' => 'F','perms' => self::getController('\System\SysShipCompanyController@show')],
                                    ['name'=>'物流词删除','key'=>'settingSiteShipDestroy','menu_type' => 'F','perms' => self::getController('\System\SysShipCompanyController@destroy')],
                                    ['name'=>'物流回收站','key'=>'settingSiteShipRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/ship/recovery','path'=>'/system/ship/recovery','perms' => self::getController(['\System\SysShipCompanyController@recovery','\System\SysShipCompanyController@restore','\System\SysShipCompanyController@erasure'])],
                                    ['name'=>'物流设置','key'=>'settingSiteShipSite','menu_type' => 'C','component' =>'/setting/site/ship/site','path'=>'/system/ship/setting','is_cache'=>1,'perms' => self::getController('\System\SysShipCompanyController@setting')]
                                ]
                            ],
                            ['name'=>'消息模板','key'=>'settingSiteMsg','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'公共模板','key'=>'settingSiteMsgCommon','menu_type' => 'C','component' =>'/setting/site/msg/common','path'=>'/system/msg/common','is_cache'=>1,'children' => [
                                        ['name'=>'新增模板','key'=>'settingSiteMsgCommonCreate','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplCommonController@create','\System\SysMsgTplCommonController@store'])],
                                        ['name'=>'修改模板','key'=>'settingSiteMsgCommonEdit','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplCommonController@edit','\System\SysMsgTplCommonController@update'])],
                                        ['name'=>'删除配置','key'=>'settingSiteMsgCommonDestroy','menu_type' => 'F','perms' => self::getController('\System\SysMsgTplCommonController@destroy')],
                                        ['name'=>'配置回收站','key'=>'settingSiteMsgCommonRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/msg/common/recovery','path'=>'/system/msg/common/recovery','perms' => self::getController(['\System\SysMsgTplCommonController@recovery','\System\SysMsgTplCommonController@restore','\System\SysMsgTplCommonController@erasure'])]
                                    ],'perms' => self::getController(['\System\SysMsgTplCommonController@index','\System\SysMsgTplCommonController@show'])],
                                    ['name'=>'消息模板','key'=>'settingSiteMsgSystem','menu_type' => 'C','component' =>'/setting/site/msg/system','path'=>'/system/msg/system','is_cache'=>1,'children' => [
                                        ['name'=>'新增配置','key'=>'settingSiteMsgSystemCreate','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplSystemController@create','\System\SysMsgTplSystemController@store'])],
                                        ['name'=>'修改配置','key'=>'settingSiteMsgSystemEdit','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplSystemController@edit','\System\SysMsgTplSystemController@update'])],
                                        ['name'=>'删除配置','key'=>'settingSiteMsgSystemDestroy','menu_type' => 'F','perms' => self::getController('\System\SysMsgTplSystemController@destroy')],
                                        ['name'=>'配置回收站','key'=>'settingSiteMsgSystemRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/msg/system/recovery','path'=>'/system/msg/system/recovery','perms' => self::getController(['\System\SysMsgTplSystemController@recovery','\System\SysMsgTplSystemController@restore','\System\SysMsgTplSystemController@erasure'])]
                                    ],'perms' => self::getController(['\System\SysMsgTplSystemController@index','\System\SysMsgTplSystemController@show'])]
                                ]
                            ],
                            ['name'=>'附件管理','key'=>'settingSiteAlbum','menu_type' => 'M','is_cache'=>1
                                    ,'children' => [
                                        ['name'=>'附件列表','key'=>'settingSiteAlbumIndex','menu_type' => 'C','component' =>'/setting/site/album','path'=>'/system/files','perms' => self::getController(['\System\SysAlbumController@index','\System\SysAlbumFilesController@index'])],
                                        ['name'=>'新增相册','key'=>'settingSiteAlbumCreate','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumController@create','\System\SysAlbumController@store'])],
                                        ['name'=>'修改相册','key'=>'settingSiteAlbumEdit','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumController@edit','\System\SysAlbumController@update'])],
                                        ['name'=>'相册详情','key'=>'settingSiteAlbumShow','menu_type' => 'F','perms' => self::getController('\System\SysShipCompanyController@show')],
                                        ['name'=>'上传图片','key'=>'settingSiteAlbumFileStore','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumFilesController@store','\System\SysAlbumFilesController@edit'])],
                                        ['name'=>'删除操作','key'=>'settingSiteAlbumDestroy','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumFilesController@destroy','\System\SysAlbumController@destroy'])],
                                        ['name'=>'相册回收站','key'=>'settingSiteAlbumRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/album/recovery','path'=>'/system/files/recovery','perms' => self::getController(['\System\SysAlbumFilesController@recovery','\System\SysAlbumFilesController@restore','\System\SysAlbumFilesController@erasure'])],
                                        ['name'=>'附件设置','key'=>'settingSiteAlbumSite','menu_type' => 'C','component' =>'/setting/site/album/site','path'=>'/system/album/setting','is_cache'=>1,'perms' => self::getController('\System\SysAlbumController@setting')]
                                ]
                            ],
                            ['name'=>'敏感词','key'=>'settingSiteSensitives','menu_type' => 'C','component' =>'/setting/site/sensitives','path'=>'/system/sensitives','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'敏感词新增','key'=>'settingSiteSensitivesCreate','menu_type' => 'F','perms' => self::getController(['\System\SysSensitivesController@create','\System\SysSensitivesController@store'])],
                                    ['name'=>'敏感词修改','key'=>'settingSiteSensitivesEdit','menu_type' => 'F','perms' => self::getController(['\System\SysSensitivesController@edit','\System\SysSensitivesController@update'])],
                                    ['name'=>'敏感词详情','key'=>'settingSiteSensitivesShow','menu_type' => 'F','perms' => self::getController('\System\SysSensitivesController@show')],
                                    ['name'=>'敏感词删除','key'=>'settingSiteSensitivesDestroy','menu_type' => 'F','perms' => self::getController('\System\SysSensitivesController@destroy')]
                                ],'perms' => self::getController('\System\SysSensitivesController@index')
                            ],
                        ]
                    ],
                    ['name'=>'系统设置','key'=>'settingSystem','menu_type' => 'M','icon'=>'ri-list-settings-line'
                        ,'children' => [
                            ['name'=>'权限管理','key'=>'settingSystemPower','menu_type' => 'M'
                                ,'children' => [
                                    ['name'=>'用户管理','key'=>'settingSystemPowerUser','menu_type' => 'C','component' =>'/setting/system/user','path'=>'/system/user','is_cache'=>1,'perms' => self::getController('\System\SysUserController@index')
                                        ,'children' => [
                                            ['name'=>'新增用户','key'=>'settingSystemPowerUserCreate','menu_type' => 'F','perms' => self::getController(['\System\SysRoleController@create','\System\SysUserController@store'])],
                                            ['name'=>'修改用户','key'=>'settingSystemPowerUserEdit','menu_type' => 'F','perms' => self::getController(['\System\SysUserController@edit','\System\SysUserController@update'])],
                                            ['name'=>'用户详情','key'=>'settingSystemPowerUserShow','menu_type' => 'F','perms' => self::getController('\System\SysUserController@show')],
                                            ['name'=>'删除用户','key'=>'settingSystemPowerUserDestroy','menu_type' => 'F','perms' => self::getController('\System\SysUserController@destroy')],
                                            ['name'=>'用户回收站','key'=>'settingSystemPowerRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/user/recovery','path'=>'/system/user/recovery','perms' => self::getController(['\System\SysUserController@recovery','\System\SysUserController@restore','\System\SysUserController@erasure'])]
                                        ]
                                    ],
                                    ['name'=>'角色管理','key'=>'settingSystemPowerRole','menu_type' => 'C','component' =>'/setting/system/role','path'=>'/system/role','is_cache'=>1,'perms' => self::getController('\System\SysRoleController@index')
                                        ,'children' => [
                                            ['name'=>'新增角色','key'=>'settingSystemPowerRoleCreate','menu_type' => 'F','perms' => self::getController(['\System\SysRoleController@create','\System\SysRoleController@store'])],
                                            ['name'=>'修改角色','key'=>'settingSystemPowerRoleEdit','menu_type' => 'F','perms' => self::getController(['\System\SysRoleController@edit','\System\SysRoleController@update'])],
                                            ['name'=>'角色详情','key'=>'settingSystemPowerRoleShow','menu_type' => 'F','perms' => self::getController('\System\SysRoleController@show')],
                                            ['name'=>'删除角色','key'=>'settingSystemPowerRoleDestroy','menu_type' => 'F','perms' => self::getController('\System\SysRoleController@destroy')],
                                            ['name'=>'角色回收站','key'=>'settingSystemPowerRoleRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/role/recovery','path'=>'/system/role/recovery','perms' => self::getController(['\System\SysRoleController@recovery','\System\SysRoleController@restore','\System\SysRoleController@erasure'])]
                                        ]
                                    ],
                                    ['name'=>'部门管理','key'=>'settingSystemPowerSector','menu_type' => 'C','component' =>'/setting/system/sector','path'=>'/system/sector','is_cache'=>1,'perms' => self::getController('\System\SysUserSectorController@index')
                                        ,'children' => [
                                            ['name'=>'新增角色','key'=>'settingSystemPowerSectorCreate','menu_type' => 'F','perms' => self::getController(['\System\SysUserSectorController@create','\System\SysUserSectorController@store'])],
                                            ['name'=>'修改角色','key'=>'settingSystemPowerSectorEdit','menu_type' => 'F','perms' => self::getController(['\System\SysUserSectorController@edit','\System\SysUserSectorController@update'])],
                                            ['name'=>'角色详情','key'=>'settingSystemPowerSectorShow','menu_type' => 'F','perms' => self::getController('\System\SysUserSectorController@show')],
                                            ['name'=>'删除角色','key'=>'settingSystemPowerSectorDestroy','menu_type' => 'F','perms' => self::getController('\System\SysUserSectorController@destroy')],
                                            ['name'=>'角色回收站','key'=>'settingSystemPowerSectorRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/sector/recovery','path'=>'/system/sector/recovery','perms' => self::getController(['\System\SysUserSectorController@recovery','\System\SysUserSectorController@restore','\System\SysUserSectorController@erasure'])]
                                        ]
                                    ],
                                    ['name'=>'操作日志','key'=>'settingSystemPowerLog','menu_type' => 'C','component' =>'/setting/system/log','path'=>'/system/log','is_cache'=>1,'perms' => self::getController('\System\SysOperLogController@index')
                                        ,'children' => [
                                            ['name'=>'清理日志','key'=>'settingSystemPowerLogDestroy','menu_type' => 'F','perms' => self::getController('\System\SysOperLogController@destroy')],
                                        ]
                                    ],
                                ]
                            ],
                            ['name'=>'系统菜单','key'=>'settingSystemMenu','menu_type' => 'C','component' =>'/setting/system/menu','path'=>'/system/menu','is_cache'=>1,'perms' => self::getController('\System\SysMenuController@index')
                                ,'children' => [
                                    ['name'=>'新增角色','key'=>'settingSystemMenuCreate','menu_type' => 'F','perms' => self::getController(['\System\SysMenuController@create','\System\SysMenuController@store'])],
                                    ['name'=>'修改角色','key'=>'settingSystemMenuEdit','menu_type' => 'F','perms' => self::getController(['\System\SysMenuController@edit','\System\SysMenuController@update'])],
                                    ['name'=>'角色详情','key'=>'settingSystemMenuShow','menu_type' => 'F','perms' => self::getController('\System\SysMenuController@show')],
                                    ['name'=>'删除角色','key'=>'settingSystemMenuDestroy','menu_type' => 'F','perms' => self::getController('\System\SysMenuController@destroy')]
                                ]
                            ],
                            ['name'=>'系统配置','key'=>'settingSystemConfig','menu_type' => 'C','component' =>'/setting/system/config','path'=>'/system/config','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@index')
                                ,'children' => [
                                    ['name'=>'新增配置','key'=>'settingSystemConfigCreate','menu_type' => 'F','perms' => self::getController(['\System\SysConfigController@create','\System\SysConfigController@store'])],
                                    ['name'=>'修改配置','key'=>'settingSystemConfigEdit','menu_type' => 'F','perms' => self::getController(['\System\SysConfigController@edit','\System\SysConfigController@update'])],
                                    ['name'=>'配置详情','key'=>'settingSystemConfigShow','menu_type' => 'F','perms' => self::getController('\System\SysConfigController@show')],
                                    ['name'=>'删除配置','key'=>'settingSystemConfigDestroy','menu_type' => 'F','perms' => self::getController('\System\SysConfigController@destroy')],
                                    ['name'=>'配置回收站','key'=>'settingSystemConfigRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/config/recovery','path'=>'/system/config/recovery','perms' => self::getController(['\System\SysConfigController@recovery','\System\SysConfigController@restore','\System\SysConfigController@erasure'])]
                                ]
                            ],
                            ['name'=>'数据字典','key'=>'settingSystemDict','menu_type' => 'C','component' =>'/setting/system/dict','path'=>'/system/dict','is_cache'=>1,'perms' => self::getController('\System\SysDictTypeController@index')
                                ,'children' => [
                                    ['name'=>'新增字典','key'=>'settingSystemDictCreate','menu_type' => 'F','perms' => self::getController(['\System\SysDictTypeController@create','\System\SysDictTypeController@store'])],
                                    ['name'=>'修改字典','key'=>'settingSystemDictEdit','menu_type' => 'F','perms' => self::getController(['\System\SysDictTypeController@edit','\System\SysDictTypeController@update'])],
                                    ['name'=>'字典详情','key'=>'settingSystemDictShow','menu_type' => 'C','visible' => '0','component' =>'/setting/system/dict/[name]','path'=>'/system/dictdata','perms' => self::getController('\System\SysDictDataController@index')],
                                    ['name'=>'删除字典','key'=>'settingSystemDictDestroy','menu_type' => 'F','perms' => self::getController('\System\SysDictTypeController@destroy')],
                                    ['name'=>'新增字典数据','key'=>'settingSystemDictDataCreate','menu_type' => 'F','perms' => self::getController(['\System\SysDictDataController@create','\System\SysDictDataController@store'])],
                                    ['name'=>'修改字典数据','key'=>'settingSystemDictDataEdit','menu_type' => 'F','perms' => self::getController(['\System\SysDictDataController@edit','\System\SysDictDataController@update'])],
                                    ['name'=>'字典数据详情','key'=>'settingSystemDictDataShow','menu_type' => 'F','perms' => self::getController('\System\SysDictDataController@show')],
                                    ['name'=>'删除字典数据','key'=>'settingSystemDictDataDestroy','menu_type' => 'F','perms' => self::getController('\System\SysDictDataController@destroy')],
                                ]
                            ],
                            ['name'=>'开发者工具','key'=>'settingSystemTools','menu_type' => 'C','component' =>'/setting/system/tools','path'=>'/system/tools','is_cache'=>1,'perms' => self::getController('\System\GenTableController@index')
                                ,'children' => [
                                    ['name'=>'导入数据表','key'=>'settingSystemToolsCreate','menu_type' => 'F','perms' => self::getController(['\System\GenTableController@create','\System\GenTableController@store'])],
                                    ['name'=>'修改配置','key'=>'settingSystemToolsEdit','menu_type' => 'F','component' => '/setting/system/tools/[id]','visible' => '0','perms' => self::getController(['\System\GenTableController@edit','\System\GenTableController@update'])],
                                    ['name'=>'查看详情','key'=>'settingSystemToolsShow','menu_type' => 'F','perms' => self::getController('\System\GenTableController@show')],
                                    ['name'=>'删除配置','key'=>'settingSystemToolsDestroy','menu_type' => 'F','perms' => self::getController('\System\GenTableController@destroy')]
                                ]
                            ],
                            ['name'=>'缓存管理','key'=>'settingSystemCache','menu_type' => 'C','component' =>'/setting/system/cache','path'=>'/system/cache','is_cache'=>1,'perms' => self::getController('\System\SysCacheController@index')
                                ,'children' => [
                                    ['name'=>'新增缓存','key'=>'settingSystemCacheCreate','menu_type' => 'F','perms' => self::getController(['\System\SysCacheController@create','\System\SysCacheController@store'])],
                                    ['name'=>'修改缓存','key'=>'settingSystemCacheEdit','menu_type' => 'F','perms' => self::getController(['\System\SysCacheController@edit','\System\SysCacheController@update'])],
                                    ['name'=>'查看缓存','key'=>'settingSystemCacheShow','menu_type' => 'F','perms' => self::getController('\System\SysCacheController@show')],
                                    ['name'=>'清理缓存','key'=>'settingSystemCacheDestroy','menu_type' => 'F','perms' => self::getController('\System\SysCacheController@destroy')],
                                ]
                            ]
                        ]
                    ],
                    ['name'=>'第三方','key'=>'settingWay','menu_type' => 'M','icon'=>'ri-typhoon-line','is_cache'=>1
                        ,'children' => [
                            ['name'=>'邮件配置','key'=>'settingWayEmail','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'邮件记录','key'=>'settingWayEmailIndex','menu_type' => 'C','component' =>'/setting/way/email','path'=>'/system/email','is_cache'=>1,'perms' => self::getController(['\System\SysEmailCodeController@index','\System\SysEmailCodeController@show'])],
                                    ['name'=>'邮件设置','key'=>'settingWayEmailSite','menu_type' => 'C','component' =>'/setting/way/email/setting','path'=>'/system/email/setting','is_cache'=>1,'perms' => self::getController(['\System\SysSmsCodeController@setting'])],
                                    ['name'=>'删除记录','key'=>'settingWayEmailDestroy','menu_type' => 'F','perms' => self::getController(['\System\SysEmailCodeController@destroy'])],
                                ]
                            ],
                            ['name'=>'短信配置','key'=>'settingWaySms','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'短信记录','key'=>'settingWaySmsIndex','menu_type' => 'C','component' =>'/setting/way/sms','path'=>'/system/sms','is_cache'=>1,'perms' => self::getController(['\System\SysSmsCodeController@index','\System\SysSmsCodeController@show'])],
                                    ['name'=>'短信设置','key'=>'settingWaySmsSite','menu_type' => 'C','component' =>'/setting/way/sms/setting','path'=>'/system/sms/setting','is_cache'=>1,'perms' => self::getController('\System\SysSmsCodeController@setting')],
                                    ['name'=>'删除记录','key'=>'settingWaySmsDestroy','menu_type' => 'F','perms' => self::getController('\System\SysSmsCodeController@destroy')],
                                ]
                            ],
                            ['name'=>'登入配置','key'=>'settingWayAuth','menu_type' => 'C','component' =>'/setting/way/auth','path'=>'/system/config/auth','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@auth')],
                            ['name'=>'支付管理','key'=>'settingWayPay','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'付款日志','key'=>'settingWayPayIndex','menu_type' => 'C','component' =>'/setting/way/pay','path'=>'/system/pay','is_cache'=>1,'perms' => self::getController(['\System\SysPayController@index']),'children' => [
                                        ['name'=>'修改状态','key'=>'settingWayPayEdit','menu_type' => 'F','perms' => self::getController(['\System\SysPayController@edit','\System\SysPayController@update'])],
                                        ['name'=>'查看详情','key'=>'settingWayPayShow','menu_type' => 'F','perms' => self::getController(['\System\SysPayController@show'])]
                                    ]],
                                    ['name'=>'支付方式','key'=>'settingWayPayPayment','menu_type' => 'C','component' =>'/setting/way/pay/payment','path'=>'/system/payment','is_cache'=>1,'perms' => self::getController(['\System\SysPaymentController@index']),'children' => [
                                        ['name'=>'新增支付方式','key'=>'settingWayPayPaymentCreate','menu_type' => 'F','perms' => self::getController(['\System\SysPaymentController@create','\System\SysPaymentController@store'])],
                                        ['name'=>'修改支付方式','key'=>'settingWayPayPaymentEdit','menu_type' => 'F','perms' => self::getController(['\System\SysPaymentController@edit','\System\SysPaymentController@update'])],
                                        ['name'=>'查看详情','key'=>'settingWayPayPaymentShow','menu_type' => 'F','perms' => self::getController('\System\SysPaymentController@show')],
                                        ['name'=>'删除支付方式','key'=>'settingWayPayPaymentDestroy','menu_type' => 'F','perms' => self::getController('\System\SysPaymentController@destroy')]
                                    ]],
                                ]
                            ],
                        ]
                    ],
                    ['name'=>'内容管理','key'=>'settingContent','menu_type' => 'M','icon'=>'ri-meteor-line','is_cache'=>1,'children' => [
                        ['name'=>'文章管理','key'=>'settingContentArticle','menu_type' => 'M','is_cache'=>1,'children' => [
                            ['name'=>'文章列表','key'=>'settingContentArticleIndex','menu_type' => 'C','component' =>'/setting/content/article','path'=>'/system/articles','is_cache'=>1,'children' => [
                                ['name'=>'新增文章','key'=>'settingContentArticleCreate','menu_type' => 'F','perms' => self::getController(['\System\SysArticleController@create','\System\SysArticleController@store'])],
                                ['name'=>'修改文章','key'=>'settingContentArticleEdit','menu_type' => 'F','perms' => self::getController(['\System\SysArticleController@edit','\System\SysArticleController@update'])],
                                ['name'=>'文章详情','key'=>'settingContentArticleShow','menu_type' => 'F','perms' => self::getController('\System\SysArticleController@show')],
                                ['name'=>'删除文章','key'=>'settingContentArticleDestroy','menu_type' => 'F','perms' => self::getController('\System\SysArticleController@destroy')],
                                ['name'=>'文章回收站','key'=>'settingContentArticleRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/content/article/recovery','path'=>'/system/articles/recovery','perms' => self::getController(['\System\SysArticleController@recovery','\System\SysArticleController@restore','\System\SysArticleController@erasure'])]
                            ],'perms' => self::getController('\System\SysArticleController@index')],
                            ['name'=>'文章分类','key'=>'settingContentArticleClass','menu_type' => 'C','component' =>'/setting/content/article/class','path'=>'/system/article/class','is_cache'=>1,'children' => [
                                ['name'=>'新增分类','key'=>'settingContentArticleClassCreate','menu_type' => 'F','perms' => self::getController(['\System\SysArticleClassController@create','\System\SysArticleClassController@store'])],
                                ['name'=>'修改分类','key'=>'settingContentArticleClassEdit','menu_type' => 'F','perms' => self::getController(['\System\SysArticleClassController@edit','\System\SysArticleClassController@update'])],
                                ['name'=>'分类详情','key'=>'settingContentArticleClassShow','menu_type' => 'F','perms' => self::getController('\System\SysArticleClassController@show')],
                                ['name'=>'删除分类','key'=>'settingContentArticleClassDestroy','menu_type' => 'F','perms' => self::getController('\System\SysArticleClassController@destroy')],
                                ['name'=>'分类回收站','key'=>'settingContentArticleClassRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/content/article/class/recovery','path'=>'/system/article/class/recovery','perms' => self::getController(['\System\SysArticleClassController@recovery','\System\SysArticleClassController@restore','\System\SysArticleClassController@erasure'])]
                            ],'perms' => self::getController('\System\SysArticleClassController@index')]
                        ]],
                        ['name'=>'帮助中心','key'=>'settingContentHelp','menu_type' => 'M','children' => [
                            ['name'=>'帮助列表','key'=>'settingContentHelpIndex','menu_type' => 'C','component' =>'/setting/content/help','path'=>'/system/helps','is_cache'=>1,'children' => [
                                ['name'=>'新增帮助','key'=>'settingContentHelpCreate','menu_type' => 'F','perms' => self::getController(['\System\SysHelpController@create','\System\SysHelpController@store'])],
                                ['name'=>'修改帮助','key'=>'settingContentHelpEdit','menu_type' => 'F','perms' => self::getController(['\System\SysHelpController@edit','\System\SysHelpController@update'])],
                                ['name'=>'帮助详情','key'=>'settingContentHelpShow','menu_type' => 'F','perms' => self::getController('\System\SysHelpController@show')],
                                ['name'=>'删除帮助','key'=>'settingContentHelpDestroy','menu_type' => 'F','perms' => self::getController('\System\SysHelpController@destroy')],
                                ['name'=>'帮助回收站','key'=>'settingContentHelpRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/content/help/recovery','path'=>'/system/helps/recovery','perms' => self::getController(['\System\SysHelpController@recovery','\System\SysHelpController@restore','\System\SysHelpController@erasure'])]
                            ],'perms' => self::getController('\System\SysHelpController@index')],
                            ['name'=>'帮助分类','key'=>'settingContentHelpClass','menu_type' => 'C','component' =>'/setting/content/help/class','path'=>'/system/help/class','is_cache'=>1,'children' => [
                                ['name'=>'新增分类','key'=>'settingContentHelpClassCreate','menu_type' => 'F','perms' => self::getController(['\System\SysHelpClassController@create','\System\SysHelpClassController@store'])],
                                ['name'=>'修改分类','key'=>'settingContentHelpClassEdit','menu_type' => 'F','perms' => self::getController(['\System\SysHelpClassController@edit','\System\SysHelpClassController@update'])],
                                ['name'=>'分类详情','key'=>'settingContentHelpClassShow','menu_type' => 'F','perms' => self::getController('\System\SysHelpClassController@show')],
                                ['name'=>'删除分类','key'=>'settingContentHelpClassDestroy','menu_type' => 'F','perms' => self::getController('\System\SysHelpClassController@destroy')]
                            ],'perms' => self::getController('\System\SysHelpClassController@index')]
                        ]],
                        ['name'=>'系统公告','key'=>'settingContentNotice','menu_type' => 'C','children' => [
                            ['name'=>'新增公告','key'=>'settingContentNoticeCreate','menu_type' => 'F','perms' => self::getController(['\System\SysNoticeController@create','\System\SysNoticeController@store'])],
                            ['name'=>'修改公告','key'=>'settingContentNoticeEdit','menu_type' => 'F','perms' => self::getController(['\System\SysNoticeController@edit','\System\SysNoticeController@update'])],
                            ['name'=>'公告详情','key'=>'settingContentNoticeShow','menu_type' => 'F','perms' => self::getController('\System\SysNoticeController@show')],
                            ['name'=>'删除公告','key'=>'settingContentNoticeDestroy','menu_type' => 'F','perms' => self::getController('\System\SysNoticeController@destroy')]
                        ],'perms' => self::getController('\System\SysNoticeController@index'),'component' => '/setting/content/notice','path'=>'/system/notice','is_cache'=>1],
                        ['name'=>'友情链接','key'=>'settingContentLink','menu_type' => 'C','children' => [
                            ['name'=>'新增链接','key'=>'settingContentLinkCreate','menu_type' => 'F','perms' => self::getController(['\System\SysLinkController@create','\System\SysLinkController@store'])],
                            ['name'=>'修改链接','key'=>'settingContentLinkEdit','menu_type' => 'F','perms' => self::getController(['\System\SysLinkController@edit','\System\SysLinkController@update'])],
                            ['name'=>'链接详情','key'=>'settingContentLinkShow','menu_type' => 'F','perms' => self::getController('\System\SysLinkController@show')],
                            ['name'=>'删除公告','key'=>'settingContentLinkDestroy','menu_type' => 'F','perms' => self::getController('\System\SysLinkController@destroy')]
                        ],'perms' => self::getController('\System\SysLinkController@index'),'component' => '/setting/content/link','path'=>'/system/link','is_cache'=>1]
                    ]]
                ]
            ],
            ['name'=>'会员','key'=>'user','menu_type' => 'M','icon' => 'ri-user-settings-fill','is_cache'=>1
                ,'children' => [
                    ['name'=>'会员管理','key'=>'userMange','menu_type' => 'M','icon' => 'ri-user-star-line','is_cache'=>1
                        ,'children'=> [
                            ['name'=>'会员统计','key'=>'userMangeStat','menu_type' => 'C','component'=> '/user/mange/stat','path'=>'/users/stat','is_cache'=>1,'perms'=>self::getController('\User\UsersController@stat')],
                            ['name'=>'会员列表','key'=>'userMangeList','menu_type' => 'C','component'=> '/user/mange/users','path'=>'/users','is_cache'=>1,'perms'=>self::getController('\User\UsersController@index'),'children' =>[
                                ['name'=>'新增会员','key'=>'userMangeListCreate','menu_type' => 'F','perms' => self::getController(['\User\UsersController@create','\User\UsersController@store'])],
                                ['name'=>'修改会员','key'=>'userMangeListEdit','menu_type' => 'F','perms' => self::getController(['\User\UsersController@edit','\User\UsersController@update'])],
                                ['name'=>'会员详情','key'=>'userMangeListShow','menu_type' => 'C','visible' => '0','component' =>'/user/mange/users/[id]','path'=>'/user/users/{id}','perms' => self::getController('\User\UsersController@show')],
                                ['name'=>'删除会员','key'=>'userMangeListDestroy','menu_type' => 'F','perms' => self::getController('\User\UsersController@destroy')],
                                ['name'=>'会员回收站','key'=>'userMangeListRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/mange/users/recovery','path'=>'/users/recovery','perms' => self::getController(['\User\UsersController@recovery','\User\UsersController@restore','\User\UsersController@erasure'])]
                            ]],
                            ['name'=>'会员标签','key'=>'userMangeLabel','menu_type' => 'C','component'=> '/user/mange/label','path'=>'/user/label','is_cache'=>1,'perms'=> self::getController('\User\UserLabelController@index'),'children' =>[
                                ['name'=>'新增标签','key'=>'userMangeLabelCreate','menu_type' => 'F','perms' => self::getController(['\User\UserLabelController@create','\User\UserLabelController@store'])],
                                ['name'=>'修改标签','key'=>'userMangeLabelEdit','menu_type' => 'F','perms' => self::getController(['\User\UserLabelController@edit','\User\UserLabelController@update'])],
                                ['name'=>'标签详情','key'=>'userMangeLabelShow','menu_type' => 'F','perms' => self::getController('\User\UserLabelController@show')],
                                ['name'=>'删除标签','key'=>'userMangeLabelDestroy','menu_type' => 'F','perms' => self::getController('\User\UserLabelController@destroy')]
                            ]],
                    ]],
                    ['name'=>'积分管理','key'=>'userPoint','menu_type' => 'M','icon' => 'ri-copper-coin-line','is_cache'=>1,'children'=> [
                        ['name'=>'积分日志','key'=>'userPointIndex','menu_type' => 'C','component'=> '/user/point','path'=>'/user/point','is_cache'=>1,'perms' => self::getController('\User\UserPointLogController@index')],
                        ['name'=>'积分设置','key'=>'userPointSite','menu_type' => 'C','component'=> '/user/point/setting','path'=>'/user/point/setting','is_cache'=>1,'perms' => self::getController('\User\UserPointLogController@setting')],
                    ]],
                    ['name'=>'资产管理','key'=>'userBalance','menu_type' => 'M','icon' => 'ri-secure-payment-line','is_cache'=>1
                        ,'children'=> [
                            ['name'=>'变动日志','key'=>'userBalanceLog','menu_type' => 'C','component' => '/user/balance/log','path'=>'/user/balance','is_cache'=>1,'perms' => self::getController('\User\UserBalanceLogController@index')],
                            ['name'=>'提现管理','key'=>'userBalanceCash','menu_type' => 'M','is_cache'=>1
                                ,'children'=> [
                                    ['name'=>'提现列表','key'=>'userBalanceCashIndex','menu_type' => 'C','children'=> [
                                        ['name'=>'提现审核','key'=>'userBalanceCashEdit','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceCashController@edit','\User\UserBalanceCashController@update'])],
                                        ['name'=>'提现详情','key'=>'userBalanceCashInfo','menu_type' => 'F','perms' => self::getController('\User\UserBalanceCashController@show')],
                                        ['name'=>'删除记录','key'=>'userBalanceCashDestroy','menu_type' => 'F','perms' => self::getController('\User\UserBalanceCashController@destroy')],
                                        ['name'=>'回收站','key'=>'userBalanceCashRecovery','menu_type' => 'F','component' => '/user/balance/recovery','perms' => self::getController('\User\UserBalanceCashController@show')],
                                    ],'perms' => self::getController('\User\UserBalanceCashController@index'),'component'=>'/user/balance/cash','path'=>'/user/cash','is_cache'=>1],
                                    ['name'=>'提现设置','key'=>'userBalanceCashSite','menu_type' => 'C','perms' => self::getController('\User\UserBalanceCashController@setting'),'component'=>'/user/balance/cash/setting','path'=>'/user/cash/setting','is_cache'=>1],
                                ]
                            ],
                            ['name'=>'充值管理','key'=>'userBalanceRecharge','menu_type' => 'M','is_cache'=>1
                                ,'children'=> [
                                    ['name'=>'充值列表','key'=>'userBalanceRechargeIndex','menu_type' => 'C','children'=> [
                                        ['name'=>'充值更改','key'=>'userBalanceRechargeEdit','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceRechargeController@edit','\User\UserBalanceRechargeController@update'])],
                                        ['name'=>'提现详情','key'=>'userBalanceRechargeInfo','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeController@show')],
                                        ['name'=>'删除记录','key'=>'userBalanceRechargeDestroy','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeController@destroy')],
                                        ['name'=>'回收站','key'=>'userBalanceRechargeRecovery','menu_type' => 'F','component' => '/user/balance/recharge/recovery','path'=>'/user/recharge/recovery','perms' => self::getController(['\User\UserBalanceRechargeController@recovery','\User\UserBalanceRechargeController@restore','\User\UserBalanceRechargeController@erasure'])],
                                    ],'perms' => self::getController('\User\UserBalanceRechargeController@index'),'component'=>'/user/balance/recharge','path'=>'/user/recharges','is_cache'=>1],
                                    ['name'=>'充值套餐','key'=>'userBalanceRechargeMeal','menu_type' => 'C','children'=> [
                                        ['name'=>'新增套餐','key'=>'userBalanceRechargeMealCreate','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceRechargeMealController@create','\User\UserBalanceRechargeMealController@store'])],
                                        ['name'=>'修改套餐','key'=>'userBalanceRechargeMealEdit','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceRechargeMealController@edit','\User\UserBalanceRechargeMealController@update'])],
                                        ['name'=>'套餐详情','key'=>'userBalanceRechargeMealInfo','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeMealController@show')],
                                        ['name'=>'删除套餐','key'=>'userBalanceRechargeMealDestroy','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeMealController@destroy')],
                                        ['name'=>'回收站','key'=>'userBalanceRechargeMealRecovery','menu_type' => 'F','component' => '/user/balance/meal/recovery','path'=>'/user/recharge/recovery','perms' => self::getController(['\User\UserBalanceRechargeMealController@recovery','\User\UserBalanceRechargeMealController@restore','\User\UserBalanceRechargeMealController@erasure'])],
                                    ],'perms' => self::getController('\User\UserBalanceRechargeMealController@index'),'component'=>'/user/balance/meal','path'=>'/user/recharge/meal','is_cache'=>1]
                            ]],
                        ]
                    ],
                    ['name'=>'实名认证','key'=>'userReal','menu_type' => 'M','icon' => 'ri-shield-user-line','children'=> [
                        ['name'=>'申请记录','key'=>'userRealIndex','menu_type' => 'C','component'=> '/user/real','path'=>'/user/real','is_cache'=>1,'perms' => self::getController('\User\UserRealnameController@index')],
                        ['name'=>'实名审核','key'=>'userRealEdit','menu_type' => 'F','perms' => self::getController(['\User\UserRealnameController@edit','\User\UserRealnameController@update'])],
                        ['name'=>'实名设置','key'=>'userRealSite','menu_type' => 'C','component'=> '/user/real/setting','path'=>'/user/real/setting','is_cache'=>1,'perms' => self::getController('\User\UserRealnameController@setting')],
                    ]],
                    ['name'=>'等级成长','key'=>'userRights','menu_type' => 'M','icon' => 'ri-vip-crown-line','is_cache'=>1,'children'=> [
                        ['name'=>'等级管理','key'=>'userRightsGrade','menu_type' => 'M','is_cache'=>1
                            ,'children' => [
                                ['name'=>'会员组别','key'=>'userRightsGroup','menu_type' => 'C','component'=> '/user/rights/group','path'=>'/user/grade/group','is_cache'=>1,'children' =>[
                                    ['name'=>'新增组别','key'=>'userRightsGroupCreate','menu_type' => 'F','perms' => self::getController(['\User\UserGradeGroupController@create','\User\UserGradeGroupController@store'])],
                                    ['name'=>'修改组别','key'=>'userRightsGroupEdit','menu_type' => 'F','perms' => self::getController(['\User\UserGradeGroupController@edit','\User\UserGradeGroupController@update'])],
                                    ['name'=>'组别详情','key'=>'userRightsGroupShow','menu_type' => 'F','perms' => self::getController('\User\UserGradeGroupController@show')],
                                    ['name'=>'删除组别','key'=>'userRightsGroupDestroy','menu_type' => 'F','perms' => self::getController('\User\UserGradeGroupController@destroy')],
                                    ['name'=>'组别回收站','key'=>'userRightsGroupRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/rights/group/recovery','path'=>'/user/grade/group','perms' => self::getController('\User\UserGradeGroupController@recovery')],
                                    ['name'=>'等级管理','key'=>'userRightsGroupGrade','menu_type' => 'C','visible' => '0','component' =>'/user/rights/grade/[id]','path'=>'/user/grades','perms' => self::getController('\User\UserGradeController@index'),'children' =>[
                                        ['name'=>'新增等级','key'=>'userRightsGradeCreate','menu_type' => 'F','perms' => self::getController(['\User\UserGradeController@create','\User\UserGradeController@store'])],
                                        ['name'=>'修改等级','key'=>'userRightsGradeEdit','menu_type' => 'F','perms' => self::getController(['\User\UserGradeController@edit','\User\UserGradeController@update'])],
                                        ['name'=>'等级详情','key'=>'userRightsGradeShow','menu_type' => 'F','perms' => self::getController('\User\UserGradeController@show')],
                                        ['name'=>'删除等级','key'=>'userRightsGradeDestroy','menu_type' => 'F','perms' => self::getController('\User\UserGradeController@destroy')],
                                        ['name'=>'等级回收站','key'=>'userRightsGradeRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/rights/grade/recovery','path'=>'/user/grades/recovery','perms' => self::getController(['\User\UserGradeController@recovery','\User\UserGradeController@restore','\User\UserGradeController@erasure'])]
                                    ]]
                                ],'perms' => self::getController('\User\UserGradeGroupController@index')],
                                ['name'=>'会员权益','key'=>'userGradeRights','menu_type' => 'C','component'=> '/user/rights','path'=>'/user/grade/rights','is_cache'=>1,'children' =>[
                                    ['name'=>'新增权益','key'=>'userGradeRightsCreate','menu_type' => 'F','perms' => self::getController(['\User\UserGradeRightsController@create','\User\UserGradeRightsController@store'])],
                                    ['name'=>'修改权益','key'=>'userGradeRightsEdit','menu_type' => 'F','perms' => self::getController(['\User\UserGradeRightsController@edit','\User\UserGradeRightsController@update'])],
                                    ['name'=>'权益详情','key'=>'userGradeRightsShow','menu_type' => 'F','perms' => self::getController('\User\UserGradeRightsController@show')],
                                    ['name'=>'删除权益','key'=>'userGradeRightsDestroy','menu_type' => 'F','perms' => self::getController('\User\UserGradeRightsController@destroy')]
                                ],'perms' => self::getController('\User\UserGradeRightsController@index')],
                        ]],
                        ['name'=>'成长值日志','key'=>'userRightsGrowth','menu_type' => 'C','component'=> '/user/rights/growth','path'=>'/user/growth','is_cache'=>1,'perms' => self::getController('\User\UserGrowthLogController@index')],
                        ['name'=>'等级设置','key'=>'userRightsSite','menu_type' => 'C','component'=> '/user/rights/setting','path'=>'/user/growth/setting','is_cache'=>1,'perms' => self::getController('\User\UserGrowthLogController@setting')],
                    ]],
                    ['name'=>'会员中心菜单','key'=>'userMenu','menu_type' => 'C','icon'=>'ri-menu-line','component'=> '/user/menu','path'=>'/user/menu','is_cache'=>1,'perms'=> self::getController('\User\UserMenuController@index'),'children' =>[
                        ['name'=>'新增菜单','key'=>'userMenuCreate','menu_type' => 'F','perms' => self::getController(['\User\UserMenuController@create','\User\UserMenuController@store'])],
                        ['name'=>'修改菜单','key'=>'userMenuEdit','menu_type' => 'F','perms' => self::getController(['\User\UserMenuController@edit','\User\UserMenuController@update'])],
                        ['name'=>'菜单详情','key'=>'userMenuShow','menu_type' => 'F','perms' => self::getController('\User\UserMenuController@show')],
                        ['name'=>'删除菜单','key'=>'userMenuDestroy','menu_type' => 'F','perms' => self::getController('\User\UserMenuController@destroy')],
                        ['name'=>'菜单回收站','key'=>'userMenuRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/menu/recovery','path'=>'/user/menu/recovery','perms' => self::getController(['\User\UserMenuController@recovery','\User\UserMenuController@restore','\User\UserMenuController@erasure'])]
                    ]],
                ]
            ]
        ];
    }
}