<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统用户控制器
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

use Shopwwi\Admin\App\Admin\Models\SysRole;
use Shopwwi\Admin\App\Admin\Models\SysUserSector;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\Admin\Libraries\Storage;
use support\Request;
use support\Response;

class SysUserController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysUser::class;
    protected $adminOp = true;
    protected $trans = 'sysUser'; // 语言文件名称
    protected $queryPath = 'system/user'; // 完整路由地址
    protected $activeKey = 'settingSystemPowerUser';
    protected $useHasRecovery = true;

    public $routePath = 'user'; // 当前路由模块不填写则直接控制器名
    public $noNeedAuth = ['list'];

    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        $sexDict = DictTypeService::getAmisDictType('sex');
        $sectorList = SysUserSector::where('pid',0)->where('status',1)->with(['children'=>function($q){
            return $q->where('status',1);
        }])->get();
        return [
            shopwwiAmisFields(trans('field.id',[],'sysUser'),'id')->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.avatar',[],'sysUser'),'avatar')->column('hidden',['md'=>12])->tableColumn(['type'=>'image','name'=>'avatarUrl','width'=>30,'height'=>30,'imageMode'=>'original']),
            shopwwiAmisFields(trans('field.avatar',[],'sysUser'),'avatarUrl')->column('input-image',['autoFill'=>['avatar'=>'${file_name}'],'initAutoFill'=>false,'crop'=>['aspectRatio'=>3],'receiver'=>shopwwiAdminUrl('system/user/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'avatarUrl','width'=>90,'height'=>90]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.username',[],'sysUser'),'username')->rules('required')->showFilter(),
            shopwwiAmisFields(trans('field.nickname',[],'sysUser'),'nickname')->showFilter(),
            shopwwiAmisFields(trans('field.password',[],'sysUser'),'password')->rules(['bail','chs_dash_pwd','min:6'])->creationRules(['required'])->updateRules(['nullable'])->showOnIndex(0)->showOnDetail(0),
            shopwwiAmisFields(trans('field.role_id',[],'sysUser'),'role_id')->tableColumn(['name'=>'role.name'])
                ->column('select',['searchable'=>true,'labelField'=>'name','valueField'=>'id','source'=>'${role}'])->rules(['bail','required','numeric','min:1']),
            shopwwiAmisFields(trans('field.sector_ids',[],'sysUser'),'sector_ids')
                ->tableColumn(['type'=>'tree-select','static'=>true,'options'=>$sectorList,'multiple'=>true,'labelField'=>'name','valueField'=>'id'])
                ->column('tree-select',['searchable'=>true,'labelField'=>'name','valueField'=>'id','options'=>$sectorList,'multiple'=>true,'autoCheckChildren'=>false,'cascade'=>true]),
            shopwwiAmisFields(trans('field.email',[],'sysUser'),'email'),
            shopwwiAmisFields(trans('field.mobile',[],'sysUser'),'mobile'),
            shopwwiAmisFields(trans('field.sex',[],'sysUser'),'sex')
                ->filterColumn('select',['options'=>$sexDict])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($sexDict,'${sex}','default')])
                ->column('radios',['selectFirst'=>true,'options'=>$sexDict])->rules(['required','in:0,1,2']),
            shopwwiAmisFields(trans('field.status',[],'sysUser'),'status')
                ->filterColumn('select',['options'=>$openOrClose])->column('radios',['selectFirst'=>true,'options'=>$openOrClose])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${status}','round')])
                ->rules(['required','in:1,0']),
            shopwwiAmisFields(trans('field.login_ip',[],'sysUser'),'login_ip')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.login_time',[],'sysUser'),'login_time')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'login_time_betweenAmisTime']),
            shopwwiAmisFields(trans('field.login_num',[],'sysUser'),'login_num')->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.last_login_ip',[],'sysUser'),'last_login_ip')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.last_login_time',[],'sysUser'),'last_login_time')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'last_login_time_betweenAmisTime']),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->showOnIndex(4)->tableColumn(['width'=>145])->showOnCreation(0)->showOnUpdate(0),
        ];
    }
    protected function afterJsonList($list){
        $ids = $list->pluck('role_id');
        $roleList = SysRole::whereIn('id',$ids)->get();
        $list->map(function ($item) use ($roleList) {
            $item->role = $roleList->where('id',$item->role_id)->first();
        });
        return $list->items();
    }
    /**
     * 重定义新增获取数据
     * @return array
     */
    protected function getCreate()
    {
        return [
            'sectorList' => SysUserSector::where('pid',0)->where('status',1)->with(['children'=>function($q){
                return $q->where('status',1);
            }])->get(),
            'role'=> SysRole::where('status',1)->get(),
            'allowOrUnAllow' => DictTypeService::getAmisDictType('allowOrUnAllow'),
            'sexSelect' => DictTypeService::getAmisDictType('sex')
        ];
    }

    /**
     * 重定义修改获取数据
     * @param $info
     * @param $id
     * @return array
     */
    protected function insertGetEdit($info,$id){
        $info->role = SysRole::where('status',1)->get();
        $info->allowOrUnAllow = DictTypeService::getAmisDictType('allowOrUnAllow');
        $info->sexSelect = DictTypeService::getAmisDictType('sex');
        return $info;
    }

    public function list(Request $request)
    {
        return $this->jsonList(false,[]);
    }

    /**
     * 上传头像
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request)
    {
        try {
            $file = $request->file('file');
            $result = Storage::path('uploads/admin/avatar')->size(1024*1024*5)->extYes(['image/jpeg','image/gif','image/png'])->upload($file);
            $result->value = $result->file_url;
            return shopwwiSuccess($result);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}
