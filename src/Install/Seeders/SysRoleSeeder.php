<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysRole;

class SysRoleSeeder
{
    public static function run()
    {
        $first = SysRole::where('id',1)->first();
        if($first == null){
            SysRole::create([
                'id' => 1,
                'name' => '管理员'
            ]);
        }

    }
}