<?php
/**
 * Description of GalleryManager
 *
 * @author kraser
 */
class GalleryManager extends CmsModel
{
    public function __construct ( $parent )
    {
        parent::__construct ( 'Gallery', $parent );
        $this->table = 'gallery';
        $this->selectQuery = "SELECT
                `g`.`id` AS id,
                `g`.`name` AS title,
                `g`.`alias` AS alias,
                `g`.`date` AS date,
                `g`.`text` AS text,
                `g`.`show` AS view,
                `g`.`deleted` AS deleted,
                `g`.`created` AS createDate,
                `g`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` `g`
            {where}
            {order}
            {limit}";
        $this->reference =
        [
            'id' => [ 'expression' => '`g`.`id`', 'type' => 'integer', 'raw' => 'id' ],
            'title' => [ 'expression' => '`g`.`name`', 'type' => 'varchar', 'raw' => 'name' ],
            'alias' => [ 'expression' => '`g`.`alias`', 'type' => 'varchar', 'raw' => 'alias' ],
            'date' => [ 'expression' => '`g`.`date`', 'type' => 'date', 'raw' => 'date' ],
            'text' => [ 'expression' => '`g`.`text`', 'type' => 'text', 'raw' => '`text`' ]
        ];
        $this->modelClass = "GalleryModel";
    }

    public function search ( $params )
    {
        $whereClause = [ "`g`.`show`='Y'", "`g`.`deleted`='N'" ];
        $galleries = $this->doSearch ( $params, implode ( " AND ", $whereClause ), "`g`.`date` DESC" );
        $ids = ArrayTools::pluck ( $galleries->data, "id" );
        $images = Starter::app ()->imager->getMainImages ( "Gallery", $ids );

        foreach ( $galleries->data as $gallery )
        {
            $gallery->image = $images[$gallery->id];
            $gallery->images = Starter::app ()->imager->getImages ( "Gallery", $gallery->id );
        }
        return $galleries;
    }

    public function searchGallery ( $params )
    {
        $whereClause = [ "`g`.`show`='Y'", "`g`.`deleted`='N'" ];
        $query = $this->createSelectQuery ( $params, implode ( " AND ", $whereClause ), "`g`.`date` DESC" );
        $gallery = SqlTools::selectObject ( $query, $this->modelClass );
        $images = Starter::app ()->imager->getImages ( "Gallery", $gallery->id );
        $itemCount = count ( $images );

        $offset = array_key_exists ( "page", $params ) ? $params['page'] : 1;
        $gallery->images = array_slice ( $images, $offset - 1, $this->pageSize );

        $paginator = new CmsPagination ( $this, $itemCount, $offset );
        $gallery->paginator = $paginator;
        return $gallery;
    }
}

class GalleryModel extends Model
{
    public $date;
    public $text;
    public $images;
}
