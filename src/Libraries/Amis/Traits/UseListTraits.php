<?php

namespace Shopwwi\Admin\Libraries\Amis\Traits;

use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\Libraries\StatusCode;

trait UseListTraits
{

    /**
     * 获取amis数据页面
     * @return \Shopwwi\Admin\Amis\Page
     */
    protected function amisList(){
        $crud = $this->crudShow();
        return $this->baseList($crud);
    }

    /**
     * 可重写crud渲染
     * @return \Shopwwi\Admin\Amis\CRUDTable
     */
    protected function crudShow(){
        $actions = [$this->bulkDeleteButton()->reload('window')];
        if(!$this->useHasDestroy) $actions=[];
        return $this->baseCRUD()->footable(true)
            ->autoGenerateFilter(true)
            ->quickSaveItemApi($this->useAmisUpdateUrl('$'.$this->key))
            ->bulkActions($actions)
            ->columns($this->getTableColumn());
    }

    /**
     * 回收站页面
     * @return \Shopwwi\Admin\Amis\Page
     */
    protected function recoveryList(){
        $crud = $this->baseCRUD()
            ->autoGenerateFilter(true)
            ->api($this->getUrl($this->queryPath . '/recovery?_format=json'))
            ->quickSaveItemApi($this->useAmisUpdateUrl('$'.$this->key))
            ->headerToolbar([
                ...$this->baseHeaderToolBar(),
                ...$this->rightHeaderTooBar(true)
            ])
            ->bulkActions([$this->bulkRestoreButton()->reload('window'),$this->bulkErasureButton()->reload('window')])
            ->columns($this->getRecoveryColumn());
        return $this->baseList($crud)->toolbar([$this->backButton()]);
    }

    /**
     * 表格字段
     * @return array
     */
    protected function getTableColumn(){
        $filter = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnIndex,[1,3])){
                $filter[] = $v->tableColumn;
            }
        }
        $filter[] = $this->operation();
        return $filter;
    }

    protected function getRecoveryColumn(){
        $filter = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnIndex,[1,3,4])){
                $filter[] = $v->tableColumn;
            }
        }
        $filter[] = Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
            $this->rowRestoreButton(),
            $this->rowErasureButton()
        ]);;
        return $filter;
    }

    /**
     * 筛选字段
     * @return array
     */
    protected function getFilterColumn(){
        $filter = [];
        foreach ($this->projectFields as $k=>$v){
            if($v->showFilter == 1){
                $filter[] = $v->filterColumn;
            }
        }
        return $filter;
    }

    /**
     * json请求数据
     * @return \support\Response
     */
    protected function jsonList($recovery=false,$unset = []){
        try {
            $model = new $this->model;
            $limit = request()->input('perPage') ?? request()->input('limit',15);
            $hasOp = request()->input('op');
            if($hasOp == 'loadOptions'){
                $model = shopwwiWhereParams($model, request()->all(),['op','value']);
            }else{
                $model = shopwwiWhereParams($model, request()->all(),$unset);
            }

            if (request()->input('orderBy') && in_array(request()->input('orderDir'), array('asc', 'desc'))) {
                $model = $model->orderBy(request()->input('orderBy'), request()->input('orderDir'));
            } else {
                foreach ($this->orderBy as $key=>$value){
                    $model = $model->orderBy($key,$value);
                }
            }
            if($recovery){
                $model = $model->onlyTrashed();
            }
            $model = $this->beforeJsonList($model);
            if($hasOp == 'loadOptions'){
                $value = request()->input('value');
                if(is_string($value)) $value = explode(',',$value);
                $model = $model->whereIn($this->key,$value);
                $list = $model->paginate(1000,'*','page',request()->input('page',1));
            }else{
                $list = $model->paginate($limit,'*','page',request()->input('page',1));
            }
            $data = $this->afterJsonList($list);
            return shopwwiSuccess(['items'=>$data,'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()] ,trans('list',[],$this->trans)
             //   , ['page' => $list->currentPage(), 'total' => $list->total()]
            );
        } catch (\Exception $e) {
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 获取数据之前
     * @param $model
     * @return mixed
     */
    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled());
        return $model;
    }

    /**
     * 获取数据之后
     * @param $list
     * @return mixed
     */
    protected function afterJsonList($list){
        //  $data['list'] = $list->items();
        return $list->items();
    }

    /**
     * 获取首页展示字段数据
     * @return array
     */
    protected function filterJsonFiled(): array
    {
        $filter = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnIndex,[1,2])){
                $filter[] = $v->attribute;
            }
        }
        return $filter;
    }
}