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
        'dbUser' => 'root',
        'dbPassword' => 'root',
        'dbName' => 'sibhl',
        'dbPrefix' => 'ofr_',
        'set_names_utf8' => true,
        'show_query_devmode' => false
    ],
    'adninMail' => '',
    'adminPhone' => '',
    'modules' =>
    [
        'FileLoader',
//        //'Catalog',
        //'Header',
//        'Slider',
//        'News',
//        //'Basket',
        'Menu'

    ],
    'develop' => true
];

return $config;
