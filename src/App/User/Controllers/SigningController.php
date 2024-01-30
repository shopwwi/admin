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

use Shopwwi\Admin\App\Admin\Service\User\PointService;
use Shopwwi\Admin\App\User\Models\UserSigning;
use Shopwwi\Admin\Libraries\StatusCode;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;

class SigningController extends Controllers
{

    /**
     * 查询积分日志
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        try {
            $user = $this->user(true);
            $list = $this->getList(new UserSigning(),function ($q) use ($user) {
                return $q->where('user_id',$user->id);
            },['id'=>'desc'],['user_id','keyword']);
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 签到
     * @param Request $request
     * @return \support\Response
     */
    public function store(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        try {
            PointService::userSigningPoints($user);
            return shopwwiSuccess();
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }
}