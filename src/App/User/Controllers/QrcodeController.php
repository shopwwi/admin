<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员签到
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

use support\Request;

class QrcodeController extends Controllers
{
    public $noNeedLogin = ['login']; //不需要登入
    public $routeAction = ['status'=>['GET','OPTIONS']]; //方法注册 未填写的则直接any
    public function index(Request $request)
    {
        try {

        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}