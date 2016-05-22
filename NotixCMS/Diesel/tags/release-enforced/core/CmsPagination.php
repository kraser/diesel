<?php
/**
 * Description of CmsPagination
 *
 * @author kraser
 */
class CmsPagination extends CmsObject
{
    private $owner;
    private $itemsCount;
    private $offset;
    public function __construct ( $owner, $itemsCount, $offset )
    {
        $this->owner = $owner;
        $this->itemsCount = $itemsCount;
        $this->offset = $offset;
    }

    public function getparent ()
    {
        return $this->owner;
    }

    public function setParent ( $owner )
    {
        $this->owner = $owner;
    }

    public function getOffset ()
    {
        return $this->offset;
    }

    public function setOffset ( $offset )
    {
        $this->offset = $offset;
    }

    public function getItemsCount ()
    {
        return $this->itemsCount;
    }

    public function setItemsCount ( $count )
    {
        $this->itemsCount = $count;
    }
}