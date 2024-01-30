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

use Shopwwi\Admin\App\Admin\Models\SysArticleClass;

trait SysArticleTraits
{
    public static function bootSysArticleTraits()
    {
        static::creating(function ($article){
            $category = SysArticleClass::where('id',$article->category_id)->first();
            $article->allow_delete = 1;
            if($category != null && !empty($category->type)){
                $article->allow_delete = 0;
            }
        });

        static::updating(function ($article){

        });

        static::deleting(function ($article){
            if ($article->allow_delete != 1 ){
                throw new \Exception('内置禁止删除');
            }
        });
    }
}