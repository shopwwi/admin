<?php

return [
    'projectName' => '短信验证',
    'list' => '短信验证列表',
    'create' => '新增短信验证',
    'update' => '编辑短信验证',
    'delete' => '删除短信验证',
    'show' => '短信验证详情',
    'back' => '返回短信验证列表',
    'confirm_delete' => '确定删除所选短信验证吗？',
    'field' => [
        'id' => '',
        'auth_code' => '短信动态码',
        'content' => '短信内容',
        'ip' => '请求IP',
        'mobile_phone' => '接收手机号',
        'send_type' => '短信类型',
        'status' => '使用状态',
    ],
    'config' => [
        'title' => '短信设置',
        'used'=> '状态',
        'timeout' => '超时',
        'gateways'=> '网关',
        "authCodeVerifyTime" => '有效期',
        "authCodeResendTime" => '发送间隔',
        "authCodeSameIpResendTime" => '同IP间隔',
        "authCodeSameMaxNum" => '最大发送',
        "authCodeSameIpMaxNum" => '同IP最大'
    ]
];