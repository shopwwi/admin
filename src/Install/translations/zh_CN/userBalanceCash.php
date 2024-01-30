<?php

return [
    'projectName' => '用户提现',
    'list' => '用户提现列表',
    'create' => '新增用户提现',
    'update' => '编辑用户提现',
    'delete' => '删除用户提现',
    'show' => '用户提现详情',
    'back' => '返回用户提现列表',
    'confirm_delete' => '确定删除所选用户提现吗？',
    'field' => [
        'id' => '',
        'sys_user_id' => '管理员ID',
        'sys_user_name' => '管理员名称',
        'cash_sn' => '提现单号',
        'cash_amount' => '提现金额',
        'service_amount' => '提现手续费',
        'amount' => '实际到账',
        'user_id' => '会员',
        'pay_time' => '支付时间',
        'refuse_reason' => '拒绝提现理由',
        'cash_status' => '状态',
        'bank_name' => '银行名称',
        'bank_account' => '提现账号',
        'bank_username' => '持卡人',
        'bank_branch' => '银行支行',
        'bank_type' => '银行类别',
        'out_sn' => '外部交易号',
        'bankId' => '提现卡'
    ],
    'config'=>[
        'title' => '提现设置',
        'used' => '开关', // 开启提现
        'isAutoAudit' => '审核类型',// 是否需要审核 0 手动审核  1 自动审核
        'isAutoTransfer' => '转账类型',// 是否自动转账 0 手动转账  1 自动转账
        'rate' => '手续费比率', // 提现手续费比率 (0-100)
        'min' => '最低提现金额', // 单次最低提现
        'max' => '最高提现金额', // 单次最高提现
        'rule' => '周期规则'
    ]
];