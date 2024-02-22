<?php
/**
 *-------------------------------------------------------------------------*
 * 会员中心菜单表控制器
 *-------------------------------------------------------------------------*
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------*
 */
namespace Shopwwi\Admin\App\Admin\Controllers\User;

use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;

class UserMenuController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserMenu::class;
    protected $orderBy = ['sort'=>'asc','id'=>'asc'];

    protected $trans = 'userMenu'; // 语言文件名称
    protected $queryPath = 'user/menu'; // 完整路由地址
    protected $activeKey = 'userMenu';

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'menu'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        $sysMenuType = DictTypeService::getAmisDictType('sysMenuType');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'userMenu'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.key',[],'userMenu'),'key')->rules('required'),
            shopwwiAmisFields(trans('field.pid',[],'userMenu'),'pid')->rules(['bail','nullable','numeric','min:0'])->column('tree-select',['source'=>'$items','labelField'=>'name','valueField'=>'id']),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.path',[],'userMenu'),'path'),
            shopwwiAmisFields(trans('field.component',[],'userMenu'),'component'),
            shopwwiAmisFields(trans('field.is_frame',[],'userMenu'),'is_frame')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_frame}','round')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.menu_type',[],'userMenu'),'menu_type')->rules('required')
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($sysMenuType,'${menu_type}','default')])
                ->column('select',['options'=>$sysMenuType]),
            shopwwiAmisFields(trans('field.status',[],'userMenu'),'status')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${status}','round')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),

        ];
    }
    /**
     * 获取新增数据
     * @return mixed
     */
    protected function getCreate(){
        return [
            'is_frame' => 0,
            'menu_type' => 'M',
            'status' => 1,
            'sort' => 999
        ];
    }

    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled());
        $model->where('pid',null)->with('children');
        return $model;
    }
}