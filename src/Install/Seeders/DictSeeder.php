<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysDictData;
use Shopwwi\Admin\App\Admin\Models\SysDictType;

class DictSeeder
{
    public static function run()
    {
        SysDictType::query()->truncate();
        SysDictData::query()->truncate();
        $list = self::dictData();
        foreach ($list as $item){
            SysDictType::updateOrCreate([
                'type' => $item['type']
            ],['name'=>$item['name'],'allow_delete'=>$item['allow_delete'],'remark' => $item['remark']]);

            foreach ($item['data'] as $data){
                $data['type'] = $item['type'];
                SysDictData::create($data);
            }
        }
        // 清除字典缓存
        \Shopwwi\Admin\App\Admin\Service\DictTypeService::clear();
    }
    protected static function dictData(){
        return [
            ['type' => 'openOrClose','name'=> '开启关闭','allow_delete'=> '0','remark' => '1开启 0关闭',
                'data'=>[
                    ['label' => '开启','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '关闭','value' => '0','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'allowOrUnAllow','name'=> '允许禁止','allow_delete'=> '0','remark' => '1允许 0禁止',
                'data'=>[
                    ['label' => '允许','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '禁止','value' => '0','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'yesOrNo','name'=> '是否','allow_delete'=> '0','remark' => '1是 0否',
                'data'=>[
                    ['label' => '是','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '否','value' => '0','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'sex','name'=> '性别','allow_delete'=> '0','remark' => '0未知 1男 2女',
                'data'=>[
                    ['label' => '未知','value' => '0','allow_delete'=> '0','list_class'=>'dark'],
                    ['label' => '男','value' => '1','allow_delete'=> '0','list_class'=>'info'],
                    ['label' => '女','value' => '2','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'businessType','name'=> '日志业务类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '其他','value' => 'O','allow_delete'=> '0','list_class'=>'dark'],
                    ['label' => '新增','value' => 'C','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '编辑','value' => 'E','allow_delete'=> '0','list_class'=>'info'],
                    ['label' => '删除','value' => 'D','allow_delete'=> '0','list_class'=>'danger'],
                    ['label' => '还原','value' => 'H','allow_delete'=> '0','list_class'=>'warning']
                ]
            ],
            ['type' => 'successOrError','name'=> '成功失败','allow_delete'=> '0','remark' => '1成功 0失败',
                'data'=>[
                    ['label' => '成功','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '失败','value' => '0','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'usedOrUnused','name'=> '已使用未使用','allow_delete'=> '0','remark' => '1已使用 0未使用',
                'data'=>[
                    ['label' => '已使用','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '未使用','value' => '0','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'sendType','name'=> '发送类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '登入','value' => 'LOGIN','allow_delete'=> '0'],
                    ['label' => '注册','value' => 'REGISTER','allow_delete'=> '0'],
                    ['label' => '验证','value' => 'AUTH','allow_delete'=> '0'],
                    ['label' => '找回密码','value' => 'FORGET','allow_delete'=> '0'],
                    ['label' => '绑定','value' => 'BIND','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'cashStatus','name'=> '提现状态','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '待处理','value' => '0','allow_delete'=> '0','list_class'=>'warning'],
                    ['label' => '提现成功','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '提现失败','value' => '2','allow_delete'=> '0','list_class'=>'danger'],
                    ['label' => '取消提现','value' => '8','allow_delete'=> '0','list_class'=>'dark'],
                ]
            ],
            ['type' => 'expressType','name'=> '快递查询渠道类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '快递鸟','value' => 'kdniao','allow_delete'=> '0'],
                    ['label' => '快递100','value' => 'kuaidi100','allow_delete'=> '0'],
                    ['label' => '万维易源','value' => 'showapi','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'realStatus','name'=> '实名状态','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '待处理','value' => '0','allow_delete'=> '0','list_class'=>'warning'],
                    ['label' => '已通过','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '未通过','value' => '2','allow_delete'=> '0','list_class'=>'danger'],
                    ['label' => '解除绑定','value' => '8','allow_delete'=> '0','list_class'=>'dark'],
                ]
            ],
            ['type' => 'verifyStatus','name'=> '审核状态','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '待处理','value' => '0','allow_delete'=> '0','list_class'=>'warning'],
                    ['label' => '已通过','value' => '1','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '未通过','value' => '2','allow_delete'=> '0','list_class'=>'danger']
                ]
            ],
            ['type' => 'trimType','name'=> '增加减少','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '增加','value' => 'INCREASE','allow_delete'=> '0','list_class'=>'info'],
                    ['label' => '减少','value' => 'DECREASE','allow_delete'=> '0','list_class'=>'danger'],
                ]
            ],
            ['type' => 'trimBalanceType','name'=> '余额操作类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '增加余额','value' => 'INCREASE','allow_delete'=> '0','list_class'=>'info'],
                    ['label' => '减少余额','value' => 'DECREASE','allow_delete'=> '0','list_class'=>'danger'],
                    ['label' => '冻结余额','value' => 'FREEZE','allow_delete'=> '0','list_class'=>'warning'],
                    ['label' => '解冻余额','value' => 'UNFREEZE','allow_delete'=> '0','list_class'=>'success'],
                ]
            ],
            ['type' => 'pointOperationStage','name'=> '积分变动类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '系统调整','value' => 'SYSTEM','allow_delete'=> '0'],
                    ['label' => '登入','value' => 'LOGIN','allow_delete'=> '0'],
                    ['label' => '注册','value' => 'REGISTER','allow_delete'=> '0'],
                    ['label' => '充值','value' => 'RECHARGE','allow_delete'=> '0'],
                    ['label' => '消费','value' => 'CONSUME','allow_delete'=> '0'],
                    ['label' => '活动','value' => 'ACTIVITY','allow_delete'=> '0'],
                    ['label' => '签到','value' => 'SIGN','allow_delete'=> '0'],
                    ['label' => '兑换','value' => 'EXCHANGE','allow_delete'=> '0'],
                    ['label' => '抽奖','value' => 'LOTTERY','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'growthOperationStage','name'=> '成长值变动类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '系统调整','value' => 'SYSTEM','allow_delete'=> '0'],
                    ['label' => '登入','value' => 'LOGIN','allow_delete'=> '0'],
                    ['label' => '注册','value' => 'REGISTER','allow_delete'=> '0'],
                    ['label' => '消费','value' => 'CONSUME','allow_delete'=> '0'],
                    ['label' => '活动','value' => 'ACTIVITY','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'balanceOperationStage','name'=> '金额变动类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '系统调整','value' => 'SYSTEM','allow_delete'=> '0'],
                    ['label' => '消费','value' => 'CONSUME','allow_delete'=> '0'],
                    ['label' => '活动','value' => 'ACTIVITY','allow_delete'=> '0'],
                    ['label' => '提现','value' => 'CASH','allow_delete'=> '0'],
                    ['label' => '返利','value' => 'REBATE','allow_delete'=> '0'],
                    ['label' => '充值','value' => 'RECHARGE','allow_delete'=> '0'],
                    ['label' => '退款','value' => 'REFUND','allow_delete'=> '0'],
                    ['label' => '打赏','value' => 'REWARD','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'gradeGroupType','name'=> '等级组类别','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '普通','value' => '0','allow_delete'=> '0'],
                    ['label' => '收费','value' => '1','allow_delete'=> '0'],
                    ['label' => '充值','value' => '2','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'payStatus','name'=> '支付状态','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '已支付','value' => '1','allow_delete'=> '0'],
                    ['label' => '待支付','value' => '0','allow_delete'=> '0']
                ]
            ],
            ['type' => 'payType','name'=> '支付订单类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '商品订单','value' => 'GOODS','allow_delete'=> '0'],
                    ['label' => '充值订单','value' => 'RECHARGE','allow_delete'=> '0']
                ]
            ],
            ['type' => 'freightCalcType','name'=> '运费模板类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '件','value' => 'NUMBER','allow_delete'=> '0'],
                    ['label' => '重量(KG)','value' => 'WEIGHT','allow_delete'=> '0'],
                    ['label' => '体积(m³)','value' => 'VOLUME','allow_delete'=> '0']
                ]
            ],
            ['type' => 'sysMenuType','name'=> '系统菜单类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '目录','value' => 'M','allow_delete'=> '0'],
                    ['label' => '菜单','value' => 'C','allow_delete'=> '0'],
                    ['label' => '按钮','value' => 'F','allow_delete'=> '0']
                ]
            ],
            ['type' => 'sysNoticePosition','name'=> '公告显示位置','allow_delete'=> '0','remark' => '公告显示的位置',
                'data'=>[
                    ['label' => '前台公告','value' => '0','allow_delete'=> '0'],
                    ['label' => '用户公告','value' => '1','allow_delete'=> '0'],
                    ['label' => '商家公告','value' => '2','allow_delete'=> '0']
                ]
            ],
            ['type' => 'sysHelpPosition','name'=> '帮助显示位置','allow_delete'=> '0','remark' => '帮助内容所显示的位置',
                'data'=>[
                    ['label' => '前台帮助','value' => '0','allow_delete'=> '0'],
                    ['label' => '用户帮助','value' => '1','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'sysArticlePosition','name'=> '文章显示位置','allow_delete'=> '0','remark' => '文章内容所显示的位置',
                'data'=>[
                    ['label' => '常规文章','value' => '0','allow_delete'=> '0'],
                    ['label' => '会员协议','value' => '1','allow_delete'=> '0']
                ]
            ],
            ['type' => 'sysArticleClassType','name'=> '文章类型','allow_delete'=> '0','remark' => '文章类型',
                'data'=>[
                    ['label' => '可删除可新增','value' => '0','allow_delete'=> '0'],
                    ['label' => '不可删除及新增','value' => '1','allow_delete'=> '0'],
                    ['label' => '不可删除可新增','value' => '2','allow_delete'=> '0']
                ]
            ],
            ['type' => 'pointOrGrowthRuleType','name'=> '积分及成长值获取规则类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '注册','value' => 'register','allow_delete'=> '0'],
                    ['label' => '每天首次登入','value' => 'login','allow_delete'=> '0']
                ]
            ],
            ['type' => 'userCardType','name'=> '绑卡类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '银行卡','value' => 'bank','allow_delete'=> '0'],
                    ['label' => '支付宝','value' => 'alipay','allow_delete'=> '0'],
                    ['label' => '微信','value' => 'wechat','allow_delete'=> '0']
                ]
            ],
            ['type' => 'msgTplSendType','name'=> '系统消息发送类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '邮件','value' => 'EMAIL','allow_delete'=> '0','list_class'=>'info'],
                    ['label' => '短信','value' => 'MSG','allow_delete'=> '0','list_class'=>'success']
                ]
            ],
            ['type' => 'sysRoleScope','name'=> '系统角色数据权限','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '全部数据','value' => '0','allow_delete'=> '0'],
                    ['label' => '自己数据','value' => '1','allow_delete'=> '0'],
                    ['label' => '本部门数据','value' => '2','allow_delete'=> '0'],
                    ['label' => '本部门及以下部门数据','value' => '3','allow_delete'=> '0']
                ]
            ],
            ['type' => 'sysNavigationPosition','name'=> '系统导航位置','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '顶部导航','value' => '0','allow_delete'=> '0'],
                    ['label' => '中部导航','value' => '1','allow_delete'=> '0'],
                    ['label' => '底部导航','value' => '2','allow_delete'=> '0'],
                ]
            ],
            ['type' => 'userMessageClass','name'=> '会员消息类型','allow_delete'=> '0','remark' => '',
                'data'=>[
                    ['label' => '交易消息','value' => '1001','allow_delete'=> '0','list_class'=>'error'],
                    ['label' => '退换货消息','value' => '1002','allow_delete'=> '0','list_class'=>'warning'],
                    ['label' => '物流消息','value' => '1003','allow_delete'=> '0','list_class'=>'info'],
                    ['label' => '资产消息','value' => '1004','allow_delete'=> '0','list_class'=>'success'],
                    ['label' => '推广消息','value' => '1005','allow_delete'=> '0','list_class'=>'success'],
                ]
            ],
        ];
    }
}