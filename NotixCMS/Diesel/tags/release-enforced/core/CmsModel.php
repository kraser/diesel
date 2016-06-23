<?php

/**
 * Description of CmsModel
 *
 * @author kraser
 */
class CmsModel extends CmsComponent
{
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
    }

    private $table;
    public function getTable ()
    {
        return $this->table;
    }

    public function setTable ($tableName)
    {
        $this->table = $tableName;
    }

    private $selectQuery;
    public function getSelectQuery ()
    {
        return $this->selectQuery;
    }

    public function setSelectQuery ( $query )
    {
        $this->selectQuery = $query;
    }

    private $reference;
    public function getReference ()
    {
        return $this->reference;
    }

    public function setReference ( $reference )
    {
        $this->reference = $reference;
    }

    private $modelClass;

    public function getModelClass ()
    {
        return $this->modelClass;
    }

    public function setModelClass ( $className )
    {
        $this->modelClass = $className;
    }

    public function doSearch ( $searchParams, $where, $order )
    {
        $query = $this->createSelectQuery ( $searchParams, $where, $order );
        $itemCount = SqlTools::selectValue ( "SELECT COUNT(*) FROM ($query) sq" );
        $offset = array_key_exists ( "page", $searchParams ) ? $searchParams['page'] - 1 : 0;
        if ( $this->pageSize < $itemCount && !array_key_exists ( 'limit', $searchParams ) )
            $limit = " LIMIT " . $offset * $this->pageSize . ", " . $this->pageSize;
        else
            $limit = "";

        $paginator = new CmsPagination ( $this, $itemCount, $offset + 1 );
        $dataProvider = new CmsDataProvider ( $this, SqlTools::selectObjects ( $query . $limit, $this->modelClass ), $paginator );
        return $dataProvider;
    }

    public function createSelectQuery ( $params, $defaultWhere = '', $defaultOrder = '' )
    {
        $clauses = $this->createClauses ( $params, $defaultWhere, $defaultOrder );

        $replace = [ ( $clauses['where'] ? : $defaultWhere ), ( $clauses['order'] ? : $defaultOrder ), $clauses['limit'] ];
        $query = str_replace ( [ "{where}", "{order}", "{limit}" ], $replace, $this->selectQuery );
        return $query;
    }

    public function createClauses ( $params, $defaultWhere = '', $defaultOrder = ''  )
    {
        $defaultOrder = $defaultOrder ? " ORDER BY " . $defaultOrder : "";
        $clauses = [ 'where' => ( $defaultWhere ? " WHERE " . $defaultWhere : "" ), 'order' => $defaultOrder ];
        if ( $params === null )
            return $clauses;

        $conditions = [];
        $orders = [];
        $limit = "";
        foreach ( $params as $field => $value )
        {
            if ( !$value || is_numeric ( $field ) )
                continue;

            if ( array_key_exists ( $field, $this->reference ) )
            {
                $expr = $this->reference[$field]['expression'];
                $type = $this->reference[$field]['type'];
                switch ( $type )
                {
                    case 'integer':
                        $castValue = ArrayTools::numberList ( $value );
                        $conditions[] = "$expr IN ($castValue)";
                        break;
                    case 'varchar':
                        $castValue = ArrayTools::stringList ( $value );
                        $conditions[] = "$expr IN ($castValue)";
                        break;
                    case 'yesno':
                        $castValue = $value && $value !== 'N' ? "Y" : "N";
                        $conditions[] = "$expr='$castValue'";
                        break;
                    case 'text':
                        $castValue = SqlTools::escapeString ( $value );
                        $conditions[] = "$expr LIKE '%$castValue%'";
                        break;
                    case 'enum':
                        $castValue = SqlTools::escapeString ( $value );
                        $conditions[] = "$expr='$castValue'";
                        break;
                    default:
                }
            }
            elseif ( $field === 'orderBy' )
            {
                list ( $col, $direction ) = explode ( ".", $value );
                if ( array_key_exists ( $col, $this->reference ) )
                    $orders[] = $this->reference[$col]['expression'] . " " . mb_strtoupper ( $direction );
            }
            elseif ( $field === 'limit' )
            {
                $limit = " LIMIT $value";
            }
        }

        if ( count ( $conditions ) )
            $clauses['where'] = " WHERE " . implode ( " AND ", $conditions ) . ( $defaultWhere ? " AND " . $defaultWhere : "" );

        if(count ( $orders ))
            $clauses['order'] = " ORDER BY " . implode ( ",", $orders );

        $clauses['limit'] = $limit;

        return $clauses;
    }

    public function createInsertQuery ( $model )
    {
        if ( is_object ( $model  ) )
            $model = (array) $model;

        $set = [];
        $values = [];
        foreach ( $model as $field => $value )
        {
            if ( array_key_exists ( $field, $this->reference ))
            {
                if ( array_key_exists ( 'raw', $this->reference[$field] ) && $this->reference[$field]['raw'] === 'none' )
                    continue;

                $set[] = array_key_exists ( 'raw', $this->reference[$field] ) ? $this->reference[$field]['raw'] : $this->reference[$field]['expression'];
                $values[] = $this->castType ( $value, $this->reference[$field]['type'] );
            }
        }
        $query = "";
        if ( count($set))
            $query = "INSERT INTO `prefix_" . $this->table . "` SET (" . implode ( ",", $set ) . ") VALUES (" .  implode ( ",", $values ) . ")";

        return [ 'set' => $set, 'values' => $values, 'query' => $query ];
    }

    public function castType ( $value, $type )
    {
        switch ( $type )
        {
            case 'integer':
                $castValue = intval ( $value );
                break;
            case 'decimal':
                $castValue = floatval ( $value );
                break;
            case 'varchar':
            case 'text':
                $castValue = "'" . SqlTools::escapeString ( $value ) . "'";
                break;
            case 'yesno':
                $castValue = $value && $value !== 'N' ? "'Y'" : "'N'";
                break;
            default:
                $castValue = "'" . SqlTools::escapeString ( $value ) . "'";
        }

        return $castValue;
    }

    private $pageSize = 10;
    public function getPageSize ()
    {
        return $this->pageSize;
    }

    public function setPageSize ( $size )
    {
        $this->pageSize = $size;
    }

//    public function createModelItem ( $model = null )
//    {
//
//    }
}

class Model extends CmsObject
{
    public $id;
    public $alias;
    public $title;
    public $image;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;
}
