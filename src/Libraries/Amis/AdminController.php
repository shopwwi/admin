<?php
/**
 *-------------------------------------------------------------------------s*
 *
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2024 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author     8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\Admin\Libraries\Amis;


use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\Admin\Libraries\Amis\Traits\UseDestroyTraits;
use Shopwwi\Admin\Libraries\Amis\Traits\UseFormTraits;
use Shopwwi\Admin\Libraries\Amis\Traits\UseListTraits;
use Shopwwi\Admin\Libraries\Amis\Traits\UseShowTraits;
use Shopwwi\Admin\Libraries\Amis\Traits\UseTraits;
use support\Request;
use support\Response;

class AdminController extends BaseController
{
    use UseTraits,UseListTraits,UseFormTraits,UseShowTraits,UseDestroyTraits;
    protected $trans = "messages";
    protected $adminOp = false;
    protected $dbConnection = null;
    protected $queryPath = '';
    protected $orderBy = [];
    protected $key = 'id';
    protected $unset =[];

    /**
     * 操作框
     * @return Operation
     */
    protected function operation()
    {
        return $this->rowActions();
    }

    protected function setTips(){
        return '';
    }



    /**
     * 首页/列表页
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        if($this->format() == 'json') return $this->jsonList(false,$this->unset);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($this->amisList());
        return $this->getAdminView(['json'=>$this->amisList()]);
    }

    /**
     * 详情页
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function show(Request $request,$id)
    {
        if($this->format() == 'json'){
            return $this->jsonShow($id);
        }
        $page = $this->basePage()->toolbar([$this->backButton()])->body($this->htmlShow($id));

       // if (!$this->isTabMode()) {
            $page = $page->subTitle(trans('show',[],$this->trans));
       // }
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getAdminView(['json'=>$page]);
    }

    /**
     * 新增页面
     * @param Request $request
     * @return \support\Response
     */
    public function create(Request $request)
    {
        if($this->format() == 'json'){
            try {
                $data = $this->getCreate();
                return shopwwiSuccess($data);
            }catch (\Exception $e){
                return  shopwwiError($e->getMessage());
            }
        }
        $form = $this->form()->api($this->useAmisStoreUrl())->initApi($this->useAmisCreateUrl())->resetAfterSubmit(true)->onEvent([
            'submitSucc' => [
                'actions' => [[
                    'actionType' => 'dialog',
                    'dialog'     => [
                        'title' => '操作提示',
                        'body' => '${event.data.result.msg}',
                        'actions' => [
                            shopwwiAmis('button')->label('继续新增')->actionType('confirm')->primary(true),
                            shopwwiAmis('button')->label('返回列表')->onClick('window.history.back()'),
                        ]
                    ],
                ]],
            ],
        ]);
        $page = $this->basePage()->body($form)->toolbar([$this->backButton()]);
//        if (!$this->isTabMode()) {
            $page = $page->subTitle(trans('create',[],$this->trans));
//        }
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getAdminView(['json'=>$page]);
    }

    /**
     * 修改保存
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        try {
            $params = $this->beforeStore($this->admin(),$validator);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败', $validator->errors());
            }
            $create = $this->storing($this->admin(),$params);
            $data = $this->afterStore($this->admin(),$create);
            return shopwwiSuccess($data);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 修改页面
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function edit(Request $request,$id)
    {
        if($this->format() == 'json'){
            try {
                $data = $this->getEdit($id);
                return shopwwiSuccess($data);
            }catch (\Exception $e){
                return  shopwwiError($e->getMessage());
            }
        }
        $page = $this->getAmisEdit($id);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
        return $this->getAdminView(['json'=>$page]);
    }

    /**
     * 修改保存
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request,$id)
    {
        try {
            $params = $this->beforeUpdate($this->admin(),$id,$validator);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败', $validator->errors());
            }
            $update = $this->updating($this->admin(),$params,$id);
            $data = $this->afterUpdate($this->admin(),$update);
            return shopwwiSuccess($data,trans('update',[],$this->trans).trans('success',[],'messages'));
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        try {
            $this->beforeDestroy($this->admin(),$ids,$id);
            $del = $this->destroying($this->admin(),$ids,$id);
            $data = $this->afterDestroy($this->admin(),$del);
            return shopwwiSuccess($data,trans('delete',[],$this->trans).trans('success',[],'messages'));
        } catch (\Exception $e) {
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 回收站
     * @param Request $request
     * @return Response
     */
    public function recovery(Request $request)
    {
        if($this->format() == 'json') return $this->jsonList(true);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($this->recoveryList());
        return $this->getAdminView(['json'=>$this->recoveryList()]);
    }

    /**
     * 恢复
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function restore(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $admin = $this->admin(true);
        try {
            if(is_numeric($id) && empty($id)){
                (new $this->model)->onlyTrashed()->restore();
            }else{
                (new $this->model)->whereIn($this->key,$ids)->onlyTrashed()->restore();
            }
            AdminService::addLog('H',1,trans('restore',[],'messages')."(". trans('number',[],'messages')."：{$id})",$admin->id,$admin->username,$ids);
            return shopwwiSuccess([],trans('restore',[],$this->trans).trans('success',[],'messages'));
        } catch (\Exception $e) {
            AdminService::addLog('H',0,trans('restore',[],'messages')."(". trans('number',[],'messages')."：{$id})",$admin->id,$admin->username,$ids);
            return shopwwiError($e->getMessage());
        }

    }

    /**
     * 彻底销毁
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function erasure(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $admin = $this->admin(true);
        try {
            if(is_numeric($id) && empty($id)){
                (new $this->model)->onlyTrashed()->forceDelete();
            }else{
                (new $this->model)->whereIn($this->key,$ids)->onlyTrashed()->forceDelete();
            }
            AdminService::addLog('E',1,trans('erasure',[],'messages')."(". trans('number',[],'messages')."：{$id})",$admin->id,$admin->username,$ids);
            return shopwwiSuccess([],trans('erasure',[],$this->trans).trans('success',[],'messages'));
        } catch (\Exception $e) {
            AdminService::addLog('E',0,trans('erasure',[],'messages')."(". trans('number',[],'messages')."：{$id})",$admin->id,$admin->username,$ids);
            return shopwwiError($e->getMessage());
        }

    }

}