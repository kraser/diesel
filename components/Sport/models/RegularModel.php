<?php
/**
 * Description of TourneyModel
 *
 * @author kraser
 */
class RegularModel extends CmsModel
{
    public function __construct ( $parent )
    {
        parent::__construct ( 'Regular', $parent );
        $this->table = 'tourneyResults';
        $this->selectQuery = "
            SELECT
                `r`.`id` AS id,
                `r`.`teamId` AS teamId,
                `t`.`name` AS teamName,
                `r`.`matches` AS matches,
                `r`.`wins` AS wins,
                `r`.`winsByBullit` AS winsByBullit,
                `r`.`lossByBullit` AS lossByBullit,
                `r`.`loss` AS loss,
                `r`.`goals` AS goals,
                `r`.`misses` AS misses,
                `r`.`scores` AS score,
                `r`.`place` AS place,
                `r`.`show` AS view,
                `r`.`deleted` AS deleted,
                `r`.`created` AS createDate,
                `r`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` `r`
            JOIN `prefix_teams` `t` ON `t`.`id`=`r`.`teamId` AND `t`.`show`='Y' AND `t`.`deleted`='N'
            {where}
            {order}
            ";

        $this->reference =
        [
            'id' => [ 'expression' => '`r`.`id`', 'type' => 'integer', 'raw' => '`id`' ],
            'teamId' => [ 'expression' => '`r`.`teamId`', 'type' => 'integer', 'raw' => 'teamId' ],
            'tourney' => [ 'expression' => '`r`.`tourneyId`', 'type' => 'integer', 'raw' => '`tourneyId`' ],
            'teamName' => [ 'expression' => '`t`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'matches' => [ 'expression' => '`r`.`matches`', 'type' => 'integer', 'raw' => '`matches`' ],
            'wins' => [ 'expression' => '`r`.`wins`', 'type' => 'integer', 'raw' => '`wins`' ],
            'winsByBullit' => [ 'expression' => '`r`.`winsByBullit`', 'type' => 'integer', 'raw' => '`winsByBullit`' ],
            'lossByBullit' => [ 'expression' => '`r`.`lossByBullit`', 'type' => 'integer', 'raw' => '`lossByBullit`' ],
            'loss' => [ 'expression' => '`r`.`loss`', 'type' => 'integer', 'raw' => '`loss`' ],
            'goals' => [ 'expression' => '`r`.`goals`', 'type' => 'integer', 'raw' => '`goals`' ],
            'misses' => [ 'expression' => '`r`.`misses`', 'type' => 'integer', 'raw' => '`misses`' ],
            'score' => [ 'expression' => '`r`.`scores`', 'type' => 'integer', 'raw' => '`scores`' ],
            'place' => [ 'expression' => '`r`.`place`', 'type' => 'integer', 'raw' => '`place`' ],
            'view' => [ 'expression' => '`r`.`show`', 'type' => 'yesno', 'raw' => '`show`' ],
            'deleted' => [ 'expression' => '`r`.`deleted`', 'type' => 'yesno', 'raw' => '`deleted`' ]
        ];
        $this->modelClass = "Tourney";;
    }

    public function getColumns ()
    {
        $columns =
        [
            'place' => [ 'colTitle' => 'Место', 'attr' => [ 'title' => 'Место' ] ],
            'teamName' => [ 'colTitle' => 'Клуб', 'attr' => [ 'title' => 'Клуб' ] ],
            'matches' => [ 'colTitle' => 'И', 'attr' => [ 'title' => 'Игры' ] ],
            'wins' => [ 'colTitle' => 'В', 'attr' => [ 'title' => 'Выигрыши' ] ],
            'winsByBullit' => [ 'colTitle' => 'ВБ', 'attr' => [ 'title' => 'Выигрыши по буллитам' ] ],
            'lossByBullit' => [ 'colTitle' => 'ПБ', 'attr' => [ 'title' => 'Проигрыши по буллитам' ] ],
            'loss' => [ 'colTitle' => 'П', 'attr' => [ 'title' => 'Проигрыши' ] ],
            'goals' => [ 'colTitle' => 'З', 'attr' => [ 'title' => 'Забитые шайбы' ] ],
            'misses' => [ 'colTitle' => 'ПР', 'attr' => [ 'title' => 'Пропущенные шайбы' ] ],
            'score' => [ 'colTitle' => 'О', 'attr' => [ 'title' => 'Очки' ] ]

        ];

        return $columns;
    }

    public function search ( $params )
    {
        $query = $this->createSelectQuery ( $params, "", "`r`.`place` ASC" );
        $rows = SqlTools::selectObjects($query, $this->modelClass, "teamId");
        return $rows;//$this->simulakr ();
    }

    private $tourneys;
    public function getTourneys ( $selected )
    {
        if ( $this->tourneys)
            return $this->tourneys;

        if ( $selected )
            $field = "IF(`t`.`id`=$selected,1,0) AS selected";
        else
            $field = "IF(NOW()>`t`.`startDate` AND NOW()<`t`.`endDate`,1,0) AS selected";
        $query = "SELECT
            `t`.`id` AS id,
            CONCAT(`s`.`title`,' / ',`t`.`title`) AS title,
            `t`.`type` AS type,
            $field
            FROM `prefix_tourneys` `t`
            JOIN `prefix_seasons` `s` ON `s`.`id`=`t`.`seasonId`";

        $this->tourneys = SqlTools::selectObjects ( $query, null, "id" );

        return $this->tourneys;
    }

    public function defaultTourneyId ()
    {
        $this->getTourneys( 0 );
        $selected = ArrayTools::head ( ArrayTools::select ( $this->tourneys, 'selected', 1 ) );
        if ( !$selected )
        {
            $selected = ArrayTools::tail ( $this->tourneys );
            $selected->selected = 1;
        }
        return $selected->id;
    }

    public function getCurrentTourneys ()
    {
        $query = "SELECT
                `t`.`id` AS id,
                `t`.`alias` AS alias,
                `t`.`title` AS title,
                IF(NOW()>`t`.`startDate` AND NOW()<`t`.`endDate`,1,0) AS selected
            FROM `prefix_tourneys` `t`
            JOIN `prefix_seasons` `s` ON `s`.`id`=`t`.`seasonId` AND NOW()>`s`.`startDate` AND NOW()<`s`.`endDate`";
        $tourneys = SqlTools::selectObjects ( $query, null, "id" );

        if ( count ( $tourneys ) == 0 )
        {
            $seasonId = SqlTools::selectValue ( "SELECT id FROM `prefix_seasons` WHERE `endDate`<NOW() ORDER BY `endDate` DESC LIMIT 1" );
            $query = "SELECT
                    `t`.`id` AS id,
                    `t`.`alias` AS alias,
                    `t`.`title` AS title,
                    0 AS selected
                FROM `prefix_tourneys` `t`
                WHERE `t`.`seasonId`=$seasonId";
            $tourneys = SqlTools::selectObjects ( $query, null, "id" );
        }

        return $tourneys;
    }
}
