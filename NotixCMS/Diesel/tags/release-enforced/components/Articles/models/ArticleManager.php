<?php
/**
 * Description of ArticleManager
 *
 * @author kraser
 */
class ArticleManager extends CmsModel
{
    private $table;

    public function __construct()
    {
        parent::__construct ( "Article", $parent );
        $this->table = 'articles';
        $this->selectQuery = "
            SELECT
                `a`.`id` AS id,
                `a`.`alias` AS alias,
                `a`.`title` AS title,
                `a`.`anons` AS anons,
                `a`.`text` AS text,
                `a`.`date` AS date,
                `a`.`authorId` AS aithorId,
                `a`.`show` AS view,
                `a`.`deleted` AS deleted,
                `a`.`created` AS createDate,
                `a`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` `a`
            {where}
            {order}
            ";

        $this->reference =
        [
            'id' => [ 'expression' => '`a`.`id`', 'type' => 'integer', 'raw' => '`id`' ],
            'alias' => [ 'expression' => '`a`.`alias`', 'type' => 'varchar', 'raw' => '`alias`' ],
            'title' => [ 'expression' => '`a`.`title`', 'type' => 'varchar', 'raw' => 'title' ],
            'anons' => [ 'expression' => '`a`.`anons`', 'type' => 'varchar', 'raw' => 'anons' ],
            'text' => [ 'expression' => '`a`.`text`', 'type' => 'text', 'raw' => '`text`' ],
            'date' => [ 'expression' => '`a`.`date`', 'type' => 'date', 'raw' => '`date`' ],
            'authorId' => [ 'expression' => '`a`.`authorId`', 'type' => 'integer', 'raw' => '`authorId`' ]
        ];
        $this->modelClass = "Article";
    }

    public function find ( $params = null )
    {
        if ( $params === null )
            return [];

        $conditions = [];
        foreach ( $params as $field => $value )
        {
            if ( !$value || is_numeric ( $field))
                continue;

            switch ($field)
            {
                case 'id':
                case 'articleId':
                    $conditions[] = "`a`.`id` IN (".ArrayTools::numberList($value).")";
                    break;

                case 'article':
                    if ( is_numeric ( $value ) )
                        $conditions[] = "`a`.`id` IN (".ArrayTools::numberList($value).")";
                    else
                        $conditions[] = "`a`.`alias` IN (" . ArrayTools::stringList($value).")";
                    break;

                case 'alias':
                    $conditions[] = "`a`.`alias` IN (" . ArrayTools::stringList($value).")";
                    break;

                case 'title':
                    $conditions[] = "`a`.`title` LIKE '%". SqlTools::escapeString ( value )."%'";
                    break;

                case 'anons':
                    $conditions[] = "`a`.`anons` LIKE '%". SqlTools::escapeString ( value )."%'";
                    break;

                case 'text':
                    $conditions[] = "`a`.`text` LIKE '%".SqlTools::escapeString ( value )."%'";
                    break;

                case 'date':
                    $conditions[] = "`a`.`date`='".SqlTools::escapeString ( value )."'";
                    break;

                case 'authorId':
                    $conditions[] = "`a`.`authorId` IN (".ArrayTools::numberList($value).")";
                    break;

                case 'show':
                    $asBool = $value && $value !== 'N' ? "Y" : "N";
                    $conditions[] = "`a`.`show`='$asBool'";
                    break;

                case 'deleted':
                    $asBool = $value && $value !== 'N' ? "Y" : "N";
                    $conditions[] = "`a`.`deleted`='$asBool'";
                    break;

                default:
            }
        }
        $whereClause = count ($conditions) ? " WHERE " . implode (" AND ", $conditions ) : "";
        $orderClause = "ORDER BY `a`.`date` DESC";
        $sql = "SELECT
                `a`.`id` AS id,
                `a`.`alias` AS alias,
                `a`.`title` AS title,
                `a`.`anons` AS anons,
                `a`.`text` AS text,
                `a`.`date` AS date,
                `a`.`authorId` AS authorId,
                CONCAT(`u`.`firstName`,' ',`u`.`lastName`) AS authorName,
                `a`.`show` AS view,
                `a`.`deleted` AS deleted,
                `a`.`created` AS createDate,
                `a`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` AS `a`
            $whereClause
            $orderClause";

        $articles = SqlTools::selectObjects ( $sql, "Article", "id" );
        return $articles;
    }
}

class Article
{
    public $id;
    public $alias;
    public $title;
    public $anons;
    public $text;
    public $date;
    public $authorId;
    public $authorName;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;
}