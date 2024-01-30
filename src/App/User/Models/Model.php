<?php


namespace Shopwwi\Admin\App\User\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Carbon;


class Model extends BaseModel
{
    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
        //  return Carbon::instance($date)->toDateTimeString();
    }
}
