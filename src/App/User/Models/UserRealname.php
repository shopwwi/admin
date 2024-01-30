<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户实名认证表模型
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
namespace Shopwwi\Admin\App\User\Models;

use Shopwwi\WebmanFilesystem\Facade\Storage;

class UserRealname extends Model
{
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user_realname';

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
        'status' => 'O'
    ];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(Users::class,'id','user_id');
    }
    protected $appends = ['idCardHandleUrl','idCardFrontUrl','idCardBackUrl'];
    public function getIdCardHandleUrlAttribute($key)
    {
        return empty($this->id_card_handle)? '': Storage::url($this->id_card_handle);
    }
    public function getIdCardFrontUrlAttribute($key)
    {
        return empty($this->id_card_front)? '': Storage::url($this->id_card_front);
    }
    public function getIdCardBackUrlAttribute($key)
    {
        return empty($this->id_card_back)? '': Storage::url($this->id_card_back);
    }

}
