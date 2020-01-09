<?php

return [
    'db' => [
        'driver'   => 'pdo_mysql',
        'user'     => 'homestead',
        'password' => 'secret',
        'dbname'   => 'phonebook',
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'port' => 6379
    ],
    'mode' => 'dev'
];
