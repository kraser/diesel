<?php

class SimpleData
{
    private $data = array ( );
    private $columns = array ( );
    private $static_filter = array ( );

    function __construct()
    {

    }

    /** SELECT из таблицы
     * @param type $table - имя таблицы без префикса
     * @param type $where_filter - условие в WHERE, начинать с AND
     * @param type $order_by - порядок ORDER, с ASC или DESC
     * @param type $limit - сколько записей вернуть
     * @return type
     */
    function GetData( $table, $where_filter = '', $order_by = '', $limit = '' )
    {
        $where_filter = trim( $where_filter );
        $order_by = trim( $order_by );
        $limit = trim( $limit );


        if ( isset( $this->data[$table] ) && $this->data[$table]['key'] == $table . $where_filter . $order_by . $limit )
            return $this->data[$table]['data'];

        $this->data[$table] = array ( );
        $this->data[$table]['key'] = $table . $where_filter . $order_by . $limit;
        $this->data[$table]['data'] = array ( );

        if ( isset( $this->columns[$table] ) )
            $syscolumns = $this->columns[$table];
        else
        {
            $fields = SqlTools::selectRows('SHOW COLUMNS FROM `prefix_' . $table . '`', MYSQLI_ASSOC);

            $syscolumns = array ( );
            foreach($fields as $i)
            {
                $syscolumns[$i['Field']] = $i;
            }
            $this->columns[$table] = $syscolumns;
        }

        //Есть ли поле статуса «удалено»
        if ( isset( $syscolumns['deleted'] ) )
            $where_deleted = ' AND `deleted` = \'N\'';
        else
            $where_deleted = '';

        //Задана ли сортировка
        if ( !empty( $order_by ) )
            $order = ' ORDER BY ' . $order_by . '';
        elseif ( isset( $syscolumns['order'] ) )
            $order = ' ORDER BY `order`';
        else
            $order = '';

        //Заданы ли лимиты
        if ( !empty( $limit ) )
            $limit_filter = 'LIMIT ' . $limit . '';
        else
            $limit_filter = '';

        //Если в таблице задано поле PRI определяем его имя $fieldId
        foreach ( $syscolumns as $column )
        {
            if ( $column['Key'] == 'PRI' )
                $fieldId = $column['Field'];
        }

        //Статический фильтр
        if ( isset( $this->static_filter[$table] ) && !empty( $this->static_filter[$table] ) )
            $static_filter = 'AND ' . $this->static_filter[$table];
        else
            $static_filter = '';

        $sql = "SELECT * FROM `prefix_$table` WHERE 1 $where_deleted $where_filter $static_filter $order $limit_filter";
        //$sql = 'SELECT * FROM `prefix_' . $table . '` WHERE 1 ' . $where_deleted . ' ' . $where_filter . ' ' . $static_filter . ' ' . $order . ' ' . $limit_filter;
        $rows = SqlTools::selectRows( $sql, MYSQLI_ASSOC );
        foreach($rows as $doc)
        {
            if ( isset( $fieldId ) )
                $this->data[$table]['data'][$doc[$fieldId]] = $doc;
            else
                $this->data[$table]['data'][] = $doc;
        }

        return $this->data[$table]['data'];
    }

    function GetDataById( $table, $id )
    {
        if ( $id == 0 )
            return false;
        if ( isset( $this->data[$table]['data'][$id] ) )
            return $this->data[$table]['data'][$id];
        else
        {
            $data = $this->GetData( $table );
            if ( empty( $data[$id] ) )
                return false;

            return $data[$id];
        }

        return array ( );
    }

    function SetStaticFilter( $table, $where )
    {
        $this->static_filter[$table] = $where;
    }
}
