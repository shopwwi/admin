<?php

namespace Shopwwi\Admin\Libraries\Amis\Traits;

use Shopwwi\Admin\Amis\Grid;
use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\Admin\Libraries\Validator;

use support\Db;
use support\Response;

trait UseFormTraits
{
    protected $useFormGrid = true;
    protected function form($type = 'create'){
        if($this->useFormGrid){
            return $this->baseForm()->body([
                Grid::make()->gap('lg')->gapRow(5)->columns($this->filterFormColumns($type))
            ]);
        }else{
            return $this->baseForm()->body($this->filterFormColumns($type));
        }

//            ->onEvent([
//            'submitSucc' => [
//                'actions' => [
//                  //  'actionType' => 'refresh',
//                    //'script'     => 'setTimeout(()=>(window.location.reload()), 1200)',
//                ],
//            ],
//        ]);
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
            AdminService::addLog('C',1,trans('create',[],'messages').trans('projectName',[],$this->trans)."(". trans('id',[],'messages')."：{$create->$id})",$user->id,$user->username,$params);
            return $create;
        }catch (\Exception $e){
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog('C',0,trans('create',[],'messages').trans('projectName',[],$this->trans).trans('error',[],'messages'),$user->id,$user->username,$params,$e->getMessage());
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
     * @return Response
     * @throws \Exception
     */
    protected function getEdit($id){
        $info =(new $this->model)->where($this->key, $id)->first();
        if ($info == null) {
            throw new \Exception(trans('dataError',[],'messages'));
        }
        return $this->insertGetEdit($info,$id);
    }

    protected function getCreate(){
        return [];
    }

    /**
     * 编辑返回数据插入
     * @param $info
     * @param $id
     * @return mixed
     */
    protected function insertGetEdit($info,$id){
        return $info;
    }

    protected function getAmisEdit($id){
        $form = $this->form('edit')->api($this->useAmisUpdateUrl($id))->initApi($this->useAmisEditUrl($id))->onEvent([
            'submitSucc' => [
                'actions' => [[
                    'actionType' => 'dialog',
                    'dialog'     => [
                        'title' => '操作提示',
                        'body' => '${event.data.result.msg}',
                        'actions' => [
                            shopwwiAmis('button')->label('继续修改')->primary(true)->onClick('window.location.reload()'),
                            shopwwiAmis('button')->label('返回列表')->onClick('window.history.back()'),
                        ]
                    ],
                ]],
            ],
        ]);
        $page = $this->basePage()->toolbar([$this->backButton()])->body($form);
        $page = $page->subTitle(trans('update',[],$this->trans));
        return $page;
    }


    /**
     * 修改之前数据处理
     * @param $user
     * @param $id
     * @param $validator
     * @return array|mixed
     */
    protected function beforeUpdate($user,$id,&$validator){
        $res =  $this->filterUpdate($id);
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
            AdminService::addLog('E',1,trans('projectName',[],$this->trans).trans('update',[],'messages')."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params);
            return $info;
        }catch (\Exception $e){
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog('E',0,trans('projectName',[],$this->trans).trans('update',[],'messages')."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params,$e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 修改数据时插入
     * @param $user
     * @param $params
     * @param $info
     * @param $oldInfo
     */
    protected function insertUpdating($user,$params,&$info,$oldInfo){
        return true;
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

    protected function filterFormColumns($type = 'create'){
        $list = [];
        if($type == 'create'){
            foreach ($this->projectFields as $k=>$v){
                if(in_array($v->showOnCreation,[1,3])){
                    $rule = array_merge_recursive(
                        [$v->attribute => $v->rules], [$v->attribute => $v->creationRules]
                    );
                    if(in_array('required',$rule[$v->attribute]??[])){
                        $v->createColumn->required(true);
                    }
                    $list[] = $v->createColumn;
                }
            }
        }else{
            foreach ($this->projectFields as $k=>$v){
                if(in_array($v->showOnUpdate,[1,3,4])){
                    if($v->showOnUpdate == '4'){
                        $v->updateColumn->static(true);
                    }else{
                        $rule = array_merge_recursive(
                            [$v->attribute => $v->rules], [$v->attribute => $v->updateRules]
                        );
                        if(in_array('required',$rule[$v->attribute]??[])){
                            $v->updateColumn->required(true);
                        }
                    }

                    $list[] = $v->updateColumn;
                }
            }
        }
        return $list;
    }

    protected function filterStore(){
        $rule = [];
        $filter = [];
        $lang = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnCreation,[1,2,3])){
                $rule = array_merge($rule,array_merge_recursive(
                    [$v->attribute => $v->rules], [$v->attribute => $v->creationRules]
                ));
                $lang[$v->attribute] = $v->name;
                if($v->showOnCreation != 3){
                    $filter[$v->attribute] = $v->value;
                }
            }
        }
        return [
            'rule'=> $rule,'filter' => $filter, 'lang' =>$lang
        ];
    }
    protected function filterUpdate($id = 0){
        $rule = [];
        $filter = [];
        $lang = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnUpdate,[1,2,3])){
                if(!empty($id)){
                    foreach($v->updateRules as $key=>$val){
                        if(empty($val)) continue;
                        $v->updateRules[$key] = str_replace('${id}',$id,$val);
                    }
                }
                $rule = array_merge($rule,array_merge_recursive(
                    [$v->attribute => $v->rules], [$v->attribute => $v->updateRules]
                ));
                $lang[$v->attribute] = $v->name;
                if($v->showOnUpdate != 3){
                    $filter[$v->attribute] = $v->value;
                }
            }
        }
        return [
            'rule'=> $rule,'filter' => $filter, 'lang' =>$lang
        ];
    }

    protected function toMappingSelect($data,$type = '',$show = 'label'){
        $new = [];
        foreach ($data as $val){
            $val->list_class = $val->list_class ?? null;
            switch ($show){
                case 'label':
                    $new[$val->value] = '<span class="label label-'.$val->list_class.'">'.$val->label.'</span>';
                    break;
                case 'text':
                    $new[$val->value] = '<span class="text-'.$val->list_class.'">'.$val->label.'</span>';
                    break;
                case 'round':
                    $new[$val->value] = "<span class='label rounded-full border border-solid border-{$val->list_class} text-{$val->list_class}'>$val->label</span>";
                    break;
                case 'default':
                    $new[$val->value] = '<span class="cxd-Tag">'.$val->label.'</span>';
                    break;
                default:
                    $new[$val->value] = '<span>'.$val->label.'</span>';
                    break;
            }
        }
        $new['*'] = $type;
        return $new;
    }

    /**
     * 删除之前
     * @param $admin
     * @param $ids
     * @param $id
     */
    protected function beforeDestroy($admin,$ids,$id){

    }

    /**
     * 删除数据
     * @param $user
     * @param $ids
     * @param $id
     * @return array
     * @throws \Throwable
     */
    protected function destroying($user,$ids,$id){
        Db::connection($this->dbConnection)->beginTransaction();
        try {
            $info = (new $this->model)->whereIn($this->key,$ids)->get();
            (new $this->model)->destroy($info->pluck($this->key));
            Db::connection($this->dbConnection)->commit();
            AdminService::addLog('D',1,trans('projectName',[],$this->trans).trans('delete',[],'messages'),$user->id,$user->username,$ids);
            return $info;
        }catch (\Exception $e){
            Db::connection($this->dbConnection)->rollBack();
            AdminService::addLog('D',0,trans('projectName',[],$this->trans).trans('delete',[],'messages'),$user->id,$user->username,$ids);
            throw new \Exception($e->getMessage());
        }
    }

    protected function afterDestroy($admin,$info){
        return [];
    }




}