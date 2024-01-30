<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserGradeRightsGradeTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_grade_rights_grade', function (Blueprint $table) {
            $table->integer('grade_id')->comment('会员等级id');
            $table->integer('grade_rights_id')->comment('会员权益ID');
            $table->text('rule')->nullable()->comment('规则配置');
            $table->primary(['grade_id', 'grade_rights_id']);
            $table->comment('用户组与权益对应表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_grade_rights_grade');
    }
}