<?php
/**
 * Description of TourneyModel
 *
 * @author kraser
 */
class TourneyModel extends CmsModel
{
    public function __construct ( $parent )
    {
        parent::__construct ( 'PlayerStat', $parent );
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

    private function simulakr ()
    {
        $str = "Комета, 21, 20, 1, 0, 0, 118, 34, 42, 1;
            Романтик,	22,	14,	2	,1	,5	,78, 57,	33, 2;
Сибирский Антрацит,	22	,11,	,1	,6	,4,	96, 77,	30,     3;
Вымпел,	21,	11,	3,	1,	6,	88,81,	29, 4;
Черная Жемчужина,	21,	10,	4,	1	,6	,91,65	,29, 5;
ЮКОС	,22	10,	2,	3,	7,	95,78,	27, 6;
Авиатор,	21,	9,	1,	4	,7,	100,80,	24, 7;
Сибирские Песцы,	21,	8,	3,	0,	10,	60,75,	22, 8;
Новосибирск,	21,	7,	2,	3,	9,	77,96,	21, 9;
Общее Дело,	22,	5,	0,	0,	17,	76,103	,10, 10;
Райво ,	22,	5,	0	,0	,17	,63,132	,10, 11;
НВИ ВВ МВД,	22,	0,	0	,0	,22	,47,91,	0, 12;";

        $keys = [ 'teamName', 'matches', 'wins', 'winsByBullit', 'lossByBullit', 'loss', 'goals', 'misses', 'score', 'place' ];

        $array = preg_split('/\s*;\s*/', $str);
        $res = [];
        foreach($array as $row)
        {
            $temp = preg_split('/\s*,\s*/', $row);
            $row = (object) array_combine ( $keys, $temp );
            $res[] = $row;
        }
        return $res;

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

class Tourney
{
    public $teamId;
    public $teamName;
    public $matches;
    public $wins;
    public $winsByBullit;
    public $lossByBullit;
    public $loss;
    public $goals;
    public $misses;
    public $score;
    public $place;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;
}