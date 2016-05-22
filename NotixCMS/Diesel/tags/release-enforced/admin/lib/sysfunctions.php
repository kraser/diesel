<?php

/**
 * Время выполения части скрипта
 *
 * @param $partname string <p>Имя засекаемого блока</p>
 * @param $stop bool Окончание измерения
 * @return void
 */
function timegen ( $partname = '', $stop = false )
{
    if ( !Starter::app ()->develop )
        return false;

    if ( !isset ( $GLOBALS['timegen'] ) )
        $GLOBALS['timegen'] = array ();
    if ( !isset ( $GLOBALS['timegen'][$partname] ) )
        $GLOBALS['timegen'][$partname] = array ();

    if ( !$stop )
    {
        $GLOBALS['timegen'][$partname]['start'] = microtime ( true );
        return true;
    }
    else
    {
        $GLOBALS['timegen'][$partname]['time'] = microtime ( true ) - $GLOBALS['timegen'][$partname]['start'];
    }

    if ( !isset ( $GLOBALS['timegen'][$partname]['result'] ) )
        $GLOBALS['timegen'][$partname]['result'] = 0;
    $GLOBALS['timegen'][$partname]['result'] += $GLOBALS['timegen'][$partname]['time'];

    return $GLOBALS['timegen'][$partname]['time'];
}

/**
 * Возвращает максимальный размер для загружаемых файлов
 *
 * @param $bytes boolean Если да, то вернет в байтах
 * @return void
 */
function get_max_filesize ( $bytes = false )
{
    $sizes = array (
        str_to_bytes ( ini_get ( 'memory_limit' ) ),
        str_to_bytes ( ini_get ( 'post_max_size' ) ),
        str_to_bytes ( ini_get ( 'upload_max_filesize' ) )
    );
    $maxfile = min ( $sizes );

    if ( !$bytes )
        $ret = bytes_to_str ( $maxfile );
    else
        $ret = $maxfile;

    return $ret;
}

function bytes_to_str ( $bytes )
{
    $d = '';
    if ( $bytes >= 1048576 )
    {
        $num = $bytes / 1048576;
        $d = 'Mb';
    }
    elseif ( $bytes >= 1024 )
    {
        $num = $bytes / 1024;
        $d = 'kb';
    }
    else
    {
        $num = $bytes;
        $d = 'b';
    }

    return number_format ( $num, 2, ',', ' ' ) . $d;
}

function str_to_bytes ( $value )
{
    $val = trim ( str_replace ( ',', '.', $value ) );
    $last = strtolower ( $val[strlen ( $val ) - 1] );
    switch ( $last )
    {
        // 'G' модификатор доступен начиная с PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * Перевод фразы через Google Translate
 *
 * @param $ru_str string Фраза по-русски
 * @return string
 */
function translate ( $ru_str )
{
    $curlHandle = curl_init (); // init curl
    // options
    $postData = array ();
    $postData['client'] = 't';
    $postData['text'] = $ru_str;
    $postData['hl'] = 'en';
    $postData['sl'] = 'ru';
    $postData['tl'] = 'en';
    curl_setopt ( $curlHandle, CURLOPT_URL, 'http://translate.google.com/translate_a/t' ); // set the url to fetch
    curl_setopt ( $curlHandle, CURLOPT_HTTPHEADER, array (
        'User-Agent: Mozilla/5.0 (X11; U; Linux i686; ru; rv:1.9.1.4) Gecko/20091016 Firefox/3.5.4',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
        'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
        'Keep-Alive: 300',
        'Connection: keep-alive'
    ) );
    curl_setopt ( $curlHandle, CURLOPT_HEADER, 0 ); // set headers (0 = no headers in result)
    curl_setopt ( $curlHandle, CURLOPT_RETURNTRANSFER, 1 ); // type of transfer (1 = to string)
    curl_setopt ( $curlHandle, CURLOPT_TIMEOUT, 10 ); // time to wait in
    curl_setopt ( $curlHandle, CURLOPT_POST, 0 );
    if ( $postData !== false )
    {
        curl_setopt ( $curlHandle, CURLOPT_POSTFIELDS, http_build_query ( $postData ) );
    }

    $response = curl_exec ( $curlHandle ); // make the call
    curl_close ( $curlHandle ); // close the connection
    $replaced = array ( ',,', ',,', ',]', '[,' );
    $replace = array ( ',"",', ',"",', ',""]', '["",' );
    $content = str_replace ( $replaced, $replace, $response );
    $result = json_decode ( $content );
    return $result[0][0][0];
}

function makeURI ( $str )
{
    $translated = translate ( $str );
    return strtolower ( preg_replace ( '/[^\w]+/i', '-', $translated ) );
}

function getVar ( $name )
{
    $vars = SqlTools::selectRows ( "SELECT * FROM `prefix_data` WHERE `key`='" . $name . "'", MYSQL_ASSOC );
    if ( count ( $vars ) == 0 )
        return false;
    $var = array_shift ( $vars );
    return $var['data'];
}

function setVar ( $name, $data )
{
    $count = count ( SqlTools::selectRows ( "SELECT * FROM `prefix_data` WHERE `key`='" . $name . "'" ) );
    if ( $count == 0 )
    {
        SqlTools::insert ( "INSERT INTO `prefix_data` (`key`,`data`) VALUES ('$name', '$data')" );
    }
    else
    {
        SqlTools::execute ( "UPDATE `prefix_data` SET `data`='$data' WHERE `key`='$name'" );
    }
    return true;
}

function giveJSON ( $data )
{
    if ( !is_array ( $data ) )
        return false;
    header ( 'Cache-Control: no-cache, must-revalidate' );
    header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
    header ( 'Content-type: application/json' );
    echo json_encode ( $data );
    exit ();
}

/**
 * Рекурсивно сканирует заданный каталог
 * @return массив строк - файлов с путями от заданного каталога,
 *          сортированный по возрастанию
 */
function scanDirRecurs ( $dir )
{
    $pathes = array ();
    $files = array ();
    $pathList = array ();

    $rows = scandir ( $dir );
    foreach ( $rows as $row )
    {
        if ( $row == '.' || $row == '..' || substr ( $row, 0, 1 ) == '.' )
        {
            continue;
        }
        $row = $dir . DS . $row;
        // каталоги для дальнейшей рекурсии
        if ( is_dir ( $row ) )
        {
            array_push ( $pathes, $row );
        }
        if ( is_file ( $row ) )
        { // здесь м.б. ещё проверка на тип файла
            // отрезаем DOCROOT
            $row = str_replace ( DOCROOT, "", $row );
            array_push ( $files, $row );
        }
    }
    // рекурсия
    foreach ( $pathes as $path )
    {
        $pathList = array_merge ( $pathList, scanDirRecurs ( $path ) );
    }
    $result = array_merge ( $files, $pathList );
    sort ( $result );
    return $result;
}
