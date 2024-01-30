<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统登入鉴权
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

namespace Shopwwi\Admin\App\Admin\Controllers;

use Shopwwi\Admin\App\Admin\Models\SysRoleMenu;
use Shopwwi\Admin\App\Admin\Models\SysUser;
use Shopwwi\Admin\App\Admin\Service\SysMenuService;
use Shopwwi\Admin\Libraries\Amis\BaseController;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;

class AuthController extends BaseController
{
    public $noNeedLogin = ['login']; //不需要登入
    public $noNeedAuth = ['me','logout']; //需要登入不需要鉴权

    /**
     * 登入
     * @param Request $request
     * @return \support\Response
     */
    public function login(Request $request)
    {
        if($request->method() == 'GET'){
            $page = shopwwiAmis('page')->bodyClassName('p-0 bg-transparent')->body(
                shopwwiAmis('grid')->className('h-full')->columns([
                    shopwwiAmis('image')->src('/static/uploads/login.gif')
                        ->imageMode('original')->innerClassName('no-border w-4/5')
                        ->columnClassName('flex items-center justify-center m:hidden'),
                    shopwwiAmis('wrapper')->columnClassName('flex items-center justify-center w-full')->className('login-box')->body([
                        shopwwiAmis('wrapper')->body('平台管理中心')->className(' flex justify-center text-2xl font-semibold mb-10'),
                        shopwwiAmis('form')->wrapWithPanel(false)->api($this->getUrl('auth/login'))->body([
                            shopwwiAmis('input-text')->prefix(' ')->name('username')->placeholder('账号')->required(true)->inputControlClassName('login-input user'),
                            shopwwiAmis('input-password')->prefix(' ')->name('password')->placeholder('密码')->required(true)->inputControlClassName('login-input password'),
                            shopwwiAmis('submit')->label('登   入')->size('lg')->level('primary')->block(true),
                            shopwwiAmis('wrapper')->body('shopwwi智能管理系统 © shopwwi.com')->className('flex items-center justify-center opacity-50')
                        ])->redirect(shopwwiAdminUrl(''))
                    ])

                ])
            );
            return view('admin/login',['json'=>$page],'');
        }
        $validator = Validator::make($request->all(), [
            'username' => 'bail|required|alpha_dash',
            'password' => 'bail|required|min:6|chs_dash_pwd',
        ], [], [
            'password' => '密码',
            'username' => '用户名',
        ]);
        if($validator->fails()){
            return shopwwiValidator('数据验证失败',$validator->errors());
        }
        try {
            $params = shopwwiParams(['username','password']);
            $user = SysUser::where('username',$request->input('username'))->first();
            if($user == null){
                throw new \Exception('账号或密码不正确');
            }
            if(!password_verify($params['password'],$user->password)){
                throw new \Exception('账号或密码不正确');
            }
            if(empty($user->status)){
                throw new \Exception('账号已禁用');
            }
            $user->last_login_ip = $user->login_ip;
            $user->login_ip = \request()->getRealIp();
            $user->last_login_time = $user->login_time;
            $user->login_time = now();
            $user->login_num = Db::raw('login_num + 1');
            $user->save();
            $token = Auth::guard($this->guard)->fail()->login($user);
            return shopwwiSuccess($token);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
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
     * 获取用户信息
     * @param Request $request
     * @return \support\Response
     */
    public function me(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        $menus = SysMenuService::getMenusList();
        if($user->role_id != 1){
            $menuIds = SysRoleMenu::where('role_id',$user->role_id)->pluck('menu_id');
            $menus = $menus->whereIn('id',$menuIds);
        }
        $user->menu =  $menus->where('menu_type','!=','F')->values();
        return shopwwiSuccess($user);
    }

}
