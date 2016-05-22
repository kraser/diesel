<?php

class SqlTools
{

    /**
     * Возвращает индексированный по полю $field массив объектов типа $className или StdClass, если $className = null
     * @param String $query текст запроса
     * @param String $className имя класса возвращаемых объектов
     * @param String $field Поле по которому индексируется массив
     * @return Array
     */
    public static function selectObjects ( $query, $className = null, $field = '' )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        $result = $db->sqlQuery ( $query );

        return $db->fetchObjectSet ( $result, $className, $field );
    }

    /**
     * Возвращает объект типа $className или StdClass, если $className = null
     * @param String $query текст запроса
     * @param String $className имя класса возвращаемых объектов
     * @return Object
     */
    public static function selectObject ( $query, $className = null )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        $result = $db->sqlQuery ( $query );

        return $db->fetchObject ( $result, $className );
    }

    /**
     * Возвращает индексированный по полю $key массив строк
     * @param String $query текст запроса
     * @param Integer $type тип возвращаемых строк
     * @param String $key поле для индексации
     * @return Array
     */
    public static function selectRows ( $query, $type = MYSQL_BOTH, $key = "" )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        $result = $db->sqlQuery ( $query );

        return $db->fetchRowSet ( $result, $type, $key );
    }

    /**
     * Возвращает индексированный по полю $key массив строку
     * @param String $query текст запроса
     * @param Integer $type тип возвращаемых строк
     * @param String $key поле для индексации
     * @return Array
     */
    public static function selectRow ( $query, $type = MYSQL_BOTH )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        $result = $db->sqlQuery ( $query );

        return $db->fetchRow ( $result, $type );
    }

    /**
     * Возвращает значение
     * @param String $query текст запроса
     * @return String
     */
    public static function selectValue ( $query )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        $db->sqlQuery ( $query );

        return $db->fetchField ();
    }

    /**
     * Выполняет запрос и возвращает true в случае успеха или false в противном случаен
     * @param string $query текст запроса
     * @return Boolean
     */
    public static function execute ( $query )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        return $db->sqlQuery ( $query );
    }

    /**
     * Выполняет INSERT возвращает id новой записи
     * @param string $query текст запроса
     * @return Integer
     */
    public static function insert ( $query )
    {
        $db = DataBase::getInstance ();
        $query = self::debug ( 1 ) . $query;
        $db->sqlQuery ( $query );

        return mysql_insert_id ();
    }

    /** Добавляет в виде комментария в начало SQL-запроса информацию о том,
     *  из какого файла:строки был сделан этот запрос
     *
     * @param $level    Глубина вложенности вызовов до прикладного кода
     */
    public static function debug ( $level = 0, $commentMark = '#' )
    {
        $stack = debug_backtrace ();
        if ( $stack )
        {
            $file = basename ( $stack[$level]['file'] );
            $line = basename ( $stack[$level]['line'] );
            return $commentMark . "$file:$line" . ( isset ( $_SERVER['REQUEST_URI'] ) ? " {$_SERVER['REQUEST_URI']}" : "" ) . "\n";
        }
        else
        {
            return '';
        }

        unset ( $stack );
    }

    public static function escapeString ( $string )
    {
        $db = DataBase::getInstance ();
        return mysql_real_escape_string ( $string );
    }
}
