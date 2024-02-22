<?php
/**
 *-------------------------------------------------------------------------s*
 * 实名认证
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author  TycoonSong 8988354@qq.com
 *-------------------------------------------------------------------------i*
 */

namespace Shopwwi\Admin\App\User\Controllers;

use Shopwwi\Admin\App\User\Models\UserRealname;
use Shopwwi\Admin\App\User\Service\RealnameService;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;

class RealController extends Controllers
{
    protected $queryPath = 'real'; // 完整路由地址
    protected $activeKey = 'authReal';

    /**
     * 获取实名认证信息
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        try {
            $user = $this->user();
            $real = UserRealname::where('user_id',$user->id)->first();
            $data['step'] = 1;
            if($real != null){
                $data['isNew'] = $real->status == 0;
                $data['isOk'] = $real->status == 1;
                $data['isFail'] = $real->status == 2;
                $data['isOut'] = $real->status == 8;
                $data['step'] = $real->status == 0 ? 3 : 4;
            }
            $data['user'] = $user;
            $data['realInfo'] = $real;

            if($this->format() == 'json' || $this->format() == 'data'){
                return shopwwiSuccess($data);
            }
            $page = $this->basePage()->body([
                shopwwiAmis('alert')->title('实名认证')->className('must m-0')->body("部分功能涉及用户账户安全以及互联网信息传播规范要求，需提供完整有效的身份证件图片，以备在发生争议时有效保障维护站点与用户的最大权益。"),
                shopwwiAmis('service')->name('realApi')->id('realApi')->className('mt-4')->api(shopwwiUserUrl($this->queryPath.'?_format=json'))->body([
                    shopwwiAmis('card')->className('pt-8')->body([
                        shopwwiAmis('tpl')->className('text-center text-xl')->tpl('个人实名信息认证')->visibleOn('!this.realInfo'),
                        shopwwiAmis('tpl')->className('text-center')->tpl('提交您的个人实名信息，审核后可开通更多功能。')->visibleOn('!this.realInfo'),
                        shopwwiAmis('tpl')->className('text-center text-xl')->tpl('您提交的信息正在审核')->visibleOn('this.isNew'),
                        shopwwiAmis('tpl')->className('text-center')->tpl('提交实名认证申请后，工作人员将在3个工作日内经核对完成审核。')->visibleOn('this.isNew'),
                        shopwwiAmis('tpl')->className('text-center text-xl')->tpl('您已完成个人实名信息认证')->visibleOn('this.isOk'),
                        shopwwiAmis('tpl')->className('text-center')->tpl('已通过的实名信息无法修改，如需解绑请联系网站工作人员。')->visibleOn('this.isOk'),
                        shopwwiAmis('tpl')->className('text-center text-xl')->tpl('您的实名认证没有通过')->visibleOn('this.isFail'),
                        shopwwiAmis('tpl')->className('text-center')->tpl('${realInfo.remark}')->visibleOn('this.isFail'),
                        shopwwiAmis('tpl')->className('text-center text-xl')->tpl('您的实名认证已经解绑')->visibleOn('this.isOut'),
                        shopwwiAmis('tpl')->className('text-center')->tpl('${realInfo.remark}')->visibleOn('this.isOut'),
                        shopwwiAmis('html')->className('mt-8')->html(<<<HTML
                        <ul class="cxd-Steps cxd-Steps--Placement-vertical cxd-Steps-- cxd-Steps--horizontal">
                            <li class="cxd-StepsItem is-finish">
                                <div class="cxd-StepsItem-container">
                                    <div class="cxd-StepsItem-containerTail"></div>
                                    <div class="cxd-StepsItem-containerIcon is-success"><span class="cxd-StepsItem-icon"><svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="icon icon-check" icon="check"><path d="M13.943 3 15 4.055l-8.531 8.54L1 7.12l1.057-1.056 4.412 4.418z" fill="currentColor"></path></svg></span></div>
                                    <div class="cxd-StepsItem-containerWrapper">
                                        <div class="cxd-StepsItem-body">
                                            <div class="cxd-StepsItem-title cxd-StepsItem- is-success mt-2">
                                                <span class="cxd-StepsItem-ellText" title="实名认证">实名认证</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="cxd-StepsItem <% if(this.step > 1) { %>is-finish<% }else{ %>is-wait<% } %>">
                                <div class="cxd-StepsItem-container">
                                    <div class="cxd-StepsItem-containerTail"></div>
                                    <div class="cxd-StepsItem-containerIcon is-success"><span class="cxd-StepsItem-icon"><svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="icon icon-check" icon="check"><path d="M13.943 3 15 4.055l-8.531 8.54L1 7.12l1.057-1.056 4.412 4.418z" fill="currentColor"></path></svg></span></div>
                                    <div class="cxd-StepsItem-containerWrapper">
                                        <div class="cxd-StepsItem-body">
                                            <div class="cxd-StepsItem-title cxd-StepsItem- is-success mt-2">
                                                <span class="cxd-StepsItem-ellText" title="完善信息">完善信息</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="cxd-StepsItem <% if(this.step > 2) { %>is-finish<% }else{ %>is-wait<% } %>">
                                <div class="cxd-StepsItem-container">
                                    <div class="cxd-StepsItem-containerTail"></div>
                                    <div class="cxd-StepsItem-containerIcon is-success"><span class="cxd-StepsItem-icon"><svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="icon icon-check" icon="check"><path d="M13.943 3 15 4.055l-8.531 8.54L1 7.12l1.057-1.056 4.412 4.418z" fill="currentColor"></path></svg></span></div>
                                    <div class="cxd-StepsItem-containerWrapper">
                                        <div class="cxd-StepsItem-body">
                                            <div class="cxd-StepsItem-title cxd-StepsItem- is-success mt-2">
                                                <span class="cxd-StepsItem-ellText" title="平台审核">平台审核</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="cxd-StepsItem <% if(this.step > 3) { %>is-finish<% }else{ %>is-wait<% } %>">
                                <div class="cxd-StepsItem-container">
                                    <div class="cxd-StepsItem-containerTail"></div>
                                    <div class="cxd-StepsItem-containerIcon is-success"><span class="cxd-StepsItem-icon"><svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="icon icon-check" icon="check"><path d="M13.943 3 15 4.055l-8.531 8.54L1 7.12l1.057-1.056 4.412 4.418z" fill="currentColor"></path></svg></span></div>
                                    <div class="cxd-StepsItem-containerWrapper">
                                        <div class="cxd-StepsItem-body">
                                            <div class="cxd-StepsItem-title cxd-StepsItem- is-success mt-2">
                                                <span class="cxd-StepsItem-ellText" title="完成">完成</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        HTML),
                        shopwwiAmis('flex')->items([
                            shopwwiAmis('button')->label('重新申请认证')->level('primary')->onEvent([
                                'click' => [
                                    'actions' => [
                                        ['actionType' => 'setValue','componentId'=>'realApi','args'=>['value'=>['step'=>2]]]
                                    ]
                                ]
                            ])->visibleOn('(this.isFail || this.isOut)  && this.step != 2'),
                            shopwwiAmis('button')->label('进行身份认证')->level('primary')->onEvent([
                                'click' => [
                                    'actions' => [
                                        ['actionType' => 'setValue','componentId'=>'realApi','args'=>['value'=>['step'=>2]]]
                                    ]
                                ]
                            ])->visibleOn('!this.realInfo && this.step != 2'),


                            shopwwiAmis('form')->mode('horizontal')->className('mx-40 my-20')->wrapWithPanel(false)->api(shopwwiUserUrl($this->queryPath))->body([
                                shopwwiAmis('input-text')->name('realInfo.id_card_name')->label('真实姓名')->static(true),
                                shopwwiAmis('input-text')->name('realInfo.id_card_no')->label('身份证号码')->static(true),
                            ])->visibleOn('this.isOk'),
                        ]),

                        shopwwiAmis('form')->className('mx-40 my-20')->wrapWithPanel(false)->api(shopwwiUserUrl($this->queryPath))->reload('window')->body([
                            shopwwiAmis('hidden')->name('id_card_front')->value('${realInfo.id_card_front}'),
                            shopwwiAmis('hidden')->name('id_card_back')->value('${realInfo.id_card_back}'),
                            shopwwiAmis('hidden')->name('id_card_handle')->value('${realInfo.id_card_handle}'),
                            shopwwiAmis('input-text')->name('id_card_name')->label('真实姓名')->value('${realInfo.id_card_name}'),
                            shopwwiAmis('input-text')->name('id_card_no')->label('证件号码')->value('${realInfo.id_card_no}'),
                            shopwwiAmis('group')->body([
                                shopwwiAmis('input-image')->name('idCardFront')->value('${realInfo.idCardFrontUrl}')->label('证件正面')->initAutoFill(false)->autoFill(['id_card_front'=>'${name}'])->receiver(shopwwiUserUrl('common/upload/real')),
                                shopwwiAmis('input-image')->name('idCardBack')->value('${realInfo.idCardBackUrl}')->label('证件背面')->initAutoFill(false)->autoFill(['id_card_back'=>'${name}'])->receiver(shopwwiUserUrl('common/upload/real')),
                            ]),
                            shopwwiAmis('input-image')->name('idCardHandle')->value('${realInfo.idCardHandleUrl}')->label('手持证件照')->initAutoFill(false)->autoFill(['id_card_handle'=>'${name}'])->receiver(shopwwiUserUrl('common/upload/real')),
                            shopwwiAmis('submit')->label('提交个人实名信息')->size('lg')->level('primary')->block(true)
                        ])->visibleOn('this.step == 2')
                    ])
                ])
            ]);
            if($this->format() == 'web') return shopwwiSuccess($page);
            return $this->getUserView(['seoTitle'=>'实名认证','menuActive'=>'authReal','json'=>$page]);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }

    /**
     * 提交实名信息
     * @param Request $request
     * @return \support\Response
     */
    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'id_card_name' => 'bail|required|min:2',
                'id_card_no' => 'bail|required|min:18|max:18 ',
                'id_card_front' => 'bail|required|min:10',
                'id_card_handle' => 'bail|required|min:10',
                'id_card_back' => 'bail|required|min:10',
            ], [], [
                'id_card_name' => trans('field.id_card_name',[],'userRealname'),
                'id_card_no' => trans('field.id_card_no',[],'userRealname'),
                'id_card_front' => trans('field.id_card_front',[],'userRealname'),
                'id_card_handle' => trans('field.id_card_handle',[],'userRealname'),
                'id_card_back' => trans('field.id_card_back',[],'userRealname'),
            ])->validate();
            $user = Auth::guard($this->guard)->fail()->user();
            $params = shopwwiParams(['id_card_name','id_card_no','id_card_front','id_card_handle','id_card_back']);
            $real = RealnameService::applyReal($params,$user->id);
            return shopwwiSuccess($real);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}