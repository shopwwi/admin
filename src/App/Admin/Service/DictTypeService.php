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

use Shopwwi\Admin\App\Admin\Models\SysDictType;
use Shopwwi\LaravelCache\Cache;

class DictTypeService
{
    /**
     * 获取数据字典表
     */
    public static function getDictsAndDatas()
    {
        $list = Cache::rememberForever('shopwwiDictAndData', function () {
            return SysDictType::with(['data'=>function($q){
                $q->orderBy('sort','asc');
                $q->orderBy('id','asc');
            }])->orderBy('type')->get();
        });
        return $list;
    }

    public static function getDictsTypeOneList()
    {
        return Cache::rememberForever('shopwwiDictAndDataOne',function (){
            $type = SysDictType::with(['data'=>function($q){
                $q->orderBy('sort','asc');
                $q->orderBy('id','asc');
            }])->orderBy('type')->get();
            $dict = [];
            $type->map(function ($item) use(&$dict){
                $dict[$item->type] = $item->data;
            });
            return $dict;
        });
    }

    /**
     * 组合所有字典
     * @return array
     */
    public static function dicts()
    {
        $dicts = self::getDictsAndDatas();
        $dict=[];
        $dicts->map(function ($item)use (&$dict){
            $arr = [];
            foreach ($item->data as $value){
                $arr[$value->value] = $value->label;
            }
            $dict[$item->type]=$arr;
        });
        return $dict;
    }

    /**
     * 根据字典键及子键来获取值
     * @param $type
     * @param $key
     * @return mixed
     */
    public static function getRowDictByKey($type, $key)
    {
        $list = self::dicts();
        foreach ($list as $k => $val) {
            if ($type == $k) {
                foreach ($val as $kk => $vv){
                    if($key == $kk){
                        return $vv;
                    }
                }
            }
        }
        return $key;
    }

    /**
     * 根据字典键及子键来获取值
     * @param $type
     * @return mixed
     */
    public static function getRowDict($type)
    {
        $list = self::dicts();
        foreach ($list as $k => $val) {
            if ($type == $k) {
                return $val;
            }
        }
    }

    public static function getAmisDictType($type)
    {
        $dicts = self::getDictsTypeOneList();
        if(isset($dicts[$type])) return $dicts[$type];
        return [];
    }

    /**
     * 清理缓存
     */
    public static function clear()
    {
        Cache::forget("shopwwiDictAndData");
        Cache::forget("shopwwiDictAndDataOne");
    }

    public static function toMappingSelect($data,$type = '',$show = 'label'){
        $new = [];
        foreach ($data as $val){
            $val->list_class = $val->list_class ?? null;
            switch ($show){
                case 'label':
                    $new[$val->value] = '<span class="label label-'.$val->list_class.'">'.$val->label.'</span>';
                    break;
                case 'text':
                    $new[$val->value] = '<span class="text-'.$val->list_class.'">'.$val->label.'</span>';
                    break;
                case 'round':
                    $new[$val->value] = "<span class='label rounded-full border border-solid border-$val->list_class text-$val->list_class'>$val->label</span>";
                    break;
                case 'default':
                    $new[$val->value] = '<span class="cxd-Tag">'.$val->label.'</span>';
                    break;
                default:
                    $new[$val->value] = '<span>'.$val->label.'</span>';
                    break;
            }
        }
        $new['*'] = $type;
        return $new;
    }
}