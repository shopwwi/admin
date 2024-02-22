<?php

namespace Shopwwi\Admin\Install\Seeders;


use Shopwwi\Admin\App\User\Models\UserGrade;
use Shopwwi\Admin\App\User\Models\UserGradeGroup;

class UserGradeGroupSeeder
{
    public static function run()
    {
        UserGradeGroup::query()->truncate();
        $list = self::getData();
        foreach ($list as $item){
            $grades = $item['grades'];
            unset($item['grades']);
            $group = UserGradeGroup::create($item);
            foreach ($grades as $gg){
                $gg['group_id'] = $group->id;
                UserGrade::create($gg);
            }

        }
    }
    protected static function getData(){
        return [
            ['name' => '普通会员','type' => 0,'is_default' => 1,'grades' => [
                ['name' => '青铜会员', 'ext_name' => '青铜', 'level' => 1, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 1],
                ['name' => '白银会员', 'ext_name' => '白银', 'level' => 2, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => '黄金会员', 'ext_name' => '黄金', 'level' => 3, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => '铂金会员', 'ext_name' => '铂金', 'level' => 4, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => '砖石会员', 'ext_name' => '砖石', 'level' => 5, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
            ]],
            ['name' => 'SVIP','type' => 1,'is_default' => 0,'rule'=>['week'=>10,'month'=>20,'year'=>180],'grades' => [
                ['name' => 'SVIP1', 'ext_name' => 'SVIP1', 'level' => 1, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 1],
                ['name' => 'SVIP2', 'ext_name' => 'SVIP2', 'level' => 2, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => 'SVIP3', 'ext_name' => 'SVIP3', 'level' => 3, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
            ]],
            ['name' => '采购会员','type' => 0,'is_default' => 0,'rule'=>['month'=>50000,'year'=>100000],'grades' => [
                ['name' => '青铜采购', 'ext_name' => 'V1', 'level' => 1, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 1],
                ['name' => '白银采购', 'ext_name' => 'V2', 'level' => 2, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => '黄金采购', 'ext_name' => 'V3', 'level' => 3, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => '铂金采购', 'ext_name' => 'V4', 'level' => 4, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
                ['name' => '砖石采购', 'ext_name' => 'V5', 'level' => 5, 'rule' => ['modal' => 0, // 0 为任意条件 1为全部条件
                    'items' => [
                        ['type'=>'consume','desc'=> '消费满xx元', 'used'=>'0','num' => 0], //消费满XX元
                        ['type'=>'orders','desc'=> '订单量','used'=>'0','num' => 0], // 订单量
                        ['type'=>'recharge','desc'=> '累计充值','used'=>'0','num' => 0], // 累计充值
                        ['type'=>'invite','desc'=> '邀请人数','used'=>'0','num' => 0], // 邀请人数
                        ['type'=>'growth','desc'=> '成长值','used'=>'0','num' => 0], // 成长值
                        ['type'=>'points','desc'=> '积分累计','used'=>'0','num' => 0], // 积分数量
                    ]], 'icon' => '', 'image_name' => '','is_default' => 0],
            ]],
        ];
    }

}