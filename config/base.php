<?php
$config =
[
    'title' => 'Diesel',
    'theme' => 'startUp',
    'adninMail' => '',
    'adminPhone' => '',

    "basePath" => __DIR__ . DS . "..",
    'modulePath' => 'components',
    'db' =>
    [
        'dbType' => 'mysql',
        'dbPort' => 3306,
        'dbHost' => 'localhost',
        'dbUser' => 'root',
        'dbPassword' => 'root',
        'dbName' => 'dbName',
        'dbPrefix' => 'ofr_',
        'set_names_utf8' => true,
        'show_query_devmode' => false

    ],

];

$config['jabber'] = array(
    'host'     => 'talk.google.com',
    'port'     => 5222,
    'user'     => 'example@gmail.com',
    'password' => 'mysecretpass',
    'resource' => 'xmpphp',
    'server'   => 'gmail.com'
);

$config['services'] = array
(
    'host' => '',
    'login' => '',
    'password' => ''
);

$config['folders'] =
[
    'SMARTY_TEMPLATES' => 'cache/_template',
    'SMARTY_CACHE'     => 'cache/_cache'
];

return $config;
