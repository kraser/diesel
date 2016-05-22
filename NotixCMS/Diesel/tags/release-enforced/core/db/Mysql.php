<?php
/**
 * @todo добавить метод fetchFields
 */
class Mysql implements DbInterface
{
    protected static $instance;
    private $dbPrefix;
    private $dbConnectId;
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

    public function __construct ( $config, $persistency = false )
    {
        $dbUser = $config["dbUser"];
        $dbPassword = $config["dbPassword"];
        $dbHost = $config["dbHost"];
        $dbName = $config["dbName"];
        $this->dbPrefix = $config["dbPrefix"];

        if ( $persistency )
            $this->dbConnectId = mysql_pconnect ( $dbHost, $dbUser, $dbPassword ) or $this->fireEvent ( mysql_error (), 2 );
        else
            $this->dbConnectId = mysql_connect ( $dbHost, $dbUser, $dbPassword ) or $this->fireEvent ( mysql_error (), 2 );

        if ( $this->dbConnectId )
        {
            $dbselect = mysql_select_db ( $dbName ) or $this->fireEvent ( mysql_error (), 2 );
            if ( !$dbselect )
            {
                mysql_close ( $this->dbConnectId );
                $this->dbConnectId = $dbselect;
            }

            mysql_query ( "SET character set 'UTF8'" ) or $this->fireEvent ( mysql_error (), 2 );
            mysql_query ( "SET names 'UTF8'" ) or $this->fireEvent ( mysql_error (), 2 );
        }
    }

    public static function init ()
    {
        if ( self::$instance === null )
            self::$instance = new self();
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
        if ( $this->dbConnectId )
        {
            if ( $this->queryResult )
                mysql_free_result ( $this->queryResult );

            $result = mysql_close ( $this->dbConnectId );

            return $result;
        }
        else
            return false;
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
        // Remove any pre-existing queries

        // Remove any pre-existing queries
        unset ( $this->row );
        unset ( $this->rowSet );
        unset ( $this->object );
        unset ( $this->objectSet );
        unset ( $this->queryResult );

        $this->query = $query;
        if ( $query != "" )
            $this->queryResult = mysql_query ( $query, $this->dbConnectId ) or $this->fireEvent ( mysql_error (), 2 );

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
     * @param Resource $query_id
     * @return Integer
     */
    function numRows ( $query_id = 0 )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            $result = mysql_num_rows ( $query_id );
            return $result;
        }
        else
            return false;
    }

    function affectedRows ()
    {
        if ( $this->dbConnectId )
        {
            $result = mysql_affected_rows ( $this->dbConnectId );
            return $result;
        }
        else
            return false;
    }

    function numFields ( $query_id = 0 )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            $result = mysql_num_fields ( $query_id );
            return $result;
        }
        else
            return false;
    }

    function fieldName ( $offset, $query_id = 0 )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            $result = mysql_field_name ( $query_id, $offset );
            return $result;
        }
        else
            return false;
    }

    function fieldType ( $offset, $query_id = 0 )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            $result = mysql_field_type ( $query_id, $offset );
            return $result;
        }
        else
            return false;
    }

    function fetchRow ( $query_id = 0, $resultType = MYSQL_BOTH )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            $this->row = mysql_fetch_array ( $query_id, $resultType );
            return $this->row;
        }
        else
            return false;
    }

    function fetchRowSet ( $query_id = 0, $resultType = MYSQL_BOTH, $key = "" )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            unset ( $this->rowSet );
            unset ( $this->row );
            $result = array ();
            while ( $this->rowSet = mysql_fetch_array ( $query_id, $resultType ) )
            {
                $row = $this->rowSet;
                if ( $key )
                    $result[$row[$key]] = $row;
                else
                    $result[] = $row;
            }

            return $result;
        }
        else
            return false;
    }

    function fetchObject ( $query_id = 0, $class_name = null )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            if ( $class_name )
                $this->object = mysql_fetch_object ( $query_id, $class_name );
            else
                $this->object = mysql_fetch_object ( $query_id );

            return $this->object;
        }
        else
            return false;
    }

    function fetchObjectSet ( $query_id = 0, $class_name = null, $field = "" )
    {
        $result = array ();
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            if ( $class_name )
            {
                while ( $object = mysql_fetch_object ( $query_id, $class_name ) )
                {
                    if ( $field )
                        $result[$object->$field] = $object;
                    else
                        $result[] = $object;
                }
            }
            else
            {
                while ( $object = mysql_fetch_object ( $query_id ) )
                {
                    if ( $field )
                        $result[$object->$field] = $object;
                    else
                        $result[] = $object;
                }
            }

            return $result;
        }
        else
            return false;
    }

    function fetchField ( $field = 0, $rownum = -1, $query_id = 0 )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            if ( $rownum > -1 )
                $result = mysql_result ( $query_id, $rownum, $field );
            else
            {
                if ( empty ( $this->row ) && empty ( $this->rowset ) )
                {
                    if ( $this->fetchRow () )
                        $result = $this->row[$field];
                    else
                        $result = null;
                }
                else
                {
                    if ( $this->rowSet )
                        $result = $this->rowSet[$field];
                    else if ( $this->row )
                        $result = $this->row[$field];
                }
            }

            return $result;
        }
        else
            return false;
    }

    function rowSeek ( $rownum, $query_id = 0 )
    {
        if ( !$query_id )
            $query_id = $this->queryResult;

        if ( $query_id )
        {
            $result = mysql_data_seek ( $query_id, $rownum );
            return $result;
        }
        else
            return false;
    }

    function nextId ()
    {
        if ( $this->dbConnectId )
        {
            $result = mysql_insert_id ( $this->dbConnectId );
            return $result;
        }
        else
            return false;
    }

    function freeResult ( $query_id = 0 )
    {
        if ( !$query_id )
        {
            $query_id = $this->queryResult;
        }

        if ( $query_id )
        {
            unset ( $this->row );
            unset ( $this->rowSet );
            unset ( $this->object );
            unset ( $this->objectSet );
            mysql_free_result ( $query_id );

            return true;
        }
        else
            return false;
    }

    function sqlError ( /* $query_id = 0 */ )
    {
        $result["message"] = mysql_error ( $this->dbConnectId );
        $result["code"] = mysql_errno ( $this->dbConnectId );

        return $result;
    }

    function fireEvent ( $event, $trigger )
    {
        if ( $trigger )
        {
            $errorNumber = $this->dbConnectId ? mysql_errno ( $this->dbConnectId ) : "";
            $sql = $this->query ? " - \"" . $this->query . "\"" : "";
            $msg = "Ошибка:" . $errorNumber . " " . $event . $sql . " " . Tools::getBackTraceLine ( 2 );
            throw new CmsException ( $msg /*$errorNumber new Message( $msg, "EXIT" ) */ );
        }
    }
}
