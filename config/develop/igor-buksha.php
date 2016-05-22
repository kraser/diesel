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
        'dbPassword' => '111',
        'dbName' => 'shl',
        'dbPrefix' => 'ofr_',
        'set_names_utf8' => true,
        'show_query_devmode' => false
    ],
    'modules' =>
    [
        'Content',
        'Catalog',
        'Header',
        'Slider',
        'News',
        'Basket',
        'Blog',
        'Gallery'
    ],
    'develop' => true
];

return $config;
