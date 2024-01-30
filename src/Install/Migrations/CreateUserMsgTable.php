<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserMsgTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_msg', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('消息编号');
            $table->text('content')->nullable()->comment('消息内容');
            $table->char('is_read', 1)->nullable()->default('0')->comment('是否已读');
            $table->bigInteger('user_id')->nullable()->comment('用户ID');
            $table->char('user_del', 1)->nullable()->default('0')->comment('用户删除 1是 0否');
            $table->char('tpl_class', 4)->nullable()->comment('消息模板分类
会员 交易-1001 退换货-1002 物流-1003 资产-1004
商家 交易-2001 退换货-2002 商品-2003 运营-2004');
            $table->string('tpl_code')->nullable()->comment('消息模板编码');
            $table->string('sn')->nullable()->comment('特定数据编号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户消息表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_msg');
    }
}