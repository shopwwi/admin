<?php
/**
 *-------------------------------------------------------------------------s*
 *
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

use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\App\User\Service\AuthService;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\Admin\Logic\WechatMpLogic;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;
use support\Response;

class AuthController extends Controllers
{
    public $noNeedLogin = ['login', 'code', 'status', 'app']; //不需要登入
    public $routeAction = ['status' => ['GET', 'OPTIONS']]; //方法注册 未填写的则直接any

    /**
     * 登入
     * @param Request $request
     * @return \support\Response
     */
    public function login(Request $request)
    {
        if($this->format() == 'json'){
            $validator = Validator::make($request->all(), [
                'account' => 'required|min:3|max:28',
                'password' => 'required|min:6|max:35'
            ], [], [
                'account' => '用户名',
                'password' => '密码'
            ]);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败',$validator->errors());
            }
            $params = shopwwiParams(['account', 'password']);
            try {
                $token = AuthService::login($params['account'], $params['password']);
                return shopwwiSuccess($token);
            } catch (\Exception $e) {
                return shopwwiError($e->getMessage());
            }
        }
        $backUrl = $request->header('Referer') ?? shopwwiUserUrl('');
        $page = $this->getAmisBody(
            shopwwiAmis('link')->body('还没有账号？立即注册')->href(shopwwiUserUrl('auth/register'))->blank(false),
            shopwwiAmis('flex')->className('w-full h-full')->items([
            shopwwiAmis('carousel')->options([
                ["image" => ""]
            ])->height(600)->width(422)->className('must m:hidden border-0'),
            shopwwiAmis('wrapper')->className('flex-1')->body(
                shopwwiAmis('wrapper')->className('login-box')->body([
                        shopwwiAmis('tabs')->tabs([
                            ['title' => '登入', 'body' => [
                                shopwwiAmis('form')->wrapWithPanel(false)->api(shopwwiUserUrl('auth/login?_format=json'))->body([
                                    shopwwiAmis('input-text')->prefix(' ')->name('account')->placeholder('账号/邮箱/手机号')->required(true)->inputControlClassName('login-input user'),
                                    shopwwiAmis('input-password')->prefix(' ')->name('password')->placeholder('密码')->required(true)->inputControlClassName('login-input password'),
                                    shopwwiAmis('flex')->items([
                                        shopwwiAmis('checkbox')->option('记住登入')->name('no'),
                                        shopwwiAmis('link')->href(shopwwiUserUrl('auth/forget'))->body('忘记密码？')->className('pb-2')->blank(false)
                                    ])->justify('space-between')->alignItems('flex-start'),
                                    shopwwiAmis('submit')->label('登   入')->size('lg')->level('primary')->block(true)
                                ])->redirect($backUrl)->className('mt-4')]
                            ],
                            ['title' => '短信码登入', 'body' => [
                                shopwwiAmis('form')->wrapWithPanel(false)->api(shopwwiUserUrl('auth/sms?_format=json'))->body([
                                    shopwwiAmis('input-text')->prefix(' ')->name('account')->placeholder('邮箱/手机号')->required(true)->inputControlClassName('login-input user'),
                                    shopwwiAmis('input-text')->prefix(' ')->name('code')->placeholder('短信码')->required(true)->inputControlClassName('login-input code')
                                        ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                                            ->actionType('ajax')->api(shopwwiUserUrl('auth/code?account=${account}&type=login'))->className('must ml-10')
                                        ),
                                    shopwwiAmis('flex')->items([
                                        shopwwiAmis('checkbox')->option('记住登入')->name('no'),
                                        shopwwiAmis('link')->href(shopwwiUserUrl('auth/forget'))->body('忘记密码？')->blank(false)
                                    ])->justify('space-between')->alignItems('flex-start'),
                                    shopwwiAmis('submit')->label('登   入')->size('lg')->level('primary')->block(true)
                                ])->redirect($backUrl)->className('mt-4')
                            ]]
                        ]),
                        shopwwiAmis('flex')->alignItems('center')->items([
                            shopwwiAmis('flex')->alignItems('center')->items([
                                shopwwiAmis('wrapper')->body('其他方式:')
                            ]),
                            shopwwiAmis('link')->href(shopwwiUserUrl('auth/register'))->body('注册账号')->blank(false)
                        ])->justify('space-between')
                    ]

                ))
        ])->alignItems('center'));
        if($this->format() == 'json') return shopwwiSuccess($page);
        return view('user/login', ['json' => $page], '', '');
    }

    /**
     * 通过验证码登入/注册
     * @param Request $request
     * @return Response
     */
    public function sms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|min:3|max:28',
            'code' => 'required|numeric'
        ], [], [
            'account' => '用户名',
            'code' => '验证码'
        ]);
        if ($validator->fails()) {
            return shopwwiValidator('数据验证失败',$validator->errors());
        }
        $params = shopwwiParams(['account', 'code']);
        try {
            $token = AuthService::smsLogin($params['account'], $params['code']);
            return shopwwiSuccess($token);
        } catch (\Exception $e) {
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 获取验证码
     * @param Request $request
     * @return \support\Response|void
     */
    public function code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|min:3|max:28',
            'type' => 'required|in:login,register,forget'
        ], [], [
            'account' => '手机号/邮箱',
            'type' => '类型'
        ]);
        if ($validator->fails()) {
            return shopwwiValidator('数据验证失败',$validator->errors());
        }
        $params = shopwwiParams(['account', 'type']);
        try {
            $user = Auth::guard($this->guard)->fail(false)->user();
            AuthService::sendCode($params['account'], $params['type'], $request->getRealIp(), $user->id ?? 0);
            return shopwwiSuccess(['authCodeVerifyTime' => shopwwiConfig('siteRule.authCodeVerifyTime',5), 'authCodeResendTime' => shopwwiConfig('siteRule.authCodeResendTime',60)]);
        } catch (\Exception $e) {
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 注册
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {

        if($this->format() == 'json'){
            $validator = Validator::make($request->all(), [
                'account' => 'required|min:3|max:28',
                'code' => 'required|min:6|max:6',
                'password' => 'required|min:6|max:35',
            ], [], [
                'account' => '手机号/邮箱',
                'code' => '验证码',
                'password' => '密码',
            ]);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败',$validator->errors());
            }
            $params = shopwwiParams(['account', 'password', 'code']);
            try {
                $token = AuthService::register($params);
                return shopwwiSuccess($token,'注册成功');
            } catch (\Exception $e) {
                return shopwwiError($e->getMessage());
            }
        }
        $page = $this->getAmisBody(
            shopwwiAmis('link')->body('已有账号?立即登入')->href(shopwwiUserUrl('auth/login'))->blank(false),
            shopwwiAmis('flex')->className('w-full h-full')->items([
                shopwwiAmis('wrapper')->className('login-box reg-box py-4')->body([
                        shopwwiAmis('form')->wrapWithPanel(false)->api(shopwwiUserUrl('auth/register?_format=json'))->body([
                            shopwwiAmis('input-text')->prefix(' ')->name('account')->placeholder('邮箱/手机号')->required(true)->inputControlClassName('login-input user'),
                            shopwwiAmis('input-text')->prefix(' ')->name('code')->placeholder('短信码')->required(true)->inputControlClassName('login-input code')
                                ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(shopwwiConfig('siteRule.authCodeResendTime',60))->countDownTpl('${timeLeft} 秒后重发')
                                    ->actionType('ajax')->api(shopwwiUserUrl('auth/code?account=${account}&type=register'))
                                ),
                            shopwwiAmis('input-password')->prefix(' ')->name('password')->placeholder('密码')->required(true)->inputControlClassName('login-input password'),
                            shopwwiAmis('flex')->items([
                                shopwwiAmis('checkbox')->option('我同意')->name('no'),
                                shopwwiAmis('button')->actionType('dialog')->dialog(['title'=>'注册协议','body'=>'5445'])->label('《注册协议》')->level('link')->className('must p-0')
                            ])->justify('flex-start')->alignItems('flex-start'),
                            shopwwiAmis('submit')->label('注册')->size('lg')->level('primary')->block(true)
                        ])->redirect(shopwwiUserUrl('auth/login'))->className('mt-4'),
                        shopwwiAmis('flex')->alignItems('center')->justify('space-between')->items([
                            shopwwiAmis('wrapper')->body('已有账号？'),
                            shopwwiAmis('link')->href(shopwwiUserUrl('auth/login'))->body('直接登入')->blank(false)
                        ])
                    ]

                )
            ])->alignItems('center'));
        if($this->format() == 'json') return shopwwiSuccess($page);
        return view('user/login', ['json' => $page], '', '');
    }

    /**
     * 找回密码
     * @param Request $request
     * @return Response
     */
    public function forget(Request $request)
    {
        if($this->format() == 'json'){
            $validator = Validator::make($request->all(), [
                'account' => 'required|min:3|max:28',
                'code' => 'required|min:6|max:6',
                'password' => 'required|min:6|max:35',
            ], [], [
                'account' => '手机号/邮箱',
                'code' => '验证码',
                'password' => '密码',
            ]);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败',$validator->errors());
            }
            $params = shopwwiParams(['account', 'password', 'code']);
            try {
                AuthService::forget($params);
                return shopwwiSuccess([],'找回密码成功');
            } catch (\Exception $e) {
                return shopwwiError($e->getMessage());
            }
        }
        $page = $this->getAmisBody(
            shopwwiAmis('link')->body('已有账号?立即登入')->href(shopwwiUserUrl('auth/login'))->blank(false),
            shopwwiAmis('flex')->className('w-full h-full')->items([
                shopwwiAmis('wrapper')->className('login-box reg-box py-4')->body([
                        shopwwiAmis('form')->wrapWithPanel(false)->api(shopwwiUserUrl('auth/forget?_format=json'))->body([
                            shopwwiAmis('input-text')->prefix(' ')->name('account')->placeholder('邮箱/手机号')->required(true)->inputControlClassName('login-input user'),
                            shopwwiAmis('input-text')->prefix(' ')->name('code')->placeholder('短信码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                                ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                                    ->actionType('ajax')->api(shopwwiUserUrl('auth/code?account=${account}&type=forget'))
                                ),
                            shopwwiAmis('input-password')->prefix(' ')->name('password')->placeholder('请输入重置密码')->required(true)->inputControlClassName('login-input password'),
                            shopwwiAmis('flex')->items([
                                shopwwiAmis('checkbox')->option('我同意')->name('no'),
                                shopwwiAmis('button')->actionType('dialog')->dialog(['title'=>'注册协议','body'=>'5445'])->label('《注册协议》')->level('link')->className('must p-0')
                            ])->justify('flex-start')->alignItems('flex-start'),
                            shopwwiAmis('submit')->label('确认找回')->size('lg')->level('primary')->block(true)
                        ])->redirect(shopwwiUserUrl('auth/login'))->className('mt-4'),
                        shopwwiAmis('flex')->alignItems('center')->justify('space-between')->items([
                            shopwwiAmis('wrapper')->body('已有账号？'),
                            shopwwiAmis('link')->href(shopwwiUserUrl('auth/login'))->body('直接登入')->blank(false)
                        ])
                    ]

                )
            ])->alignItems('center'));
        if($this->format() == 'json') return shopwwiSuccess($page);
        return view('user/login', ['json' => $page], '', '');
    }

    /**
     * 获取会员信息
     * @param Request $request
     * @return Response
     */
    public function me(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        return shopwwiSuccess($user);
    }

    /**
     * 退出
     */
    public function logout()
    {
        Auth::guard($this->guard)->logout();
        return shopwwiSuccess();
    }

    /**
     * 微信登入
     * @param Request $request
     * @return Response|void
     */
    public function wechat(Request $request)
    {
        try {
            $params = shopwwiParams(['code', 'inviteId']);
            $oauth = WechatMpLogic::getOauth();
            $user = $oauth->userFromCode($params['code']);
            return shopwwiSuccess();
        } catch (\Exception $e) {
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * app登入
     * @param Request $request
     * @return Response
     */
    public function app(Request $request)
    {
        try {
            $params = shopwwiParams(['platform' => 'wechat']);
            return shopwwiSuccess();
        } catch (\Exception $e) {
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 获取登入接口信息
     * @param Request $request
     * @return Response
     */
    public function status(Request $request)
    {
        try {
            $config = SysConfigService::getRowSetting(['wechatConfig', 'authApi'], true);
            return shopwwiSuccess([
                'qq' => !empty($config['authApi']['qq']['used']),
                'sina' => !empty($config['authApi']['sina']['used']),
                'wechat' => !empty($config['wechatConfig']['app_id']['app_id']),
            ]);
        } catch (\Exception $e) {
            return shopwwiSuccess($e->getMessage());
        }
    }

    private function getAmisBody($right,$body){
        return shopwwiAmis('page')->bodyClassName('must p-0 bg-transparent')->body(
            [
                shopwwiAmis('wrapper')->body(
                    shopwwiAmis('flex')->items([
                        shopwwiAmis('link')->href('/')->body(
                            shopwwiAmis('image')->src(shopwwiConfig('siteInfo.siteLogo','/static/uploads/logo.svg'))->imageMode('original')->innerClassName('must border-0')
                        ),
                        shopwwiAmis('wrapper')->body($right)
                    ])->className('max-w-5xl mx-auto')->alignItems('center')->justify('space-between')
                )->className('hd'),
                shopwwiAmis('wrapper')->body(
                    $body
                )->className('max-w-5xl mx-auto must mt-20 px-4 bg-white'),
                shopwwiAmis('flex')->alignItems('center')->items('Shopwwi 智能管理系统')->className('py-4')
            ]
        );
    }


}