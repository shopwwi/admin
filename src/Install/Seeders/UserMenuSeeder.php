<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\User\Models\UserMenu;

class UserMenuSeeder
{
    public static function run()
    {
        UserMenu::query()->truncate();
        $list = self::getData();
        $sort = 1;
        foreach ($list as $item){
            if(isset($item['children'])){
                $level = $item['children'];
                unset($item['children']);
            }
            if(!isset($item['sort'])){
                $item['sort'] = $sort;
            }
            $first = UserMenu::create($item);
            if(isset($level) && count($level) > 0) {
                self::setMenu($level, $first->id);
            }
            $sort++;
        }
    }

    /**
     * 循环处理下级菜单
     * @param $data
     * @param $pid
     */
    protected static function setMenu($data,$pid){
        $sort = 1;
        foreach ($data as $item){
            $level = $item['children'] ?? null;
            if(!empty($level)){
                unset($item['children']);
            }
            $item['pid'] = $pid;
            if(!isset($item['sort'])){
                $item['sort'] = $sort;
            }
            $first = UserMenu::create($item);
            if(!empty($level)) {
                self::setMenu($level, $first->id);
            }
            $sort++;
        }
    }

    protected static function getData(){
        return [
            ['name' => '账户设置','id'=>'account','menu_type' => 'M','children' => [
                ['name'=>'会员资料','id'=>'auth','menu_type' => 'M','children' => [
                    ['name'=>'账户安全','id'=>'authSecurity','menu_type' => 'C','component' => '/security','path'=>'/security'],
                    ['name'=>'收货地址','id'=>'authAddress','menu_type' => 'C','component' => '/address','path'=>'/address'],
                    ['name'=>'实名认证','id'=>'authReal','menu_type' => 'C','component' => '/real','path'=>'/real'],
                ]],
                ['name'=>'财产中心','id'=>'my','menu_type' => 'M','children' => [
                    ['name'=>'我的资产','id'=>'balance','menu_type' => 'M','children' => [
                        ['name'=>'我的余额','id'=>'balanceIndex','menu_type' => 'C','component' => '/balance','path'=>'/balance'],
                        ['name'=>'我的充值','id'=>'balanceRecharge','menu_type' => 'C','component' => '/recharge','path'=>'/recharge'],
                        ['name'=>'我的提现','id'=>'balanceCash','menu_type' => 'C','component' => '/cash','path'=>'/cash'],
                        ['name'=>'我的银行卡','id'=>'balanceBank','menu_type' => 'C','component' => '/bank','path'=>'/bank'],
                    ]],
                    ['name'=>'我的积分','id'=>'myPoint','menu_type' => 'C','component' => '/points','path'=>'/points'],
                    ['name'=>'我的等级','id'=>'myGrade','menu_type' => 'C','component' => '/grade','path'=>'/grade'],
                ]]
            ]]
        ];
    }
}