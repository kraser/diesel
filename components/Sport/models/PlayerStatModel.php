<?php

/**
 * <pre>Класс PlayerStatModel - модель данных статистики игроков</pre>
 * @author kraser
 */
class PlayerStatModel extends CmsModel
{
    /**
     * <pre>Конструктор</pre>
     * @param CmsComponent $parent <p>Владелец модели</p>
     */
    public function __construct ( $parent )
    {
        parent::__construct ( 'PlayerStat', $parent );
        $this->table = 'playerStat';
        $this->selectQuery = "SELECT
            `ps`.`playerId` AS playerId,
            `p`.`name` AS name,
            `p`.`num` AS num,
            `p`.`amplua` AS amplua,
            `p`.`teamId` AS teamId,
            `t`.`name` AS teamName,
            SUM(`ps`.`matches`) AS matches,
            SUM(`ps`.`goals`) AS goals,
            SUM(`ps`.`pass`) AS pass,
            SUM(`ps`.`goals`+`ps`.`pass`) AS scores,
            SUM(`ps`.`utility`) AS utility,
            SUM(`ps`.`penaltyTime`) AS  penaltyTime,
            ROUND(SUM(`ps`.`penaltyTime`)/SUM(`ps`.`matches`), 2 ) AS penaltyPerMatch,
            SUM(`ps`.`winGoals`) AS winGoals,
            SUM(`ps`.`equalGoals`) AS equalGoals,
            SUM(`ps`.`majorityGoals`) AS majorityGoals,
            SUM(`ps`.`minorityGoals`) AS minorityGoals,
            SUM(`ps`.`overtimeGoals`) AS overtimeGoals,
            SUM(`ps`.`winBullit`) AS winBullit,
            SUM(`ps`.`shots`) AS shots,
            ROUND(SUM(`ps`.`goals`)/SUM(`ps`.`shots`) * 100, 2 ) AS successShotsPercent,
            SUM(`ps`.`throw`) AS throws,
            SUM(`ps`.`winThrow`) AS winThrows,
            ROUND(SUM(`ps`.`winThrow`)/SUM(`ps`.`throw`) * 100, 2 ) AS percentThrows,
            ROUND(SUM(`ps`.`shots`)/SUM(`ps`.`matches`), 2 ) AS shotsPerMatch
        FROM `prefix_" . $this->table . "` `ps`
        JOIN `prefix_players` `p` ON `p`.`id`=`ps`.`playerId` AND `p`.`show`='Y' AND `p`.`deleted`='N'
        JOIN `prefix_teams` `t` ON `t`.`id`=`p`.`teamId` AND `t`.`show`='Y' AND `t`.`deleted`='N'
        {where}
        GROUP BY `ps`.`playerId`
        {order}
        {limit}";
        $this->reference =
        [
            'playerId' => [ 'expression' => '`ps`.`playerId`', 'type' => 'integer', 'raw' => 'none' ],
            'name' => [ 'expression' => '`p`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'amplua' => [ 'expression' => '`p`.`amplua`', 'type' => 'enum', 'raw' => 'none' ],
            'teamId' => [ 'expression' => '`p`.`teamId`', 'type' => 'integer', 'raw' => 'none' ],
            'tourney' => [ 'expression' => '`ps`.`tourneyId`', 'type' => 'integer', 'raw' => '`tourneyId`' ],
            'teamName' => [ 'expression' => '`t`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'matches' => [ 'expression' => 'SUM(`ps`.`matches`)', 'type' => 'integer', 'raw' => '`matches`' ],
            'goals' => [ 'expression' => 'SUM(`ps`.`goals`)', 'type' => 'integer', 'raw' => '`goals`' ],
            'pass' => [ 'expression' => 'SUM(`ps`.`pass`)', 'type' => 'integer', 'raw' => '`pass`' ],
            'scores' => [ 'expression' => 'SUM(`ps`.`goals`+`ps`.`pass`)', 'type' => 'integer', 'raw' => 'none' ],
            'utility' => [ 'expression' => 'SUM(`ps`.`utility`)', 'type' => 'integer', 'raw' => '`utility`' ],
            'penaltyTime' => [ 'expression' => 'SUM(`ps`.`penaltyTime`)', 'type' => 'integer', 'raw' => '`penaltyTime`' ],
            'penaltyPerMatch' => [ 'expression' => 'ROUND(SUM(`ps`.`penaltyTime`)/SUM(`ps`.`matches`), 2 )', 'type' => 'decimal', 'raw' => 'none' ],
            'winGoals' => [ 'expression' => 'SUM(`ps`.`winGoals`)', 'type' => 'integer', 'raw' => '`winGoals`' ],
            'equalGoals' => [ 'expression' => 'SUM(`ps`.`equalGoals`)', 'type' => 'integer', 'raw' => '`equalGoals`' ],
            'majorityGoals' => [ 'expression' => 'SUM(`ps`.`majorityGoals`)', 'type' => 'integer', 'raw' => '`majorityGoals`' ],
            'minorityGoals' => [ 'expression' => 'SUM(`ps`.`minorityGoals`)', 'type' => 'integer', 'raw' => '`minorityGoals`' ],
            'overtimeGoals' => [ 'expression' => 'SUM(`ps`.`overtimeGoals`)', 'type' => 'integer', 'raw' => '`overtimeGoals`' ],
            'winBullit' => [ 'expression' => 'SUM(`ps`.`winBullit`)', 'type' => 'integer', 'raw' => '`winBullit`' ],
            'shots' => [ 'expression' => 'SUM(`ps`.`shots`)', 'type' => 'integer', 'raw' => '`shots`' ],
            'successShotsPercent' => [ 'expression' => 'ROUND(SUM(`ps`.`goals`)/SUM(`ps`.`shots`) * 100, 2 )', 'type' => 'decimal', 'raw' => 'none' ],
            'throws' => [ 'expression' => 'SUM(`ps`.`throw`)', 'type' => 'integer', 'raw' => '`throw`' ],
            'winThrows' => [ 'expression' => 'SUM(`ps`.`winThrow`)', 'type' => 'integer', 'raw' => '`winThrow`' ],
            'percentThrows' => [ 'expression' => 'ROUND(SUM(`ps`.`winThrow`)/SUM(`ps`.`throw`) * 100, 2 )', 'type' => 'decimal', 'raw' => 'none' ],
            'shotsPerMatch' => [ 'expression' => 'ROUND(SUM(`ps`.`shots`)/SUM(`ps`.`matches`), 2 )', 'type' => 'decimal', 'raw' => 'none' ]
        ];
        $this->modelClass = "PlayerStat";
        $this->pageSize = 15;
    }

    /**
     * <pre>Поиск по параметрам</pre>
     * @param Array $params <p>Параметры поиска</p>
     * @return Array Of PlayerStat
     */
    public function search ( $params )
    {
        $playerManager = new PlayerModel ( $this );
        $amplua = $playerManager->getAmplua ();
        $whereClause = [ "`p`.`show`='Y'", "`p`.`deleted`='N'" ];
        $players = $this->doSearch ( $params, implode ( " AND ", $whereClause ), "`t`.`name` ASC, `p`.`name` ASC" );
        $ids = ArrayTools::pluck ( $players->data, "playerId" );
        $images = Starter::app ()->imager->getMainImages ( "Player", $ids );

        foreach ( $players->data as $player )
        {
            $player->amplua = $amplua[$player->amplua];
            $player->image = $images[$player->playerId];
        }
        return $players;
    }

    /**
     * <pre>Возвращает массив настроек для построения таблицы</pre>
     * @return Array <p>Массив настроек таблицы</p>
     */
    public function getColumns ()
    {
        $columns =
        [
            'name' => [ 'colTitle' => 'Имя игрока', 'attr' => [ 'title' => 'Имя игрока' ] ],
            'amplua' => [ 'colTitle' => 'Амплуа', 'attr' => [ 'title' => 'Амплуа игрока' ] ],
            'teamName' => [ 'colTitle' => 'Команда', 'attr' => [ 'title' => 'Команда игрока' ] ],
            'matches' => [ 'colTitle' => 'И', 'attr' => [ 'title' => 'Количество проведенных игр' ] ],
            'goals' => [ 'colTitle' => 'Ш', 'attr' => [ 'title' => 'Заброшенные шайбы' ] ],
            'pass' => [ 'colTitle' => 'А', 'attr' => [ 'title' => 'Передачи' ] ],
            'scores' => [ 'colTitle' => 'О', 'attr' => [ 'title' => 'Очки' ] ],
            'utility' => [ 'colTitle' => '+/­', 'attr' => [ 'title' => 'Показатель полезности' ] ],
            'penaltyTime' => [ 'colTitle' => 'Штр', 'attr' => [ 'title' => 'Штрафное время' ] ],
            'penaltyPerMatch' => [ 'colTitle' => 'Штр/И', 'attr' => [ 'title' => 'Среднее время штрафа за игру' ] ],
            'winGoals' => [ 'colTitle' => 'ШП', 'attr' => [ 'title' => 'Победные шайбы' ] ],
            'equalGoals' => [ 'colTitle' => 'ШР', 'attr' => [ 'title' => 'Шайбы, забитые в равенстве' ] ],
            'majorityGoals' => [ 'colTitle' => 'ШБ', 'attr' => [ 'title' => 'Шайбы, забитые в большинстве' ] ],
            'minorityGoals' => [ 'colTitle' => 'ШМ', 'attr' => [ 'title' => 'Шайбы, забитые в меньшинстве' ] ],
            'overtimeGoals' => [ 'colTitle' => 'ШО', 'attr' => [ 'title' => 'Шайбы, забитые в овертайме' ] ],
            'winBullit' => [ 'colTitle' => 'РБ', 'attr' => [ 'title' => 'Решающие буллиты' ] ],
            'shots' => [ 'colTitle' => 'БВ', 'attr' => [ 'title' => 'Броски по воротам' ] ],
            'successShotsPercent' => [ 'colTitle' => '%БВ', 'attr' => [ 'title' => 'Процент реализованных бросков' ] ],
            'throws' => [ 'colTitle' => 'Вбр', 'attr' => [ 'title' => 'Вбрасывания' ] ],
            'winThrows' => [ 'colTitle' => 'ВВбр', 'attr' => [ 'title' => 'Выигранные вбрасывания' ] ],
            'percentThrows' => [ 'colTitle' => '%Вбр', 'attr' => [ 'title' => 'Процент выигранных вбрасываний' ] ],
            'shotsPerMatch' => [ 'colTitle' => 'БВ/И', 'attr' => [ 'title' => 'Среднее количество бросков за игру' ] ]
        ];
        return $columns;
    }
}

/**
 * <pre>Модель данных статистики игроков<pre>
 */
class PlayerStat
{
    /**
     * @var Integer <p>ID</p>
     */
    public $id;

    /**
     * @var Integer <p>ID игрока</p>
     */
    public $playerId;

    /**
     * @var String <p>Имя</p>
     */
    public $name;

    /**
     * @var String <p>Номер игрока</p>
     */
    public $num;

    /**
     * @var String <p>URL фото</p>
     */
    public $image;

    /**
     * @var String <p>Амплуа</p>
     */
    public $amplua;

    /**
     * @var Integer <p>ID команды</p>
     */
    public $teamId;

    /**
     * @var String <p>Название команды</p>
     */
    public $teamName;

    /**
     * @var Integer <p>Количество матчей</p>
     */
    public $matches;

    /**
     * @var Integer <p>Количество забитых шайб</p>
     */
    public $goals;

    /**
     * @var Integer <p>Количество голевых передач</p>
     */
    public $pass;

    /**
     * @var Integer <p>Очки (гол+пас)</p>
     */
    public $scores;

    /**
     * @var Integer <p>Полезность</p>
     */
    public $utility;

    /**
     * @var Integer <p>Штрафное время</p>
     */
    public $penaltyTime;

    /**
     * @var Float <p>Штрафное время в среднем за матч</p>
     */
    public $penaltyPerMatch;

    /**
     * @var Integer <p>Решающие голы</p>
     */
    public $winGoals;

    /**
     * @var Integer <p>Голы в равных составах</p>
     */
    public $equalGoals;

    /**
     * @var Integer <p>Голы в большинстве</p>
     */
    public $majorityGoals;

    /**
     * @var Integer <p>Голы в меньшинстве</p>
     */
    public $minorityGoals;

    /**
     * @var Integer <p>Голы в овертайме</p>
     */
    public $overtimeGoals;

    /**
     * @var Integer <p>Победные буллиты</p>
     */
    public $winBullit;

    /**
     * @var Integer <p>Броски по воротам</p>
     */
    public $shots;

    /**
     * @var Float <p>Процент успешеых бросков</p>
     */
    public $successShotsPercent;

    /**
     * @var Integer <p>Игра на вбрасывании</p>
     */
    public $throws;

    /**
     * @var Integer <p>Выигранные вбрасывания</p>
     */
    public $winThrows;

    /**
     * @var Float <p>Процент выигранных вбрасываний</p>
     */
    public $percentThrows;

    /**
     * @var Float <p>Среднее количество бросков за игру</p>
     */
    public $shotsPerMatch;

    /**
     * @var Date <p>Статистическая дата</p>
     */
    public $date;
}
