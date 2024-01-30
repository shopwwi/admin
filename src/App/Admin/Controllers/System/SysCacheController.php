<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统缓存控制器
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

use Shopwwi\Admin\Amis\AjaxAction;
use Shopwwi\Admin\App\Admin\Models\SysCache;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Response;

class SysCacheController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysCache::class;
    protected $key = 'key';

    protected $trans = 'sysCache'; // 语言文件名称
    protected $queryPath = 'system/cache'; // 完整路由地址
    protected $activeKey = 'settingSystemCache';

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'cache'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权

    /**
     * 批量删除操作
     * @return AjaxAction
     */
    protected function bulkDeleteButton(): AjaxAction
    {
        return AjaxAction::make()
            ->api($this->useAmisBatchDestroyUrl())
            ->icon('fa-solid fa-trash-can')->level('danger')
            ->label('批量清除缓存')
            ->confirmText('确认要批量清除所选缓存项吗？');
    }

    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.key',[],'sysCache'),'key')->showOnCreation(0)->showOnUpdate(0)->rules('required')->showOnIndex(2),
            shopwwiAmisFields(trans('field.name',[],'sysCache'),'name')->rules('required')->showOnIndex(2),
            shopwwiAmisFields(trans('field.desc',[],'sysCache'),'desc')->column('textarea',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.model',[],'sysCache'),'model')->rules('required')->column('textarea',['md'=>12,'desc'=>'多个缓存处理器请以逗号分隔'])->showColumn('input-text',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.operation_time',[],'sysCache'),'operation_time')->showOnCreation(0)->showOnUpdate(0),

        ];
    }

    protected function crudShow()
    {
        return $this->baseCardCRUD()->primaryField('key')->card([
            'header'=>['title'=>'$name','subTitle'=>'$desc'],
            'actions' => [$this->rowEditButton($this->useEditDialog,'$key'),$this->rowShowButton($this->useShowDialog,'$key')],
            'body'=>$this->getTableColumn()
        ])->columns($this->getTableColumn());
    }
    protected function getTableColumn(){
        $filter = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnIndex,[1,3])){
                $filter[] = $v->tableColumn;
            }
        }
        return $filter;
    }

    /**
     * 删除缓存
     * @param $user
     * @param $ids
     * @param $id
     * @return Response
     */
    protected function destroying($user,$ids,$id){
       try {
            $cacheList = SysCache::whereIn('key',$ids)->get();
            foreach ($cacheList as $item){
                $modelList = explode(',',$item->model);
                foreach ($modelList as $v){
                    $models = explode('@',$v);
                    call_user_func(array($models[0], $models[1]));
                }
                $item->operation_time = now();
                $item->save();
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError( $e->getMessage());
        }
    }

}



