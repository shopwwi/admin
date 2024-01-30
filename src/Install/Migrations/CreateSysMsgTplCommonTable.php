<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysMsgTplCommonTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_msg_tpl_common', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->longText('email_content')->nullable()->comment('邮件内容');
            $table->char('email_status', 1)->nullable()->default('0')->comment('邮件开关 0-关闭 1-开启');
            $table->string('email_title', 500)->nullable()->comment('邮件标题');
            $table->longText('notice_content')->nullable()->comment('站内信内容');
            $table->string('sms_content', 1000)->nullable()->comment('短信内容');
            $table->char('sms_status', 1)->nullable()->default('0')->comment('短信开关 0-关闭 1-开启');
            $table->char('class', 4)->nullable()->comment('消息模板分类
会员 交易-1001 退换货-1002 物流-1003 资产-1004
商家 交易-2001 退换货-2002 商品-2003 运营-2004');
            $table->string('name')->nullable()->comment('消息模板名称');
            $table->char('type', 1)->nullable()->comment('消息模板类型 U-用户消息模板 S-商家消息模板');
            $table->longText('wechat_data_params')->nullable()->comment('微信消息内容');
            $table->string('wechat_mp_template_id', 500)->nullable()->comment('微信公众平台我的模板ID');
            $table->string('wechat_mp_template_store_id', 500)->nullable()->comment('微信公众平台模板库编号');
            $table->string('wechat_mp_template_store_title', 500)->nullable()->comment('微信公众平台模板库标题');
            $table->char('wechat_status', 1)->nullable()->default('0')->comment('微信开关 0-关闭 1-开启');
            $table->string('wechat_template_url', 500)->nullable()->comment('微信消息跳转链接');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统消息公共模板表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_msg_tpl_common');
    }
}