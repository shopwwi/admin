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

use Shopwwi\Admin\App\User\Models\UserCard;
use Shopwwi\Admin\App\User\Models\UserRealname;
use Shopwwi\Admin\App\User\Service\UserBankService;
use Shopwwi\Admin\Libraries\StatusCode;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;
use support\Response;

class BankController extends Controllers
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserCard::class;
    protected $activeKey = 'balanceBank';
    /**
     * 查询绑卡列表
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $user = $this->user(true);
            if($this->format() == 'json'){
                $list = $this->getList(new $this->model,function ($q) use ($user) {
                    return $q->where('status',1)->where('user_id',$user->id);
                },['id'=>'desc'],['user_id','keyword']);
                return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
            }
            $page =$this->basePage()->body([
                shopwwiAmis('alert')->title('我的银行卡')->className('must m-0')
                    ->body("绑定的银行卡不可修改，可解绑后重新绑定"),
                UserBankService::getIndexAmis()]);
            if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
            return $this->getUserView(['seoTitle'=>'我的银行卡','menuActive'=>'balanceBank','json'=>$page]);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }

    /**
     * 申请绑卡
     * @param Request $request
     * @return Response|void
     */
    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'bank_name' => 'bail|nullable|min:3',
                'bank_type' => 'bail|required|in:BANK,ALIPAY,WECHAT ',
                'bank_branch' => 'bail|nullable|min:10',
                'bank_account' => 'bail|required|min:3',
                'mobile' => 'bail|required|min:11|max:11',
                'bank_username' =>  'bail|required|min:2',
            ], [], [
                'bank_name' => trans('field.bank_name',[],'userCard'),
                'bank_account' => trans('field.bank_account',[],'userCard'),
                'bank_username' => trans('field.bank_username',[],'userCard'),
                'bank_branch' => trans('field.bank_branch',[],'userCard'),
                'bank_type' => trans('field.bank_type',[],'userCard'),
                'mobile' => trans('field.mobile',[],'userCard'),
            ])->validate();
            $user = Auth::guard($this->guard)->fail()->user();
            if(empty($user->is_real)){
                throw new \Exception('请先完成实名认证');
            }
            $params = shopwwiParams(['bank_name','bank_type','bank_branch','bank_account','mobile','bank_username']);
            $real = UserRealname::where('user_id',$user->id)->where('status',1)->first();
            if($params['bank_type'] == 'ALIPAY'){
                $params['bank_name'] = '支付宝';
            }
            if($params['bank_type'] == 'WECHAT'){
                $params['bank_name'] = '微信零钱';
            }
            if($real == null || $real->id_card_name != $params['bank_username']){
                throw new \Exception('只能绑定实名账户');
            }

            $params['user_id'] = $user->id;
            $params['status'] = 1;
            $card = UserCard::create($params);
            return shopwwiSuccess($card);
        }catch (\Exception $e){
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 获取详情
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request,$id)
    {
        try {
            $user = Auth::guard($this->guard)->user(true);
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
     * 解除绑卡
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            Db::connection()->beginTransaction();
            $info = (new $this->model)->whereIn($this->key,$ids)->where('user_id',$user->id)->get();
            foreach($info as $item){
                $item->status = 0;
                $item->save();
            }
//            (new $this->model)->destroy($info->pluck($this->key));
            Db::connection()->commit();
            return shopwwiSuccess([],'解除绑定成功');
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError($e->getMessage());
        }
    }
}