<?php

namespace Shopwwi\Admin\Libraries\Amis;

use Shopwwi\Admin\App\Admin\Models\SysRoleMenu;
use Shopwwi\Admin\App\Admin\Service\SysMenuService;
use Shopwwi\Admin\Libraries\Amis\Traits\UseRoutePathTraits;
use Shopwwi\Admin\Libraries\Appoint;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\View;

class BaseController
{
    use UseRoutePathTraits;
    protected $guard = "admin";
    protected $dbConnection = null;
    protected $activeKey = '';
    protected $trans = "messages";
    /**
     * _format 支持格式 json data none
     */
    protected function format()
    {
        $format = \request()->input('_format','none');
        if(!in_array($format,['json','data','none','web'])) $format = 'none';
        return $format;
    }
    /**
     * 数据集中化定义
     * @return array
     */
    protected function fields()
    {
        return [];
    }
    protected function admin($cache = false,$fail = true){
        return Auth::guard($this->guard)->fail($fail)->user($cache);
    }

    protected function getAdminView($data = [],$plugin = ''){
        $admin = $this->admin(true,false);
        if($admin == null) return redirect(shopwwiAdminUrl('auth/login'));
        View::assign('adminInfo',$admin);
        $menus = SysMenuService::getAmisMenusList();
        if($admin->role_id != 1){
            $menuIds = SysRoleMenu::where('role_id',$admin->role_id)->pluck('menu_id');
            $menus = $menus->whereIn('id',$menuIds);
        }
        // 显示菜单和授权菜单不同
        $all = $menus->where('menu_type','!=','F')->where('visible',1);

        $menuList = Appoint::ShopwwiChindNode(json_decode($all->toJson(),true));
        View::assign('adminMenus',json_decode(json_encode($menuList)));
        View::assign('activeKey',$this->activeKey);
        View::assign('seoTitle',trans('projectName',[],$this->trans));
        View::assign('adminUrl',shopwwiAdminUrl(''));
        return view($data['tpl']??'admin/view',$data,'',$plugin);
    }

    /**
     * 异常返回数据
     * @param $e
     * @param $auto
     * @return \support\Response
     */
    protected function backError($e,$auto = true){

        if($auto && $this->format() == 'none'){
            return view('admin/error', ['code' => $e->getCode(), 'msg' => $e->getMessage()], '','');
        }
        return shopwwiError($e->getMessage());
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
}