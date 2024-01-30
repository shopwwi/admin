<?php
/**
 *-------------------------------------------------------------------------s*
 *
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 */

namespace Shopwwi\Admin\App\Admin\Controllers\User;

use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\Admin\App\Admin\Service\User\BalanceService;
use Shopwwi\Admin\App\Admin\Service\User\GrowthService;
use Shopwwi\Admin\App\Admin\Service\User\PointService;
use Shopwwi\Admin\Libraries\Amis\BaseController;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;

class TrimController extends BaseController
{
    /**
     * 操作积分
     * @param Request $request
     * @return \support\Response
     * @throws \Throwable
     */
    public function point(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        $params = shopwwiParams(['trimType','num'=>0,'userId'=>0,'cause'=>'']);
        try {
            Db::connection()->beginTransaction();
            PointService::adminTrim($params['userId'],strtoupper($params['trimType']),$params['num'],$user->id,$user->username,$params['cause']);
            Db::connection()->commit();
            AdminService::addLog('O','1','调整会员积分',$user->id,$user->username,$params);
            return shopwwiSuccess([],'调整成功');
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            AdminService::addLog('O','0','调整会员积分',$user->id,$user->username,$params,$e->getMessage());
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 操作经验值
     * @param Request $request
     * @return \support\Response
     * @throws \Throwable
     */
    public function growth(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        $params = shopwwiParams(['trimType','num'=>0,'userId'=>0,'cause'=>'']);
        try {
            Db::connection()->beginTransaction();
            GrowthService::adminTrim($params['userId'],strtoupper($params['trimType']),$params['num'],$user->id,$user->username,$params['cause']);
            Db::connection()->commit();
            AdminService::addLog('O','1','调整会员成长值',$user->id,$user->username,$params);
            return shopwwiSuccess([],'调整成功');
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            AdminService::addLog('O','0','调整会员成长值',$user->id,$user->username,$params,$e->getMessage());
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 操作余额
     * @param Request $request
     * @return \support\Response
     * @throws \Throwable
     */
    public function balance(Request $request)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        $params = shopwwiParams(['trimType','num'=>0,'userId'=>0,'cause'=>'']);
        try {
            Db::connection()->beginTransaction();
            BalanceService::adminTrim($params['userId'],strtoupper($params['trimType']),$params['num'],$user->id,$user->username,$params['cause']);
            Db::connection()->commit();
            AdminService::addLog('O','1','调整会员余额',$user->id,$user->username,$params);
            return shopwwiSuccess([],'调整成功');
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            AdminService::addLog('O','0','调整会员余额',$user->id,$user->username,$params,$e->getMessage());
            return shopwwiError($e->getMessage());
        }
    }
}