<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysRole;
use Shopwwi\Admin\App\Admin\Models\SysUserSector;

class SysUserSectorSeeder
{
    public static function run()
    {
        $first = SysUserSector::where('id',1)->first();
        if($first == null){
            SysUserSector::create([
                'id' => 1,
                'name' => '总公司',
                'leader' => 1
            ]);
        }

    }
}