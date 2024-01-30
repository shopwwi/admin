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

use Shopwwi\Admin\App\Admin\Models\SysArea;
use Shopwwi\Admin\App\Admin\Service\DateRangeService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\User\Models\UserBalanceCash;
use Shopwwi\Admin\App\User\Models\UserBalanceLog;
use Shopwwi\Admin\App\User\Models\UserBalanceRecharge;
use Shopwwi\Admin\App\User\Models\UserGrowthLog;
use Shopwwi\Admin\App\User\Models\UserPointLog;
use Shopwwi\Admin\App\User\Models\Users;
use support\Db;

class UserService
{
    /**
     * 统计会员区域数量
     */
    public static function getStatSpread($type,$date)
    {
        if($type === 'week'){
            $range = DateRangeService::thisWeekRange($date);
        }elseif ($type === 'month'){
            $range = DateRangeService::thisMonthRange($date);
        }else{
            $range = DateRangeService::todayRange($date);
        }
        $userCount = Users::count();
        return SysArea::leftJoin('users','sys_area.id',  'users.address_province_id')
            ->where(function ($q)use($range){
                $q->where('pid',0)->whereBetween('users.created_at',$range);
            })
            ->select(
                Db::raw("concat(round(count(users.id)/{$userCount}*100,2),'%') as percentage"),
                Db::raw('count(users.id) as user_total'),
                'sys_area.id',
                Db::raw('any_value(sys_area.ext_name) as area_name'),
            )
            ->groupBy('sys_area.id')
            ->orderBy('user_total','desc')
            ->orderBy('id','asc')
            ->get();
    }

    /**
     * 统计新增会员
     */
    public static function getStatNew($type,$date)
    {
        if($type === 'month'){
            $last_month = DateRangeService::lastMonthRange($date);
            $this_month = DateRangeService::thisMonthRange($date);
            $lastMonthData = Users::whereBetween('created_at',$last_month)
                ->select(
                    Db::raw('count(id) as count'),
                    Db::raw('date_format(created_at,"%d") as day')
                )
                ->groupBy('day')
                ->get();
            $lastMonthData = DateRangeService::monthNew($lastMonthData,$last_month[0]);
            $thisMonthData = Users::whereBetween('created_at',$this_month)
                ->select(
                    Db::raw('count(id) as count'),
                    Db::raw('date_format(created_at,"%d") as day')
                )
                ->groupBy('day')
                ->get();
            $thisMonthData = DateRangeService::monthNew($thisMonthData,$this_month[0]);
            return ['last'=>$lastMonthData,'this'=>$thisMonthData];
        }else if($type==='week'){
            $last_range = DateRangeService::lastWeekRange($date);
            $last_week = Users::whereBetween('created_at',$last_range)
                ->select(
                    Db::raw('count(id) as count'),
                    Db::raw('date_format(created_at,"%Y-%m-%d") as day')
                )
                ->groupBy('day')
                ->get();

            $last_week = DateRangeService::weekNew($last_week,$last_range[0]);
            $this_range = DateRangeService::thisWeekRange($date);
            $this_week = Users::whereBetween('created_at',$this_range)
                ->select(
                    Db::raw('count(id) as count'),
                    Db::raw('date_format(created_at,"%Y-%m-%d") as day')
                )
                ->groupBy('day')
                ->get();
            $this_week = DateRangeService::weekNew($this_week,$this_range[0]);
            return ['last'=>$last_week,'this'=>$this_week];
        }else{
            //昨天和今天的日期
            $yesterday = DateRangeService::yesterdayRange($date);
            $today = DateRangeService::todayRange($date);
            $yesterdayData = Users::whereBetween('created_at',$yesterday)
                ->select(
                    Db::raw('count(id) as count'),
                    Db::raw('date_format(created_at,"%H") as hour')
                )
                ->groupBy('hour')
                ->get();

            $todayData = Users::whereBetween('created_at',$today)
                ->select(
                    Db::raw('count(id) as count'),
                    Db::raw('date_format(created_at,"%H") as hour')
                )
                ->groupBy('hour')
                ->get();
            //处理小时不存在的情况
            $yesterdayData = DateRangeService::dayNew($yesterdayData,$yesterday[0]);
            $todayData = DateRangeService::dayNew($todayData,$today[0]);
            return ['last'=>$yesterdayData,'this'=>$todayData];
        }

    }

