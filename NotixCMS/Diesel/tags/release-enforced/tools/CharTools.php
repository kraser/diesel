<?php
/**
 * Description of CharTools
 *
 * @author kraser
 */
class CharTools
{

    public static function upperFirst ( $string )
    {
        return mb_strtoupper ( mb_substr ( $string, 0, 1 ) ) . mb_substr ( $string, 1 );
    }

    public static function strcasecmp ( $a, $b )
    {
        return strcmp ( mb_strtolower ( $a ), mb_strtolower ( $b ) );
    }
}