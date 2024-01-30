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

namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\App\User\Models\UserRealname;

class RealnameService
{
    /**
     * 提交实名认证
     * @param $params
     * @param $userId
     * @throws \Exception
     */
    public static function applyReal($params,$userId)
    {
        $realInfo = UserRealname::where('user_id',$userId)->first();
        $config = SysConfigService::getSettingByKey('realname');
        $auto = $config['used'] ?? '0';
        if($realInfo != null){
            if($realInfo->status == 1){
                throw new \Exception('实名认证已通过，请勿重复提交');
            }
            if(empty($realInfo->status)){
                throw new \Exception('实名认证申请已经提交过了，请耐心等待。');
            }
            foreach ($params as $key=>$val){
                $realInfo->$key = $val;
            }
            $realInfo->status = $auto?0:1; // 不需要审核
            $realInfo->remark = '';
            $realInfo->save();
        }else{
            $params['user_id'] = $userId;
            $params['status'] = $auto?0:1; // 不需要审核
            $realInfo = UserRealname::create($params);
        }
        return $realInfo;
    }
}