    /**
     * @return array
     */
    public static function getStatCard($type,$date)
    {
        if($type === 'month'){
            $last_month = DateRangeService::lastMonthRange($date);
            $this_month = DateRangeService::thisMonthRange($date);

            // 统计新增用户
            $userLastCount = Users::whereBetween('created_at',$last_month)->count();
            $userThisCount = Users::whereBetween('created_at',$this_month)->count();

            // 统计充值用户
            $rechargeLastCount = UserBalanceRecharge::whereBetween('created_at',$last_month)->where('status',1)->distinct('user_id')->count();
            $rechargeThisCount = UserBalanceRecharge::whereBetween('created_at',$this_month)->where('status',1)->distinct('user_id')->count();

            //统计提现用户
            $cashLastCount = UserBalanceCash::whereBetween('created_at',$last_month)->distinct('user_id')->count();
            $cashThisCount = UserBalanceCash::whereBetween('created_at',$this_month)->distinct('user_id')->count();

            //统计消费用户
            $balanceLastCount = UserBalanceLog::whereBetween('created_at',$last_month)->where('available_balance','<',0)->distinct('user_id')->count();
            $balanceThisCount = UserBalanceLog::whereBetween('created_at',$this_month)->where('available_balance','<',0)->distinct('user_id')->count();

            return ['last'=>[
                'create'=>$userLastCount,'recharge' => $rechargeLastCount , 'cash'=> $cashLastCount , 'balance' => $balanceLastCount
            ],'this'=>[
                'create'=>$userLastCount,'recharge' => $rechargeThisCount , 'cash'=> $cashThisCount , 'balance' => $balanceThisCount
            ]];
        }else if($type==='week'){
            $last_range = DateRangeService::lastWeekRange($date);
            $this_range = DateRangeService::thisWeekRange($date);

            // 统计新增用户
            $userLastCount = Users::whereBetween('created_at',$last_range)->count();
            $userThisCount = Users::whereBetween('created_at',$this_range)->count();

            // 统计充值用户
            $rechargeLastCount = UserBalanceRecharge::whereBetween('created_at',$last_range)->where('status',1)->distinct('user_id')->count();
            $rechargeThisCount = UserBalanceRecharge::whereBetween('created_at',$this_range)->where('status',1)->distinct('user_id')->count();

            //统计提现用户
            $cashLastCount = UserBalanceCash::whereBetween('created_at',$last_range)->distinct('user_id')->count();
            $cashThisCount = UserBalanceCash::whereBetween('created_at',$this_range)->distinct('user_id')->count();

            //统计消费用户
            $balanceLastCount = UserBalanceLog::whereBetween('created_at',$last_range)->where('available_balance','<',0)->distinct('user_id')->count();
            $balanceThisCount = UserBalanceLog::whereBetween('created_at',$this_range)->where('available_balance','<',0)->distinct('user_id')->count();

            return ['last'=>[
                'create'=>$userLastCount,'recharge' => $rechargeLastCount , 'cash'=> $cashLastCount , 'balance' => $balanceLastCount
            ],'this'=>[
                'create'=>$userLastCount,'recharge' => $rechargeThisCount , 'cash'=> $cashThisCount , 'balance' => $balanceThisCount
            ]];
        }else{
            //昨天和今天的日期
            $yesterday = DateRangeService::yesterdayRange($date);
            $today = DateRangeService::todayRange($date);

            // 统计新增用户
            $userLastCount = Users::whereBetween('created_at',$yesterday)->count();
            $userThisCount = Users::whereBetween('created_at',$today)->count();

            // 统计充值用户
            $rechargeLastCount = UserBalanceRecharge::whereBetween('created_at',$yesterday)->where('status',1)->distinct('user_id')->count();
            $rechargeThisCount = UserBalanceRecharge::whereBetween('created_at',$today)->where('status',1)->distinct('user_id')->count();

            //统计提现用户
            $cashLastCount = UserBalanceCash::whereBetween('created_at',$yesterday)->distinct('user_id')->count();
            $cashThisCount = UserBalanceCash::whereBetween('created_at',$today)->distinct('user_id')->count();

            //统计消费用户
            $balanceLastCount = UserBalanceLog::whereBetween('created_at',$yesterday)->where('available_balance','<',0)->distinct('user_id')->count();
            $balanceThisCount = UserBalanceLog::whereBetween('created_at',$today)->where('available_balance','<',0)->distinct('user_id')->count();

            return ['last'=>[
                'create'=>$userLastCount,'recharge' => $rechargeLastCount , 'cash'=> $cashLastCount , 'balance' => $balanceLastCount
            ],'this'=>[
                'create'=>$userLastCount,'recharge' => $rechargeThisCount , 'cash'=> $cashThisCount , 'balance' => $balanceThisCount
            ]];
        }
    }

