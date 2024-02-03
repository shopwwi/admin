<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\User\Models\UserGrade;

class UserGradeSeeder
{
    public static function run(){
        UserGrade::query()->truncate();
        $list = self::getData();
        foreach ($list as $item){
            UserGrade::create($item);
        }
    }

    public static function getData(){
        return [
            ['name' => '青铜会员', 'ext_name' => '青铜','level' => '', 'rule' => [], 'icon' => '', 'image_name' => ''],
            ['name' => '', 'ext_name' => '', 'rule' => [], 'icon' => '', 'image_name' => '']
        ];
    }
}