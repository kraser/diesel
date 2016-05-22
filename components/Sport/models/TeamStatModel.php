<?php
/**
 * Description of TeamStatModel
 *
 * @author kraser
 */
class TeamStatModel
{
    public function __construct ($parent )
    {
        parent::__construct ( "TeamStat", $parent );
        $this->table = 'byPeriodStat';
        $this->selectQuery = "SELECT
                `tp`.`teamId` AS teamId,
                `t`.`name` AS name,
                `tp`.`period` AS period,
                SUM(`tp`.`matches`) AS matches,
                SUM(`tp`.`majority`) AS majority,
                SUM(`tp`.`majorityGoals`) AS majorityGoals,
                SUM(`tp`.`majorityMisses`) AS majorityMisses,
                ROUND(100*SUM(`tp`.`majorityGoals`)/SUM(`tp`.`majority`, 2) AS majorityRealizationPercent,
                SUM(`tp`.`minority`) AS minority,
                SUM(`tp`.`minorityGoals`) AS minorityGoals,
                SUM(`tp`.`minorityMisses`) AS minorityMisses,
                ROUND(100-100*SUM(`tp`.`minorityMisses`)/SUM(`tp`.`minority`), 2) AS minorityKillPercent,
                SUM(`tp`.`penaltyTime`) AS penaltyTime,
                ROUND(SUM(`tp`.`penaltyTime`)/SUM(`tp`.`matches`), 2) AS penaltyTimePerMatch,
                SUM(`tp`.`contestPenaltyTime`) AS contestPenaltyTime,
                ROUND(SUM(`tp`.`contestPenaltyTime`)/SUM(`tp`.`matches`), 2) AS contestPenaltyTimePerMatch,
                SUM(`tp`.`goalless`) AS goalless,
                SUM(`tp`.`missless`) AS missless,
                SUM(`tp`.`win`) AS win,
                SUM(`tp`.`draw`) AS draw,
                SUM(`tp`.`loss`) AS loss
            FROM `prefix_" . $this->table . "` `tp`
            JOIN `prefix_teams` `t` ON `t`.`id`=`tp`.`teamId` AND `t`.`show`='Y' AND `t`.`deleted`='N'
            {where}
            GROUP BY `tp`.`teamId`, `tp`.`period`
            {having}
            {order}";
        $this->reference =
        [
            'teamId' => [ 'expression' => '`tp`.`teamId`', 'type' => 'integer', 'raw' => '`teamId`' ],
            'name' => [ 'expression' => '`t`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'period' => [ 'expression' => '`tp`.`period`', 'type' => 'integer', 'raw' => '`period`' ],
            'matches' => [ 'expression' => 'SUM(`tp`.`matches`)', 'type' => 'integer', 'raw' => '`matches`' ],
            'majority' => [ 'expression' => 'SUM(`tp`.`majority`)', 'type' => 'integer', 'raw' => '`majority`' ],
            'majorityGoals' => [ 'expression' => 'SUM(`tp`.`majorityGoals`)', 'type' => 'integer', 'raw' => '`majorityGoals`' ],
            'majorityMisses' => [ 'expression' => 'SUM(`tp`.`majorityMisses`)', 'type' => 'integer', 'raw' => '`majorityMisses`' ],
            'majorityRealizationPercent' => [ 'expression' => 'ROUND(100*SUM(`tp`.`majorityGoals`)/SUM(`tp`.`majority`, 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'minority' => [ 'expression' => 'SUM(`tp`.`minority`)', 'type' => 'integer', 'raw' => '`minority`' ],
            'minorityGoals' => [ 'expression' => 'SUM(`tp`.`minorityGoals`)', 'type' => 'integer', 'raw' => '`minorityGoals`' ],
            'minorityMisses' => [ 'expression' => 'SUM(`tp`.`minorityMisses`)', 'type' => 'integer', 'raw' => '`minorityMisses`' ],
            'minorityKillPercent' => [ 'expression' => 'ROUND(100-100*SUM(`tp`.`minorityMisses`)/SUM(`tp`.`minority`), 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'penaltyTime' => [ 'expression' => 'SUM(`tp`.`penaltyTime`)', 'type' => 'integer', 'raw' => '`penaltyTime`' ],
            'penaltyTimePerMatch' => [ 'expression' => 'ROUND(SUM(`tp`.`penaltyTime`)/SUM(`tp`.`matches`), 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'contestPenaltyTime' => [ 'expression' => 'SUM(`tp`.`contestPenaltyTime`)', 'type' => 'integer', 'raw' => '`contestPenaltyTime`' ],
            'contestPenaltyTimePerMatch' => [ 'expression' => 'ROUND(SUM(`tp`.`contestPenaltyTime`)/SUM(`tp`.`matches`), 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'goalless' => [ 'expression' => 'SUM(`tp`.`goalless`)', 'type' => 'integer', 'raw' => '`goalless`' ],
            'missless' => [ 'expression' => 'SUM(`tp`.`missless`)', 'type' => 'integer', 'raw' => '`missless`' ],
            'win' => [ 'expression' => 'SUM(`tp`.`win`)', 'type' => 'integer', 'raw' => '`win`' ],
            'draw' => [ 'expression' => 'SUM(`tp`.`draw`)', 'type' => 'integer', 'raw' => '`draw`' ],
            'loss' => [ 'expression' => 'SUM(`tp`.`loss`)', 'type' => 'integer', 'raw' => '`loss`' ]
        ];
        $this->modelClass = "TeamStat";
    }

    public function search ( $params )
    {
        $query = $this->createSelectQuery ( $params, "", "`t`.`name` ASC" );
        $players = SqlTools::selectObjects ( $query, $this->modelClass );
        return $players;
    }

    public function getColumns ()
    {
        $columns =
        [
            'name' => [ 'colTitle' => 'Имя игрока', 'attr' => [ 'title' => 'Имя игрока' ] ],
            'name' => [ 'colTitle' => 'Команда', 'attr' => [ 'title' => 'Команда' ] ],
            'period' => [ 'colTitle' => 'И', 'attr' => [ 'title' => 'Период' ] ],
            'matches' => [ 'colTitle' => 'И', 'attr' => [ 'title' => 'Количество проведенных игр' ] ],
            'majority' => [ 'colTitle' => 'Бол', 'attr' => [ 'title' => 'Количество раз, которое команда играла в большинстве' ] ],
            'majorityGoals' => [ 'colTitle' => 'ШБ', 'attr' => [ 'title' => 'шайбы заброшенные в большинстве' ] ],
            'majorityMisses' => [ 'colTitle' => 'ПШБ', 'attr' => [ 'title' => 'шайбы пропущенные в большинстве' ] ],
            'majorityRealizationPercent' => [ 'colTitle' => '%ИБ', 'attr' => [ 'title' => 'процент реализации большинства' ] ],
            'minority' => [ 'colTitle' => 'ШМ', 'attr' => [ 'title' => 'количество раз, которое команда играла в меньшинстве' ] ],
            'minorityGoals' => [ 'colTitle' => 'Мен', 'attr' => [ 'title' => 'шайбы заброшенные в меньшинстве' ] ],
            'minorityMisses' => [ 'colTitle' => 'ПШМ', 'attr' => [ 'title' => 'шайбы пропущенные в меньшинстве' ] ],
            'minorityKillPercent' => [ 'colTitle' => '%ИМ', 'attr' => [ 'title' => 'процент убивания меньшинтсва' ] ],
            'penaltyTime' => [ 'colTitle' => 'Штр', 'attr' => [ 'title' => 'Штрафное время' ] ],
            'penaltyTimePerMatch' => [ 'colTitle' => 'Штр/И', 'attr' => [ 'title' => 'Штрафное время в среднем за игру' ] ],
            'contestPenaltyTime' => [ 'colTitle' => 'ШтрС', 'attr' => [ 'title' => 'Штрафное время соперника' ] ],
            'contestPenaltyTimePerMatch' => [ 'colTitle' => 'Штр/И', 'attr' => [ 'title' => 'Штрафное время соперника в среднем за игру' ] ],
            'goalless' => [ 'colTitle' => 'ИБЗ', 'attr' => [ 'title' => 'Игры без забитых шайб' ] ],
            'missless' => [ 'colTitle' => 'ИБП', 'attr' => [ 'title' => 'Игры без пропущенных шайб' ] ],
            'win' => [ 'colTitle' => 'В', 'attr' => [ 'title' => 'Победы' ] ],
            'draw' => [ 'colTitle' => 'Н', 'attr' => [ 'title' => 'Ничьи' ] ],
            'loss' => [ 'colTitle' => 'Пр', 'attr' => [ 'title' => 'Проигрыши' ] ],
        ];
        return $columns;
    }

//    public function createModelItem ( $model )
//    {
//        $queryParams = parent::createInsertQuery ( $model );
//        $queryParams['set'][] = "`created`";
//        $queryParams['values'][] = "NOW()";
//        $query = "INSERT INTO `prefix_" . $this->table . "` (" . implode ( ",", $queryParams['set'] ) . ") VALUES (" . implode ( "," , $queryParams['values'] ) . ")";
//        return SqlTools::insert ( $query );
//    }


}

class TeamStat
{
    public $id;
    public $teamId;
    public $name;
    public $period;
    public $matches;
    public $majority;
    public $majorityGoals;
    public $majorityMisses;
    public $majorityRealizationPercent;
    public $minority;
    public $minorityGoals;
    public $minorityMisses;
    public $minorityKillPercent;
    public $penaltyTime;
    public $penaltyTimePerMatch;
    public $contestPenaltyTime;
    public $contestPenaltyTimePerMatch;
    public $goalless;
    public $missless;
    public $win;
    public $draw;
    public $loss;
    public $date;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;
}


/*


*/