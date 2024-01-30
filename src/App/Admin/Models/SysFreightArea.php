<?php
/**
 *-------------------------------------------------------------------------s*
 * 运费模板地区模型
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


use Shopwwi\Admin\Libraries\ArrayCast;
use Shopwwi\Admin\Libraries\ArrayStringCast;
use Shopwwi\Admin\Libraries\NumberCast;

class SysFreightArea extends Model
{
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'sys_freight_area';

    /**
     * 与表关联的主键
     *
     * @var string
     */
    // protected $primaryKey = 'flight_id';

    /**
     * 主键是否主动递增
     *
     * @var bool
     */
    // public $incrementing = false;

    /**
     * 自动递增主键的「类型」
     *
     * @var string
     */
    // protected $keyType = 'string';

    /**
     * 是否主动维护时间戳
     *
     * @var bool
     */
     public $timestamps = false;

    /**
     * 模型日期的存储格式
     *
     * @var string
     */
    // protected $dateFormat = 'U';

    /**
     * 模型的数据库连接名
     *
     * @var string
     */
    // protected $connection = 'connection-name';

    /**
     * 可批量赋值属性
     *
     * @var array
     */
    // protected $fillable = [];

    /**
     * 模型属性的默认值
     *
     * @var array
     */
    protected $attributes = [

    ];

    protected $casts = [
        'area_id' => ArrayCast::class,
        'area_name' => ArrayStringCast::class,
        'item1' => NumberCast::class,
        'item2' => NumberCast::class,
        'price1' => NumberCast::class,
        'price2' => NumberCast::class,
    ];
    protected $appends = ['areaIds'];

    public function getAreaIdsAttribute()
    {
        $list = [];
        foreach ($this->area_id as $k=>$v){
            $list[] = ['code'=>$v,'name'=>$this->area_name[$k]??'无'];
        }
        return $list;
    }
    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

}
