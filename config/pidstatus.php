<?php

return [
    /**
     * Defaults work for Ubuntu 16.04 feel free to change this file
     */
    'nginx' => [
        'pid' => env('NGINX_PID', '/var/run/nginx.pid'),
        'name' => 'Nginx',
    ],
    'cron' => [
        'pid' => env('CRON_PID', '/var/run/crond.pid'),
        'name' => 'Cron',
    ],
    'ssh' => [
        'pid' => env('SSH_PID', '/var/run/sshd.pid'),
        'name' => 'SSH',
    ],
    'mysql' => [
        'pid' => env('MYSQL_PID', '/var/run/mysqld/mysqld.pid'),
        'name' => 'MYSQL',
    ],
    'php' => [
        'pid' => env('PHP_PID', '/var/run/php/php7.0-fpm.pid'),
        'name' => 'PHP',
    ]
];
