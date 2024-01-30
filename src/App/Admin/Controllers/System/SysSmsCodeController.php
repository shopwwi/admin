<?php
/**
 *-------------------------------------------------------------------------s*
 * 短信发送日志控制器
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

use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Request;


class SysSmsCodeController extends AdminController
{
    public $model = \Shopwwi\Admin\App\Admin\Models\SysSmsCode::class;
    public  $orderBy = ['id' => 'desc'];
    protected $trans = 'sysSmsCode'; // 语言文件名称
    protected $queryPath = 'system/sms'; // 完整路由地址
    protected $activeKey = 'settingWaySmsIndex';
    protected $useHasCreate = false;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'sms'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','edit','update','store','recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $usedOrUnused = DictTypeService::getAmisDictType('usedOrUnused');
        $sendType = DictTypeService::getAmisDictType('sendType');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.auth_code',[],'sysSmsCode'),'auth_code')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.content',[],'sysSmsCode'),'content')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.ip',[],'sysSmsCode'),'ip')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.mobile_phone',[],'sysSmsCode'),'mobile_phone')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.send_type',[],'sysEmailCode'),'send_type')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($sendType,'${send_type}')])
                ->filterColumn('select',['options'=>$sendType]),
            shopwwiAmisFields(trans('field.status',[],'sysEmailCode'),'status')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($usedOrUnused,'${status}')])
                ->filterColumn('select',['options'=>$usedOrUnused]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }
    protected function operation()
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(160)->buttons([
            $this->rowShowButton(1),
            $this->rowDeleteButton(),
        ]);
    }

    /**
     * 短信设置
     * @param Request $request
     * @return \support\Response
     */
    public function setting(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'sms'
                    ],['name'=>'短信服务','value'=>[
                        'used'=>'0',
                        'timeout' => 5.0,
                        'gateways'=>[
                            'yunpian' => [
                                'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                                'signature' => '【默认签名】'
                            ],
                            'aliyun' => [
                                'access_key_id' => '',
                                'access_key_secret' => '',
                                'sign_name' => '',
                            ],
                        ],
                        "authCodeVerifyTime" => 5,
                        "authCodeResendTime" => 60,
                        "authCodeSameIpResendTime" => 30,
                        "authCodeSameMaxNum" => 12,
                        "authCodeSameIpMaxNum" => 3
                    ]]);
                    return shopwwiSuccess($info);
                }
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('radios')->name('sms.used')->label(trans('config.used',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12),
                        shopwwiAmis('input-text')->name('sms.timeout')->label(trans('config.timeout',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('config.timeout',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('json-editor')->label(trans('config.gateways',[],$this->trans))->name('sms.gateways')->placeholder(trans('form.input',['attribute'=>trans('config.gateways',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-number')->name('sms.authCodeVerifyTime')->label(trans('config.authCodeVerifyTime',[],$this->trans))->min(1)->placeholder(trans('form.input',['attribute'=>trans('config.authCodeVerifyTime',[],$this->trans)],'messages'))->xs(12)->description('验证有效期[n]分钟内有效'),
                        shopwwiAmis('input-number')->name('sms.authCodeResendTime')->label(trans('config.authCodeResendTime',[],$this->trans))->min(0)->placeholder(trans('form.input',['attribute'=>trans('config.authCodeResendTime',[],$this->trans)],'messages'))->xs(12)->description('同一类型[n]秒内只能发一条'),
                        shopwwiAmis('input-number')->name('sms.authCodeSameIpResendTime')->label(trans('config.authCodeSameIpResendTime',[],$this->trans))->min(0)->placeholder(trans('form.input',['attribute'=>trans('config.authCodeSameIpResendTime',[],$this->trans)],'messages'))->xs(12)->description('同一类型同一IP[n]秒内只能发一条'),
                        shopwwiAmis('input-number')->name('sms.authCodeSameMaxNum')->label(trans('config.authCodeSameMaxNum',[],$this->trans))->min(0)->placeholder(trans('form.input',['attribute'=>trans('config.authCodeSameMaxNum',[],$this->trans)],'messages'))->xs(12)->description('24小时内只能发[n]条'),
                        shopwwiAmis('input-number')->name('sms.authCodeSameIpMaxNum')->label(trans('config.authCodeSameIpMaxNum',[],$this->trans))->min(0)->placeholder(trans('form.input',['attribute'=>trans('config.authCodeSameIpMaxNum',[],$this->trans)],'messages'))->xs(12)->description('同一IP24小时内只能发[n]条'),
                    ])
                ])->api('post:' . shopwwiAdminUrl('system/sms/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/sms/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('config.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingWaySmsSite']);
            }else{
                $params = shopwwiParams(['sms']);
                if(isset($params['sms']['gateways']) && is_string($params['sms']['gateways'])){
                    $params['sms']['gateways'] = json_decode(trim($params['sms']['gateways']),true);
                }
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}
