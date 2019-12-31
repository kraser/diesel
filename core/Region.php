<?php

/**
 * Класс реализующий режим Region
 */
class Region
{
    private static $instance;
    private static $default_region = 1;
    private static $regions = array ();

    /**
     * Конструктор режима Region
     */
    public function __construct ()
    {

    }

    /**
     * Возвращает синглтон класса User
     * @return User
     */
    public static function &getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function Init ()
    {
        $instance = self::getInstance ();
        self::Run ();
    }

    /**
     * Запуск режима Region
     * @return void
     * @throws Exception
     */
    public static function Run ()
    {
        $sql = "SELECT * FROM `prefix_regions` WHERE `show` = 'Y' AND `deleted` = 'N'";
        self::$regions = SqlTools::selectObjects ( $sql, '', 'id' );
        if ( empty ( self::$regions ) )
        {
            $foundRegion = null;
            $_SESSION['region_id'] = null;
            self::$default_region = null;
        }
        elseif ( count ( self::$regions ) == 1 )
        {
            $region = array_shift ( self::$regions );
            $foundRegion = $region->id;
            $_SESSION['region_id'] = $region->id;
            self::$default_region = $region->id;
        }

        if ( isset ( $_REQUEST['region_id'] ) )
        {
            $region_id = ( int ) $_REQUEST['region_id'];
            if ( !isset ( self::$regions[$region_id] ) )
            {
                $_SESSION['region_id'] = self::$default_region;
                $foundRegion = self::$default_region;
            }
            else
            {
                $_SESSION['region_id'] = $region_id;
                $foundRegion = $region_id;
            }
        }
        else
        {
            if ( !isset ( $_SESSION['region_id'] ) )
            {
                $_SESSION['region_id'] = self::$default_region;
            }
            $foundRegion = $_SESSION['region_id'];
        }
        define ( "_REGION", $foundRegion );
    }

    public function regionsBar ()
    {
        return TemplateEngine::view ( 'parts/regions', array (
                'regions' => self::$regions,
                'region_id' => $_SESSION['region_id'],
        ) );
    }
}
