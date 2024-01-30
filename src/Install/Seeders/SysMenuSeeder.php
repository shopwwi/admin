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
            $first = SysMenu::create($item);
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
            $first = SysMenu::create($item);
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
            ['name'=>'首页','id'=>'index','menu_type'=>'C','component'=>'/','path'=>'/','icon'=>'ri-home-smile-fill','is_cache'=>0],
            ['name'=>'设置','id'=>'setting','menu_type' => 'M','icon' => 'ri-settings-4-fill','is_cache'=>1
                ,'children' => [
                    ['name'=>'基础设置','id'=>'settingSite','menu_type' => 'M','icon' => 'ri-settings-2-line','is_cache'=>1
                        ,'children' => [
                            ['name'=>'站点设置','id'=>'settingSiteBase','menu_type' => 'M','is_cache'=>1 ,'children' => [
                                ['name'=>'基础设置','id'=>'settingSiteBaseSite','menu_type' => 'C','component' => '/setting/site/base','path'=>'/system/config/site','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@site')],
                                ['name'=>'规则设置','id'=>'settingSiteBaseRule','menu_type' => 'C','component' => '/setting/site/rule','path'=>'/system/config/rule','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@rule')],
                                ['name'=>'图片设置','id'=>'settingSiteBasePic','menu_type' => 'C','component' => '/setting/site/pic','path'=>'/system/config/pic','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@pic')],
                            ]],
                            ['name'=>'导航设置','id'=>'settingSiteNavigation','menu_type' => 'C','component'=>'/setting/site/navigation','path'=>'/system/navigation','is_cache'=>1,'children' => [
                                ['name'=>'导航新增','id'=>'settingSiteNavigationCreate','menu_type' => 'F','perms' => self::getController(['\System\SysNavigationController@create','\System\SysNavigationController@store'])],
                                ['name'=>'导航修改','id'=>'settingSiteNavigationEdit','menu_type' => 'F','perms' => self::getController(['\System\SysNavigationController@edit','\System\SysNavigationController@update'])],
                                ['name'=>'导航删除','id'=>'settingSiteNavigationDestroy','menu_type' => 'F','perms' => self::getController('\System\SysNavigationController@destroy')],
                            ],'perms' => self::getController(['\System\SysNavigationController@index','\System\SysNavigationController@show'])],
                            ['name'=>'地区设置','id'=>'settingSiteArea','menu_type' => 'C','component'=>'/setting/site/area','path'=>'/system/area','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'地区新增','id'=>'settingSiteAreaCreate','menu_type' => 'F','perms' => self::getController(['\System\SysAreaController@create','\System\SysAreaController@store'])],
                                    ['name'=>'地区修改','id'=>'settingSiteAreaEdit','menu_type' => 'F','perms' => self::getController(['\System\SysAreaController@edit','\System\SysAreaController@update'])],
                                    ['name'=>'地区删除','id'=>'settingSiteAreaDestroy','menu_type' => 'F','perms' => self::getController('\System\SysAreaController@destroy')],
                                    ['name'=>'地区回收站','id'=>'settingSiteAreaRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/area/recovery','path'=>'/system/area/recovery','perms' => self::getController(['\System\SysAreaController@recovery','\System\SysAreaController@restore','\System\SysAreaController@erasure'])]
                                ],'perms' => self::getController(['\System\SysAreaController@index','\System\SysAreaController@show'])
                            ],
                            ['name'=>'运费模板','id'=>'settingSiteFreight','menu_type' => 'C','component'=>'/setting/site/freight','path'=>'/system/freight','is_cache' => 1
                                ,'children' => [
                                    ['name'=>'模板新增','id'=>'settingSiteFreightCreate','menu_type' => 'F','perms' => self::getController(['\System\SysFreightTemplateController@create','\System\SysFreightTemplateController@store'])],
                                    ['name'=>'模板修改','id'=>'settingSiteFreightEdit','menu_type' => 'F','perms' => self::getController(['\System\SysFreightTemplateController@edit','\System\SysFreightTemplateController@update'])],
                                    ['name'=>'模板删除','id'=>'settingSiteFreightDestroy','menu_type' => 'F','perms' => self::getController('\System\SysFreightTemplateController@destroy')],
                                    ['name'=>'模板复制','id'=>'settingSiteFreightCopy','menu_type' => 'F','perms' => self::getController('\System\SysFreightTemplateController@copy')]
                                ],'perms' => self::getController(['\System\SysFreightTemplateController@index','\System\SysFreightTemplateController@show'])
                            ],
                            ['name'=>'物流设置','id'=>'settingSiteShip','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'物流公司','id'=>'settingSiteShipIndex','menu_type' => 'C','component' =>'/setting/site/ship','path'=>'/system/ship','is_cache'=>1,'perms' => self::getController('\System\SysShipCompanyController@index')],
                                    ['name'=>'物流新增','id'=>'settingSiteShipCreate','menu_type' => 'F','perms' => self::getController(['\System\SysShipCompanyController@create','\System\SysShipCompanyController@store'])],
                                    ['name'=>'物流修改','id'=>'settingSiteShipEdit','menu_type' => 'F','perms' => self::getController(['\System\SysShipCompanyController@edit','\System\SysShipCompanyController@update'])],
                                    ['name'=>'物流详情','id'=>'settingSiteShipShow','menu_type' => 'F','perms' => self::getController('\System\SysShipCompanyController@show')],
                                    ['name'=>'物流词删除','id'=>'settingSiteShipDestroy','menu_type' => 'F','perms' => self::getController('\System\SysShipCompanyController@destroy')],
                                    ['name'=>'物流回收站','id'=>'settingSiteShipRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/ship/recovery','path'=>'/system/ship/recovery','perms' => self::getController(['\System\SysShipCompanyController@recovery','\System\SysShipCompanyController@restore','\System\SysShipCompanyController@erasure'])],
                                    ['name'=>'物流设置','id'=>'settingSiteShipSite','menu_type' => 'C','component' =>'/setting/site/ship/site','path'=>'/system/ship/setting','is_cache'=>1,'perms' => self::getController('\System\SysShipCompanyController@setting')]
                                ]
                            ],
                            ['name'=>'消息模板','id'=>'settingSiteMsg','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'公共模板','id'=>'settingSiteMsgCommon','menu_type' => 'C','component' =>'/setting/site/msg/common','path'=>'/system/msg/common','is_cache'=>1,'children' => [
                                        ['name'=>'新增模板','id'=>'settingSiteMsgCommonCreate','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplCommonController@create','\System\SysMsgTplCommonController@store'])],
                                        ['name'=>'修改模板','id'=>'settingSiteMsgCommonEdit','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplCommonController@edit','\System\SysMsgTplCommonController@update'])],
                                        ['name'=>'删除配置','id'=>'settingSiteMsgCommonDestroy','menu_type' => 'F','perms' => self::getController('\System\SysMsgTplCommonController@destroy')],
                                        ['name'=>'配置回收站','id'=>'settingSiteMsgCommonRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/msg/common/recovery','path'=>'/system/msg/common/recovery','perms' => self::getController(['\System\SysMsgTplCommonController@recovery','\System\SysMsgTplCommonController@restore','\System\SysMsgTplCommonController@erasure'])]
                                    ],'perms' => self::getController(['\System\SysMsgTplCommonController@index','\System\SysMsgTplCommonController@show'])],
                                    ['name'=>'消息模板','id'=>'settingSiteMsgSystem','menu_type' => 'C','component' =>'/setting/site/msg/system','path'=>'/system/msg/system','is_cache'=>1,'children' => [
                                        ['name'=>'新增配置','id'=>'settingSiteMsgSystemCreate','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplSystemController@create','\System\SysMsgTplSystemController@store'])],
                                        ['name'=>'修改配置','id'=>'settingSiteMsgSystemEdit','menu_type' => 'F','perms' => self::getController(['\System\SysMsgTplSystemController@edit','\System\SysMsgTplSystemController@update'])],
                                        ['name'=>'删除配置','id'=>'settingSiteMsgSystemDestroy','menu_type' => 'F','perms' => self::getController('\System\SysMsgTplSystemController@destroy')],
                                        ['name'=>'配置回收站','id'=>'settingSiteMsgSystemRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/msg/system/recovery','path'=>'/system/msg/system/recovery','perms' => self::getController(['\System\SysMsgTplSystemController@recovery','\System\SysMsgTplSystemController@restore','\System\SysMsgTplSystemController@erasure'])]
                                    ],'perms' => self::getController(['\System\SysMsgTplSystemController@index','\System\SysMsgTplSystemController@show'])]
                                ]
                            ],
                            ['name'=>'附件管理','id'=>'settingSiteAlbum','menu_type' => 'M','is_cache'=>1
                                    ,'children' => [
                                        ['name'=>'附件列表','id'=>'settingSiteAlbumIndex','menu_type' => 'C','component' =>'/setting/site/album','path'=>'/system/files','perms' => self::getController(['\System\SysAlbumController@index','\System\SysAlbumFilesController@index'])],
                                        ['name'=>'新增相册','id'=>'settingSiteAlbumCreate','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumController@create','\System\SysAlbumController@store'])],
                                        ['name'=>'修改相册','id'=>'settingSiteAlbumEdit','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumController@edit','\System\SysAlbumController@update'])],
                                        ['name'=>'相册详情','id'=>'settingSiteAlbumShow','menu_type' => 'F','perms' => self::getController('\System\SysShipCompanyController@show')],
                                        ['name'=>'上传图片','id'=>'settingSiteAlbumFileStore','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumFilesController@store','\System\SysAlbumFilesController@edit'])],
                                        ['name'=>'删除操作','id'=>'settingSiteAlbumDestroy','menu_type' => 'F','perms' => self::getController(['\System\SysAlbumFilesController@destroy','\System\SysAlbumController@destroy'])],
                                        ['name'=>'相册回收站','id'=>'settingSiteAlbumRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/site/album/recovery','path'=>'/system/files/recovery','perms' => self::getController(['\System\SysAlbumFilesController@recovery','\System\SysAlbumFilesController@restore','\System\SysAlbumFilesController@erasure'])],
                                        ['name'=>'附件设置','id'=>'settingSiteAlbumSite','menu_type' => 'C','component' =>'/setting/site/album/site','path'=>'/system/album/setting','is_cache'=>1,'perms' => self::getController('\System\SysAlbumController@setting')]
                                ]
                            ],
                            ['name'=>'敏感词','id'=>'settingSiteSensitives','menu_type' => 'C','component' =>'/setting/site/sensitives','path'=>'/system/sensitives','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'敏感词新增','id'=>'settingSiteSensitivesCreate','menu_type' => 'F','perms' => self::getController(['\System\SysSensitivesController@create','\System\SysSensitivesController@store'])],
                                    ['name'=>'敏感词修改','id'=>'settingSiteSensitivesEdit','menu_type' => 'F','perms' => self::getController(['\System\SysSensitivesController@edit','\System\SysSensitivesController@update'])],
                                    ['name'=>'敏感词详情','id'=>'settingSiteSensitivesShow','menu_type' => 'F','perms' => self::getController('\System\SysSensitivesController@show')],
                                    ['name'=>'敏感词删除','id'=>'settingSiteSensitivesDestroy','menu_type' => 'F','perms' => self::getController('\System\SysSensitivesController@destroy')]
                                ],'perms' => self::getController('\System\SysSensitivesController@index')
                            ],
                        ]
                    ],
                    ['name'=>'系统设置','id'=>'settingSystem','menu_type' => 'M','icon'=>'ri-list-settings-line'
                        ,'children' => [
                            ['name'=>'权限管理','id'=>'settingSystemPower','menu_type' => 'M'
                                ,'children' => [
                                    ['name'=>'用户管理','id'=>'settingSystemPowerUser','menu_type' => 'C','component' =>'/setting/system/user','path'=>'/system/user','is_cache'=>1,'perms' => self::getController('\System\SysUserController@index')
                                        ,'children' => [
                                            ['name'=>'新增用户','id'=>'settingSystemPowerUserCreate','menu_type' => 'F','perms' => self::getController(['\System\SysRoleController@create','\System\SysUserController@store'])],
                                            ['name'=>'修改用户','id'=>'settingSystemPowerUserEdit','menu_type' => 'F','perms' => self::getController(['\System\SysUserController@edit','\System\SysUserController@update'])],
                                            ['name'=>'用户详情','id'=>'settingSystemPowerUserShow','menu_type' => 'F','perms' => self::getController('\System\SysUserController@show')],
                                            ['name'=>'删除用户','id'=>'settingSystemPowerUserDestroy','menu_type' => 'F','perms' => self::getController('\System\SysUserController@destroy')],
                                            ['name'=>'用户回收站','id'=>'settingSystemPowerRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/user/recovery','path'=>'/system/user/recovery','perms' => self::getController(['\System\SysUserController@recovery','\System\SysUserController@restore','\System\SysUserController@erasure'])]
                                        ]
                                    ],
                                    ['name'=>'角色管理','id'=>'settingSystemPowerRole','menu_type' => 'C','component' =>'/setting/system/role','path'=>'/system/role','is_cache'=>1,'perms' => self::getController('\System\SysRoleController@index')
                                        ,'children' => [
                                            ['name'=>'新增角色','id'=>'settingSystemPowerRoleCreate','menu_type' => 'F','perms' => self::getController(['\System\SysRoleController@create','\System\SysRoleController@store'])],
                                            ['name'=>'修改角色','id'=>'settingSystemPowerRoleEdit','menu_type' => 'F','perms' => self::getController(['\System\SysRoleController@edit','\System\SysRoleController@update'])],
                                            ['name'=>'角色详情','id'=>'settingSystemPowerRoleShow','menu_type' => 'F','perms' => self::getController('\System\SysRoleController@show')],
                                            ['name'=>'删除角色','id'=>'settingSystemPowerRoleDestroy','menu_type' => 'F','perms' => self::getController('\System\SysRoleController@destroy')],
                                            ['name'=>'角色回收站','id'=>'settingSystemPowerRoleRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/role/recovery','path'=>'/system/role/recovery','perms' => self::getController(['\System\SysRoleController@recovery','\System\SysRoleController@restore','\System\SysRoleController@erasure'])]
                                        ]
                                    ],
                                    ['name'=>'部门管理','id'=>'settingSystemPowerSector','menu_type' => 'C','component' =>'/setting/system/sector','path'=>'/system/sector','is_cache'=>1,'perms' => self::getController('\System\SysUserSectorController@index')
                                        ,'children' => [
                                            ['name'=>'新增角色','id'=>'settingSystemPowerSectorCreate','menu_type' => 'F','perms' => self::getController(['\System\SysUserSectorController@create','\System\SysUserSectorController@store'])],
                                            ['name'=>'修改角色','id'=>'settingSystemPowerSectorEdit','menu_type' => 'F','perms' => self::getController(['\System\SysUserSectorController@edit','\System\SysUserSectorController@update'])],
                                            ['name'=>'角色详情','id'=>'settingSystemPowerSectorShow','menu_type' => 'F','perms' => self::getController('\System\SysUserSectorController@show')],
                                            ['name'=>'删除角色','id'=>'settingSystemPowerSectorDestroy','menu_type' => 'F','perms' => self::getController('\System\SysUserSectorController@destroy')],
                                            ['name'=>'角色回收站','id'=>'settingSystemPowerSectorRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/sector/recovery','path'=>'/system/sector/recovery','perms' => self::getController(['\System\SysUserSectorController@recovery','\System\SysUserSectorController@restore','\System\SysUserSectorController@erasure'])]
                                        ]
                                    ],
                                    ['name'=>'操作日志','id'=>'settingSystemPowerLog','menu_type' => 'C','component' =>'/setting/system/log','path'=>'/system/log','is_cache'=>1,'perms' => self::getController('\System\SysOperLogController@index')
                                        ,'children' => [
                                            ['name'=>'清理日志','id'=>'settingSystemPowerLogDestroy','menu_type' => 'F','perms' => self::getController('\System\SysOperLogController@destroy')],
                                        ]
                                    ],
                                ]
                            ],
                            ['name'=>'系统菜单','id'=>'settingSystemMenu','menu_type' => 'C','component' =>'/setting/system/menu','path'=>'/system/menu','is_cache'=>1,'perms' => self::getController('\System\SysMenuController@index')
                                ,'children' => [
                                    ['name'=>'新增角色','id'=>'settingSystemMenuCreate','menu_type' => 'F','perms' => self::getController(['\System\SysMenuController@create','\System\SysMenuController@store'])],
                                    ['name'=>'修改角色','id'=>'settingSystemMenuEdit','menu_type' => 'F','perms' => self::getController(['\System\SysMenuController@edit','\System\SysMenuController@update'])],
                                    ['name'=>'角色详情','id'=>'settingSystemMenuShow','menu_type' => 'F','perms' => self::getController('\System\SysMenuController@show')],
                                    ['name'=>'删除角色','id'=>'settingSystemMenuDestroy','menu_type' => 'F','perms' => self::getController('\System\SysMenuController@destroy')]
                                ]
                            ],
                            ['name'=>'系统配置','id'=>'settingSystemConfig','menu_type' => 'C','component' =>'/setting/system/config','path'=>'/system/config','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@index')
                                ,'children' => [
                                    ['name'=>'新增配置','id'=>'settingSystemConfigCreate','menu_type' => 'F','perms' => self::getController(['\System\SysConfigController@create','\System\SysConfigController@store'])],
                                    ['name'=>'修改配置','id'=>'settingSystemConfigEdit','menu_type' => 'F','perms' => self::getController(['\System\SysConfigController@edit','\System\SysConfigController@update'])],
                                    ['name'=>'配置详情','id'=>'settingSystemConfigShow','menu_type' => 'F','perms' => self::getController('\System\SysConfigController@show')],
                                    ['name'=>'删除配置','id'=>'settingSystemConfigDestroy','menu_type' => 'F','perms' => self::getController('\System\SysConfigController@destroy')],
                                    ['name'=>'配置回收站','id'=>'settingSystemConfigRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/system/config/recovery','path'=>'/system/config/recovery','perms' => self::getController(['\System\SysConfigController@recovery','\System\SysConfigController@restore','\System\SysConfigController@erasure'])]
                                ]
                            ],
                            ['name'=>'数据字典','id'=>'settingSystemDict','menu_type' => 'C','component' =>'/setting/system/dict','path'=>'/system/dict','is_cache'=>1,'perms' => self::getController('\System\SysDictTypeController@index')
                                ,'children' => [
                                    ['name'=>'新增字典','id'=>'settingSystemDictCreate','menu_type' => 'F','perms' => self::getController(['\System\SysDictTypeController@create','\System\SysDictTypeController@store'])],
                                    ['name'=>'修改字典','id'=>'settingSystemDictEdit','menu_type' => 'F','perms' => self::getController(['\System\SysDictTypeController@edit','\System\SysDictTypeController@update'])],
                                    ['name'=>'字典详情','id'=>'settingSystemDictShow','menu_type' => 'C','visible' => '0','component' =>'/setting/system/dict/[name]','path'=>'/system/dictdata','perms' => self::getController('\System\SysDictDataController@index')],
                                    ['name'=>'删除字典','id'=>'settingSystemDictDestroy','menu_type' => 'F','perms' => self::getController('\System\SysDictTypeController@destroy')],
                                    ['name'=>'新增字典数据','id'=>'settingSystemDictDataCreate','menu_type' => 'F','perms' => self::getController(['\System\SysDictDataController@create','\System\SysDictDataController@store'])],
                                    ['name'=>'修改字典数据','id'=>'settingSystemDictDataEdit','menu_type' => 'F','perms' => self::getController(['\System\SysDictDataController@edit','\System\SysDictDataController@update'])],
                                    ['name'=>'字典数据详情','id'=>'settingSystemDictDataShow','menu_type' => 'F','perms' => self::getController('\System\SysDictDataController@show')],
                                    ['name'=>'删除字典数据','id'=>'settingSystemDictDataDestroy','menu_type' => 'F','perms' => self::getController('\System\SysDictDataController@destroy')],
                                ]
                            ],
                            ['name'=>'开发者工具','id'=>'settingSystemTools','menu_type' => 'C','component' =>'/setting/system/tools','path'=>'/system/tools','is_cache'=>1,'perms' => self::getController('\System\GenTableController@index')
                                ,'children' => [
                                    ['name'=>'导入数据表','id'=>'settingSystemToolsCreate','menu_type' => 'F','perms' => self::getController(['\System\GenTableController@create','\System\GenTableController@store'])],
                                    ['name'=>'修改配置','id'=>'settingSystemToolsEdit','menu_type' => 'F','component' => '/setting/system/tools/[id]','visible' => '0','perms' => self::getController(['\System\GenTableController@edit','\System\GenTableController@update'])],
                                    ['name'=>'查看详情','id'=>'settingSystemToolsShow','menu_type' => 'F','perms' => self::getController('\System\GenTableController@show')],
                                    ['name'=>'删除配置','id'=>'settingSystemToolsDestroy','menu_type' => 'F','perms' => self::getController('\System\GenTableController@destroy')]
                                ]
                            ],
                            ['name'=>'缓存管理','id'=>'settingSystemCache','menu_type' => 'C','component' =>'/setting/system/cache','path'=>'/system/cache','is_cache'=>1,'perms' => self::getController('\System\SysCacheController@index')
                                ,'children' => [
                                    ['name'=>'新增缓存','id'=>'settingSystemCacheCreate','menu_type' => 'F','perms' => self::getController(['\System\SysCacheController@create','\System\SysCacheController@store'])],
                                    ['name'=>'修改缓存','id'=>'settingSystemCacheEdit','menu_type' => 'F','perms' => self::getController(['\System\SysCacheController@edit','\System\SysCacheController@update'])],
                                    ['name'=>'查看缓存','id'=>'settingSystemCacheShow','menu_type' => 'F','perms' => self::getController('\System\SysCacheController@show')],
                                    ['name'=>'清理缓存','id'=>'settingSystemCacheDestroy','menu_type' => 'F','perms' => self::getController('\System\SysCacheController@destroy')],
                                ]
                            ]
                        ]
                    ],
                    ['name'=>'第三方','id'=>'settingWay','menu_type' => 'M','icon'=>'ri-typhoon-line','is_cache'=>1
                        ,'children' => [
                            ['name'=>'邮件配置','id'=>'settingWayEmail','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'邮件记录','id'=>'settingWayEmailIndex','menu_type' => 'C','component' =>'/setting/way/email','path'=>'/system/email','is_cache'=>1,'perms' => self::getController(['\System\SysEmailCodeController@index','\System\SysEmailCodeController@show'])],
                                    ['name'=>'邮件设置','id'=>'settingWayEmailSite','menu_type' => 'C','component' =>'/setting/way/email/setting','path'=>'/system/email/setting','is_cache'=>1,'perms' => self::getController(['\System\SysSmsCodeController@setting'])],
                                    ['name'=>'删除记录','id'=>'settingWayEmailDestroy','menu_type' => 'F','perms' => self::getController(['\System\SysEmailCodeController@destroy'])],
                                ]
                            ],
                            ['name'=>'短信配置','id'=>'settingWaySms','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'短信记录','id'=>'settingWaySmsIndex','menu_type' => 'C','component' =>'/setting/way/sms','path'=>'/system/sms','is_cache'=>1,'perms' => self::getController(['\System\SysSmsCodeController@index','\System\SysSmsCodeController@show'])],
                                    ['name'=>'短信设置','id'=>'settingWaySmsSite','menu_type' => 'C','component' =>'/setting/way/sms/setting','path'=>'/system/sms/setting','is_cache'=>1,'perms' => self::getController('\System\SysSmsCodeController@setting')],
                                    ['name'=>'删除记录','id'=>'settingWaySmsDestroy','menu_type' => 'F','perms' => self::getController('\System\SysSmsCodeController@destroy')],
                                ]
                            ],
                            ['name'=>'登入配置','id'=>'settingWayAuth','menu_type' => 'C','component' =>'/setting/way/auth','path'=>'/system/config/auth','is_cache'=>1,'perms' => self::getController('\System\SysConfigController@auth')],
                            ['name'=>'支付管理','id'=>'settingWayPay','menu_type' => 'M','is_cache'=>1
                                ,'children' => [
                                    ['name'=>'付款日志','id'=>'settingWayPayIndex','menu_type' => 'C','component' =>'/setting/way/pay','path'=>'/system/pay','is_cache'=>1,'perms' => self::getController(['\System\SysPayController@index']),'children' => [
                                        ['name'=>'修改状态','id'=>'settingWayPayEdit','menu_type' => 'F','perms' => self::getController(['\System\SysPayController@edit','\System\SysPayController@update'])],
                                        ['name'=>'查看详情','id'=>'settingWayPayShow','menu_type' => 'F','perms' => self::getController(['\System\SysPayController@show'])]
                                    ]],
                                    ['name'=>'支付方式','id'=>'settingWayPayPayment','menu_type' => 'C','component' =>'/setting/way/pay/payment','path'=>'/system/payment','is_cache'=>1,'perms' => self::getController(['\System\SysPaymentController@index']),'children' => [
                                        ['name'=>'新增支付方式','id'=>'settingWayPayPaymentCreate','menu_type' => 'F','perms' => self::getController(['\System\SysPaymentController@create','\System\SysPaymentController@store'])],
                                        ['name'=>'修改支付方式','id'=>'settingWayPayPaymentEdit','menu_type' => 'F','perms' => self::getController(['\System\SysPaymentController@edit','\System\SysPaymentController@update'])],
                                        ['name'=>'查看详情','id'=>'settingWayPayPaymentShow','menu_type' => 'F','perms' => self::getController('\System\SysPaymentController@show')],
                                        ['name'=>'删除支付方式','id'=>'settingWayPayPaymentDestroy','menu_type' => 'F','perms' => self::getController('\System\SysPaymentController@destroy')]
                                    ]],
                                ]
                            ],
                        ]
                    ],
                    ['name'=>'内容管理','id'=>'settingContent','menu_type' => 'M','icon'=>'ri-meteor-line','is_cache'=>1,'children' => [
                        ['name'=>'文章管理','id'=>'settingContentArticle','menu_type' => 'M','is_cache'=>1,'children' => [
                            ['name'=>'文章列表','id'=>'settingContentArticleIndex','menu_type' => 'C','component' =>'/setting/content/article','path'=>'/system/articles','is_cache'=>1,'children' => [
                                ['name'=>'新增文章','id'=>'settingContentArticleCreate','menu_type' => 'F','perms' => self::getController(['\System\SysArticleController@create','\System\SysArticleController@store'])],
                                ['name'=>'修改文章','id'=>'settingContentArticleEdit','menu_type' => 'F','perms' => self::getController(['\System\SysArticleController@edit','\System\SysArticleController@update'])],
                                ['name'=>'文章详情','id'=>'settingContentArticleShow','menu_type' => 'F','perms' => self::getController('\System\SysArticleController@show')],
                                ['name'=>'删除文章','id'=>'settingContentArticleDestroy','menu_type' => 'F','perms' => self::getController('\System\SysArticleController@destroy')],
                                ['name'=>'文章回收站','id'=>'settingContentArticleRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/content/article/recovery','path'=>'/system/articles/recovery','perms' => self::getController(['\System\SysArticleController@recovery','\System\SysArticleController@restore','\System\SysArticleController@erasure'])]
                            ],'perms' => self::getController('\System\SysArticleController@index')],
                            ['name'=>'文章分类','id'=>'settingContentArticleClass','menu_type' => 'C','component' =>'/setting/content/article/class','path'=>'/system/article/class','is_cache'=>1,'children' => [
                                ['name'=>'新增分类','id'=>'settingContentArticleClassCreate','menu_type' => 'F','perms' => self::getController(['\System\SysArticleClassController@create','\System\SysArticleClassController@store'])],
                                ['name'=>'修改分类','id'=>'settingContentArticleClassEdit','menu_type' => 'F','perms' => self::getController(['\System\SysArticleClassController@edit','\System\SysArticleClassController@update'])],
                                ['name'=>'分类详情','id'=>'settingContentArticleClassShow','menu_type' => 'F','perms' => self::getController('\System\SysArticleClassController@show')],
                                ['name'=>'删除分类','id'=>'settingContentArticleClassDestroy','menu_type' => 'F','perms' => self::getController('\System\SysArticleClassController@destroy')],
                                ['name'=>'分类回收站','id'=>'settingContentArticleClassRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/content/article/class/recovery','path'=>'/system/article/class/recovery','perms' => self::getController(['\System\SysArticleClassController@recovery','\System\SysArticleClassController@restore','\System\SysArticleClassController@erasure'])]
                            ],'perms' => self::getController('\System\SysArticleClassController@index')]
                        ]],
                        ['name'=>'帮助中心','id'=>'settingContentHelp','menu_type' => 'M','children' => [
                            ['name'=>'帮助列表','id'=>'settingContentHelpIndex','menu_type' => 'C','component' =>'/setting/content/help','path'=>'/system/helps','is_cache'=>1,'children' => [
                                ['name'=>'新增帮助','id'=>'settingContentHelpCreate','menu_type' => 'F','perms' => self::getController(['\System\SysHelpController@create','\System\SysHelpController@store'])],
                                ['name'=>'修改帮助','id'=>'settingContentHelpEdit','menu_type' => 'F','perms' => self::getController(['\System\SysHelpController@edit','\System\SysHelpController@update'])],
                                ['name'=>'帮助详情','id'=>'settingContentHelpShow','menu_type' => 'F','perms' => self::getController('\System\SysHelpController@show')],
                                ['name'=>'删除帮助','id'=>'settingContentHelpDestroy','menu_type' => 'F','perms' => self::getController('\System\SysHelpController@destroy')],
                                ['name'=>'帮助回收站','id'=>'settingContentHelpRecovery','menu_type' => 'C','visible' => '0','component' =>'/setting/content/help/recovery','path'=>'/system/helps/recovery','perms' => self::getController(['\System\SysHelpController@recovery','\System\SysHelpController@restore','\System\SysHelpController@erasure'])]
                            ],'perms' => self::getController('\System\SysHelpController@index')],
                            ['name'=>'帮助分类','id'=>'settingContentHelpClass','menu_type' => 'C','component' =>'/setting/content/help/class','path'=>'/system/help/class','is_cache'=>1,'children' => [
                                ['name'=>'新增分类','id'=>'settingContentHelpClassCreate','menu_type' => 'F','perms' => self::getController(['\System\SysHelpClassController@create','\System\SysHelpClassController@store'])],
                                ['name'=>'修改分类','id'=>'settingContentHelpClassEdit','menu_type' => 'F','perms' => self::getController(['\System\SysHelpClassController@edit','\System\SysHelpClassController@update'])],
                                ['name'=>'分类详情','id'=>'settingContentHelpClassShow','menu_type' => 'F','perms' => self::getController('\System\SysHelpClassController@show')],
                                ['name'=>'删除分类','id'=>'settingContentHelpClassDestroy','menu_type' => 'F','perms' => self::getController('\System\SysHelpClassController@destroy')]
                            ],'perms' => self::getController('\System\SysHelpClassController@index')]
                        ]],
                        ['name'=>'系统公告','id'=>'settingContentNotice','menu_type' => 'C','children' => [
                            ['name'=>'新增公告','id'=>'settingContentNoticeCreate','menu_type' => 'F','perms' => self::getController(['\System\SysNoticeController@create','\System\SysNoticeController@store'])],
                            ['name'=>'修改公告','id'=>'settingContentNoticeEdit','menu_type' => 'F','perms' => self::getController(['\System\SysNoticeController@edit','\System\SysNoticeController@update'])],
                            ['name'=>'公告详情','id'=>'settingContentNoticeShow','menu_type' => 'F','perms' => self::getController('\System\SysNoticeController@show')],
                            ['name'=>'删除公告','id'=>'settingContentNoticeDestroy','menu_type' => 'F','perms' => self::getController('\System\SysNoticeController@destroy')]
                        ],'perms' => self::getController('\System\SysNoticeController@index'),'component' => '/setting/content/notice','path'=>'/system/notice','is_cache'=>1],
                        ['name'=>'友情链接','id'=>'settingContentLink','menu_type' => 'C','children' => [
                            ['name'=>'新增链接','id'=>'settingContentLinkCreate','menu_type' => 'F','perms' => self::getController(['\System\SysLinkController@create','\System\SysLinkController@store'])],
                            ['name'=>'修改链接','id'=>'settingContentLinkEdit','menu_type' => 'F','perms' => self::getController(['\System\SysLinkController@edit','\System\SysLinkController@update'])],
                            ['name'=>'链接详情','id'=>'settingContentLinkShow','menu_type' => 'F','perms' => self::getController('\System\SysLinkController@show')],
                            ['name'=>'删除公告','id'=>'settingContentLinkDestroy','menu_type' => 'F','perms' => self::getController('\System\SysLinkController@destroy')]
                        ],'perms' => self::getController('\System\SysLinkController@index'),'component' => '/setting/content/link','path'=>'/system/link','is_cache'=>1]
                    ]]
                ]
            ],
            ['name'=>'会员','id'=>'user','menu_type' => 'M','icon' => 'ri-user-settings-fill','is_cache'=>1
                ,'children' => [
                    ['name'=>'会员管理','id'=>'userMange','menu_type' => 'M','icon' => 'ri-user-star-line','is_cache'=>1
                        ,'children'=> [
                            ['name'=>'会员统计','id'=>'userMangeStat','menu_type' => 'C','component'=> '/user/mange/stat','path'=>'/users/stat','is_cache'=>1,'perms'=>self::getController('\User\UsersController@stat')],
                            ['name'=>'会员列表','id'=>'userMangeList','menu_type' => 'C','component'=> '/user/mange/users','path'=>'/users','is_cache'=>1,'perms'=>self::getController('\User\UsersController@index'),'children' =>[
                                ['name'=>'新增会员','id'=>'userMangeListCreate','menu_type' => 'F','perms' => self::getController(['\User\UsersController@create','\User\UsersController@store'])],
                                ['name'=>'修改会员','id'=>'userMangeListEdit','menu_type' => 'F','perms' => self::getController(['\User\UsersController@edit','\User\UsersController@update'])],
                                ['name'=>'会员详情','id'=>'userMangeListShow','menu_type' => 'C','visible' => '0','component' =>'/user/mange/users/[id]','path'=>'/user/users/{id}','perms' => self::getController('\User\UsersController@show')],
                                ['name'=>'删除会员','id'=>'userMangeListDestroy','menu_type' => 'F','perms' => self::getController('\User\UsersController@destroy')],
                                ['name'=>'会员回收站','id'=>'userMangeListRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/mange/users/recovery','path'=>'/users/recovery','perms' => self::getController(['\User\UsersController@recovery','\User\UsersController@restore','\User\UsersController@erasure'])]
                            ]],
                            ['name'=>'会员标签','id'=>'userMangeLabel','menu_type' => 'C','component'=> '/user/mange/label','path'=>'/user/label','is_cache'=>1,'perms'=> self::getController('\User\UserLabelController@index'),'children' =>[
                                ['name'=>'新增标签','id'=>'userMangeLabelCreate','menu_type' => 'F','perms' => self::getController(['\User\UserLabelController@create','\User\UserLabelController@store'])],
                                ['name'=>'修改标签','id'=>'userMangeLabelEdit','menu_type' => 'F','perms' => self::getController(['\User\UserLabelController@edit','\User\UserLabelController@update'])],
                                ['name'=>'标签详情','id'=>'userMangeLabelShow','menu_type' => 'F','perms' => self::getController('\User\UserLabelController@show')],
                                ['name'=>'删除标签','id'=>'userMangeLabelDestroy','menu_type' => 'F','perms' => self::getController('\User\UserLabelController@destroy')]
                            ]],
                    ]],
                    ['name'=>'积分管理','id'=>'userPoint','menu_type' => 'M','icon' => 'ri-copper-coin-line','is_cache'=>1,'children'=> [
                        ['name'=>'积分日志','id'=>'userPointIndex','menu_type' => 'C','component'=> '/user/point','path'=>'/user/point','is_cache'=>1,'perms' => self::getController('\User\UserPointLogController@index')],
                        ['name'=>'积分设置','id'=>'userPointSite','menu_type' => 'C','component'=> '/user/point/setting','path'=>'/user/point/setting','is_cache'=>1,'perms' => self::getController('\User\UserPointLogController@setting')],
                    ]],
                    ['name'=>'资产管理','id'=>'userBalance','menu_type' => 'M','icon' => 'ri-secure-payment-line','is_cache'=>1
                        ,'children'=> [
                            ['name'=>'变动日志','id'=>'userBalanceLog','menu_type' => 'C','component' => '/user/balance/log','path'=>'/user/balance','is_cache'=>1,'perms' => self::getController('\User\UserBalanceLogController@index')],
                            ['name'=>'提现管理','id'=>'userBalanceCash','menu_type' => 'M','is_cache'=>1
                                ,'children'=> [
                                    ['name'=>'提现列表','id'=>'userBalanceCashIndex','menu_type' => 'C','children'=> [
                                        ['name'=>'提现审核','id'=>'userBalanceCashEdit','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceCashController@edit','\User\UserBalanceCashController@update'])],
                                        ['name'=>'提现详情','id'=>'userBalanceCashInfo','menu_type' => 'F','perms' => self::getController('\User\UserBalanceCashController@show')],
                                        ['name'=>'删除记录','id'=>'userBalanceCashDestroy','menu_type' => 'F','perms' => self::getController('\User\UserBalanceCashController@destroy')],
                                        ['name'=>'回收站','id'=>'userBalanceCashRecovery','menu_type' => 'F','component' => '/user/balance/recovery','perms' => self::getController('\User\UserBalanceCashController@show')],
                                    ],'perms' => self::getController('\User\UserBalanceCashController@index'),'component'=>'/user/balance/cash','path'=>'/user/cash','is_cache'=>1],
                                    ['name'=>'提现设置','id'=>'userBalanceCashSite','menu_type' => 'C','perms' => self::getController('\User\UserBalanceCashController@setting'),'component'=>'/user/balance/cash/setting','path'=>'/user/cash/setting','is_cache'=>1],
                                ]
                            ],
                            ['name'=>'充值管理','id'=>'userBalanceRecharge','menu_type' => 'M','is_cache'=>1
                                ,'children'=> [
                                    ['name'=>'充值列表','id'=>'userBalanceRechargeIndex','menu_type' => 'C','children'=> [
                                        ['name'=>'充值更改','id'=>'userBalanceRechargeEdit','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceRechargeController@edit','\User\UserBalanceRechargeController@update'])],
                                        ['name'=>'提现详情','id'=>'userBalanceRechargeInfo','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeController@show')],
                                        ['name'=>'删除记录','id'=>'userBalanceRechargeDestroy','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeController@destroy')],
                                        ['name'=>'回收站','id'=>'userBalanceRechargeRecovery','menu_type' => 'F','component' => '/user/balance/recharge/recovery','path'=>'/user/recharge/recovery','perms' => self::getController(['\User\UserBalanceRechargeController@recovery','\User\UserBalanceRechargeController@restore','\User\UserBalanceRechargeController@erasure'])],
                                    ],'perms' => self::getController('\User\UserBalanceRechargeController@index'),'component'=>'/user/balance/recharge','path'=>'/user/recharges','is_cache'=>1],
                                    ['name'=>'充值套餐','id'=>'userBalanceRechargeMeal','menu_type' => 'C','children'=> [
                                        ['name'=>'新增套餐','id'=>'userBalanceRechargeMealCreate','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceRechargeMealController@create','\User\UserBalanceRechargeMealController@store'])],
                                        ['name'=>'修改套餐','id'=>'userBalanceRechargeMealEdit','menu_type' => 'F','perms' => self::getController(['\User\UserBalanceRechargeMealController@edit','\User\UserBalanceRechargeMealController@update'])],
                                        ['name'=>'套餐详情','id'=>'userBalanceRechargeMealInfo','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeMealController@show')],
                                        ['name'=>'删除套餐','id'=>'userBalanceRechargeMealDestroy','menu_type' => 'F','perms' => self::getController('\User\UserBalanceRechargeMealController@destroy')],
                                        ['name'=>'回收站','id'=>'userBalanceRechargeMealRecovery','menu_type' => 'F','component' => '/user/balance/meal/recovery','path'=>'/user/recharge/recovery','perms' => self::getController(['\User\UserBalanceRechargeMealController@recovery','\User\UserBalanceRechargeMealController@restore','\User\UserBalanceRechargeMealController@erasure'])],
                                    ],'perms' => self::getController('\User\UserBalanceRechargeMealController@index'),'component'=>'/user/balance/meal','path'=>'/user/recharge/meal','is_cache'=>1]
                            ]],
                        ]
                    ],
                    ['name'=>'实名认证','id'=>'userReal','menu_type' => 'M','icon' => 'ri-shield-user-line','children'=> [
                        ['name'=>'申请记录','id'=>'userRealIndex','menu_type' => 'C','component'=> '/user/real','path'=>'/user/real','is_cache'=>1,'perms' => self::getController('\User\UserRealnameController@index')],
                        ['name'=>'实名审核','id'=>'userRealEdit','menu_type' => 'F','perms' => self::getController(['\User\UserRealnameController@edit','\User\UserRealnameController@update'])],
                        ['name'=>'实名设置','id'=>'userRealSite','menu_type' => 'C','component'=> '/user/real/setting','path'=>'/user/real/setting','is_cache'=>1,'perms' => self::getController('\User\UserRealnameController@setting')],
                    ]],
                    ['name'=>'等级成长','id'=>'userRights','menu_type' => 'M','icon' => 'ri-vip-crown-line','is_cache'=>1,'children'=> [
                        ['name'=>'等级管理','id'=>'userRightsGrade','menu_type' => 'M','is_cache'=>1
                            ,'children' => [
                                ['name'=>'会员组别','id'=>'userRightsGroup','menu_type' => 'C','component'=> '/user/rights/group','path'=>'/user/grade/group','is_cache'=>1,'children' =>[
                                    ['name'=>'新增组别','id'=>'userRightsGroupCreate','menu_type' => 'F','perms' => self::getController(['\User\UserGradeGroupController@create','\User\UserGradeGroupController@store'])],
                                    ['name'=>'修改组别','id'=>'userRightsGroupEdit','menu_type' => 'F','perms' => self::getController(['\User\UserGradeGroupController@edit','\User\UserGradeGroupController@update'])],
                                    ['name'=>'组别详情','id'=>'userRightsGroupShow','menu_type' => 'F','perms' => self::getController('\User\UserGradeGroupController@show')],
                                    ['name'=>'删除组别','id'=>'userRightsGroupDestroy','menu_type' => 'F','perms' => self::getController('\User\UserGradeGroupController@destroy')],
                                    ['name'=>'组别回收站','id'=>'userRightsGroupRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/rights/group/recovery','path'=>'/user/grade/group','perms' => self::getController('\User\UserGradeGroupController@recovery')],
                                    ['name'=>'等级管理','id'=>'userRightsGroupGrade','menu_type' => 'C','visible' => '0','component' =>'/user/rights/grade/[id]','path'=>'/user/grades','perms' => self::getController('\User\UserGradeController@index'),'children' =>[
                                        ['name'=>'新增等级','id'=>'userRightsGradeCreate','menu_type' => 'F','perms' => self::getController(['\User\UserGradeController@create','\User\UserGradeController@store'])],
                                        ['name'=>'修改等级','id'=>'userRightsGradeEdit','menu_type' => 'F','perms' => self::getController(['\User\UserGradeController@edit','\User\UserGradeController@update'])],
                                        ['name'=>'等级详情','id'=>'userRightsGradeShow','menu_type' => 'F','perms' => self::getController('\User\UserGradeController@show')],
                                        ['name'=>'删除等级','id'=>'userRightsGradeDestroy','menu_type' => 'F','perms' => self::getController('\User\UserGradeController@destroy')],
                                        ['name'=>'等级回收站','id'=>'userRightsGradeRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/rights/grade/recovery','path'=>'/user/grades/recovery','perms' => self::getController(['\User\UserGradeController@recovery','\User\UserGradeController@restore','\User\UserGradeController@erasure'])]
                                    ]]
                                ],'perms' => self::getController('\User\UserGradeGroupController@index')],
                                ['name'=>'会员权益','id'=>'userGradeRights','menu_type' => 'C','component'=> '/user/rights','path'=>'/user/grade/rights','is_cache'=>1,'children' =>[
                                    ['name'=>'新增权益','id'=>'userGradeRightsCreate','menu_type' => 'F','perms' => self::getController(['\User\UserGradeRightsController@create','\User\UserGradeRightsController@store'])],
                                    ['name'=>'修改权益','id'=>'userGradeRightsEdit','menu_type' => 'F','perms' => self::getController(['\User\UserGradeRightsController@edit','\User\UserGradeRightsController@update'])],
                                    ['name'=>'权益详情','id'=>'userGradeRightsShow','menu_type' => 'F','perms' => self::getController('\User\UserGradeRightsController@show')],
                                    ['name'=>'删除权益','id'=>'userGradeRightsDestroy','menu_type' => 'F','perms' => self::getController('\User\UserGradeRightsController@destroy')]
                                ],'perms' => self::getController('\User\UserGradeRightsController@index')],
                        ]],
                        ['name'=>'成长值日志','id'=>'userRightsGrowth','menu_type' => 'C','component'=> '/user/rights/growth','path'=>'/user/growth','is_cache'=>1,'perms' => self::getController('\User\UserGrowthLogController@index')],
                        ['name'=>'等级设置','id'=>'userRightsSite','menu_type' => 'C','component'=> '/user/rights/setting','path'=>'/user/growth/setting','is_cache'=>1,'perms' => self::getController('\User\UserGrowthLogController@setting')],
                    ]],
                    ['name'=>'会员中心菜单','id'=>'userMenu','menu_type' => 'C','icon'=>'ri-menu-line','component'=> '/user/menu','path'=>'/user/menu','is_cache'=>1,'perms'=> self::getController('\User\UserMenuController@index'),'children' =>[
                        ['name'=>'新增菜单','id'=>'userMenuCreate','menu_type' => 'F','perms' => self::getController(['\User\UserMenuController@create','\User\UserMenuController@store'])],
                        ['name'=>'修改菜单','id'=>'userMenuEdit','menu_type' => 'F','perms' => self::getController(['\User\UserMenuController@edit','\User\UserMenuController@update'])],
                        ['name'=>'菜单详情','id'=>'userMenuShow','menu_type' => 'F','perms' => self::getController('\User\UserMenuController@show')],
                        ['name'=>'删除菜单','id'=>'userMenuDestroy','menu_type' => 'F','perms' => self::getController('\User\UserMenuController@destroy')],
                        ['name'=>'菜单回收站','id'=>'userMenuRecovery','menu_type' => 'C','visible' => '0','component' =>'/user/menu/recovery','path'=>'/user/menu/recovery','perms' => self::getController(['\User\UserMenuController@recovery','\User\UserMenuController@restore','\User\UserMenuController@erasure'])]
                    ]],
                ]
            ]
        ];
    }
}