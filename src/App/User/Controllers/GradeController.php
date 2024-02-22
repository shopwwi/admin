<?php

namespace Shopwwi\Admin\App\User\Controllers;

use Shopwwi\Admin\App\User\Models\UserGrowthLog;
use Shopwwi\Admin\App\User\Service\GradeService;
use support\Request;
use support\Response;

class GradeController extends Controllers
{
    protected $activeKey = 'myGrade';
    /**
     * 获取等级
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $user = $this->user(true);
            if($this->format() == 'json'){
                $list = $this->getList(new UserGrowthLog(),function ($q) use ($user) {
                    return $q->where('user_id',$user->id);
                },['id'=>'desc'],['user_id']);
                return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
            }
            $page =$this->basePage()->body([
                shopwwiAmis('alert')->title('我的等级')->className('must m-0')
                    ->body("绑定的银行卡不可修改，可解绑后重新绑定"),
                GradeService::getIndexAmis()]);
            if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
            return $this->getUserView(['seoTitle'=>'我的等级','menuActive'=>'myGrade','json'=>$page]);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }
}