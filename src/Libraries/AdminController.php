<?php
/**
 *-------------------------------------------------------------------------s*
 * 管理后台通用控制器
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\Admin\Libraries;

use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;

class AdminController
{

    protected   $guard = "admin";
    protected   $dbConnection = null;
    protected   $model = '';
    protected   $name;
    protected   $projectName;
    protected   $orderBy = ['id' => 'desc'];
    protected   $limit;
    protected   $key = 'id';
    protected   $adminOp = false;

    /**
     * 数据设定
     * @return array
     */
    protected  function fields()
    {
        return [];
    }

    /**
     * 获取列表
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        try {
            $model = new $this->model;
            $model = shopwwiWhereParams($model, $request->all());
            if (request()->input('sortName') && in_array(request()->input('sortOrder'), array('asc', 'desc'))) {
                $model = $model->orderBy(request()->input('sortName'), request()->input('sortOrder'));
            } else {
                foreach ($this->orderBy as $key=>$value){
                    $model = $model->orderBy($key,$value);
                }
            }
            if($request->input('dataRecovery')){
                $model = $model->onlyTrashed();
            }
            $model = $this->beforeIndex($model);
            $list = $model->paginate($request->input('limit',$this->limit ?? StatusCode::PAGE_LIMIT),'*','page',$request->input('page',1));
            $data = $this->afterIndex($list);
            return shopwwiSuccess( $data,$this->name.'列表', ['page' => $list->currentPage(), 'total' => $list->total(),'hasMore' =>$list->hasMorePages()]);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 新增数据
     * @param Request $request
     * @return \support\Response
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        try {
            $data = $this->getCreate();
            return shopwwiSuccess($data);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 新增数据保存
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        try {
            $params = $this->beforeStore($user,$validator);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败', $validator->errors());
            }
            $create = $this->storing($user,$params);
            $data = $this->afterStore($user,$create);
            return shopwwiSuccess($data,trans('create',[],'messages').$this->name.trans('success',[],'messages'));
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 获取编辑数据
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function edit(Request $request,$id)
    {
        try {
            $data = $this->getEdit($id);
            return shopwwiSuccess($data);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 修改数据
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    public function update(Request $request,$id)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        try {
            $params = $this->beforeUpdate($user,$id,$validator);
            if ($validator->fails()) {
                return shopwwiValidator('数据验证失败', $validator->errors());
            }
            $update = $this->Updating($user,$params,$id);
            $data = $this->afterUpdate($user,$update);
            return shopwwiSuccess($data,trans('update',[],'messages').$this->name.trans('success',[],'messages'));
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 获取详情
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function show(Request $request,$id)
    {
        try {
            $data = $this->getShow($id);
            return shopwwiSuccess($data);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @param $id
     * @return \support\Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $user = Auth::guard($this->guard)->fail()->user();
        Db::connection($this->dbConnection)->beginTransaction();
        try {
            $data = $this->destroying($user,$ids,$id);
            Db::connection($this->dbConnection)->commit();
            AdminService::addLog('D',1,$this->projectName.trans('del',[],'messages').$this->name,$user->id,$user->username,$ids);
            return shopwwiSuccess($data,trans('del',[],'messages').$this->name.trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog('D',0,$this->projectName.trans('del',[],'messages').$this->name,$user->id,$user->username,$ids);
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 回收站还原
     * @param Request $request
     * @param $id
     * @return \support\Response
     * @throws \Throwable
     */
    public function recovery(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $user = Auth::guard($this->guard)->fail()->user();
        Db::connection($this->dbConnection)->beginTransaction();
        try {
            if($request->method() == 'DELETE'){
                $this->recoveryDelete($user,$ids,$id);
            }else{
                $this->recovering($user,$ids,$id);
            }
            Db::connection($this->dbConnection)->commit();
            AdminService::addLog($request->method() == 'DELETE'?'D':'H',1,$this->projectName.trans('recovery',[],'messages').$this->name."(". trans('number',[],'messages')."：{$id})",$user->id,$user->username,$ids);
            return shopwwiSuccess();
        } catch (\Exception $e) {
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog($request->method() == 'DELETE'?'D':'H',0,$this->projectName.trans('recovery',[],'messages').$this->name."(". trans('number',[],'messages')."：{$id})",$user->id,$user->username,$ids);
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 获取数据之前
     * @param $model
     * @return mixed
     */
    protected function beforeIndex($model){
        $model = $model->select($this->filterFiled());
        return $model;
    }

    /**
     * 获取数据之后
     * @param $list
     * @return mixed
     */
    protected function afterIndex($list){
      //  $data['list'] = $list->items();
        return $list->items();
    }

    /**
     * @return array
     */
    protected function getCreate(){
        return [];
    }

    /**
     * 新增之前数据处理
     * @param $user
     * @param $validator
     * @return array|mixed
     */
    protected function beforeStore($user,&$validator){
        $res =  $this->filterStore();
        $validator = Validator::make(\request()->all(), $res['rule'], [], $res['lang']);
        return shopwwiParams($res['filter']);  //指定字段
    }
    /**
     * 新增数据之后处理
     * @param $user
     * @param $create
     * @return mixed
     */
    protected function afterStore($user,$create){
        $data['info'] = $create;
        return $data;
    }

    /**
     * 新增数据
     * @param $user
     * @param $params
     * @return mixed
     * @throws \Throwable
     */
    protected function storing($user,$params){
        Db::connection($this->dbConnection)->beginTransaction();
        try {
            if($this->adminOp){
                $params['created_user_id'] = $user->id;
            }
            $create =(new $this->model)->create($params);
            $id = $this->key;
            $this->insertStoring($user,$create);
            Db::connection($this->dbConnection)->commit();
            AdminService::addLog('C',1,$this->projectName.trans('create',[],'messages').$this->name."(". trans('number',[],'messages')."：{$create->$id})",$user->id,$user->username,$params);
            return $create;
        }catch (\Exception $e){
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog('C',0,$this->projectName.trans('ShopwwiCommonCreateFail',[],'messages'),$user->id,$user->username,$params,$e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $user
     * @param $create
     */
    protected function insertStoring($user,$create){

    }

    /**
     * 获取修改数据
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function getEdit($id){
        $info =(new $this->model)->where($this->key, $id)->first();
        if ($info == null) {
            throw new \Exception(trans('dataError',[],'messages'));
        }
        $data = $this->insertGetEdit($info,$id);
        return $data;
    }

    /**
     * 编辑返回数据插入
     * @param $info
     * @param $id
     * @return mixed
     */
    protected function insertGetEdit($info,$id){
        $data['info'] = $info;
        return $data;
    }

    /**
     * 修改之前数据处理
     * @param $user
     * @param $id
     * @return array|mixed
     */
    protected function beforeUpdate($user,$id,&$validator){
        $res =  $this->filterUpdate();
        $validator = Validator::make(\request()->all(), $res['rule'], [], $res['lang']);
        return shopwwiParams($res['filter']);  //指定字段
    }

    /**
     * 修改数据
     * @param $user
     * @param $params
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    protected function updating($user,$params,$id){
        $info = (new $this->model)->where($this->key,$id)->first();
        if($info == null){
            throw new \Exception(trans('dataError',[],'messages'));
        }
        Db::connection($this->dbConnection)->beginTransaction();
        try {
            $oldInfo = $info->replicate();
            foreach ($params as $key=>$val){
                $info->$key = $val;
            }
            if($this->adminOp){
                $info->updated_user_id = $user->id;
            }
            $this->insertUpdating($user,$params,$info,$oldInfo);
            $info->save();
            Db::connection($this->dbConnection)->commit();
            AdminService::addLog('E',1,$this->projectName.trans('update',[],'messages').$this->name."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params);
            return $info;
        }catch (\Exception $e){
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog('E',0,$this->projectName.trans('update',[],'messages').$this->name."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params,$e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 修改数据时插入
     * @param $user
     * @param $params
     * @param $info
     * @param $id
     */
    protected function insertUpdating($user,$params,$info,$oldInfo){

    }

    /**
     * 修改数据之后抛出
     * @param $user
     * @param $update
     * @return mixed
     */
    protected function afterUpdate($user,$update){
        $data['info'] = $update;
        return $data;
    }

    /**
     * 获取详情
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function getShow($id){
        $info =(new $this->model)->where($this->key, $id)->select($this->filterFiled('show'))->first();
        if ($info == null) {
            throw new \Exception(trans('dataError',[],'messages'));
        }
        $data['info'] = $info;
        return $data;
    }

    /**
     * 删除数据
     * @param $user
     * @param $ids
     * @param $id
     * @return array
     */
    protected function destroying($user,$ids,$id){
        $info = (new $this->model)->whereIn($this->key,$ids)->get();
        (new $this->model)->destroy($info->pluck($this->key));
        return [];
    }

    /**
     * 数据恢复
     * @param $user
     * @param $ids
     * @param $id
     */
    protected function recovering($user,$ids,$id){
        if(is_numeric($id) && empty($id)){
            (new $this->model)->onlyTrashed()->restore();
        }else{
            (new $this->model)->whereIn($this->key,$ids)->onlyTrashed()->restore();
        }
    }

    /**
     * 数据彻底删除
     * @param $user
     * @param $ids
     * @param $id
     */
    protected function recoveryDelete($user,$ids,$id){
        if(is_numeric($id) && empty($id)){
            (new $this->model)->onlyTrashed()->forceDelete();
        }else{
            (new $this->model)->whereIn($this->key,$ids)->onlyTrashed()->forceDelete();
        }
        return [];
    }
    protected function filterFiled($type = 'list')
    {
        $filter = [];
        foreach ($this->fields() as $k=>$v){
            if($type==='list' ? $v->showOnIndex : $v->showOnDetail){
                $filter[] = $v->attribute;
            }
        }
        return $filter;
    }
    protected function filterStore(){
        $rule = [];
        $filter = [];
        $lang = [];
        foreach ($this->fields() as $k=>$v){
            if($v->showOnCreation){
                $rule = array_merge($rule,array_merge_recursive(
                    [$v->attribute => $v->rules], [$v->attribute => $v->creationRules]
                ));
                $lang[$v->attribute] = $v->name;
                $filter[$v->attribute] = $v->value;
            }
        }
        return [
            'rule'=> $rule,'filter' => $filter, 'lang' =>$lang
        ];
    }
    protected function filterUpdate(){
        $rule = [];
        $filter = [];
        $lang = [];
        foreach ($this->fields() as $k=>$v){
            if($v->showOnUpdate){
                $rule = array_merge($rule,array_merge_recursive(
                    [$v->attribute => $v->rules], [$v->attribute => $v->updateRules]
                ));
                $lang[$v->attribute] = $v->name;
                $filter[$v->attribute] = $v->value;
            }
        }
        return [
            'rule'=> $rule,'filter' => $filter, 'lang' =>$lang
        ];
    }
}