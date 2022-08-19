<?php

declare(strict_types=1);
/**
 */
return [
    'read' => 'default',//读redis使用该连接，写则为其他所有，如未找到则默认使用第一个配置并host=127.0.0.1
    'redis_prefix' => 'jc_trunk_',
    'config' => [
        'default' => [
            'host'   => '192.168.1.154',
            'auth'   => 'a35bv4dc534nf45c44v3b44v65f5ff1h',
            'port'   => 6379,
            'db'     => 0,
            'prefix' => 'jc_trunk_',
            'pool'   => [
                'size' => 64,
            ],
        ],
        '1' => [
            'host'   => '192.168.1.154',
            'auth'   => 'a35bv4dc534nf45c44v3b44v65f5ff1h',
            'port'   => 6379,
            'db'     => 0,
            'prefix' => 'jc_trunk_',
            'pool'   => [
                'size' => 64,
            ],
        ],
    ]
];
