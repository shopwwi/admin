<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统角色控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\System;


use Shopwwi\Admin\App\Admin\Models\SysMenu;
use Shopwwi\Admin\App\Admin\Models\SysRoleMenu;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;


class SysRoleController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysRole::class;
    protected $trans = 'sysRole'; // 语言文件名称
    protected $queryPath = 'system/role'; // 完整路由地址
    protected $activeKey = 'settingSystemPowerRole';
    protected $useHasRecovery = true;
    public $routePath = 'role'; // 当前路由模块不填写则直接控制器名
    protected $adminOp = true;
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $allowOrUnAllow = DictTypeService::getAmisDictType('allowOrUnAllow');
        $sysRoleScope = DictTypeService::getAmisDictType('sysRoleScope');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysRole'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.key',[],'sysRole'),'key'),
            shopwwiAmisFields(trans('field.menus',[],'sysRole'),'menus')->column('input-tree',['source'=>'$menuList','labelField'=>'name','valueField'=>'id','multiple'=>true,'cascade'=>true,'hideNodePathLabel'=>true,'md'=>12])->showOnUpdate(3)->showOnCreation(3)->showOnIndex(0)->showOnDetail(0),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.status',[],'sysRole'),'status')->filterColumn('select',['options'=>$allowOrUnAllow])
                ->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                    ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$allowOrUnAllow])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.scope',[],'sysRole'),'scope')->filterColumn('select',['options'=>$sysRoleScope])
                ->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($sysRoleScope,'${scope}','default')])->column('radios',['selectFirst'=>true,'options'=>$sysRoleScope,'md'=>12])
                ->rules('required','numeric','min:0'),
            shopwwiAmisFields(trans('field.remark',[],'sysRole'),'remark')->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime'])
        ];
    }

    /**
     * 重定义新增获取数据
     * @return array
     */
    function getCreate()
    {
        $menus = SysMenu::where('pid',0)->with('children')->get();
        return ['menuList'=>$menus,'sort'=>999,'status' => 1];
    }

    /**
     * 重定义修改获取数据
     * @param $info
     * @param $id
     * @return array
     */
    function insertGetEdit($info,$id){
        $info->menus = SysRoleMenu::where('role_id',$info->id)->implode('menu_id',',');
        $info->menuList = SysMenu::where('pid',0)->with('children')->get();
        return $info;
    }

    /**
     * 新增数据后写入
     * @param $user
     * @param $create
     * @return array
     */
    function afterStore($user,$create){
        $menus = \request()->input('menus');
        if($menus){
            $menuList = is_array($menus) ? $menus : (is_string($menus) ? explode(',', $menus) : func_get_args());
            //清空角色关联菜单
            SysRoleMenu::where('role_id',$create->id)->delete();
            //循环写入关联菜单
            if(count($menuList) > 0) {
                $menuData = [];
                foreach ($menuList as $key=>$val){
                    $menuData[] = [
                        'role_id'=> $create->id,
                        'menu_id' => $val
                    ];
                }
                sysRoleMenu::insert($menuData);
            }
        }
        $data['info'] = $create;
        return $data;
    }

    /**
     * 修改数据后写入
     * @param $user
     * @param $update
     * @return array
     */
    function afterUpdate($user,$update){
        $menus = \request()->input('menus');
        if($menus){
            $menuList = is_array($menus) ? $menus : (is_string($menus) ? explode(',', $menus) : func_get_args());
            //清空角色关联菜单
            SysRoleMenu::where('role_id',$update->id)->delete();
            //循环写入关联菜单
            if(count($menuList) > 0) {
                $menuData = [];
                foreach ($menuList as $key=>$val){
                    $menuData[] = [
                        'role_id'=> $update->id,
                        'menu_id' => $val
                    ];
                }
                sysRoleMenu::insert($menuData);
            }
        }
        $data['info'] = $update;
        return $data;
    }

}
