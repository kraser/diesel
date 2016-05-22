<?php
/**
 * Description of CmsDataProvider
 *
 * @author kraser
 */
class CmsDataProvider extends CmsObject
{
    private $owner;
    //private $query;
    private $paginator;
    private $data;
    public function __construct ( $owner, $data, $paginator )
    {
        parent::__construct ();
        $this->owner = $owner;
        $this->data = $data;
        $this->paginator = $paginator;
        //$this->query = $query;
    }

    public function getData ()
    {
        return $this->data;
        //$items = SqlTools::selectObjects ( $query . $limit, $this->modelClass );
    }

    public function setData ( $data )
    {
        $this->data = $data;
    }

    public function getParent ()
    {
        return $this->owner;
    }

    public function setParent ( $object )
    {
        $this->owner = $object;
    }

    public function getPaginator ()
    {
        return $this->paginator;
    }

    public function setPaginator ( $paginator )
    {
        $this->paginator = $paginator;
    }
}