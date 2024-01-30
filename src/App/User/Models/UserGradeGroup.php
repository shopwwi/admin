<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户组表模型
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2024 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author     8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 * @property integer $id 用户组
 * @property string $name 组名称
 * @property string $type 组类别 0普通 1收费 2充值
 * @property string $rule 购买条件适应非普通组别
 * @property integer $is_default 默认组别
 * @property integer $created_user_id 添加者
 * @property string $created_at 创建时间
 * @property integer $updated_user_id 修改者
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除标识
 */
namespace Shopwwi\Admin\App\User\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shopwwi\Admin\App\User\Traits\UserGradeGroupTraits;

class UserGradeGroup extends Model
{
    use SoftDeletes;
    use UserGradeGroupTraits;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user_grade_group';

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
    // public $timestamps = false;

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
    protected $casts = ['rule'=>'json'];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

}
