<?php
/**
 * Отправляет заголовок Content-Type: text/xml
 */
function xmlHeader ( $encoding = 'windows-1251' )
{
    header ( "Content-Type: text/xml; charset=" . _CHARSET );
    header ( "Cache-Control: no-cache" );
    echo "<?xml version='1.0' encoding='" . _CHARSET . "'?>";
}

/**
 * Отправляет заголовок Content-type: application/json
 */
function jsonHeader ()
{
    header ( 'Cache-Control: no-cache, must-revalidate' );
    header ( 'Content-type: application/json; charset=' . _CHARSET );
}

/**
 * Отправляет данные в формате JSON
 * @param Mixed $data отправляемые данные
 */
function sendJSON ( $data )
{
    if ( !is_array ( $data ) )
    {
        return false;
    }

    jsonHeader ();
    echo json_encode ( $data );
}

/**
 * Отправляет заголовок Content-Type: text/html
 */
function htmlHeader ()
{
    header ( 'Content-Type: text/html; charset=' . _CHARSET );
    header ( 'Cache-Control: no-cache' );
}

/**
 * <pre>Форматирует цену согласно установкам в базе</pre>
 * @param Float $price <p>Форматируемая цена</p>
 * @param Integer $decimals <p>Число десятичных знаков или null для применения параметров нстройки</p>
 * @param String $pointSep <p>Десятичный разделитель</p>
 * @param String $thousandSep <p>Разделитель разрядов</p>
 * @return String <p>Строковое представление отформатированного числа</p>
 */
function priceFormat ( $price, $decimals = null, $pointSep = ',', $thousandSep = ' ' )
{
    $decimals = !is_numeric ( $decimals ) ? Tools::getSettings ( 'Catalog', 'price_round', 0 ) : ( int ) $decimals;
    $retVal = number_format ( $price, $decimals, $pointSep, $thousandSep );

//    if ( $thousandSep === ' ' )
//    {
//        $retVal = str_replace ( ' ', "&nbsp;", $retVal );
//    }

    return $retVal;
}
