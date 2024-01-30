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

use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\App\User\Models\UserGrowthLog;
use Shopwwi\Admin\App\User\Models\Users;
use support\Db;

class GrowthService
{
    /**
     * 管理员操作积分增减
     * @param $userId
     * @param $trimType
     * @param $num
     * @param $adminId
     * @param $adminName
     * @param string $cause
     * @throws \Exception
     */
    public static function adminTrim($userId,$trimType,$num,$adminId,$adminName,$cause='')
    {
        $points = 0;
        $description = '';
        switch ($trimType){
            case 'INCREASE':
                $points = $num;
                $description = '系统调整成长值，成长值增加 '.$num;
                break;
            case 'DECREASE':
                $points = -$num;
                $description = '系统调整成长值，成长值减少 '.$num;
                break;
        }
        $log = [
            'sys_user_id' => $adminId,
            'sys_user_name' => $adminName,
            'description' => $description.(!empty($cause)?"（操作原因：{$cause}）":''),
            'user_id' => $userId,
            'operation_stage' => 'SYSTEM',
            'growth' => $points,
        ];
        self::addLog($userId,$trimType,$num,$log);
    }
    /**
     * 快速操作积分
     * @param $type
     * @param $userId
     * @return void
     * @throws \Exception
     */
    public static function optionsPoints( $type , $userId)
    {
        $log = [];
        $info = SysConfigService::getSettingByKey('growth',true);
        if($info['used']) {
            switch ($type) {
                case 'LOGIN':
                    $login = collect($info['rules'])->where('label','login')->first();
                    $has = UserGrowthLog::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                        ->where('user_id', $userId)
                        ->where('operation_stage', 'LOGIN')->first();
                    if ($has != null) { // 判断今天是否已赠送
                        return;
                    }
                    $log['description'] = '会员登入';
                    $log['growth'] = $login['value'] ?? 5;
                    break;
                case 'REGISTER':
                    $register = collect($info['rules'])->where('label','register')->first();
                    $log['description'] = '会员注册';
                    $log['growth'] = $register['value'] ?? 5;
                    break;
            }
            $log['operation_stage'] = $type;
            $log['user_id'] = $userId;
            self::addLog($userId, 'INCREASE', $log['growth'], $log);
        }
    }
    /**
     * 写入日志及变更处理
     * @param $userId
     * @param $trimType
     * @param $num
     * @param $log
     * @throws \Exception
     */
    public static function addLog($userId,$trimType,$num,$log)
    {
        $user = Users::where('id',$userId)->first();
        if($user == null){
            throw new \Exception('会员不存在');
        }
        if(!in_array($trimType,['INCREASE','DECREASE'])){
            throw new \Exception('操作类型错误');
        }
        if($trimType == 'INCREASE'){
            $user->growth = Db::raw('growth +'.$num);
        }else{
            $user->growth = Db::raw('growth -'.$num);
        }
        $user->save();
        // 写入积分日志
        UserGrowthLog::create($log);
    }
}