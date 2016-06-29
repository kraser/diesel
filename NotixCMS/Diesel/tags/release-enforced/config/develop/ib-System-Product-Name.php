<?php
$config =
[
    'title' => 'Новый проект',
    'theme' => 'project',
    'adninMail' => '',
    'adminPhone' => '',
    'db' =>
    [
        'dbType' => 'mysql',
        'dbPort' => 3306,
        'dbHost' => 'localhost',
        'dbUser' => 'root',
        'dbPassword' => '111',
        'dbName' => 'project',
        'dbPrefix' => 'mce_',
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
        //'Blog',
        'Gallery'
    ],
    'develop' => true
];

return $config;