    public static function getAmisStat($id,$type,$time)
    {
        if($id=='new'){
            $list = UserService::getStatNew($type,$time);
            $series = [
                [
                    'name'=> trans('stat.this_'.$type,[],'messages'), 'type' => 'line','stack' =>'Total','areaStyle'=> [],
                    'emphasis'=>['focus'=>'series'],
                    'smooth'=> true,
                    'data'=>[],
                ],
                [
                    'name'=> trans('stat.last_'.$type,[],'messages'), 'type' => 'line','stack' =>'Total','areaStyle'=> [],
                    'emphasis'=>['focus'=>'series'],
                    'smooth'=> true,
                    'data'=>[],
                ]
            ];
            $xAxisData = [];
            foreach ($list['this'] as $item){
                if(!in_array($item['xNum'],$xAxisData)){
                    $xAxisData[] = $item['xNum'];
                }
                $series[0]['data'][]= $item['count'];
            }
            foreach ($list['last'] as $item){
                if(!in_array($item['xNum'],$xAxisData)){
                    $xAxisData[] = $item['xNum'];
                }
                $series[1]['data'][]= $item['count'];
            }
            return [
                'toolbox'=>['show'=>true,'orient'=>'vertical','left'=>'right','top'=>'center',
                    'feature' => ['dataView' =>[ 'readOnly'=>false],'restore'=>[],'saveAsImage'=>[]]
                ],
                'legend'=>['data'=>[trans('stat.this_'.$type,[],'messages'),trans('stat.last_'.$type,[],'messages')]],
                'series' => $series,
                'tooltip'=>['trigger'=>'axis'],
                'grid' => ['left'=>'3%','right'=>'4%','bottom'=>'3%','containLabel'=>true],
                'xAxis' => ['type'=>'category','boundaryGap'=>false,'data'=>$xAxisData],
                'yAxis' => ['type'=>'value']
            ];
        }else if($id=='card'){
            $list = UserService::getStatCard($type,$time);
        }else{
            $list = UserService::getStatSpread($type,$time);
            $charData = [];
            $list->map(function ($item) use(&$charData){
                $charData[] = ['name'=>$item->area_name,'value'=>$item->user_total];
            });

            return [
                'toolbox'=>['show'=>true,'orient'=>'vertical','left'=>'right','top'=>'center',
                    'feature' => ['dataView' =>[ 'readOnly'=>false],'restore'=>[],'saveAsImage'=>[]]
                ],
                'series' => [
                    'type'=>'map',
                    'map'=>'china',
                    'label'=>[
                        'show'=>true,
                        'fontSize'=>10,
                    ],
                    // 地图大小倍数
                    'zoom'=>1.2,
                    'data'=>$charData
                ],
                'visualMap'=>[
                    'min'=>800,
                    'max'=>50000,
                    'text'=>['High', 'Low'],
                    'realtime'=>false,
                    'calculable'=>true,
                    'inRange'=>[
                        'color' =>['lightskyblue', 'yellow', 'orangered']
                    ]
                ]
            ];

        }
        return [];
    }

