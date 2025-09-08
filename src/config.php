<?php
global $appConfig;
$appConfig = [
    'authMethod' => 'db', //db or file
    'sessionType' => "file", //db or file
    "db" => [
        'hostname' => 'MySQL-8.2',
        'user' => 'root',
        'password' => '',
        'database' => 'lk_php'
    ]
];
