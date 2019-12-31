<?php

//Больше памяти крону
ini_set ( 'memory_limit', '128M' );

define ( 'DOCROOT', __DIR__ );

require DOCROOT . '/config.php';

//Временная зона
//Кодировка и сжатие
header ( "Content-Type: text/html; charset=utf-8" );
setlocale ( LC_ALL, "ru_RU.UTF8" );
setlocale ( LC_NUMERIC, "en_US.utf8" );
mb_internal_encoding ( "UTF-8" );

//Загрузка библиотек админки
$lib = scandir ( DOCROOT . '/admin/lib', 1 );
foreach ( $lib as $file )
{
    if ( substr ( $file, -3, 3 ) == 'php' )
        include DOCROOT . '/admin/lib/' . $file;
}

//Библиотеки с сайтовой части
include_once DOCROOT . '/system/lib/additionalFunctions.php';
include_once DOCROOT . '/system/lib/media.php';

$CronJobs = new CronJobs();
