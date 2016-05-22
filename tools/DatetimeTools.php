<?php
/**
 * Description of DatetimeTools
 *
 * @author kraser
 */
class DatetimeTools
{
    private static $monthesIn = array ( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
    private static $monthes = [ 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь' ];
    /**
     * <pre>Возвращает строку с правильным склонением месяца</pre>
     * @param String $dateStr
     * @return String
     */
    public static function inclinedDate ( $dateStr = null )
    {
        $date = $dateStr ? strtotime ( $dateStr ) : time ();
    //    $monthesIn = array ( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );

    //    $Year = ( int ) date ( 'Y', $date );
    //    $Month = ( int ) date ( 'm', $date );
    //    $Day = ( int ) date ( 'd', $date );
        $date = strftime ( "%d %b %Y", $date );
        return $date;//Day . ' ' . $monthesIn[$Month - 1] . ' ' . $Year;
    }

    public static function monthNum ( $monthStr )
    {
        $arrayKey = array_search ( mb_strtolower ( $monthStr ), self::$monthes );
        return is_numeric ( $arrayKey ) ? $arrayKey + 1 : false;
    }
}