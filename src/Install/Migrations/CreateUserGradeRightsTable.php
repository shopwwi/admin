<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserGradeRightsTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_grade_rights', function (Blueprint $table) {
            $table->increments('id');
            $table->string('r_name')->nullable()->comment('权益名称');
            $table->string('r_key')->nullable()->unique('r_key')->comment('权益类型');
            $table->string('r_icon')->nullable()->comment('权益图标');
            $table->text('r_remark')->nullable()->comment('权益说明');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户组权益表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_grade_rights');
    }
}