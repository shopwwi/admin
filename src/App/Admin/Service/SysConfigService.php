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

use Shopwwi\Admin\App\Admin\Models\SysConfig;
use Shopwwi\LaravelCache\Cache;

class SysConfigService
{
    /**
     * 获取所有配置项目
     * @param bool $cache
     * @return array
     */
    public static function getList(bool $cache = false): array
    {
        $listSetting = [];
        if($cache){
            $settings = Cache::rememberForever('shopwwiConfig', function () {
                return SysConfig::all();
            });
        }else{
            $settings = SysConfig::all();
        }

        foreach ($settings as $key => $setting) {
            $listSetting[$setting->key] = $setting->value;
        }
        return $listSetting;
    }

    public static function clear()
    {
        Cache::forget("shopwwiConfig");
    }

    /**
     * 根据KEY获取值
     * @param $key
     * @param bool $cache
     * @return mixed
     */
    public static function getSettingByKey($key, bool $cache = false) {
        $listSetting = self::getList($cache);
        foreach ($listSetting as $k => $v) {
            if ($key == $k) {
                return $v;
            }
        }
    }

    /**
     * 查询指定列
     * @param array $keys
     * @param bool $cache
     * @return array
     */
    public static function getRowSetting(array $keys = [], bool $cache = false): array
    {
        $listSetting = [];
        foreach ($keys as $key) {
            $listSetting[$key] = self::getSettingByKey($key,$cache);
        }
        return $listSetting;
    }

    /**
     * 更新配置信息
     * @param $list
     * @return void
     */
    public static function updateSetting($list) {
        if (!count($list) || !is_array($list)) {
            return;
        }
        collect($list)->map(function ($value, $key) {
            $setting = SysConfig::where('key', $key)->first();
            if ($setting) {
                $setting->value = $value;
                $setting->save();
            } else {
                SysConfig::create([
                    'key'  => $key,
                    'value' => $value,
                ]);
            }
        });
        self::clear();
    }

    public static function getFirstOrCreate($find,$default): array
    {
        $info = SysConfig::firstOrCreate($find,$default);
        self::clear();
        return [$info->key=>$info->value];
    }
}