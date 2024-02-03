<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户等级表模型
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
 * @property integer $id
 * @property integer $group_id 组ID
 * @property integer $level 数字等级
 * @property string $name 等级名称
 * @property string $ext_name 等级个性化名称
 * @property string $rule 升级条件json
 * @property string $icon 等级图标
 * @property string $image_name 等级背景
 * @property string $remark 等级说明
 * @property integer $status 是否开启 1开启 0关闭
 * @property integer $is_default 默认等级
 * @property integer $created_user_id 添加者
 * @property string $created_at 创建时间
 * @property integer $updated_user_id 修改者
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除标识
 */
namespace Shopwwi\Admin\App\User\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shopwwi\Admin\App\User\Traits\UserGradeTraits;
use Shopwwi\Admin\Libraries\Storage;

class UserGrade extends Model
{
    use UserGradeTraits;
    use SoftDeletes;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user_grade';

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

    protected $appends = ['iconUrl','imageUrl'];

    public function getIconUrlAttribute($key)
    {
        return $this->icon ? Storage::url($this->icon):'';
    }
    public function getImageUrlAttribute($key)
    {
        return $this->image_name ? Storage::url($this->image_name):'';
    }
}
