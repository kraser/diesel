<?php
/**
 * Description of NewsManager
 *
 * @author kraser
 */
class NewsManager extends CmsModel
{
    public function __construct ( $parent )
    {
        parent::__construct ( 'News', $parent );
        $this->table = 'news';
        $this->selectQuery = "SELECT
                `n`.`id` AS id,
                `n`.`name` AS title,
                `n`.`alias` AS alias,
                `n`.`date` AS date,
                `n`.`anons` AS anons,
                `n`.`text` AS text,
                `n`.`show` AS view,
                `n`.`deleted` AS deleted,
                `n`.`created` AS createDate,
                `n`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` `n`
            {where}
            {order}
            {limit}";
        $this->reference =
        [
            'id' => [ 'expression' => '`n`.`id`', 'type' => 'integer', 'raw' => 'id' ],
            'title' => [ 'expression' => '`n`.`name`', 'type' => 'varchar', 'raw' => 'name' ],
            'date' => [ 'expression' => '`n`.`date`', 'type' => 'date', 'raw' => 'date' ],
            'anons' => [ 'expression' => '`n`.`anons`', 'type' => 'text', 'raw' => 'anons' ],
            'text' => [ 'expression' => '`n`.`text`', 'type' => 'text', 'raw' => '`text`' ]
        ];
        $this->modelClass = "NewsModel";
    }

    public function search ( $params )
    {
        $whereClause = [ "`n`.`show`='Y'", "`n`.`deleted`='N'" ];
        $news = $this->doSearch ( $params, implode ( " AND ", $whereClause ), "`n`.`date` DESC" );
        $ids = ArrayTools::pluck ( $news->data, "id" );
        $images = Starter::app ()->imager->getMainImages ( "News", $ids );

        foreach ( $news->data as $newsItem )
        {
            $newsItem->image = $images[$newsItem->id];
        }
        return $news;
    }

    public function getWalkLink ( $id )
    {
        $whereClause = [ "`n`.`show`='Y'", "`n`.`deleted`='N'" ];
        $query = $this->createSelectQuery ( [], implode ( " AND ", $whereClause ), "`n`.`date` DESC" );
        $ids = ArrayTools::pluck ( SqlTools::selectObjects ( $query, $this->modelClass ), "id" );
        $key = array_search ( $id, $ids );
        if ( $key === 0 )
        {
            $prev = $ids[count ( $ids ) - 1];
            $next = $ids[$key + 1];
        }
        elseif ( $key === ( count ( $ids ) - 1 ) )
        {
            $prev = $ids[$key - 1];
            $next = $ids[0];
        }
        else
        {
            $prev = $ids[$key - 1];
            $next = $ids[$key + 1];
        }

        return [ 'prev' => $prev, 'next' => $next ];
    }
}

class NewsModel extends Model
{
    public $date;
    public $anons;
    public $text;
}