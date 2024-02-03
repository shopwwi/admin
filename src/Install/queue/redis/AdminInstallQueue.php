<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class AdminInstallQueue implements Consumer
{
    // 要消费的队列名
    public $queue = 'admin-install';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    public function consume($data)
    {
        \Shopwwi\Admin\Install\InstallService::Seeders();
    }
}