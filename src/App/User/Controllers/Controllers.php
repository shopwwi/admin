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

use Closure;
use Shopwwi\Admin\App\User\Service\UserMenuService;
use Shopwwi\Admin\Libraries\Appoint;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\View;

class Controllers
{
    protected $guard = "member";
    protected   $key = 'id';
    protected $trans = 'messages';
    protected $activeKey = '';
    /**
     * _format 支持格式 json data none
     */
    protected function format()
    {
        $format = \request()->input('_format','none');
        if(!in_array($format,['json','data','none','web'])) $format = 'none';
        return $format;
    }

    protected function fields(){

    }

    protected function user($cache = false,$fail = true){
        $user = Auth::guard($this->guard)->fail($fail)->user($cache);
        return $user;
    }

    protected function getUserView($data=[],$plugin=''){
        $user = $this->user(true,false);
        if($user == null) return redirect(shopwwiUserUrl('auth/login'));
        View::assign('userInfo',$user);
        $menus = UserMenuService::getMenusList();
        $menuList = Appoint::ShopwwiChindNode(json_decode($menus->toJson(),true));
        View::assign('userMenus',json_decode(json_encode($menuList)));
        return view($data['tpl']??'user/view',$data,'',$plugin);
    }

    /**
     * 通用型获取数据列表
     * @param $model
     * @param $whereFunction
     * @param $orderBy
     * @param $unset
     * @param $limit
     * @return mixed
     */
    protected function getList($model,$whereFunction = null, $orderBy=[], $unset=[], $limit = 15){
        $limit = request()->input('perPage') ?? request()->input('limit',$limit);
        $hasOp = request()->input('op');
        if($hasOp == 'loadOptions'){
            $model = shopwwiWhereParams($model, request()->all(),array_merge($unset,['op','value','id']));
        }else{
            $model = shopwwiWhereParams($model, request()->all(),$unset);
        }

        if (request()->input('orderBy') && in_array(request()->input('orderDir'), array('asc', 'desc'))) {
            $model = $model->orderBy(request()->input('orderBy'), request()->input('orderDir'));
        } else {
            foreach ($orderBy as $key=>$value){
                $model = $model->orderBy($key,$value);
            }
        }
        if(is_callable($whereFunction)){
            $model = $whereFunction($model);
        }
        if($hasOp == 'loadOptions'){
            $value = request()->input('value');
            if(is_string($value)) $value = explode(',',$value);
            $model = $model->whereIn('id',$value);
            $list = $model->paginate(1000,'*','page',request()->input('page',1));
        }else{
            $list = $model->paginate($limit,'*','page',request()->input('page',1));
        }
        return $list;
    }

    protected function backButton(){
        return shopwwiAmis('button')->actionType('prev')->label(trans('back',[],'messages'))
            ->icon('ri-arrow-go-back-line')
            ->level('primary')
            ->onClick('window.history.back()');
    }
    protected function basePage(){
        return shopwwiAmis('page')->className('must m:overflow-auto bg-transparent')->headerClassName('must border-b-0');
    }

    /**
     * 异常返回数据
     * @param $e
     * @param $auto
     * @return \support\Response
     */
    protected function backError($e,$auto = true){

        if($auto && $this->format() == 'none'){
            return view('user/error', ['code' => $e->getCode(), 'msg' => $e->getMessage()], '', '');
        }
        return shopwwiError($e->getMessage());
    }

}