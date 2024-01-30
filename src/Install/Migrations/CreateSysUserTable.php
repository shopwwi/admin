<?php

namespace Shopwwi\Admin\Install\Migrations;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysUserTable
{
    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_user', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增');
            $table->string('username')->nullable()->comment('账号');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->bigInteger('role_id')->comment('角色');
            $table->string('sector_ids')->nullable()->comment('部门');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('mobile')->nullable()->comment('手机号');
            $table->char('sex',1)->nullable()->default('0')->comment('性别 0未知 1男 2女');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('password')->nullable()->comment('密码');
            $table->char('status', 1)->nullable()->default('1')->comment('帐号状态（1正常 0停用）');
            $table->string('login_ip')->nullable()->comment('登入IP');
            $table->timestamp('login_time')->nullable()->comment('登入时间');
            $table->integer('login_num')->nullable()->default(0)->comment('登入次数');
            $table->string('last_login_ip')->nullable()->comment('上次登入IP');
            $table->timestamp('last_login_time')->nullable()->comment('上次登入时间');
            $table->bigInteger('created_user_id')->nullable()->comment('创建人员');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改人员');
            $table->timestamp('updated_at')->nullable()->comment('修改时间');
            $table->softDeletes()->comment('删除时间');
            $table->comment('系统用户表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_user');
    }
}