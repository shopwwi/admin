<?php
/**
 *-------------------------------------------------------------------------s*
 * 收货地址控制器
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

use Shopwwi\Admin\App\User\Service\AddressService;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;
use support\Response;

class AddressController extends Controllers
{

    protected $model = \Shopwwi\Admin\App\User\Models\UserAddress::class;
    protected $activeKey = 'authAddress';
    /**
     * 查询消费日志
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        if($this->format() == 'json'){
            $user = $this->user(true);
            $list = $this->getList(new $this->model,function ($q) use ($user, $request) {
                $q->where('user_id',$user->id);
                if($request->input('keyword')){
                    $q->where(function ($q) use ($request) {
                        $q->where('real_name',$request->input('keyword'))
                            ->orWhere('mobile',$request->input('keyword'))
                            ->orWhere('area_info',$request->input('keyword'))
                            ->orWhere('address_info',$request->input('keyword'));
                    });
                }
                return $q;
            },['address_default'=>'asc','id'=>'desc'],['user_id','keyword']);
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        }
        $page =$this->basePage()->body([
            shopwwiAmis('alert')->title('收货地址')->body("最多可保存20个有效地址。 <br/> 可将您的常用地址设置为“默认地址”，从购物车下单时收货人信息将直接读取该条地址。"),
            AddressService::getIndexAmis()]);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
       return $this->getUserView(['seoTitle'=>'收货地址','menuActive'=>'authAddress','json'=>$page]);
    }

    public function create(Request $request)
    {
        if($this->format() == 'json'){
            try {
                $data = ['address_default'=>0];
                return shopwwiSuccess($data);
            }catch (\Exception $e){
                return  shopwwiError($e->getMessage());
            }
        }
        $form = AddressService::getAmisForm()->api('post:'.shopwwiUserUrl('address'))->initApi(shopwwiUserUrl('address/create?_format=json'));
        $page = $this->basePage()->body($form)->toolbar([$this->backButton()]);
        $page = $page->subTitle(trans('create',[],$this->trans));

        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getUserView(['seoTitle'=>'新增收货地址','menuActive'=>'authAddress','json'=>$page]);

    }

    /**
     * 新增收货地址
     * @param Request $request
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $user = $this->user(true);
        try {
            Validator::make($request->all(), [
                'area_info' => 'bail|required|min:3',
                'area_id' => 'bail|required|numeric|min:1',
                'area_ids' => 'bail|required',
                'address_info' => 'bail|required|min:1',
                'address_default' => 'bail|required|in:1,0',
                'mobile' => 'bail|required|min:11|max:11',
                'real_name' => 'bail|required|min:1|max:20',
                'telphone' => 'bail|nullable|min:6',
            ], [], [
                'area_info' => trans('field.area_info',[],'userAddress'),
                'area_id' => trans('field.area_id',[],'userAddress'),
                'area_ids' => trans('field.area_ids',[],'userAddress'),
                'address_info' => trans('field.address_info',[],'userAddress'),
                'address_default' => trans('field.address_default',[],'userAddress'),
                'mobile' => trans('field.mobile',[],'userAddress'),
                'real_name' => trans('field.real_name',[],'userAddress'),
                'telphone' => trans('field.telphone',[],'userAddress'),
            ])->validate();
            $params = shopwwiParams([
                'area_info',
                'area_id',
                'area_ids',
                'address_info',
                'address_default',
                'mobile','lat','lng',
                'real_name',
                'telphone']);
            $params['user_id'] = $user->id;
            Db::connection()->beginTransaction();
            $data =(new $this->model)->create($params);
            Db::connection()->commit();
            return shopwwiSuccess($data,trans('create',[],'messages').trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request,$id)
    {
        if($this->format() == 'json'){
            try {
                $info =(new $this->model)->where($this->key, $id)->first();
                if ($info == null) {
                    throw new \Exception(trans('dataError',[],'messages'));
                }
                $info->city = ['code'=>$info->area_id];
                $info->location = ['address'=>$info->area_info.$info->address_info,'lat'=>$info->lat,'lng'=>$info->lng];
                return shopwwiSuccess($info);
            }catch (\Exception $e){
                return  shopwwiError($e->getMessage());
            }
        }
        $form = AddressService::getAmisForm()->api('put:'.shopwwiUserUrl('address/$id'))->initApi(shopwwiUserUrl('address/$id/edit?_format=json'));
        $page = $this->basePage()->body($form)->toolbar([$this->backButton()]);
        $page = $page->subTitle(trans('create',[],$this->trans));

        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getUserView(['seoTitle'=>'修改收货地址','menuActive'=>'authAddress','json'=>$page]);
    }

    /**
     * 更新地址
     * @param Request $request
     * @param $id
     * @return \support\Response
     * @throws \Throwable
     */
    public function update(Request $request,$id)
    {
        $user = $this->user(true);
        try {
            Validator::make($request->all(), [
                'area_info' => 'bail|required|min:3',
                'area_id' => 'bail|required|min:1',
                'area_ids' => 'bail|required',
                'address_info' => 'bail|required|min:1',
                'address_default' => 'bail|required|in:1,0',
                'mobile' => 'bail|required|min:11|max:11',
                'real_name' => 'bail|required|min:1|max:20',
                'telphone' => 'bail|nullable|min:6',
            ], [], [
                'area_info' => trans('field.area_info',[],'userAddress'),
                'area_id' => trans('field.area_id',[],'userAddress'),
                'area_ids' => trans('field.area_ids',[],'userAddress'),
                'address_info' => trans('field.address_info',[],'userAddress'),
                'address_default' => trans('field.address_default',[],'userAddress'),
                'mobile' => trans('field.mobile',[],'userAddress'),
                'real_name' => trans('field.real_name',[],'userAddress'),
                'telphone' => trans('field.telphone',[],'userAddress'),
            ])->validate();
            $params = shopwwiParams([
                'area_info',
                'area_id',
                'area_ids',
                'address_info',
                'address_default',
                'mobile','lat','lng',
                'real_name',
                'telphone']);
            Db::connection()->beginTransaction();
            $info = (new $this->model)->where($this->key,$id)->where('user_id',$user->id)->first();
            if($info == null){
                throw new \Exception(trans('dataError',[],'messages'));
            }
            foreach ($params as $key=>$val){
                $info->$key = $val;
            }
            $info->save();
            Db::connection()->commit();
            return shopwwiSuccess($info,trans('edit',[],'messages').trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 地址详情
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request,$id)
    {
        $user = $this->user(true);
        try {
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
     * 删除地址
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $user = $this->user(true);
        try {
            Db::connection()->beginTransaction();
            $info = (new $this->model)->whereIn($this->key,$ids)->where('user_id',$user->id)->get();
            (new $this->model)->destroy($info->pluck($this->key));
            Db::connection()->commit();
            return shopwwiSuccess([],trans('del',[],'messages').trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 设置默认地址
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function default(Request $request,$id)
    {
        $user = $this->user(true);
        try {
            Db::connection()->beginTransaction();
            $info = (new $this->model)->where($this->key,$id)->where('user_id',$user->id)->first();
            if($info->address_default){
                $info->address_default = 0;
            }else{
                $info->address_default = 1;
            }
            $info->save();
            Db::connection()->commit();
            return shopwwiSuccess([],trans('edit',[],'messages').trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError($e->getMessage());
        }
    }
}