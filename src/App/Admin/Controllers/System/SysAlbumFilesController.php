<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统相册文件控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\System;
use Shopwwi\Admin\App\Admin\Models\SysAlbum;
use Shopwwi\Admin\App\Admin\Models\SysAlbumFiles;
use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\WebmanAuth\Facade\Auth;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Request;

class SysAlbumFilesController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysAlbumFiles::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    protected $activeKey = 'settingSiteAlbumIndex';
    protected $trans = 'sysAlbumFiles'; // 语言文件名称
    protected $queryPath = 'system/files';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'files'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['edit','store']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.album_id',[],'sysAlbumFiles'),'album_id')->rules('required'),
            shopwwiAmisFields(trans('field.name',[],'sysAlbumFiles'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.size',[],'sysAlbumFiles'),'size')->rules('required'),
            shopwwiAmisFields(trans('field.files_type',[],'sysAlbumFiles'),'files_type')->rules('required'),
            shopwwiAmisFields(trans('field.width',[],'sysAlbumFiles'),'width')->rules('required'),
            shopwwiAmisFields(trans('field.height',[],'sysAlbumFiles'),'height')->rules('required'),
            shopwwiAmisFields(trans('field.original_name',[],'sysAlbumFiles'),'original_name')->rules('required'),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->showFilter(),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->showFilter(),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    public function index(Request $request)
    {
        if($this->format() == 'json') return $this->jsonList(false,$this->unset);
        if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($this->amisList());
        return $this->getAdminView(['json'=>$this->amisList(),'tpl'=>'admin/files']);
    }

    /**
     * 上传图片
     * @param Request $request
     * @return mixed|\support\Response
     */
    public function upload(Request $request)
    {
        $admin = $this->admin(true);
        try {
            $user = Auth::guard($this->guard)->fail()->user(true);
            $params = shopwwiParams(['albumId'=>0,'type'=>0]);
            $file = $request->file('file');

            $extYes = ['image/jpeg','image/gif','image/png'];
            if($params['type'] == 1){
                $extYes = ['video/wmv','video/avi','video/mpg','video/mpeg','video/3gp','video/mov','video/mp4','video/flv','video/f4v','video/m2t','video/mts','video/rmvb','video/vob','video/mkv'];
            }
            if($params['type'] > 1){
                $extYes = ['image/jpeg','image/gif','image/png','video/wmv','video/avi','video/mpg','video/mpeg','video/3gp','video/mov','video/mp4','video/flv','video/f4v','video/m2t','video/mts','video/rmvb','video/vob','video/mkv'];
            }
            $result = Storage::path('uploads/album/'.$params['albumId']."/".date('Ym')."/".date('d'))->size(1024*1024*10)->extYes($extYes)->upload($file);
            $result->value = $result->file_url;
            $result->link = $result->file_url;
            $albumId = $params['albumId'];
            if($albumId <= 0){
                $first = SysAlbum::firstOrCreate([
                    'id' => 1
                ],['name'=>'默认相册']);
                $albumId = $first->id;
            }
            $model = new $this->model;
            $model->create([
                'album_id' => $albumId,
                'height' => $result->file_height ?? 0,
                'name' => $result->file_name,
                'size' => $result->size,
                'files_type' => $result->mime_type,
                'width' => $result->file_width ?? 0,
                'original_name' => $result->origin_name,
                'created_user_id' => $user->id
            ]);
            AdminService::addLog('C',1,'上传附件成功',$admin->id,$admin->username,(array) $result);
            return shopwwiSuccess($result);
        }catch (\Exception $e){
            AdminService::addLog('C',1,'上传附件失败',$admin->id,$admin->username);
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 移动素材
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function move(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $seller = $this->admin(true,false);
        try {
            $params = shopwwiParams(['album_id'=>0]);
            SysAlbumFiles::whereIn('id',$ids)->update(['album_id'=>$params['album_id']]);
            AdminService::addLog('D',1,'移动附件成功',$seller->id,$seller->username,$ids);
            return shopwwiSuccess();
        }catch (\Exception $e){
            AdminService::addLog('D',0,'移动附件失败',$seller->id,$seller->username,$ids);
            return shopwwiError($e->getMessage());
        }
    }

}
