<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysAlbum;

class SysAlbumSeeder
{
    public static function run(){
        SysAlbum::query()->truncate();
        SysAlbum::create([
            'name' => '默认分组'
        ]);
    }
}