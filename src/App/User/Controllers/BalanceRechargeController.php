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

use Shopwwi\Admin\App\User\Models\UserBalanceRechargeMeal;
use Shopwwi\Admin\App\User\Service\BalanceRechargeService;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;
use support\Response;

class BalanceRechargeController extends Controllers
{
    public $routePath = 'recharge'; // 当前路由模块不填写则直接控制器名
    protected $model = \Shopwwi\Admin\App\User\Models\UserBalanceRecharge::class;
    protected $activeKey = 'balanceRecharge';

    /**
     * 查询充值日志
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $user = $this->user();
            if($this->format() == 'json'){
                $list = $this->getList(new $this->model,function ($q) use ($user) {
                    return $q->where('user_id',$user->id);
                },['id'=>'desc'],['user_id','keyword']);
                return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
            }

            $page =$this->basePage()->body([
                shopwwiAmis('alert')->title('我的余额')->className('must m-0')
                    ->body("可用余额：<b class='text-success'>$user->available_balance 元</b>。 <br/> 冻结余额：<b class='text-danger'>$user->frozen_balance 元</b>。"),
                BalanceRechargeService::getIndexAmis()]);
            if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
            return $this->getUserView(['seoTitle'=>'我的充值','menuActive'=>'balanceRecharge','json'=>$page]);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }

    /**
     * 获取充值需要的数据
     * @param Request $request
     * @return \support\Response
     */
    public function create(Request $request)
    {
        try {
            $user = Auth::guard($this->guard)->fail()->user();
            $meal = UserBalanceRechargeMeal::get();
            $params = [
                'meallist' => $meal,
                'amount' => $user->available_balance
            ];
            return shopwwiSuccess($params);
        }catch (\Exception $E){
            return shopwwiError($E->getMessage());
        }

    }

    /**
     * 提交充值
     * @param Request $request
     * @return \support\Response
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            Validator::make($request->all(), [
                'amount' => 'bail|required|min:0.01',
                'mealId' => 'bail|required|min:1',
            ], [], [
                'amount' => '充值金额',
                'mealId' => '套餐编号',
            ])->validate();
            $params = shopwwiParams(['amount', 'mealId']);
            $params['user_id'] = $user->id;
            Db::connection()->beginTransaction();
            $pay = BalanceRechargeService::rechargeAdd($params,$user->id);
            Db::connection()->commit();
            return shopwwiSuccess($pay,'创建充值订单'.trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 充值详情
     * @param Request $request
     * @param $id
     * @return \support\Response
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
     * 取消充值
     * @param Request $request
     * @param $id
     * @return \support\Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            Db::connection()->beginTransaction();
            $info =(new $this->model)->where($this->key, $id)->where('user_id',$user->id)->first();
            if ($info == null) {
                throw new \Exception(trans('dataError',[],'messages'));
            }
            if($info->status){
                throw new \Exception('此状态不允许删除');
            }
            $info->delete();
            Db::connection()->commit();
            return shopwwiSuccess([],'删除订单'.trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError( $e->getMessage());
        }
    }
}