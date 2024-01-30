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
namespace Shopwwi\Admin\App\Admin\Traits;


trait SysDiyMobileTraits
{
    public static function bootSysDiyMobileTraits()
    {
        static::deleting(function ($sysDiyMobile) {
            if ($sysDiyMobile->key != 'VIEW_DIY') {
                throw new \Exception('系统定义页面不允许删除');
            }
        });
    }
}