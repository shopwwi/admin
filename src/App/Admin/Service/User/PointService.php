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
use Shopwwi\Admin\App\User\Models\UserPointLog;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\Admin\App\User\Models\UserSigning;
use support\Db;

class PointService
{
    /**
     * 管理员操作积分增减
     * @param $userId
     * @param $trimType
     * @param $num
     * @param $adminId
     * @param $adminName
     * @return void
     */
    public static function adminTrim($userId,$trimType,$num,$adminId,$adminName,$cause='')
    {
        $points = 0;
        $description = '';
        $available = 0;
        $frozen = 0;
        switch ($trimType){
            case 'INCREASE':
                $available = $num;
                $description = '系统调整积分，积分增加 '.$num;
                break;
            case 'DECREASE':
                $available = -$num;
                $description = '系统调整积分，积分减少 '.$num;
                break;
            case 'FREEZE':
                $available = -$num;
                $frozen = $num;
                $description = '系统冻结积分，减少可用积分 '.$num.' ，增加冻结积分 '.$num;
                break;
            case 'UNFREEZE':
                $available = $num;
                $frozen = -$num;
                $description = '系统解冻积分，增加可用积分 '.$num.' ，减少冻结积分 '.$num;
                break;
        }
        $log = [
            'sys_user_id' => $adminId,
            'sys_user_name' => $adminName,
            'description' => $description.(!empty($cause)?"（操作原因：{$cause}）":''),
            'user_id' => $userId,
            'operation_stage' => 'SYSTEM',
            'points' => 0,
            'available_points' => $available,
            'frozen_points' => $frozen,
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
        $info = SysConfigService::getSettingByKey('point',true);
        if($info['used']) {
            switch ($type) {
                case 'LOGIN':
                    $login = collect($info['rules'])->where('label','login')->first();
                    $has = UserPointLog::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                        ->where('user_id', $userId)
                        ->where('operation_stage', 'LOGIN')->first();
                    if ($has != null) { // 判断今天是否已赠送
                        return;
                    }
                    $log['description'] = '会员登入';
                    $log['available_points'] = $login['value'] ?? 5;
                    break;
                case 'REGISTER':
                    $register = collect($info['rules'])->where('label','register')->first();
                    $log['description'] = '会员注册';
                    $log['available_points'] = $register['value'] ?? 5;
                    break;
                case 'COMMENT':
                    $register = collect($info['rules'])->where('label','orderEvaluate')->first();
                    $log['description'] = '评论商品';
                    $log['available_points'] = $register['value'] ?? 5;
                    break;
            }
            $log['operation_stage'] = $type;
            $log['user_id'] = $userId;
            self::addLog($userId, 'INCREASE', $log['points'], $log);
        }
    }

    /**
     * 签到添加会员积分
     * @param $user
     * @throws \Exception
     */
    public static function userSigningPoints($user)
    {
        $today = UserSigning::where('user_id',$user->id)->whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->first();
        if($today){
            throw new \Exception('今日已签到');
        }
        $pointsConfig = SysConfigService::getSettingByKey('signing',true);

        $ruleList = collect($pointsConfig['list'])->sortByDesc('day');
        $yesterday = UserSigning::where('user_id',$user->id)->whereBetween('created_at',[now()->subDay()->startOfDay(),now()->subDay()->endOfDay()])->first();
        $signingDays = $yesterday != null && $yesterday->days < $ruleList->max('day') ? $yesterday->days+1 : 1;
        $addPoints = $pointsConfig['one'];
        foreach ($ruleList as $item){
            if($item['day'] <= $signingDays){
                $addPoints += $item['points'];
                break;
            }
        }
        if($addPoints < 1){
            throw new \Exception('数据错误');
        }
        UserSigning::create([
            'user_id' => $user->id,
            'user_nickname' => $user->nickname,
            'user_avatar' => $user->avatar,
            'days' => $signingDays,
            'points' => $addPoints
        ]);
        $log = [
            'description' => '会员签到',
            'user_id' => $user->id,
            'operation_stage' => 'SIGNING',
            'available_points' => $addPoints,
        ];
        self::addLog($user->id,'INCREASE' , $addPoints , $log);
    }

    /**
     * 写入日志及变更处理
     * @param $userId
     * @param $trimType
     * @param $num
     * @param $log
     * @return void
     * @throws \Exception
     */
    public static function addLog($userId,$trimType,$num,$log,$tap = false)
    {
        if(empty($userId)){
            throw new \Exception(trans('dataError',[],'messages'));
        }
        $user = Users::where('id',$userId)->first();
        if($user == null){
            throw new \Exception('会员不存在');
        }
        if(!in_array($trimType,['INCREASE','DECREASE','FREEZE','UNFREEZE','SUBFREEZE'])){
            throw new \Exception('操作类型错误');
        }
        $log['user_name'] = $user->nickname;
        $log['points'] = \bcadd($user->available_points,$user->frozen_points,2) ;
        switch ($trimType){
            case 'INCREASE':
                $user->available_points = Db::raw('available_points +'.$num);
                if($tap){
                    $user->points = Db::raw('points +'.$num);
                }
                break;
            case 'DECREASE':
                if($user->available_points < $num){
                    throw new \Exception('可操作可用积分不足');
                }
                $user->available_points = Db::raw('available_points -'.$num);
                if($tap){
                    $user->points = Db::raw('points -'.$num);
                }
                break;
            case 'FREEZE':
                if($user->available_points < $num){
                    throw new \Exception('可操作可用积分不足');
                }
                $user->available_points = Db::raw('available_points -'.$num);
                $user->frozen_points = Db::raw('frozen_points +'.$num);
                break;
            case 'UNFREEZE':
                if($user->frozen_balance < $num){
                    throw new \Exception('可操作解冻积分不足');
                }
                $user->available_points = Db::raw('available_points +'.$num);
                $user->frozen_points = Db::raw('frozen_points -'.$num);
                break;
            case 'SUBFREEZE':
                if($user->frozen_points < $num){
                    throw new \Exception('可操作解冻积分不足');
                }
                $user->frozen_points = Db::raw('frozen_points -'.$num);
                break;
        }

        $user->save();
        // 写入积分日志
        UserPointLog::create($log);
    }
}