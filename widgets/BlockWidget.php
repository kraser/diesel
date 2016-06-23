<?php
/**
 * Description of BlockWidget
 *
 * @author kraser
 */
class BlockWidget extends CmsWidget
{
    private $table;
    public function __construct ( $parent )
    {
        parent::__construct ( "Block", $parent );
        $this->table = "blocks";
    }

    private $blockId;
    public function getBlockId ()
    {
        return $this->blockId;
    }

    public function setBlockId ( $blockId )
    {
        $this->blockId = $blockId;
    }

    public function run ()
    {
        $block = $this->getBlock ();
        $template = $block->template ? : "block";
        return $this->renderPart ( "widgets/$template", [ 'block' => $block ] );
    }

    private function getBlock ()
    {
        $where =
        [
            "`b`.`deleted`= 'N'",
            "`b`.`show`='Y'"
        ];
        if ( !$this->blockId )
            return null;
        else if ( is_numeric ( $this->blockId ) )
            $where[] = "`b`.`id`=$this->blockId";
        else
            $where[] = "`b`.`callname`='$this->blockId'";

        $query = "SELECT `b`.*
            FROM `prefix_blocks` AS `b`
            WHERE " . implode ( " AND ", $where );

        $block = SqlTools::selectObject ( $query );

        return $block;
    }
}