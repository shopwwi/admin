<?php

namespace Shopwwi\Admin\Libraries\Amis\Traits;

use Shopwwi\Admin\Amis\Grid;
use Shopwwi\Admin\Amis\TextControl;

trait UseShowTraits
{
    protected function htmlShow($id){
        if($this->useFormGrid){
            return $this->baseShow($id)->body([Grid::make()->gap('lg')->columns($this->filterShowColumns())]);
        }else{
            return $this->baseShow($id)->body($this->filterShowColumns());
        }
    }

    protected function jsonShow($id){
        try {
            $model = new $this->model;
            $info = $model->where($this->key,$id)->first();
            $info = $this->beforeShow($info,$id);
            return shopwwiSuccess($info);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
    protected function beforeShow($info,$id){
        return $info;
    }

    protected function filterShowColumns(){
        $list = [];
        foreach ($this->projectFields as $k=>$v){
            if(in_array($v->showOnDetail,[1,2])){
                $list[] = $v->showColumn;
            }
        }
        return $list;
    }
}