    /**
     * 积分操作弹框
     * @return mixed
     */
    public static function getTrimPointsAmisModel()
    {
        return shopwwiAmis('button')->actionType('dialog')
            ->dialog(
                shopwwiAmis('dialog')->title('调整积分')->body(
                    shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->api(shopwwiAdminUrl('user/trim/point'))
                    ->mode('horizontal')->body([
                            shopwwiAmis('hidden')->name('userId')->value('${id}'),
                            shopwwiAmis('input-number')->name('points')->label('累计积分')->static(true),
                            shopwwiAmis('input-number')->name('available_points')->label('可用积分')->static(true),
                            shopwwiAmis('input-number')->name('frozen_points')->label('冻结积分')->static(true),
                            shopwwiAmis('select')->name('trimType')->label('调整类型')->options(DictTypeService::getAmisDictType('trimBalanceType'))->required(true),
                            shopwwiAmis('input-number')->name('num')->min(0)->label('积分数')->required(true),
                            shopwwiAmis('textarea')->name('cause')->label('原因'),
                        ])
                )
            )->label('调整积分')->icon('fa-regular fa-pen-to-square')->level('link');
    }

    /**
     * 调整余额操作框
     * @return mixed
     */
    public static function getTrimBalanceAmisModel()
    {
        return shopwwiAmis('button')->actionType('dialog')
            ->dialog(
                shopwwiAmis('dialog')->title('调整余额')->body(
                    shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->api(shopwwiAdminUrl('user/trim/balance'))
                        ->mode('horizontal')->body([
                            shopwwiAmis('hidden')->name('userId')->value('${id}'),
                            shopwwiAmis('input-number')->name('balance')->label('累计金额')->static(true),
                            shopwwiAmis('input-number')->name('available_balance')->label('可用余额')->static(true),
                            shopwwiAmis('input-number')->name('frozen_balance')->label('冻结余额')->static(true),
                            shopwwiAmis('select')->name('trimType')->label('调整类型')->options(DictTypeService::getAmisDictType('trimBalanceType'))->required(true),
                            shopwwiAmis('input-number')->name('num')->min(0)->label('金额')->required(true),
                            shopwwiAmis('textarea')->name('cause')->label('原因'),
                        ])
                )
            )->label('调整余额')->icon('fa-regular fa-pen-to-square')->level('link');
    }

    /**
     * 调整成长值
     * @return mixed
     */
    public static function getTrimGrowthAmisModel()
    {
        return shopwwiAmis('button')->actionType('dialog')
            ->dialog(
                shopwwiAmis('dialog')->title('调整成长值')->body(
                    shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->api(shopwwiAdminUrl('user/trim/growth'))
                        ->mode('horizontal')->body([
                            shopwwiAmis('hidden')->name('userId')->value('${id}'),
                            shopwwiAmis('select')->name('trimType')->label('调整类型')->options(DictTypeService::getAmisDictType('trimType'))->required(true),
                            shopwwiAmis('input-number')->name('num')->min(0)->label('成长值')->required(true),
                            shopwwiAmis('textarea')->name('cause')->label('原因'),
                        ])
                )
            )->label('调整成长值')->icon('fa-regular fa-pen-to-square')->level('link');
    }

    /**
     * 首页计数
     * @return mixed
     */
    public static function getIndexInfo(){
        $user = Users::selectRaw('sum(growth) as growthCount,sum(available_balance + frozen_balance) as balanceCount,sum(available_points + frozen_points) as pointsCount,Count(distinct id) as userNum')->first();
        $user->newUserNum = Users::whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->count();
        $user->newGrowthCount = UserGrowthLog::whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->selectRaw('sum(growth) as growthCount')->value('growthCount') ?? 0;
        $user->newBalanceCount = UserBalanceLog::whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->selectRaw('sum(available_balance + frozen_balance) as balanceCount')->value('balanceCount') ?? 0;
        $user->newPointsCount = UserPointLog::whereBetween('created_at',[now()->startOfDay(),now()->endOfDay()])->selectRaw('sum(available_points + frozen_points) as pointsCount')->value('pointsCount') ?? 0;
        return $user;
    }
}