<?php

$db = parse_ini_file(__DIR__ . '/../.env');

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host={$db['DB_HOST']};dbname={$db['DB_NAME']}",
    'username' => $db['DB_USERNAME'],
    'password' => $db['DB_PASSWORD'],
    'charset' => $db['DB_CHARSET'],
];