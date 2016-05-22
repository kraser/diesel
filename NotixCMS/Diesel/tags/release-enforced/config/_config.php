<?php
$config =
[
    'title' => 'Сибирская хоккейная лига',
    'theme' => 'sibhl',
    'adninMail' => '',
    'adminPhone' => '',
    'db' =>
    [
        'dbType' => 'mysql',
        'dbPort' => 3306,
        'dbHost' => 'localhost',
        'dbUser' => 'notix-test14',
        'dbPassword' => '3o6rlvGY',
        'dbName' => 'notix-test14',
        'dbPrefix' => 'ofr_',
        'set_names_utf8' => true,
        'show_query_devmode' => false
    ],
    'modules' =>
    [
        'Content',
        //'Catalog',
        'Header',
        'Slider',
        'News',
        //'Basket'
    ]
];

return $config;