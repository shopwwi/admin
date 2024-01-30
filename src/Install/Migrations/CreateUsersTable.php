<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUsersTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('会员编号');
            $table->string('username')->nullable()->comment('会员账号');
            $table->string('nickname')->nullable()->comment('会员昵称');
            $table->string('avatar')->nullable()->comment('会员头像');
            $table->string('password')->comment('会员密码');
            $table->string('pay_pwd')->nullable()->comment('会员支付密码');
            $table->integer('grade_id')->nullable()->comment('等级编号');
            $table->decimal('balance',20)->nullable()->default(0)->comment('累计金额');
            $table->decimal('available_balance', 20)->nullable()->default(0)->comment('可用余额');
            $table->decimal('frozen_balance', 20)->nullable()->default(0)->comment('冻结余额');
            $table->string('phone')->nullable()->comment('手机号');
            $table->char('phone_bind', 1)->nullable()->default('0')->comment('是否绑定手机');
            $table->string('email')->nullable()->comment('邮箱');
            $table->char('email_bind', 1)->nullable()->default('0')->comment('是否绑定邮箱');
            $table->char('sex', 1)->nullable()->default('0')->comment('会员性别 0未知 1男 2女');
            $table->unsignedBigInteger('invite_id')->nullable()->comment('邀请人');
            $table->char('status', 1)->nullable()->default('1')->comment('会员状态 1 正常 0禁用');
            $table->unsignedBigInteger('growth')->nullable()->default(0)->comment('成长值');
            $table->decimal('points',20)->nullable()->default(0)->comment('累计积分');
            $table->decimal('available_points', 20)->nullable()->default(0)->comment('可用积分');
            $table->decimal('frozen_points', 20)->nullable()->default(0)->comment('冻结积分');
            $table->string('label')->nullable()->comment('标签');
            $table->string('last_login_ip', 50)->nullable()->comment('最后登入IP');
            $table->timestamp('last_login_time')->nullable()->comment('最后登入时间');
            $table->string('login_ip', 50)->nullable()->comment('当前登入IP');
            $table->unsignedInteger('login_num')->nullable()->default(0)->comment('登入次数');
            $table->timestamp('login_time')->nullable()->comment('当前登入时间');
            $table->dateTime('birthday')->nullable()->comment('生日');
            $table->integer('address_area_id')->nullable()->comment('地区ID');
            $table->string('address_area_info', 500)->nullable()->comment('所在地区');
            $table->integer('address_city_id')->nullable()->comment('所在城市ID');
            $table->integer('address_province_id')->nullable()->comment('所在省份ID');
            $table->unsignedInteger('modify_num')->nullable()->default(0)->comment('更名次数');
            $table->char('is_real', 1)->nullable()->default('0')->comment('实名状态');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('用户表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('users');
    }
}