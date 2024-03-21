<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员等级表控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\User;

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\User\Models\UserGradeGroup;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Request;


class UserGradeController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserGrade::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'userGrade'; // 语言文件名称
    protected $queryPath = 'user/grades'; // 完整路由地址
    protected $activeKey = 'userRightsGroup';
    protected $useIndexBack = true;
    protected $buttonNext = false;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'grades'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        $groupList = UserGradeGroup::get(['id as value','name as label']);
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.group_id',[],'userGrade'),'group_id')->rules(['bail','required','numeric','min:1'])->column('select',['options'=>$groupList,'disabled'=>true])->showOnUpdate(3)->showOnIndex(2),
            shopwwiAmisFields(trans('field.level',[],'userGrade'),'level')->rules(['bail','required','numeric','min:1'])->column('input-number',['min'=>1,'description'=>'请输入数字等级,数字越大等级越大,升级根据此数字从小往大，不可重复']),
            shopwwiAmisFields(trans('field.name',[],'userGrade'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.ext_name',[],'userGrade'),'ext_name')->rules('required'),
            shopwwiAmisFields(trans('field.rule',[],'userGrade'),'rule')->rules('required')->column('combo',[
                'items' => [
                    shopwwiAmis('switch')->name('modal')->onText('满足任意条件')->offText('满足全部条件')->trueValue(1)->falseValue(0),
                    shopwwiAmis('combo')->items([
                        shopwwiAmis('switch')->name('used')->label('$desc')->trueValue(1)->falseValue(0),
                        shopwwiAmis('input-number')->name('num')->disabledOn('${used != 1}'),
                    ])->name('items')->multiple(true)->addable(false)->removable(false)
                ],'multiLine' => true,'md'=>12,'description'=>'满足任意条件即开启的条件中满足任意一项即可升级，满足全部条件即开启的条件需全部满足方可升级'])->showOnIndex(0),
            shopwwiAmisFields(trans('field.icon',[],'userGrade'),'icon')->column('hidden',['md'=>12])->tableColumn(['type'=>'image','name'=>'iconUrl','width'=>30,'height'=>30,'imageMode'=>'original']),
            shopwwiAmisFields(trans('field.icon',[],'userGrade'),'iconUrl')->column('input-image',['autoFill'=>['icon'=>'${file_name}'],'initAutoFill'=>false,'crop'=>['aspectRatio'=>1],'receiver'=>shopwwiAdminUrl('common/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'iconUrl','width'=>60,'height'=>60]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.image_name',[],'userGrade'),'image_name')->column('hidden',['md'=>12])->tableColumn(['type'=>'image','name'=>'imageUrl','width'=>30,'height'=>30,'imageMode'=>'original']),
            shopwwiAmisFields(trans('field.image_name',[],'userGrade'),'imageUrl')->column('input-image',['autoFill'=>['image_name'=>'${file_name}'],'initAutoFill'=>false,'crop'=>['aspectRatio'=>3],'receiver'=>shopwwiAdminUrl('common/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'imageUrl','width'=>90,'height'=>30]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.remark',[],'userGrade'),'remark')->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.status',[],'userGrade'),'status')->rules(['bail','required','numeric','in:0,1'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${status}')])->filterColumn('select',['options'=>$openOrClose])->column('radios',['options'=>$openOrClose,'selectFirst'=>true]),
            shopwwiAmisFields(trans('field.is_default',[],'userGrade'),'is_default')->rules(['bail','required','numeric','in:0,1'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${status}')])->filterColumn('select',['options'=>$yesOrNo])->column('radios',['options'=>$yesOrNo,'selectFirst'=>true]),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    /**
     * 新建数据获取
     */
    protected function getCreate(){
        return [
            'is_default' => 0,
            'status' => 1,
            'rule' => [
                'modal' => 0, // 0 为任意条件 1为全部条件
                'items' => config('plugin.shopwwi.admin.app.GRADE_RULE',[
                    ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                    ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                    ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                    ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                    ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                    ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                ])
            ]
        ];
    }

    public function list(Request $request)
    {
        return $this->jsonList(false,[]);
    }

    /**
     * 编辑返回数据插入
     * @param $info
     * @param $id
     * @return mixed
     */
    protected function insertGetEdit($info,$id){
        $rule = [
            'modal' => $info->rule['modal'] ?? 0, // 0 为任意条件 1为全部条件
            'items' => config('plugin.shopwwi.admin.app.GRADE_RULE',[
                ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
            ])
        ];
        if(isset($info->rule['items']) && count($info->rule['items']) > 0){
            foreach ($info->rule['items'] as $val){
                foreach ($rule['items'] as $key=>$val2){
                    if($val['type'] == $val2['type']){
                        $rule['items'][$key]['used'] = $val['used'] ?? '0';
                        $rule['items'][$key]['num'] = $val['num'] ?? 0;
                    }
                }
            }
        }
        $info->rule = $rule;
        return $info;
    }

    /**
     * 获取数据之后
     * @param $list
     * @return mixed
     */
    protected function afterJsonList($list){
        $groupList = UserGradeGroup::get();
        $list->map(function ($item) use ($groupList) {
            $item->group_name = $groupList->where('id',$item->group_id)->value('name');
        });
        //  $data['list'] = $list->items();
        return $list->items();
    }

}
