<?php

declare(strict_types = 1);


use Illuminate\Redis\Connections\PhpRedisConnection;

Artisan::command('demo', function () {

    /**
     * @var PhpRedisConnection $redis
     */
    $redis = \Illuminate\Support\Facades\Redis::connection();

    $redis->setnx("c68b0525-b482-480a-984d-13929308fe17", "yeah");

//    $redis->publish('channel_2', json_encode([
//        'name' => 'Adam Wathan',
//    ]));

});
