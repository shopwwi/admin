<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户实名认证表控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\User;

use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\App\Admin\Service\User\RealNameService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;

class UserRealnameController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserRealname::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'userRealname'; // 语言文件名称
    protected $queryPath = 'user/real'; // 完整路由地址
    protected $activeKey = 'userRealIndex';
    protected $useHasCreate = false;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'real'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','store','destroy','recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.user_id',[],'userRealname'),'user_id')->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${user.nickname}(ID:${user_id})</span>'])
                ->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.id_card_name',[],'userRealname'),'id_card_name')->rules('required'),
            shopwwiAmisFields(trans('field.id_card_no',[],'userRealname'),'id_card_no')->rules('required'),
            shopwwiAmisFields(trans('field.id_card_handle',[],'userRealname'),'id_card_handle')->rules('required'),
            shopwwiAmisFields(trans('field.id_card_front',[],'userRealname'),'id_card_front')->rules('required'),
            shopwwiAmisFields(trans('field.id_card_back',[],'userRealname'),'id_card_back')->rules('required'),
            shopwwiAmisFields(trans('field.status',[],'userRealname'),'status')->rules('required'),
            shopwwiAmisFields(trans('field.remark',[],'userRealname'),'remark')->rules('required'),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }

    /**
     * 添加关联关系
     * @param $model
     * @return mixed
     */
    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled());
        $model->with(['user'=>function($q){
            $q->select('id','username','avatar','nickname');
        }]);
        return $model;
    }

    /**
     * 编辑返回数据插入
     * @param $info
     * @param $id
     * @return mixed
     */
    protected function insertGetEdit($info,$id){
        $info->load(['user'=>function($q){
            $q->select('id','username','avatar','nickname');
        }]);
        $data['info'] = $info;
        return $data;
    }

    /**
     * 更改状态
     * @param Request $request
     * @param $id
     * @return mixed|\support\Response
     * @throws \Throwable
     */
    public function update(Request $request,$id)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        $params = shopwwiParams(['status'=>'1','remark']);
        try {
            $info = (new $this->model)->where($this->key,$id)->first();
            if($info == null){
                throw new \Exception(trans('dataError',[],'messages'));
            }
            if($info->status === 'O'){
                if(!in_array($params['status'],['1','2'])){
                    throw new \Exception(trans('dataError',[],'messages'));
                }
            }elseif($info->status === '1'){
                if($params['status'] !== '8'){
                    throw new \Exception(trans('dataError',[],'messages'));
                }
            }else{
                throw new \Exception(trans('dataError',[],'messages'));
            }
            Db::connection()->beginTransaction();
            RealNameService::adminVerifyReal($info,$params['status'],$params['remark']??'');
            AdminService::addLog('E','1','实名认证'."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params);
            Db::connection()->commit();
            return shopwwiSuccess($info,trans('update',[],'messages').$this->name.trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            AdminService::addLog('E','0','实名认证'."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params,$e->getMessage());
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 实名设置
     * @param Request $request
     * @return \support\Response
     */
    public function setting(Request $request)
    {

        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'realname'
                    ],['name'=>'实名认证设置','value'=>[
                        'used' => '1',
                    ]]);
                    return shopwwiSuccess($info);
                }
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('radios')->name('growth.used')->label(trans('config.used',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12),
                    ])
                ])->api('post:' . shopwwiAdminUrl('user/real/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('user/real/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('config.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'userRealSite']);
            }else{
                $params = shopwwiParams(['realname']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}
