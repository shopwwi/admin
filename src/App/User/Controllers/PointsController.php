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

use Shopwwi\Admin\App\User\Models\UserPointLog;
use Shopwwi\Admin\App\User\Service\PointLogService;
use Shopwwi\Admin\Libraries\StatusCode;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;

class PointsController extends Controllers
{
    public $routeAction = ['log'=>['GET','POST','OPTIONS']]; //方法注册 未填写的则直接any

    /**
     * 我的积分列表
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        $user = $this->user();
        if($this->format() == 'json'){
            $list = $this->getList(new UserPointLog(),function ($q) use ($user) {
                return $q->where('user_id',$user->id);
            },['id'=>'desc'],['user_id']);
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        }

        $page =$this->basePage()->body([
            shopwwiAmis('alert')->title('我的积分')->className('must m-0')
                ->body("可用积分：<b class='text-success'>$user->available_points </b>。 <br/> 冻结积分：<b class='text-danger'>$user->frozen_points </b>。"),
            PointLogService::getIndexAmis()]);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getUserView(['seoTitle'=>'我的积分','menuActive'=>'myPoint','json'=>$page]);
    }

}