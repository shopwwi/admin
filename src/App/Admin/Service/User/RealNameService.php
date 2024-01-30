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
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\Admin\App\Admin\Service\User;

use Shopwwi\Admin\App\User\Models\Users;

class RealNameService
{
    /**
     * 平台审核实名状态
     * @return void
     */
    public static function adminVerifyReal($realName, $status,$remark)
    {
        switch ($status){
            case 1:
                $user = Users::where('id',$realName->user_id)->first();
                $user->is_real = 1;
                $user->save();
                break;
            case 2:
                break;
            case 8:
                $user = Users::where('id',$realName->user_id)->first();
                $user->is_real = 0;
                $user->save();
                break;
        }
        $realName->status = $status;
        $realName->remark = $remark;
        $realName->save();
        return $realName;
    }
}