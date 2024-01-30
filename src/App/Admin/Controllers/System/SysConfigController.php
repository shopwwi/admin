<?php
/**
 *-------------------------------------------------------------------------s*
 * 参数配置控制器
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
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\Admin\Libraries\Validator;
use support\Request;

class SysConfigController extends AdminController
{
    public  $model = \Shopwwi\Admin\App\Admin\Models\SysConfig::class;
    protected $trans = 'sysConfig'; // 语言文件名称
    protected $queryPath = 'system/config'; // 完整路由地址
    protected $activeKey = 'settingSystemConfig';
    protected $useHasRecovery = true;

    public $routePath = 'config'; // 当前路由模块不填写则直接控制器名
    public  $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysConfig'),'name')->rules('required')->showFilter(),
            shopwwiAmisFields(trans('field.key',[],'sysConfig'),'key')->rules('required'),
            shopwwiAmisFields(trans('field.value',[],'sysConfig'),'value')->showOnIndex(2)->column('json-editor',['md'=>12]),
            shopwwiAmisFields(trans('field.is_system',[],'sysConfig'),'is_system')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_system}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.is_open',[],'sysConfig'),'is_open')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_open}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.remark',[],'sysConfig'),'remark')->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }
    protected function beforeStore($user,&$validator){
        $res =  $this->filterStore();
        $validator = Validator::make(\request()->all(), $res['rule'], [], $res['lang']);
        $params = shopwwiParams($res['filter']); //指定字段
        if(isset($params['value']) && is_string($params['value'])){
            $params['value'] = json_decode(trim($params['value']),true);
        }
        return $params;
    }

    protected function insertUpdating($user,$params,&$info,$oldInfo){
        if(isset($info->value) && is_string($info->value)){
            $info->value = json_decode(trim($info->value),true);
        }
    }

    /**
     * 第三方登入
     * @param Request $request
     * @return \support\Response|void
     */
    public function auth(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'socialite'
                    ],['name'=>'登入接口信息','value'=>[]]);
                    return shopwwiSuccess($info);
                }
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('json-editor')->label('登入配置')->name('socialite')->placeholder('请输入登入配置')->xs(12),
                    ])
                ])->api('post:' . shopwwiAdminUrl('system/config/auth'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/config/auth?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle('登入配置');
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingWayAuth']);
            }else{
                $params = shopwwiParams(['socialite']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 站点信息配置
     * @param Request $request
     * @return \support\Response
     */
    public function site(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'siteInfo'
                    ],['name'=>'站点信息','value'=>[
                        'siteName' => 'ShopWWI智能管理系统',
                        'siteIcp' => '',
                        'siteBol' => '',
                        'sitePoliceNet' => '',
                        'siteLogo' => '',
                        'siteIcon' => '',
                        'siteKeyword' => '',
                        'siteDescription' => '',
                        'siteStatus' => '0',
                        'siteCloseRemark' => '',
                        'siteEmail' => '',
                        'sitePhone' => '',
                        'siteFlowCode' => ''
                    ]]);
                    return shopwwiSuccess($info);
                }
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('input-text')->name('siteInfo.siteName')->label(trans('siteInfo.siteName',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteName',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-image')->name('siteInfo.siteLogo')->label(trans('siteInfo.siteLogo',[],$this->trans))->xs(12)->md(6),
                        shopwwiAmis('input-image')->name('siteInfo.siteIcon')->label(trans('siteInfo.siteIcon',[],$this->trans))->xs(12)->md(6),
                        shopwwiAmis('input-text')->name('siteInfo.siteEmail')->label(trans('siteInfo.siteEmail',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteEmail',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.sitePhone')->label(trans('siteInfo.sitePhone',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.sitePhone',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteIcp')->label(trans('siteInfo.siteIcp',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteIcp',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteBol')->label(trans('siteInfo.siteBol',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteBol',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.sitePoliceNet')->label(trans('siteInfo.sitePoliceNet',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.sitePoliceNet',[],$this->trans)],'messages'))->xs(12),

                        shopwwiAmis('input-text')->name('siteInfo.siteKeyword')->label(trans('siteInfo.siteKeyword',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteKeyword',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('textarea')->name('siteInfo.siteDescription')->label(trans('siteInfo.siteDescription',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteDescription',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('textarea')->name('siteInfo.siteFlowCode')->label(trans('siteInfo.siteFlowCode',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteFlowCode',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('radios')->name('siteInfo.siteStatus')->label(trans('siteInfo.siteStatus',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12),
                        shopwwiAmis('textarea')->name('siteInfo.siteCloseRemark')->label(trans('siteInfo.siteCloseRemark',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteCloseRemark',[],$this->trans)],'messages'))->xs(12),
                    ])
                ])->api('post:' . shopwwiAdminUrl('system/config/site'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/config/site?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('siteInfo.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingSiteBase']);
            }else{
                $params = shopwwiParams(['siteInfo']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 站点规则配置
     * @param Request $request
     * @return \support\Response
     */
    public function rule(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'siteAuthRule'
                    ],['name'=>'站点规则设置','value'=>[
                          "authCodeVerifyTime" => 5,
                          "authCodeResendTime" => 60,
                          "authCodeSameIpResendTime" => 30,
                          "authCodeSameIpEmailResendTime" => 5,
                          "authCodeSamePhoneMaxNum" => 12,
                          "authCodeSameEmailMaxNum" => 50,
                          "authCodeSameEmailIpMaxNum" => 3,
                          "authCodeSameIpMaxNum" => 3
                    ]]);
                    return shopwwiSuccess($info);
                }
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('input-text')->name('siteAuthRule.authCodeVerifyTime')->label(trans('siteInfo.siteName',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteName',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteLogo')->label(trans('siteInfo.siteLogo',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteLogo',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteIcon')->label(trans('siteInfo.siteIcon',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteIcon',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteEmail')->label(trans('siteInfo.siteEmail',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteEmail',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.sitePhone')->label(trans('siteInfo.sitePhone',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.sitePhone',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteIcp')->label(trans('siteInfo.siteIcp',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteIcp',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('siteInfo.siteKeyword')->label(trans('siteInfo.siteKeyword',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('siteInfo.siteKeyword',[],$this->trans)],'messages'))->xs(12),
                    ])
                ])->api('post:' . shopwwiAdminUrl('system/config/rule'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/config/rule?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('siteInfo.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingSiteBaseRule']);
            }else{
                $params = shopwwiParams(['siteAuthRule']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    public function pic(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'siteDefaultImage'
                    ],['name'=>'站点默认图片','value'=>[
                        'goodsImage' => '',
                        'userImage' => '',
                    ]]);
                    return shopwwiSuccess($info);
                }
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('input-image')->name('siteDefaultImage.goodsImage')->label(trans('siteDefaultImage.goodsImage',[],$this->trans))->xs(12),
                        shopwwiAmis('input-image')->name('siteDefaultImage.userImage')->label(trans('siteDefaultImage.userImage',[],$this->trans))->xs(12),
                    ])
                ])->api('post:' . shopwwiAdminUrl('system/config/pic'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/config/pic?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle('默认图片设置');
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingSiteBasePic']);
            }else{
                $params = shopwwiParams(['siteDefaultImage']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 签到奖励
     * @param Request $request
     * @return \support\Response
     */
    public function signing(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                $info = SysConfigService::getFirstOrCreate([
                    'key' => 'signing'
                ],['name'=>'签到规则','value'=>[
                    'used' => '1', // 签到状态
                    'days' =>  30, //签到周期
                    'points' => 10, // 日常积分
                    'growth' => 0, // 日常成长值
                    'list' => [
                        ['day' => 2, 'points' => 15 , 'growth' => 0],
                        ['day' => 4, 'points' => 20 , 'growth' => 0]
                    ],
                ]]);
                return shopwwiSuccess($info);
            }else{
                $params = shopwwiParams(['signing']);
                SysConfigService::updateSetting($params);
                return shopwwiSuccess();
            }
        }catch (\Exception $E){
            return shopwwiError($E->getMessage());
        }
    }
}
