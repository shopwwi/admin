<?php
/**
 *-------------------------------------------------------------------------s*
 * 公用访问
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

namespace Shopwwi\Admin\App\Admin\Controllers;

use Illuminate\Support\Facades\Validator;
use Shopwwi\Admin\App\Admin\Models\SysAlbum;
use Shopwwi\Admin\App\Admin\Models\SysAlbumFiles;
use Shopwwi\Admin\App\Admin\Models\SysArea;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\BaseController;
use Shopwwi\Admin\Libraries\Storage;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;
use support\Response;

class CommonController extends BaseController
{
    // 路由注入
    public $routeAction = ['dict'=>['POST','OPTIONS'],'area'=>['GET','POST','OPTIONS']]; //方法注册 未填写的则直接any
    public $noNeedAuth = ['dict','area','sysUser']; //需要登入不需要鉴权

    /**
     * @return \support\Response
     */
    public function dict()
    {
        $dicts = DictTypeService::getDictsAndDatas();
        return shopwwiSuccess($dicts);
    }

    /**
     * 根据上级ID获取子集合
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function area(Request $request)
    {
        $model = new SysArea();
        $model = shopwwiWhereParams($model,$request->all());
        $list = $model->get();
        $list->map(function ($item){
            $item->isLeaf = false;
        });
        return shopwwiSuccess($list);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \support\Response
     */
    public function password(Request $request)
    {
        try {
            Validator::make($request->all(),[
                'old_password' => 'bail|required|min:6|chs_dash_pwd',
                'password' => 'bail|required|confirmed|min:6|chs_dash_pwd'
            ],['old_password'=>'原密码','password' => '密码']);
            $params =shopwwiParams(['password','old_password']);
            $user = Auth::guard($this->guard)->fail()->user();
            if(!password_verify($params['old_password'],$user->password)){
                throw new \Exception('原密码不正确');
            }
            $user->password = $params['password'];
            $user->save();
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    public function sys_user(Request $request)
    {

    }

    /**
     * 上传头像
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request)
    {
//        try {
            $file = $request->file('file');
            $params = shopwwiParams(['type'=>0,'albumId'=>0]);
            $path = 'uploads/common/'.date('Ymd');
            $ext = ['image/jpeg','image/gif','image/png'];
            if($params['type'] > 0){
                $ext = ['wmv','avi','mpg','mpeg','3gp','mov','mp4','flv','f4v','m2t','mts','rmvb','vob','mkv'];
            }
            $result = Storage::path($path)->size(1024*1024*10)->extYes($ext)->upload($file);
            $result->value = $result->file_url;
            $result->link = $result->file_url;
            $albumId = $params['albumId'];
            if($albumId <= 0){
                $first = SysAlbum::first();
                $albumId = $first->id;
            }
            SysAlbumFiles::create([
                'album_id' => $albumId,
                'height' => $result->file_height ?? 0,
                'name' => $result->file_name,
                'size' => $result->size,
                'files_type' => $result->mime_type,
                'width' => $result->file_width ?? 0,
                'original_name' => $result->origin_name,
            ]);
            return shopwwiSuccess($result);
//        }catch (\Exception $e){
//            return $e;
//        }
    }

}