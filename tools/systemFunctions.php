<?php

/**
 * Берем функции из админки (тоже очень полезные)
 */
require_once DOCROOT . '/admin/lib/sysfunctions.php';


/**
 * Вывод ошибки
 * @param string $error
 */
function error ( $error )
{
    if ( !Starter::app ()->develop )
    {
        return false;
    }
    $array_debug = debug_backtrace ();
    if ( !empty ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
    {
        echo $array_debug[0]['file'] . ': ' . $array_debug[0]['line'] . "\r\n" . $error;
    }
    else
    {
        echo '<div style="background:white; color:black display:block;font-family:Trebuchet MS,Arial,sans-serif;">
            <h1 style="color:#0498EC;font-size:1.5em;">Ошибка</h1>
                <ul style="padding: 0em 1em 1em 1.55em;font-size:0.8em;margin:0 0 0 2.5em;">
                    <li>Файл <code style="font-weight:bold;">' . $array_debug[0]['file'] . '</code> на строке: <code style="font-weight:bold;">' . $array_debug[0]['line'] . '</code> говорит что:</li>
                    <li>' . $error . '</li>
                </ul>
            </div>';
    }
}
