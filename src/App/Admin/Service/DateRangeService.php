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

namespace Shopwwi\Admin\App\Admin\Service;

use Carbon\Carbon;

class DateRangeService
{
    /**昨天的日期范围
     * @param $searchDate
     * @return array
     */
    public static function yesterdayRange($searchDate)
    {
        $now = Carbon::parse($searchDate)->subDays(1);
        $first = $now->startOfDay()->toDateTimeString();
        $end = $now->endOfDay()->toDateTimeString();
        return [$first,$end];
    }

    /**
     * 今天的日期范围
     * @param $searchDate
     * @return array
     */
    public static function todayRange($searchDate)
    {
        $now = Carbon::parse($searchDate);
        $first = $now->startOfDay()->toDateTimeString();
        $end = $now->endOfDay()->toDateTimeString();
        return [$first,$end];
    }

    /**
     * 上个星期的日期范围
     */
    public static function lastWeekRange($searchWeek)
    {
        $date = Carbon::parse($searchWeek)->subWeeks(1);
        $first = $date->startOfWeek()->toDateTimeString();
        $end = $date->endOfWeek()->toDateTimeString();
        return [$first,$end];
    }

    /**
     * 上个星期的日期范围
     */
    public static function thisWeekRange($searchWeek)
    {
        $date = Carbon::parse($searchWeek);
        $first = $date->startOfWeek()->toDateTimeString();
        $end = $date->endOfWeek()->toDateTimeString();
        return [$first,$end];
    }
    /**
     * 上个月日期范围
     * @param $data
     * @return array
     */
    public static function lastMonthRange($data)
    {
        $month = Carbon::parse($data)->subMonths(1);
        $first = $month->startOfMonth()->toDateTimeString();
        $end = $month->endOfMonth()->toDateTimeString();
        return [$first,$end];
    }

    /**
     * 本月日期范围
     */
    public static function thisMonthRange($data)
    {
        $month = Carbon::parse($data);
        $first = $month->startOfMonth()->toDateTimeString();
        $end = $month->endOfMonth()->toDateTimeString();
        return [$first,$end];
    }
    /**
     * 按日整理数据
     * @param $list
     * @param string $today
     * @param string[] $param
     * @return array
     */
    public static function dayNew($list,$today = '',$param=['count'])
    {
        $arr = [];
        for ($i=0;$i<=23;$i++){
            if (in_array($i,$list->pluck('hour')->toArray())){
                foreach ($list as $item){
                    if ($item->hour == $i){
                        $u['xNum'] = $item->hour;
                        foreach ($param as $value){
                            $u[$value] = $item->$value??0;
                        }
              
                        $start_time = Carbon::parse($today)->addHours($i)->startOfMinute()->toDateTimeString();
                        $u['startTime'] = $start_time;
                        $end_time = Carbon::parse($today)->addHours($i)->endOfMinute()->toDateTimeString();
                        $u['endTime'] = $end_time;
            
                        array_push($arr,$u);
                    }
                }
            }else{
                $u['xNum'] = "$i";
                foreach ($param as $value){
                    $u[$value] =0;
                }
                $start_time = Carbon::parse($today)->addHours($i)->startOfHour()->toDateTimeString();
                $u['startTime'] = $start_time;
                $end_time = Carbon::parse($today)->addHours($i)->endOfHour()->toDateTimeString();
                $u['endTime'] = $end_time;

                array_push($arr,$u);
            }
        }
        return $arr;
    }

    /**
     * 按周整理数据
     * @param $list
     * @param $range
     * @param string[] $param
     * @return array
     */
    public static function weekNew($list,$range,$param=['count'])
    {
        $arr = [];
        for ($i=0;$i<=6;$i++){
            $day = Carbon::parse($range)->addDays($i)->format('Y-m-d');

            if (in_array($day,$list->pluck('day')->toarray())){
                foreach ($list as $item){
                    if ($item->day == $day){
                        $u['xNum'] = ($i+1).'';
                        foreach ($param as $value){
                            $u[$value] = $item->$value??0;
                        }
                        $start_time = Carbon::parse($range)->addDays($i)->startOfDay()->toDateTimeString();
                        $u['startTime'] = $start_time;
                        $end_time = Carbon::parse($range)->addDays($i)->endOfDay()->toDateTimeString();
                        $u['endTime'] = $end_time;
                        array_push($arr,$u);
                    }
                }
            }else{
                $u['xNum'] = ($i+1).'';
                foreach ($param as $value){
                    $u[$value] = 0;
                }
                $start_time = Carbon::parse($range)->addDays($i)->startOfDay()->toDateTimeString();
                $u['startTime'] = $start_time;
                $end_time = Carbon::parse($range)->addDays($i)->endOfDay()->toDateTimeString();
                $u['endTime'] = $end_time;
                array_push($arr,$u);
            }
        }
        return $arr;
    }

    /**
     * 按年整理数据
     * @param $list
     * @param $date
     * @param string[] $param
     * @return array
     */
    public static function monthNew($list,$date,$param=['count'])
    {
        //处理数组
        $arr = [];
        //获得第一天

        //获得最后一天
        $last_day = Carbon::parse($date)->endOfMonth()->day;

        for ($i = 1;$i<=$last_day;$i++){
            if (in_array($i,$list->pluck('day')->toarray())){
                foreach ($list as $item){
                    if ($i==$item->day){
                        $u['xNum'] = "$i";
                        foreach ($param as $value){
                            $u[$value] = $item->$value??0;
                        }
                        $start_time =  Carbon::parse($date)->addDays($i)->startOfDay()->toDateTimeString();
                        $u['startTime'] = $start_time;
                        $end_time =  Carbon::parse($date)->addDays($i)->endOfDay()->toDateTimeString();
                        $u['endTime'] = $end_time;
                        array_push($arr,$u);
                    }
                }
            }else{
                $u['xNum'] = "$i";
                foreach ($param as $value){
                    $u[$value] = 0;
                }
                $start_time = Carbon::parse($date)->addDays($i)->startOfDay()->toDateTimeString();
                $u['startTime'] = $start_time;
                $end_time = Carbon::parse($date)->addDays($i)->endOfDay()->toDateTimeString();
                $u['endTime'] = $end_time;
                array_push($arr,$u);
            }
        }
        return $arr;
    }

    public static function getCarbon($date)
    {
        if(is_numeric($date) || strlen($date) == 13){
            return Carbon::createFromTimestampMs($date);
        }
        return Carbon::parse($date);
    }
}