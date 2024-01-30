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
use Shopwwi\Admin\App\User\Models\UserCard;
use Shopwwi\Admin\App\User\Service\BalanceCashService;
use Shopwwi\Admin\Libraries\StatusCode;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;
use support\Response;

class BalanceCashController extends Controllers
{
    public $routePath = 'cash'; // 当前路由模块不填写则直接控制器名
    protected $model = \Shopwwi\Admin\App\User\Models\UserBalanceCash::class;

    /**
     * 查询消费日志
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {

        $user = $this->user();
        if($this->format() == 'json'){
            $list = $this->getList(new $this->model,function ($q) use ($user) {
                return $q->where('user_id',$user->id);
            },['id'=>'desc'],['user_id','keyword']);
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        }
        $page =$this->basePage()->body([
            shopwwiAmis('alert')->title('我的提现')->className('must m-0')
                ->body("可提现金额：<b class='text-success'>$user->available_balance </b>元，每 <b class='text-success'>".shopwwiConfig('cash.rule.time',1)."</b> 天,可提现<b class='text-success'>".shopwwiConfig('cash.rule.num',10)."</b>次；最低提现金额：<b class='text-success'>".shopwwiConfig('cash.min',0.01)."</b>，最高提现金额：<b class='text-success'>".shopwwiConfig('cash.max',10000)."</b>，提现手续费<b class='text-success'>".shopwwiConfig('cash.rate',0)."/％</b>
"),
            BalanceCashService::getIndexAmis($user->id)]);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getUserView(['seoTitle'=>'我的提现','menuActive'=>'balanceCash','json'=>$page]);
    }

    /**
     * 获取提现需要的数据
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            $user = Auth::guard($this->guard)->fail()->user();
            $config = shopwwiConfig('cash');
            $bankList = UserCard::where('user_id',$user->id)->get();
            $params = [
                'config' => $config,
                'bankList' => $bankList,
                'isReal' => $user->is_real,
                'amount' => $user->available_balance
            ];
            return shopwwiSuccess($params);
        }catch (\Exception $E){
            return shopwwiError($E->getMessage());
        }

    }

    /**
     * 申请提现
     * @param Request $request
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            Validator::make($request->all(), [
                'amount' => 'bail|required|min:0.01',
                'bankId' => 'bail|required|min:1',
            ], [], [
                'amount' => '提现金额',
                'bankId' => '绑卡编号',
            ])->validate();
            $params = shopwwiParams(['amount', 'bankId']);
            $params['user_id'] = $user->id;
            Db::connection()->beginTransaction();
            BalanceCashService::addCash($params['amount'],$params['bankId'],$user->id);
            Db::connection()->commit();
            return shopwwiSuccess([],'申请提现'.trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 提现详情
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request,$id)
    {
        try {
            $user = Auth::guard($this->guard)->fail()->user(true);
            $info =(new $this->model)->where($this->key, $id)->where('user_id',$user->id)->first();
            if ($info == null) {
                throw new \Exception(trans('dataError',[],'messages'));
            }
            $data['info'] = $info;
            return shopwwiSuccess($data);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 取消提现
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        try {
            $user = Auth::guard($this->guard)->fail()->user(true);
            Db::connection()->beginTransaction();
            BalanceCashService::cancelCash($id,$user->id);
            Db::connection()->commit();
            return shopwwiSuccess([],'取消提现'.trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError( $e->getMessage());
        }
    }
}