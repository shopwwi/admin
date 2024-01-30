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
use Shopwwi\Admin\App\Admin\Traits\SysUserTraits;
use Shopwwi\Admin\Libraries\ArrayStringCast;
use Shopwwi\WebmanFilesystem\Facade\Storage;


class SysUser extends Model
{
    use SoftDeletes;
    use SysUserTraits;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'sys_user';

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
        //  return Carbon::instance($date)->toDateTimeString();
    }
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
        'sex' => '0',
        'status' => '1'
    ];

    protected $casts = [
        'sector_ids' => ArrayStringCast::class
    ];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends=['avatarUrl'];
    protected $hidden=['password'];

    public function getAvatarUrlAttribute()
    {
        return empty($this->avatar)?$this->avatar:Storage::url($this->avatar);
    }
}
