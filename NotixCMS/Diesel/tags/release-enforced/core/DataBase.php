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

    public function numRows ( $queryId );

    public function affectedRows ();

    public function numFields ( $queryId );

    public function fieldName ( $offset, $queryId );

    public function fieldType ( $offset, $queryId );

    public function fetchRow ( $queryId );

    public function fetchRowSet ( $queryId );

    public function fetchObject ( $queryId, $className );

    public function fetchObjectSet ( $queryId, $className );

    public function fetchField ( $field, $rowNum, $queryId );

    public function rowSeek ( $rowNum, $queryId = 0 );

    public function nextId ();

    public function freeResult ( $queryId );
}
