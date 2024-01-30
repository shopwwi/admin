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

use Shopwwi\Admin\App\Admin\Models\SysPay;
use Shopwwi\Admin\App\Admin\Models\SysPayment;
use Shopwwi\Admin\Logic\PayLogic;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;
use support\Response;
use Yansongda\Pay\Pay;

class PayController extends Controllers
{
    /**
     * 获取支付信息
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request,$id)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            $clientType =  $request->input('client_type','web');
            $info = SysPay::where('pay_sn',$id)->where('user_id',$user->id)->first();
            if($info == null){
                throw new \Exception('支付单不存在');
            }
            $data['info'] = $info;
            $data['paymentList'] = SysPayment::where('status',1)->get();
            return shopwwiSuccess($data);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 微信支付
     * @param Request $request
     * @param $id
     * @return Response|void
     */
    public function wxpay(Request $request,$id)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            $params = shopwwiParams(['code','client_type'=>'web']);
            $info = SysPay::where('pay_sn',$id)->where('user_id',$user->id)->first();
            if($info == null || $info->status ==  1){
                throw new \Exception('已支付');
            }
            $info->payment_code = 'wechat';
            $info->payment_name = '微信支付';
            $info->pay_client_type = $params['client_type'];
            $info->save();

            $order = [
                'out_trade_no' => time().'',
                'description' => 'subject-测试',
                'amount' => [
                    'total' => 1,
                ],
                'payer' => [
                    'openid' => 'onkVf1FjWS5SBxxxxxxxx',
                ],
            ];
            Pay::config(PayLogic::config());
            $result = Pay::wechat()->mp($order);
        }catch (\Exception $e){

            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 获取订单状态
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function status(Request $request,$id)
    {
        try {
            $info = SysPay::where('pay_sn',$id)->first();
            if($info == null){
                throw new \Exception('支付单不存在');
            }
            return shopwwiSuccess($info);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}