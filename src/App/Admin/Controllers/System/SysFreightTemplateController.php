<?php
/**
 *-------------------------------------------------------------------------s*
 * 运费模板控制器
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

use Shopwwi\Admin\App\Admin\Models\SysArea;
use Shopwwi\Admin\App\Admin\Models\SysFreightArea;
use Shopwwi\Admin\App\Admin\Models\SysFreightTemplate;
use Shopwwi\Admin\Amis\AjaxAction;
use Shopwwi\Admin\Amis\DropdownButton;
use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\Admin\Libraries\Appoint;
use support\Request;
use support\Response;

class SysFreightTemplateController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysFreightTemplate::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'sysFreightTemplate'; // 语言文件名称
    protected $queryPath = 'system/freight'; // 完整路由地址
    protected $activeKey = 'settingSiteFreight';
    protected $useFormGrid = false;
    protected $useCreateDialog = 0;
    protected $useEditDialog = 0;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'freight'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = []; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        $freightCalcType = DictTypeService::getAmisDictType('freightCalcType');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.title',[],'sysFreightTemplate'),'title')->rules('required'),
            shopwwiAmisFields(trans('field.freight_free',[],'sysFreightTemplate'),'freight_free')->filterColumn('select',['options'=>$yesOrNo])->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$yesOrNo,'description'=>'设置免费后，下列设置的地区，价格将失效，地区有效'])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.calc_type',[],'sysFreightTemplate'),'calc_type')->filterColumn('select',['options'=>$freightCalcType])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($freightCalcType,'${calc_type}','default')])
                ->column('radios',['selectFirst'=>true,'options'=>$freightCalcType])
                ->rules('required'),
            shopwwiAmisFields(trans('field.store_id',[],'sysFreightTemplate'),'store_id')->showOnIndex(2)->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.templates',[],'sysFreightTemplate'),'areas')->
            column('input-table',
                ['addable'=>true,'needConfirm'=>false,'removable'=>true,'columns'=>[
                shopwwiAmis('input-tree')->name('areaIds')->source('${selectAreaList}')->label('地区')->labelField('name')->valueField('code')->multiple(true)->width(200)->initiallyOpen(false)->joinValues(false),
                shopwwiAmis('input-number')->min(1)->name('item1')->value(1)->label('首')->precision(2),
                shopwwiAmis('input-number')->min(0)->name('price1')->value(0)->label('首运费')->precision(2),
                shopwwiAmis('input-number')->min(1)->name('item2')->value(1)->label('续')->precision(2),
                shopwwiAmis('input-number')->min(0)->name('price2')->value(0)->label('续运费')->precision(2),
            ],'description'=>'可对不同地区设置不同运费及续费价格,请勿设置重复地区'])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    /**
     * 获取数据之前
     * @param $model
     * @return mixed
     */
    protected function beforeJsonList($model){
        $model = $model->with('areas');
        return $model;
    }

    /**
     * 保存数据中插入
     * @param $user
     * @param $create
     */
    protected function insertStoring($user,$create){
        $areas = \request()->input('areas');
        if($areas){
            $areaIds = [];
            foreach ($areas as $item){
                if(!isset($item['areaIds'])) continue;
               $areaList = collect($item['areaIds']);

               if($areaList->count() < 1) continue;
               $area = SysFreightArea::create([
                    'area_id' => $areaList->pluck('code')->all(),
                    'area_name' => $areaList->pluck('name')->all(),
                    'freight_id' => $create->id,
                    'item1' => $item['item1'],
                    'item2' => $item['item2'],
                    'price1' => $item['price1'],
                    'price2' => $item['price2'],
                ]);
                $areaIds[] = $area->id;
            }
            if(count($areaIds) > 0) $create->area_id = $areaIds;
        }

    }

    /**
     * 修改数据时插入
     * @param $user
     * @param $params
     * @param $info
     * @param $id
     */
    protected function insertUpdating($user,$params,&$info,$oldInfo){
        $areas = \request()->input('areas');
        if($areas){
            SysFreightArea::where('freight_id',$info->id)->delete();
            $areaIds = [];
            foreach ($areas as $item){
                if(!isset($item['areaIds'])) continue;
                $areaList = collect($item['areaIds']);
                if($areaList->count() < 1) continue;
                $area = SysFreightArea::create([
                    'area_id' => $areaList->pluck('code')->all(),
                    'area_name' => $areaList->pluck('name')->all(),
                    'freight_id' => $info->id,
                    'item1' => $item['item1'],
                    'item2' => $item['item2'],
                    'price1' => $item['price1'],
                    'price2' => $item['price2'],
                ]);
                $areaIds[] = $area->id;
            }
            $info->area_id = $areaIds;
        }

    }
    protected function getCreate(){
        $areas = SysArea::where('deep','<',3)->select('name','id','pid','code')->get();
        $areaList = Appoint::ShopwwiChindNode(json_decode($areas->toJson(),true));
        return ['selectAreaList'=>$areaList];
    }

    protected function insertGetEdit($info,$id){
        $areas = SysArea::where('deep','<',3)->select('name','id','pid','code')->get();
        $areaList = Appoint::ShopwwiChindNode(json_decode($areas->toJson(),true));
        $info->load('areas');
        $info->selectAreaList = $areaList;
        return $info;
    }

    protected function rowActions(): Operation
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
            $this->rowEditButton($this->useEditDialog,'$'.$this->key),
            DropdownButton::make()->label(trans('more',[],'messages'))->icon('fa fa-ellipsis-v')->trigger('hover')->align('right')->level('link')->buttons([
                $this->rowShowButton($this->useShowDialog,'$'.$this->key),
                $this->rowDeleteButton(),
                AjaxAction::make()
                    ->label('复制')
                    ->icon('ri-file-copy-line')
                    ->level('link')
                    ->actionType('ajax')
                    ->api('post:' . $this->getUrl($this->queryPath . '/copy/${'.$this->key.'}'))
            ])
        ]);
    }

    /**
     * 复制运费模板
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function copy(Request $request,$id)
    {
        try {
            $freightTemplate = SysFreightTemplate::where('id',$id)->first()->replicate();
            if($freightTemplate == null){
                throw new \Exception('不存在');
            }
            $freightTemplate->title = $freightTemplate->title . '_副本';
            $freightTemplate->save();
            $freightAreaList = SysFreightArea::where('freight_id',$id)->get();
            foreach ($freightAreaList as $item){
                $area = $item->replicate();
                $area->freight_id = $freightTemplate->id;
                $area->save();
            }
            return shopwwiSuccess([],'复制成功');
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }


}
