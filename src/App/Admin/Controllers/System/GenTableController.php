<?php
/**
 *-------------------------------------------------------------------------s*
 * 代码生成控制器
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

use Shopwwi\Admin\App\Admin\Models\GenTable;
use Shopwwi\Admin\App\Admin\Models\GenTableColumn;
use Shopwwi\Admin\Amis\Dialog;
use Shopwwi\Admin\Amis\DialogAction;
use Shopwwi\Admin\Amis\TableColumn;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\GenTablePreviewService;
use Shopwwi\Admin\App\Admin\Service\GenTableService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\Admin\Libraries\Validator;
use support\Db;
use support\Request;
use support\Response;

class GenTableController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\GenTable::class;
    protected $trans = 'genTable'; // 语言文件名称
    protected $queryPath = 'system/tools'; // 完整路由地址
    protected $activeKey = 'settingSystemTools';
    protected $routePath = 'tools'; // 当前路由模块不填写则直接控制器名
    protected $adminOp = true;
    protected $useEditDialog = 0;

    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['sortable'=>true,'width'=>80]),
            shopwwiAmisFields(trans('field.name',[],'genTable'),'name')->showFilter()->tableColumn(['sortable'=>true]),
            shopwwiAmisFields(trans('field.comment',[],'genTable'),'comment')->showFilter(),
            shopwwiAmisFields(trans('field.class_name',[],'genTable'),'class_name')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.tpl_category',[],'genTable'),'tpl_category')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.package_name',[],'genTable'),'package_name')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.module_name',[],'genTable'),'module_name')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.business_name',[],'genTable'),'business_name')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.function_name',[],'genTable'),'function_name')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.function_author',[],'genTable'),'function_author')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.gen_type',[],'genTable'),'gen_type')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.gen_path',[],'genTable'),'gen_path')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.options',[],'genTable'),'options')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.remark',[],'genTable'),'remark')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.updated_user_id',[],'genTable'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->showOnIndex(2)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_user_id',[],'genTable'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->showOnIndex(2)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',["format"=> "YYYY-MM-DD HH:mm:ss",'name'=>'created_at_betweenAmisTime']),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true]),
        ];
    }
    protected function createButton(int $dialog = 0){
        return DialogAction::make()->dialog(
            Dialog::make()->title('导入数据表')->size($this->useCreateDialogSize)
                ->body(
                    shopwwiAmis('form')->mode(null)->api($this->useAmisStoreUrl())->body(
                        shopwwiAmis('picker')->embed(true)->name('tables')->source($this->useAmisCreateUrl())
                            ->multiple(true)
                            ->valueField('tableName')
                            ->labelField('tableComment')
                            ->pickerSchema(
                                ['mode' => 'table','autoGenerateFilter'=>true,'columns'=>[
                                    TableColumn::make()->name('tableName')->label('表名')->searchable(true),
                                    TableColumn::make()->name('tableComment')->label('注释')->searchable(true),
                                    TableColumn::make()->name('createTime')->label('创建时间'),
                                    TableColumn::make()->name('updateTime')->label('更新时间')
                                ],'headerToolbar'=>[shopwwiAmis('reload')->align('right')],'perPageField'=>'limit']
                            )
                    )

                )->label('导入数据表')->icon('fa fa-add')->level('primary')
        )->label('导入表');
    }

    /**
     * 导入表
     */
    public function create(Request $request)
    {
        $list = GenTableService::getTables($request->input('limit',15),$request);
        return shopwwiSuccess(['items'=>$list->items(),'total' => $list->total(),'page' => $list->currentPage(),'hasMore' =>$list->hasMorePages() ]);
    }

    /**
     * 导入表提交
     * @param Request $request
     * @return \support\Response
     */
    public function store(Request $request)
    {
        $tables = $request->input('tables');
        $tables = is_array($tables) ? $tables : ( is_string($tables) ?explode (',',$tables) :func_get_args());
        $admin = $this->admin();
        Db::beginTransaction();
        try {
            set_time_limit(0);
            if ($tables){
                foreach ($tables as $table){
                    $desc = GenTableService::getTableInfo($table);
                    $entity = GenTableService::getModelName($table);
                    $lot = GenTableService::getLangName($table);
                    $columns = GenTableService::getColumns($table);
                    $data = [
                        'name'=> $table,
                        'comment'=> $desc,
                        'class_name'=> $entity,
                        'business_name' => 'Shopwwi\\Admin\\App\\Admin\\Controllers',//'Shopwwi\\Admin\\App\\Admin\\Controllers',
                        'module_name' => 'Shopwwi\\Admin\\App\\Admin\\Models',//'Shopwwi\\Admin\\App\\Admin\\Models',
                        'function_name' => 'Shopwwi\\Admin\\App\\Admin\\Service',//'Shopwwi\\Admin\\App\\Admin\\Service',
                     //   'created_user_id'=> $admin->id,
                     //   'updated_user_id'=> $admin->id,
                        'function_author' => '8988354@qq.com TycoonSong',
                        'gen_path' => $lot
                    ];
                    $res = GenTable::create($data);
                    $id = $res->id;

                    foreach ($columns as $column){
                        $column_data = [
                            'table_id'=>$id,
                            'name'=>$column->columnName,
                            'comment'=>$column->columnComment,
                            'column_type'=>$column->dataType,
                            'is_pk' =>$column->columnKey=='PRI'?'1':'0',
                            'is_increment' =>$column->extra=='auto_increment'?'1':'0',
                            'is_required' =>$column->isNullable?'1':'0',
                            'is_insert' => in_array($column->columnName,['created_at','updated_at','deleted_at','created_user_id','updated_user_id'])?'0':'1',
                            'is_edit' => in_array($column->columnName,['created_at','updated_at','deleted_at','created_user_id','updated_user_id'])?'0':'1',
                          //  'created_user_id'=> $admin->id,
                          //  'updated_user_id'=> $admin->id,
                            'sort'=>$column->ordinalPosition
                        ];
                        GenTableColumn::create($column_data);
                    }
                }
            }
            Db::commit();
            return shopwwiSuccess();
        }catch (\Exception $exception){
            Db::rollBack();
            return shopwwiError($exception->getMessage());
        }
    }

    protected function getAmisEdit($id){
        $form = $this->baseForm()->panelClassName('')->body([
            shopwwiAmis('tabs')->tabs([
                ['title'=>'基本信息','body'=>shopwwiAmis('grid')->gap('lg')->columns([
                    shopwwiAmis('input-text')->name('table.name')->label(trans('field.name',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('input-text')->name('table.comment')->label(trans('field.comment',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('input-text')->name('table.class_name')->label(trans('field.class_name',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('input-text')->name('table.function_author')->label(trans('field.function_author',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('textarea')->name('table.remark')->label(trans('field.remark',[],'genTable'))->md(12),
                ])],
                ['title'=>'字段信息','body'=>shopwwiAmis('input-table')->name('tableColumn')->source('$tableColumn')->columns([
                    shopwwiAmis('input-number')->name('sort')->label(trans('field.sort',[],'genTableColumn'))->width(50),
                    shopwwiAmis('input-text')->name('name')->label(trans('field.name',[],'genTableColumn'))->width(130),
                    shopwwiAmis('input-text')->name('comment')->label(trans('field.comment',[],'genTableColumn'))->width(145),
                    shopwwiAmis('input-text')->name('column_type')->label(trans('field.column_type',[],'genTableColumn'))->width(145),
                    shopwwiAmis('switch')->name('is_insert')->label(trans('field.is_insert',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('switch')->name('is_edit')->label(trans('field.is_edit',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('switch')->name('is_list')->label(trans('field.is_list',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('switch')->name('is_show')->label(trans('field.is_show',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('switch')->name('is_query')->label(trans('field.is_query',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('select')->name('query_type')->label(trans('field.query_type',[],'genTableColumn'))->options([
                        [ 'label' => "=", 'value' => 'EQ'],
                        [ 'label' => "like", 'value' => 'LIKE'],
                        [ 'label' => "between", 'value' => 'BETWEEN'],
                        [ 'label' => "!=", 'value' => 'NE'],
                        [ 'label' => ">", 'value' => 'GT'],
                        [ 'label' => ">=", 'value' =>  'GE'],
                        [ 'label' => "<", 'value' => 'LT'],
                        [ 'label' => "<=", 'value' =>  'LE']
                    ]),
                    shopwwiAmis('switch')->name('is_required')->label(trans('field.is_required',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('select')->name('html_type')->label(trans('field.html_type',[],'genTableColumn'))->options([
                        [ 'label' => "文本框", 'value' => 'input'],
                        [ 'label' => "文本域", 'value' => 'textarea'],
                        [ 'label' => "下拉框", 'value' => 'select'],
                        [ 'label' => "单选框", 'value' => 'radio'],
                        [ 'label' => "复选框", 'value' => 'checkbox'],
                        [ 'label' => "日期控件", 'value' =>  'date'],
                        [ 'label' => "图片上传", 'value' => 'upload'],
                        [ 'label' => "文件上传", 'value' =>  'file'],
                        [ 'label' => "富文本控件", 'value' =>  'editor']
                    ]),
                    shopwwiAmis('select')->name('dict_type')->label(trans('field.dict_type',[],'genTableColumn'))
                        ->options(DictTypeService::getDictsAndDatas())->labelField('name')->valueField('type')->width(130),
                    shopwwiAmis('switch')->name('is_pk')->label(trans('field.is_pk',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                    shopwwiAmis('switch')->name('is_increment')->label(trans('field.is_increment',[],'genTableColumn'))->trueValue(1)->falseValue(0),
                ])],
                ['title'=>'生成信息','body'=>shopwwiAmis('grid')->gap('lg')->columns([
                    shopwwiAmis('input-text')->name('table.package_name')->label(trans('field.package_name',[],'genTable'))->md(6),
                    shopwwiAmis('input-text')->name('table.module_name')->label(trans('field.module_name',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('input-text')->name('table.business_name')->label(trans('field.business_name',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('input-text')->name('table.function_name')->label(trans('field.function_name',[],'genTable'))->md(6)->required(true),
                    shopwwiAmis('textarea')->name('table.gen_path')->label(trans('field.gen_path',[],'genTable'))->md(12)->required(true),
                ])],
            ])

        ])->onEvent([
            'submitSucc' => [
                'actions' => [
                    'actionType' => 'custom',
                    'script'     => 'setTimeout(()=>(window.location.reload()), 1200)',
                ],
            ],
        ])->api($this->useAmisUpdateUrl($id))->initApi($this->useAmisEditUrl($id));
        $page = $this->basePage()->toolbar([$this->backButton()])->body($form);
        $page = $page->subTitle(trans('update',[],$this->trans));
        return $page;
    }

    protected function getEdit($id){
        $genTable = GenTable::where('id',$id)->first();
        $genTableColumn = GenTableColumn::where('table_id',$id)->orderBy('sort','asc')->get();
        return ['table'=>$genTable,'tableColumn'=>$genTableColumn];
    }

    /**
     * 修改数据
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
        try {
            Validator::make(\request()->all(), [
                'table' => 'required|array',
                'tableColumn' => 'required|array',
                'tableColumn.*.id' => 'required|numeric',
                'tableColumn.*.name' => 'required',
            ], [], [
            ])->validate();
            $params = shopwwiParams(['table','tableColumn']);
            $table = GenTable::where('id',$id)->first();
            foreach ($params['table'] as $key=>$val){
                $table->$key = $val;
            }
            $table->save();
            $tableColumn = GenTableColumn::where('table_id',$id)->get();
            foreach ($tableColumn as $item){
                foreach ($params['tableColumn'] as $key=>$val){
                    if($item->id == $val['id']){
                        foreach ($val as $key2=>$val2){
                            $item->$key2 = $val2;
                        }
                        $item->save();
                    }
                }
            }
            return shopwwiSuccess();
        }catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    protected function htmlShow($id){
        return shopwwiAmis('form')->initApi($this->useAmisShowUrl($id))->body(
            shopwwiAmis('tabs')->tabsMode('crad')->tabs([
                ['title'=>'${title}','body'=>[
                    shopwwiAmis('button')->actionType('copy')->label('复制')->content('${tab|raw}'),
                    shopwwiAmis('code')->language('${lang}')->name('${tab|raw}')]]
            ])->source('$tabs')
        );

       //     $this->baseShow($id)->body([Grid::make()->gap('lg')->columns($this->filterShowColumns())]);
    }

    /**
     * 获取详情
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function show(Request $request,$id)
    {
        try {
            $genTable = GenTable::where('id',$id)->with(['columns'=>function($query){
                $query->orderBy('sort','asc');
            }])->first();
            $controller = GenTablePreviewService::createController($genTable);
            $lang = GenTablePreviewService::createLang($genTable);
            $model = GenTablePreviewService::createModel($genTable);
            $langVue = GenTablePreviewService::createVueLang($genTable);
            $apiTs = GenTablePreviewService::createVueRoute($genTable);
            $moduleVue = GenTablePreviewService::createVueModule($genTable);
            $listVue = GenTablePreviewService::createVueList($genTable);
            $recoveryVue = GenTablePreviewService::createVueRecoveryList($genTable);
            return shopwwiSuccess(['tabs' => [
                ['title'=>'controller.php' ,'lang'=>'php', 'tab' => $controller],
                ['title'=>'model.php' ,'lang'=>'php', 'tab' => $model],
                ['title'=>'lang.php' ,'lang'=>'php', 'tab' => $lang],
                ['title'=>'api.ts' ,'lang'=>'javascript', 'tab' => $apiTs],
                ['title'=>'lang.json' ,'lang'=>'json', 'tab' => $langVue],
                ['title'=>'module.vue' ,'lang'=>'javascript', 'tab' => $moduleVue],
                ['title'=>'list.vue' ,'lang'=>'javascript', 'tab' => $listVue],
                ['title'=>'recovery.vue' ,'lang'=>'javascript', 'tab' => $recoveryVue]
            ]]);
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }


}
