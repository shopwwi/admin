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

use Shopwwi\Admin\App\User\Models\UserBalanceLog;
use Shopwwi\Admin\App\User\Service\BalanceLogService;
use Shopwwi\Admin\Libraries\StatusCode;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;

class BalanceController extends Controllers
{
    public function index(Request $request)
    {
        $user = $this->user();
        if($this->format() == 'json'){
            $list = $this->getList(new UserBalanceLog(),function ($q) use ($user) {
                return $q->where('user_id',$user->id);
            },['id'=>'desc'],['user_id','keyword']);
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        }

        $page =$this->basePage()->body([
            shopwwiAmis('alert')->title('我的余额')->className('must m-0')->body("可用余额：<b class='text-success'>$user->available_balance 元</b>。 <br/> 冻结余额：<b class='text-danger'>$user->frozen_balance 元</b>。"),
            BalanceLogService::getIndexAmis()]);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getUserView(['seoTitle'=>'我的余额','menuActive'=>'balanceIndex','json'=>$page]);
    }


    /**
     * 余额明细
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function show(Request $request,$id)
    {
        try {
            $user = Auth::guard($this->guard)->fail()->user(true);
            $info =UserBalanceLog::where($this->key, $id)->where('user_id',$user->id)->first();
            if ($info == null) {
                throw new \Exception(trans('dataError',[],'messages'));
            }
            $data['info'] = $info;
            return shopwwiSuccess($data);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }
}