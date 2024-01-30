<?php

return [
    'projectName' => '邮件验证',
    'list' => '邮件验证列表',
    'create' => '新增邮件验证',
    'update' => '编辑邮件验证',
    'delete' => '删除邮件验证',
    'show' => '邮件验证详情',
    'back' => '返回邮件验证列表',
    'confirm_delete' => '确定删除所选邮件验证吗？',
    'field' => [
        'id' => '',
        'auth_code' => '动态码',
        'content' => '邮件内容',
        'email' => '接收邮件',
        'ip' => '请求IP',
        'send_type' => '邮件类型',
        'status' => '使用状态',
    ],
    'config' => [
        'title' => '邮件设置',
        'used'=>'开关',
        'username'=>'邮箱账号',
        'password'=>'邮箱密码',
        'encryption'=>'加密类型',
        'port' => '端口号',
        'smtp' => 'smtp地址',
        'form_address' => '发件人邮箱',
        'form_name' => '发件人',
        "authCodeVerifyTime" => '有效期',
        "authCodeResendTime" => '发送间隔',
        "authCodeSameIpResendTime" => '同IP间隔',
        "authCodeSameMaxNum" => '最大发送',
        "authCodeSameIpMaxNum" => '同IP最大'
    ]
];