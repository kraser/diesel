<?php

class Configuration
{
    private static $instance;
    private static $config;
    private static $settings;

    private function __construct ()
    {
        /*
          if ( file_exists( '/etc/site.ini' ) )
          {
          $config = parse_ini_file( '/etc/site.ini', true );
          $GLOBALS['config'] = $config;
          }
          else if(file_exists(DOCROOT . "/config.php"))
          {
          require_once( DOCROOT . "/config.php" );
          }
         */
        if ( array_key_exists ( "config", $GLOBALS ) )
        {
            self::$config = $GLOBALS['config'];
        }
        else
        { // здесь необходима инсталляция каких-то недостающих файлов
            throw new Exception ( "Необходима установка конфигурации...." );
        }

        try
        {
            $GLOBALS['head_add'] = '';
            $GLOBALS["modulesSettings"] = array ();
            //Переопределяем конфиг

            Sterter::app ()->title = Tools::getSettings ( 'Blocks', 'site_title', Sterter::app ()->title );
            $GLOBALS['config']['site']['admin_mail'] = Tools::getSettings ( 'Blocks', 'admin_mail', $GLOBALS['config']['site']['admin_mail'] );
            $GLOBALS['config']['site']['theme'] = Tools::getSettings ( 'Blocks', 'site_theme', $GLOBALS['config']['site']['theme'] );
            self::$config["theme"] = $GLOBALS['config']['site']['theme'];

            //----необходимые проверочно-дозагрузочные действия, вроде постоянной инсталляции----------//
            // обновление базы, если найдены новые - исполняем дополнительные файлы обновления - изменится и версия
            $db = $GLOBALS['config']['db']['dbName'];
            if ( !Install::isNoAnyTables ( $db ) )
            {
                Install::updateDb (); //addUpdatesDB($db);
            }

            //создание корня хранилища картинок, если нет
            if ( !file_exists ( DOCROOT . DS . IMGS ) )
            {
                mkdir ( DOCROOT . DS . IMGS, 0777, true );
            }
        }
        catch ( Exception $e )
        {
            if ( $e->getCode () && ($e->getCode () == 1102 || $e->getCode () == 1146) )
            {
                echo "<p>Необходимо инсталлировать базу данных.</p>";
                echo $e->getMessage () . "<br>";
                echo $e->getFile () . ":" . $e->getLine () . "<br>";
            }
            //Обработка исключения
        }
    }

    public static function &getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public static function init ()
    {
        self::getInstance ();
    }

    public static function getConfigParam ( $paramName, $defaultValue = null )
    {
        return array_key_exists ( $paramName, self::$config ) ? self::$config[$paramName] : $defaultValue;
    }
}
