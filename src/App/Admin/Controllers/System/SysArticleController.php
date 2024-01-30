<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统文章控制器
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

use Shopwwi\Admin\App\Admin\Models\SysArticleClass;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;


class SysArticleController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysArticle::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    protected $trans = 'sysArticle'; // 语言文件名称
    protected $queryPath = 'system/articles'; // 完整路由地址
    protected $activeKey = 'settingContentArticleIndex';
    protected $useCreateDialog = 0;
    protected $useEditDialog = 0;
    protected $useShowDialog = 2;
    protected $useEditDialogSize = 'lg';
    protected $useCreateDialogSize = 'lg';
    protected $useShowDialogSize = 'lg';

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'articles'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.image',[],'sysArticle'),'image')->column('hidden',['md'=>12])->tableColumn(['type'=>'image','name'=>'imageUrl','width'=>30,'height'=>30,'imageMode'=>'original']),
            shopwwiAmisFields(trans('field.image',[],'sysArticle'),'imageUrl')->column('input-image',['autoFill'=>['image'=>'${file_name}'],'crop'=>['aspectRatio'=>3],'receiver'=>shopwwiAdminUrl('system/user/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'imageUrl','width'=>90,'height'=>90]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.title',[],'sysArticle'),'title')->rules(['required','min:3','max:145'])->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.category_id',[],'sysArticle'),'category_id')->rules(['required','numeric','min:1'])->column('select',['source'=>'$classList','valueField'=>'id'])->tableColumn(['type'=>'link','href'=>shopwwiAdminUrl('system/article/class?id=${class.id}'),'body'=>'${class.title}[${class.id}]']),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.content',[],'sysArticle'),'content')->column('input-rich-text',['md'=>12])->showOnIndex(0)->showColumn('control',['body'=>['type'=>'tpl','tpl'=>'${content|raw}','md'=>12]]),
            shopwwiAmisFields(trans('field.url',[],'sysArticle'),'url'),
            shopwwiAmisFields(trans('field.allow_delete',[],'sysArticle'),'allow_delete')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_open}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),

        ];
    }
    /**
     * 添加关联关系
     * @param $model
     * @return mixed
     */
    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled())->with(['class'=>function($q){
            $q->select('id','title');
        }]);
        return $model;
    }

    /**
     * 新增查询赋值
     * @return array
     */
    protected function getCreate(){
        $sysArticlePosition = DictTypeService::getRowDict('sysArticlePosition');
        $articleClassList = SysArticleClass::where('type','!=',1)->get();
        $articleClassList->map(function ($item) use ($sysArticlePosition) {
            $item->label = $item->title.'['.$sysArticlePosition[$item->position]??'其他'.']';
        });
        return ['classList'=>$articleClassList,'sort'=>999];
    }

    /**
     * 编辑查询赋值
     * @param $info
     * @param $id
     * @return array
     */
    protected function insertGetEdit($info,$id){
        $sysArticlePosition = DictTypeService::getRowDict('sysArticlePosition');
        $articleClassList = SysArticleClass::where('type','!=',1)->get();
        $articleClassList->map(function ($item) use ($sysArticlePosition) {
            $item->label = $item->title.'['.$sysArticlePosition[$item->position]??'其他'.']';
        });
        $info->classList = $articleClassList;
        return $info;
    }
}
