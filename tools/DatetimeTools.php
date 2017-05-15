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
    private static $quartersByRome = [ 'I', 'II', 'III', 'IV' ];
    private static $quarters = [ 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4 ];

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
        $monthNum = ( int ) date ( 'm', $date );
    //    $Day = ( int ) date ( 'd', $date );
        $date = strftime ( "%d " . self::$monthesIn[$monthNum - 1] . " %Y", $date );
        return $date;//Day . ' ' . $monthesIn[$Month - 1] . ' ' . $Year;
    }

    public static function monthNum ( $monthStr )
    {
        $arrayKey = array_search ( mb_strtolower ( $monthStr ), self::$monthes );
        return is_numeric ( $arrayKey ) ? $arrayKey + 1 : false;
    }

    public static function quarterDate ( $dateStr = null, $byRome=true, $inShort=false )
    {
        if ( stripos ( $dateStr, "кв" ) !== false)
        {
            list ($quarterNum, $dummy, $year ) = explode(" ", $dateStr);
            if(is_numeric($quarterNum) && $byRome )
            {
                $quarter = self::$quartersByRome[$quarterNum];
            }
            else
                $quarter = $quarterNum;
        }
        else
        {
            $date = $dateStr ? strtotime ( $dateStr ) : time ();
            $monthNum = ( int ) date ( 'm', $date );
            $quarter = $byRome ? self::$quartersByRome[self::$quarters[$monthNum - 1] - 1] : self::$quarters[$monthNum];
            $year = (int) date("Y", $date);
        }

        return $quarter . ( $inShort ? " кв. " : " квартал " ) . $year;
    }
}