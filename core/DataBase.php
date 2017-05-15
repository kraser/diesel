<?php

/**
 * <pre>Фабрика </pre>
 */
class DataBase
{
    public static $instance;

    private function __construct ()
    {
        Starter::import ( "db.*" );
    }

    public static function &getInstance ()
    {
        if ( self::$instance === null )
            self::init ();

        return self::$instance;
    }

    private static function init ()
    {
        if ( self::$instance === null )
        {
            Starter::import ( "core.db.*" );
            $dbConf = Starter::app ()->db;
            $dbType = ucfirst ( strtolower ( $dbConf["dbType"] ) );
            self::$instance = new $dbType ( $dbConf );
        }
    }
}

interface DbInterface
{

    public static function init ();

    public static function &getInstance ();

    public function sqlClose ();

    public function sqlQuery ( $query, $transaction );

    public function numRows ();

    public function affectedRows ();

    public function numFields ();

    public function fieldName ( $offset );

    public function fieldType ( $offset );

    public function fetchRow ( $type );

    public function fetchRowSet ( $type, $key );

    public function fetchObject ( $className );

    public function fetchObjectSet ( $className, $key );

    public function fetchField ( $field, $rowNum );

    public function insertId ();

    public function freeResult ();
}
