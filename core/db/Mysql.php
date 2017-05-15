<?php
/**
 * @todo добавить метод fetchFields
 */
class Mysql implements DbInterface
{
    protected static $instance;
    private $dbPrefix;
    private $dbConnect;
    private $queryResult;
    private $row = array ();
    private $rowSet = array ();
    private $object = array ();
    private $objectSet = array ();
    private $numQueries = 0;
    private $textQueries = array ();
    private $query;
    private $events;
    private $eventMsg;

    public function __construct ( $config )
    {
        $dbUser = $config["dbUser"];
        $dbPassword = $config["dbPassword"];
        $dbHost = $config["dbHost"];
        $dbName = $config["dbName"];
        $this->dbPrefix = $config["dbPrefix"];

        $this->dbConnect = new mysqli ( $dbHost, $dbUser, $dbPassword, $dbName );
        if ( $this->dbConnect->connect_error )
            $this->fireEvent ( $this->dbConnect->connect_error, 2 );
//        else
//            $this->dbConnectId = mysql_connect ( $dbHost, $dbUser, $dbPassword ) or $this->fireEvent ( mysql_error (), 2 );

//        if ( $this->dbConnectId )
//        {
//            $dbselect = mysql_select_db ( $dbName ) or $this->fireEvent ( mysql_error (), 2 );
//            if ( !$dbselect )
//            {
//                mysql_close ( $this->dbConnectId );
//                $this->dbConnectId = $dbselect;
//            }

            $this->dbConnect->query ( "SET character set 'UTF8'" ) or $this->fireEvent ( mysql_error (), 2 );
            $this->dbConnect->query ( "SET names 'UTF8'" ) or $this->fireEvent ( mysql_error (), 2 );
//        }
    }

    public static function init ()
    {
        if ( self::$instance === null )
            self::$instance = new self();
    }

    public static function isInited()
    {
        return !empty(self::$instance);
    }

    public static function &getInstance ()
    {
        if ( self::$instance === null )
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * <pre>Закрывает соединение с БД</pre>
     * @return Boolean
     */
    function sqlClose ()
    {
        if ( $this->queryResult )
            $this->queryResult->free ();

        $this->queryResult->close ();
    }

    /**
     * <pre>Выполнение SQL-запроса<pre>
     * @param String $query
     * @param Boolean $transaction
     * @return Booleean
     */
    function sqlQuery ( $query = "", $transaction = FALSE )
    {
        $query = str_replace ( 'prefix_', $this->dbPrefix, $query );
        $this->query = $query;
        if ( $query != "" )
            $this->queryResult = $this->dbConnect->query ( $query ) or $this->fireEvent ( $this->dbConnect->error, 2 );

        if ( $this->queryResult )
        {
            //Подсчет количества запросов и запись самих запросов
            $this->numQueries += 1; // Количество запросов
            $this->textQueries[] = $query; // Тексты запросов

            return $this->queryResult;
        }
        else
            return ( $transaction == END_TRANSACTION ) ? true : false;
    }

    /**
     * <pre>Возвращает кол-во записей</pre>
     * @param Resource $queryId
     * @return Integer
     */
    function numRows ()
    {
        $result = $this->queryResult->num_rows;
        return $result;
    }

    function affectedRows ()
    {
        $result = $this->dbConnect->affected_rows ();
        return $result;
    }

    function numFields ()
    {
        $result = $this->queryResult->field_count;
        return $result;
    }

    function fieldName ( $offset )
    {
        $field = $this->queryResult->fetch_field_direct ( $offset );
        $result = $field->name;
        return $result;
    }

    function fieldType ( $offset )
    {
        $field = $this->queryResult->fetch_field_direct ( $offset );
        $result = $field->type;
        return $result;
    }

    function fetchRow ( $resultType = MYSQLI_BOTH )
    {
        return $this->queryResult->fetch_array ( $resultType );
    }

    function fetchRowSet ( $resultType = MYSQLI_BOTH, $key = "" )
    {
        $result = [];
        while ( $row = $this->queryResult->fetch_array ( $resultType ) )
        {
            if ( $key )
                $result[$row[$key]] = $row;
            else
                $result[] = $row;
        }

        return $result;
    }

    function fetchObject ( $className = null )
    {
        $class = $className ? : "stdClass";
        $object = $this->queryResult->fetch_object ( $class );
        return $object;
    }

    function fetchObjectSet ( $className = 'stdClass', $field = "" )
    {
        $class = $className ? : "stdClass";
        $result = [];
        while ( $object = $this->queryResult->fetch_object ( $class ) )
        {
            if ( $field )
                $result[$object->$field] = $object;
            else
                $result[] = $object;
        }

        return $result;
    }

    function fetchField ( $field = 0, $rownum = -1 )
    {
        if ( $rownum > -1 )
            $this->queryResult->data_seek ( $rownum );

        $row = $this->fetchRow ();
        $result = $row[$field];

        return $result;
    }

    function fetchFieldSet ( $field = 0 )
    {
        $result = [];
        while ( $row = $this->queryResult->fetch_array () )
        {
            $result[] = $row[$field];
        }

        return $result;
    }

    function insertId ()
    {
        $result = $this->dbConnect->insert_id;
        return $result;
    }

    public function escapeString ( $string )
    {
        return $this->dbConnect->real_escape_string ( $string );
    }

    function freeResult ()
    {
        $this->queryResult->free ();
    }

    function sqlError ( /* $query_id = 0 */ )
    {
        $result["message"] = $this->dbConnect->error;
        $result["code"] = $this->dbConnect->errno;

        return $result;
    }

    function fireEvent ( $event, $trigger )
    {
        if ( $trigger )
        {
            $errorNumber = $this->dbConnect ? $this->dbConnect->errno : "";
            $sql = $this->query ? " - \"" . $this->query . "\"" : "";
            $msg = "Ошибка:" . $errorNumber . " " . $event . $sql . " " . Tools::getBackTraceLine ( 2 );
            throw new CmsException ( $msg /*$errorNumber new Message( $msg, "EXIT" ) */ );
        }
    }
}
