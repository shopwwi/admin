<?php
/**
 *-------------------------------------------------------------------------s*
 * 菜单权限控制器
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

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;


class SysMenuController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysMenu::class;
    protected $with = ['children'];
    protected $orderBy = ['sort'=>'asc','id'=>'asc'];
    protected $adminOp = true;

    protected $trans = 'sysMenu'; // 语言文件名称
    protected $queryPath = 'system/menu'; // 完整路由地址
    protected $activeKey = 'settingSystemMenu';
    /**
     * 路由注册
     * @var string
     */
    public $routePath = 'menu'; // 当前路由模块不填写则直接控制器名
   // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
  //  public $routeNoAction = ['index']; //不允许方法注册

    /**
     * 数据字段处理
     * @return array
     */
    protected function fields()
    {
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        $sysMenuType = DictTypeService::getAmisDictType('sysMenuType');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysMenu'),'name'),
            shopwwiAmisFields(trans('field.pid',[],'sysMenu'),'pid')->rules(['bail','nullable','numeric','min:0'])->column('tree-select',['source'=>'$items','labelField'=>'name','valueField'=>'id']),
            shopwwiAmisFields(trans('field.icon',[],'sysMenu'),'icon'),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.path',[],'sysMenu'),'path'),
            shopwwiAmisFields(trans('field.component',[],'sysMenu'),'component'),
            shopwwiAmisFields(trans('field.key',[],'sysMenu'),'key')->showOnIndex(2)->rules('required'),
            shopwwiAmisFields(trans('field.menu_type',[],'sysMenu'),'menu_type')->rules('required')
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($sysMenuType,'${menu_type}','default')])
                ->column('select',['options'=>$sysMenuType]),
            shopwwiAmisFields(trans('field.is_frame',[],'sysMenu'),'is_frame')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_frame}','round')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.is_cache',[],'sysMenu'),'is_cache')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_cache}','round')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.visible',[],'sysMenu'),'visible')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${visible}','round')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.status',[],'sysMenu'),'status')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${status}','round')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.perms',[],'sysMenu'),'perms')->column('textarea',['md'=>12,'desc'=>'请输入权限 多个权限英文逗号分隔 如Shopwwi\Admin\App\Admin\Controllers\System\SysConfigController@site'])->showOnIndex(2)->showColumn('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.remark',[],'sysMenu'),'remark')->column('textarea',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.role',[],'sysMenu'),'role')->showOnCreation(3)->showOnUpdate(0)->column('textarea',['md'=>12,'desc'=>'自动生成权限菜单 Shopwwi\Admin\App\Admin\Controllers\System\SysConfigController@index,create,store,show,edit,update,recovery,restore,erasure'])->showOnIndex(0)->showOnDetail(0),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }


    /**
     * 获取新增数据
     * @return mixed
     */
    protected function getCreate(){

        return [
            'is_frame' => 0,
            'is_cache' => 1,
            'menu_type' => 'M',
            'visible' => 1,
            'status' => 1,
            'sort' => 999
        ];
    }

    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled());
        $model->whereNull('pid')->with('children');
        return $model;
    }

    /**
     * 新增保存后处理
     * @param $user
     * @param $create
     * @return array
     */
    protected function afterStore($user,$create){
        $data['info'] = $create;
        $role = request()->input('role','');
        if(!empty($role)){
            $attr = explode('@', $role);
            $action = explode(',',$attr[1]);
            $controller = $attr[0];
            $name = '数据权限';
            foreach ($action as $val){
                switch ($val){
                    case 'create':
                        $name = '新增获取';
                        break;
                    case 'store':
                        $name = '新增保存';
                        break;
                    case 'edit':
                        $name = '修改获取';
                        break;
                    case 'show':
                        $name = '获取详情';
                        break;
                    case 'update':
                        $name = '修改保存';
                        break;
                    case 'destroy':
                        $name = '删除数据';
                        break;
                    case 'recovery':
                        $name = '回收站';
                        break;
                    default:

                }
                $perms = $controller.'@'.$val;
                if($val == 'recovery'){
                    $perms = $controller.'@'.$val.','.$controller.'@restore,'.$controller.'@erasure';
                }
                (new $this->model)->create([
                    'name' => $name,
                    'pid' => $create->id,
                    'key' => $create->key.ucfirst($val),
                    'perms' => $perms,
                    'menu_type' => 'F'
                ]);
            }

        }
        return $data;
    }

}
