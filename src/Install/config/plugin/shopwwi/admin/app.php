<?php

return [
    'enable' => true,
    'cross' => [
        'origin' => '*',
        'methods' => 'GET,POST,PUT,DELETE,OPTIONS',
        'headers' => 'Content-Type,Authorization,X-Requested-With,Accept,Origin'
    ],
    'prefix' => [
        'admin' => '/admin',
        'user' => '/user'
    ],
    'GRADE_RULE' => [
        ['type'=>'consume','desc'=>'消费满xx元','used'=>'0', 'num'=> 0],
        ['type'=>'orders','desc'=>'订单量','used'=>'0', 'num'=> 0],
        ['type'=>'recharge','desc'=>'累计充值','used'=>'0', 'num'=> 0],
        ['type'=>'invite','desc'=>'邀请人数','used'=>'0', 'num'=> 0],
        ['type'=>'growth','desc'=>'成长值','used'=>'0', 'num'=> 0],
        ['type'=>'points','desc'=>'积分累计','used'=>'0', 'num'=> 0],
    ],
    'sensitive_int' => ['(', ')', ',', '，', ';', '；', '。','*','）','（','-','_','=','+','&','^','…','%','$','￥','#','@','!','！'], // 敏感词干扰因子
    'http' => [
        /**
         * 日志相关
         */
        'log' => [
            /**
             * 日志是否启用，建议启用
             */
            'enable' => false,
            /**
             * 日志的 channel
             */
            'channel' => 'default',
            /**
             * 日志的级别
             */
            'level' => 'info',
            /**
             * 日志格式
             * 启用 custom 时无实际作用
             * @link \GuzzleHttp\MessageFormatter::format()
             */
            'format' => \GuzzleHttp\MessageFormatter::CLF,
            /**
             * 自定义日志
             * 建议使用 CustomLogInterface 形式，支持慢请求、请求时长、更多配置
             */
            'custom' => function (array $config) {
                $config = [
                    'log_channel' => $config['channel'],
                ];
                return new \Shopwwi\Admin\Libraries\Log\CustomLog($config);
            }
        ],
        /**
         * guzzle 全局的 options
         */
        'guzzle' => [
            'debug' => false,
            'timeout' => 10,
        ],
        /**
         * 扩展 Http 功能，一般可用于快速定义 api 信息
         */
        'macros' => [
        ]
    ]
];