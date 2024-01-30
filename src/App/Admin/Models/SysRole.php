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
namespace Shopwwi\Admin\App\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Shopwwi\Admin\App\Admin\Traits\SysRoleTraits;


class SysRole extends Model
{
    use SoftDeletes;
    use SysRoleTraits;
    protected $table = 'sys_role';
    /**
     * 模型属性的默认值
     *
     * @var array
     */
    protected $attributes = [
        'sort' => 999
    ];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * 菜单关联
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Relations\BelongsToMany|SysMenu[]
     */
    public function menu()
    {
        return $this->belongsToMany(SysMenu::class,'sys_role_menu','role_id','menu_id');
    }

}
