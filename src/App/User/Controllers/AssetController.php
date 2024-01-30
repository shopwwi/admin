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

use Shopwwi\Admin\App\User\Models\UserGrowthLog;
use Shopwwi\Admin\App\User\Models\UserPointLog;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\Admin\Libraries\StatusCode;
use Shopwwi\WebmanAuth\Facade\Auth;

use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Request;


class AssetController extends Controllers
{
    /**
     * 会员成长值
     * @param Request $request
     * @return \support\Response
     */
    public function growth(Request $request)
    {
        try {
            $user = $this->user(true);
            $list = $this->getList(new UserGrowthLog(),function ($q) use ($user) {
                return $q->where('user_id',$user->id);
            },['id'=>'desc'],['user_id','keyword']);
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }
    /**
     * 资料设置
     * @param Request $request
     * @return \support\Response
     */
    public function set(Request $request)
    {
        $params = shopwwiParams(['nickname','sex','birthday','avatar','address_area_id','address_area_info','address_city_id','address_province_id']);
        try {
            $user = Auth::guard($this->guard)->fail()->user();
            if(isset($params['sex'])){
                if(!in_array($params['sex'],['0','1','2'])){
                    throw new \Exception('性别不正确');
                }
            }
            if(isset($params['nickname']) && !isset($params['nickname']{5})){
                throw new \Exception('昵称不得少于5位');
            }
            foreach ($params as $key=>$val){
                $user->$key = $val;
            }
            $user->save();
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 头像
     * @param Request $request
     */
    public function avatar(Request $request)
    {
        try {
            $file = $request->file('file');
            $user = Auth::guard($this->guard)->fail()->user();
            $result = Storage::path('static/uploads/user/avatar')->size(1024*1024*5)->extYes(['image/jpeg','image/gif','image/png'])->reUpload($file,$user->id.'.png');
            $user->avatar = $result->file_name;
            $user->save();
            $result->value = $result->file_url;
            return shopwwiSuccess($result);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}