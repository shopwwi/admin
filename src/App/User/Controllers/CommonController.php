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

use Shopwwi\Admin\App\Admin\Models\SysArea;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\User\Models\UserFiles;
use Shopwwi\WebmanAuth\Facade\Auth;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Request;
use support\Response;

class CommonController extends Controllers
{
    // 路由注入
    public $routeAction = ['dict'=>['GET','POST','OPTIONS'],'area'=>['GET','POST','OPTIONS']]; //方法注册 未填写的则直接any
    /**
     * @return \support\Response
     */
    public function dict()
    {
        $dicts = DictTypeService::getDictsAndDatas();
        return shopwwiSuccess($dicts);
    }

    /**
     * 请求地区
     * @param Request $request
     * @param $id
     * @return \support\Response|void
     */
    public function area(Request $request,$id)
    {
        try {
            $list = SysArea::where('pid',$id)->get();
            return shopwwiSuccess($list);
        }catch (\Exception $E){
            return shopwwiError($E->getMessage());
        }
    }

    /**
     * 上传图片
     * @param Request $request
     * @param $path
     * @return Response
     */
    public function upload(Request $request,$path)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            $file = $request->file('file');
            $result = Storage::path('uploads/user/'.$path.'/'.$user->id)->size(1024*1024*10)->extYes(['image/jpeg','image/gif','image/png'])->upload($file);
            $files = UserFiles::create([
                'path' => $path,
                'height' => $result->file_height ?? 0,
                'name' => $result->file_name,
                'size' => $result->size,
                'files_type' => 'image',
                'width' => $result->file_width ?? 0,
                'original_name' => $result->origin_name,
                'user_id' => $user->id
            ]);
            $files->value = $result->file_url;
            $files->link = $result->file_url;
            return shopwwiSuccess($files);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 上传视频
     * @param Request $request
     * @param $path
     * @return Response
     */
    public function video(Request $request,$path)
    {
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            $file = $request->file('file');
            $result = Storage::path('uploads/user/'.$path.'/'.$user->id)->size(1024*1024*10)->extYes(['video/mp4','video/swf'])->upload($file);
            $files = UserFiles::create([
                'path' => $path,
                'height' => $result->file_height ?? 0,
                'name' => $result->file_name,
                'size' => $result->size,
                'files_type' => 'video',
                'width' => $result->file_width ?? 0,
                'original_name' => $result->origin_name,
                'user_id' => $user->id
            ]);
            $files->value = $result->file_url;
            $files->link = $result->file_url;
            return shopwwiSuccess($files);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
    public function audio(Request $request,$path){
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            $file = $request->file('file');
            $result = Storage::path('uploads/user/'.$path.'/'.$user->id)->size(1024*1024*10)->extYes(['audio/mpeg'])->upload($file);
            $files = UserFiles::create([
                'path' => $path,
                'height' => $result->file_height ?? 0,
                'name' => $result->file_name,
                'size' => $result->size,
                'files_type' => 'audio',
                'width' => $result->file_width ?? 0,
                'original_name' => $result->origin_name,
                'user_id' => $user->id
            ]);
            $files->value = $result->file_url;
            $files->link = $result->file_url;
            return shopwwiSuccess($files);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}