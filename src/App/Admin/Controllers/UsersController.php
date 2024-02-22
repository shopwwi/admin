<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户信息表控制器
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


use Shopwwi\Admin\Amis\DropdownButton;
use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DateRangeService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\User\UserService;
use Shopwwi\Admin\App\User\Models\UserLabel;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\Admin\Libraries\Storage;
use support\Request;

class UsersController extends AdminController
{
    public $model = \Shopwwi\Admin\App\User\Models\Users::class;
    public  $orderBy = ['id' => 'desc'];
    protected $trans = 'users'; // 语言文件名称
    protected $queryPath = 'users'; // 完整路由地址
    protected $activeKey = 'userMangeList';
    public $routeDelay = true;

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'users'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedAuth = []; //需要登入不需要鉴权
    public $noNeedAuth = ['list']; //不需要登入
    
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $allowOrUnAllow = DictTypeService::getAmisDictType('allowOrUnAllow');
        $sexDict = DictTypeService::getAmisDictType('sex');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.username',[],'users'),'username')->rules('required')->creationRules(['chs_unique:users'])->updateRules(["chs_as_unique:users,\${id}"]),
            shopwwiAmisFields(trans('field.nickname',[],'users'),'nickname')->rules('required'),
            shopwwiAmisFields(trans('field.avatar',[],'users'),'avatar')->column('hidden',['md'=>12])->tableColumn(['type'=>'image','name'=>'avatarUrl','width'=>30,'height'=>30,'imageMode'=>'original']),
            shopwwiAmisFields(trans('field.avatar',[],'users'),'avatarUrl')->column('input-image',['autoFill'=>['avatar'=>'${file_name}'],'crop'=>['aspectRatio'=>3],'receiver'=>shopwwiAdminUrl('user/users/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'avatarUrl','width'=>90,'height'=>90]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.password',[],'users'),'password')->rules(['bail','chs_dash_pwd','min:6'])->column('input-password')->creationRules(['required'])->updateRules(['nullable'])->showOnIndex(0)->showOnDetail(0),
            shopwwiAmisFields(trans('field.pay_pwd',[],'users'),'pay_pwd')->rules(['bail','chs_dash_pwd','min:6'])->column('input-password')->creationRules(['required'])->updateRules(['nullable'])->showOnIndex(0)->showOnDetail(0),
            shopwwiAmisFields(trans('field.grade_id',[],'users'),'grade_id')->rules('required')->column('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('user/grades/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'name','pickerSchema'=>AmisService::getGradeList()]),
            shopwwiAmisFields(trans('field.phone',[],'users'),'phone')->rules('required')->creationRules(['chs_unique:users'])->updateRules(["chs_as_unique:users,\${id}"])->tableColumn(["classNameExpr"=>"<%= data.phone_bind == 1 ? 'text-success' : '' %>",'width'=>145]),
            shopwwiAmisFields(trans('field.phone_bind',[],'users'),'phone_bind')->showOnIndex(2)->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.email',[],'users'),'email')->rules('required')->creationRules(['chs_unique:users'])->updateRules(["chs_as_unique:users,\${id}"])->tableColumn(["classNameExpr"=>"<%= data.email_bind == 1 ? 'text-success' : '' %>",'width'=>145]),
            shopwwiAmisFields(trans('field.email_bind',[],'users'),'email_bind')->showOnIndex(2)->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.sex',[],'users'),'sex')->filterColumn('select',['options'=>$sexDict])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($sexDict,'${sex}','default')])
                ->column('radios',['selectFirst'=>true,'options'=>$sexDict])->rules(['required','in:0,1,2']),
            shopwwiAmisFields(trans('field.invite_id',[],'users'),'invite_id')->column('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.status',[],'users'),'status')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($allowOrUnAllow,'${status}')])
                ->filterColumn('select',['options'=>$allowOrUnAllow])
                ->column('radios',['selectFirst'=>true,'options'=>$allowOrUnAllow]),
            shopwwiAmisFields(trans('field.growth',[],'users'),'growth')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['classNameExpr'=>'text-yellow-600']),
            shopwwiAmisFields(trans('field.points',[],'users'),'points')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['toggled'=>false,'sortable'=>true]),
            shopwwiAmisFields(trans('field.available_points',[],'users'),'available_points')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['classNameExpr'=>'text-cyan-600']),
            shopwwiAmisFields(trans('field.frozen_points',[],'users'),'frozen_points')->showOnCreation(0)->tableColumn(['toggled'=>false,'sortable'=>true]),
            shopwwiAmisFields(trans('field.balance',[],'users'),'balance')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['toggled'=>false,'sortable'=>true]),
            shopwwiAmisFields(trans('field.available_balance',[],'users'),'available_balance')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['classNameExpr'=>'text-red-600']),
            shopwwiAmisFields(trans('field.frozen_balance',[],'users'),'frozen_balance')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['toggled'=>false,'sortable'=>true]),
            shopwwiAmisFields(trans('field.label',[],'users'),'label')->column('select',['multiple'=>true,'source'=>'${labelList}','labelField'=>'name','valueField'=>'id']),
            shopwwiAmisFields(trans('field.last_login_ip',[],'users'),'last_login_ip')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.last_login_time',[],'users'),'last_login_time')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.login_ip',[],'users'),'login_ip')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.login_num',[],'users'),'login_num')->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.login_time',[],'users'),'login_time')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.birthday',[],'users'),'birthday')->column('input-date',['format'=>'YYYY-MM-DD'])->tableColumn(['width'=>90,'toggled'=>false,'sortable'=>true]),
            shopwwiAmisFields(trans('field.address_area_info',[],'users'),'city')->column('input-city',['extractValue'=>false,'md'=>12])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.address_area_id',[],'users'),'address_area_id')->showOnIndex(2)->column('hidden',['value'=>'${city.districtCode}']),
            shopwwiAmisFields(trans('field.address_area_info',[],'users'),'address_area_info')->showOnIndex(2)->column('hidden',['value'=>'${city.province} ${city.city} ${city.district}']),
            shopwwiAmisFields(trans('field.address_city_id',[],'users'),'address_city_id')->showOnIndex(2)->column('hidden',['value'=>'${city.cityCode}']),
            shopwwiAmisFields(trans('field.address_province_id',[],'users'),'address_province_id')->showOnIndex(2)->column('hidden',['value'=>'${city.provinceCode}']),
            shopwwiAmisFields(trans('field.modify_num',[],'users'),'modify_num')->showOnCreation(0)->showOnUpdate(0),
            shopwwiAmisFields(trans('field.is_real',[],'users'),'is_real')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_real}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('radios',['selectFirst'=>true,'options'=>$yesOrNo]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    /**
     * 获取预览概况
     * @param Request $request
     * @param $id
     * @return \support\Response|void
     */
    public function stat(Request $request,$id)
    {
        $type = $request->input('type','day');
        $date = $request->input('date',now());
        if($this->format() == 'json') {

            $time = DateRangeService::getCarbon($date)->format('Y-m-d');

            if($id == 'spread'){
                return shopwwiSuccess(UserService::getStatSpread($type,$time));
            }else if($id=='new'){
                return shopwwiSuccess(UserService::getStatNew($type,$time));
            }else if($id=='card'){
                return shopwwiSuccess(UserService::getStatCard($type,$time));
            }
            return shopwwiError();
        }
        if($this->format() == 'data') {
            $time = DateRangeService::getCarbon($date)->format('Y-m-d');
            return shopwwiSuccess(UserService::getAmisStat($id,$type,$time));
        };
        $page = $this->basePage()->bodyClassName('p-0 bg-transparent')->body([
            shopwwiAmis('form')->target('chart1,chart2')->submitOnInit(true)->wrapWithPanel(false)->mode('inline')->body([
                shopwwiAmis('select')->options([['label'=>trans('stat.search.day',[],'messages'),'value'=>'day'],['label'=>trans('stat.search.week',[],'messages'),'value'=>'week'],['label'=>trans('stat.search.month',[],'messages'),'value'=>'month']])->name('type')->value('day')->clearable(false),
                shopwwiAmis('input-date')->name('date')->format('YYYY-MM-DD')->visibleOn("this.type == 'day' || this.type == 'week'"),
                shopwwiAmis('input-month')->name('date')->format('YYYY-MM-DD')->visibleOn("this.type == 'month'"),
                shopwwiAmis('button')->type('submit')->label('更新')
            ]),
            shopwwiAmis('grid')->columns([

            ]),
            shopwwiAmis('card')->body(shopwwiAmis('chart')->name('chart1')->height(400)->api(shopwwiAdminUrl('users/stat/new?_format=data&type=$type&date=$date'))),
            shopwwiAmis('card')->body(shopwwiAmis('chart')->name('chart2')->height(600)->api(shopwwiAdminUrl('users/stat/spread?_format=data&type=$type&date=$date'))
                ->mapURL('/static/js/china.json')->mapName('china')),

        ]);
        if($this->format() == 'web'){
            return shopwwiSuccess($page);
        }
        return $this->getAdminView(['json'=>$page,'activeKey'=>'userMangeStat']);
    }

    protected function rowActions(): Operation
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
            $this->rowEditButton($this->useEditDialog),
            DropdownButton::make()->label(trans('more',[],'messages'))->icon('fa fa-ellipsis-v')->trigger('hover')->align('right')->level('link')->buttons([
                $this->rowShowButton($this->useShowDialog),
                $this->rowDeleteButton(),
                UserService::getTrimBalanceAmisModel(),
                UserService::getTrimPointsAmisModel(),
                UserService::getTrimGrowthAmisModel()
            ])
        ]);
    }

    /**
     * 新增查询赋值
     * @return array
     */
    protected function getCreate(){
        return ['labelList'=>UserLabel::get()];
    }

    /**
     * 编辑查询赋值
     * @param $info
     * @param $id
     * @return array
     */
    protected function insertGetEdit($info,$id){
        $info->labelList = UserLabel::get();
        $info->city = ['code'=>$info->address_area_id];
        return $info;
    }

    /**
     * 上传头像
     * @param Request $request
     * @return \support\Response
     */
    public function upload(Request $request)
    {
        try {
            $file = $request->file('file');
            $result = Storage::path('uploads/user/avatar')->size(1024*1024*5)->extYes(['image/jpeg','image/gif','image/png'])->upload($file);
            $result->value = $result->file_url;
            return shopwwiSuccess($result);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    public function list(Request $request)
    {
        return $this->jsonList(false,[]);
    }
}
