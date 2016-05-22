<?php
/**
 * Description of KeeperStatModel
 *
 * @author kraser
 */
class KeeperStatModel extends CmsModel
{
    public function __construct ( $parent )
    {
        parent::__construct ( 'KeeperStat', $parent );
        $this->table = 'keeperStat';
        $this->selectQuery = "SELECT
                `k`.`id` AS id,
                `k`.`playerId` AS playerId,
                `p`.`name` AS name,
                `p`.`num` AS num,
                `p`.`teamId` AS teamId,
                `t`.`name` AS teamName,
                `k`.`matches` AS matches,
                `k`.`wins` AS wins,
                `k`.`loss` AS loss,
                `k`.`missed` AS missed,
                `k`.`shots` AS view,
                ROUND(`k`.`shots`/`k`.`matches`, 2) AS shotsPerMatch,
                `k`.`shots`-`k`.`missed` AS saved,
                ROUND((`k`.`shots`-`k`.`missed`)/`k`.`shots`*100, 2) AS savedPercent,
                ROUND((`k`.`shots`-`k`.`missed`)/`k`.`matches`, 2) AS savedPerMatch,
                ROUND((`k`.`missed`*45)/`k`.`multy`, 2) AS reliability,
                `k`.`zero` AS zero,
                `k`.`playTime` AS playTime,
                `k`.`multy` AS multy
            FROM `prefix_" . $this->table . "` `k`
            JOIN `prefix_players` `p` ON `p`.`id`=`k`.`playerId` AND `p`.`show`='Y' AND `p`.`deleted`='N'
            JOIN `prefix_teams` `t` ON `t`.`id`=`p`.`teamId` AND `t`.`show`='Y' AND `t`.`deleted`='N'
            {where}
            {order}
            {limit}";
        $this->reference =
        [
            'playerId' => [ 'expression' => '`k`.`playerId`', 'type' => 'integer', 'raw' => 'none' ],
            'name' => [ 'expression' => '`p`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'teamId' => [ 'expression' => '`p`.`teamId`', 'type' => 'integer', 'raw' => 'none' ],
            'tourney' => [ 'expression' => '`k`.`tourneyId`', 'type' => 'integer', 'raw' => '`tourneyId`' ],
            'teamName' => [ 'expression' => '`t`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'matches' => [ 'expression' => '`k`.`matches`', 'type' => 'integer', 'raw' => '`matches`' ],
            'wins' => [ 'expression' => '`k`.`wins`', 'type' => 'integer', 'raw' => '`wins`' ],
            'loss' => [ 'expression' => '`k`.`loss`', 'type' => 'integer', 'raw' => '`loss`' ],
            'missed' => [ 'expression' => '`k`.`missed`', 'type' => 'integer', 'raw' => '`missed`' ],
            'shots' => [ 'expression' => '`k`.`shots`', 'type' => 'integer', 'raw' => '`shots`' ],
            'shotsPerMatch' => [ 'expression' => 'ROUND(`k`.`shots`/`k`.`matches`, 2)', 'type' => 'integer', 'raw' => 'none' ],
            'saved' => [ 'expression' => '`k`.`saved`-`k`.`missed`', 'type' => 'integer', 'raw' => 'none' ],
            'savedPercent' => [ 'expression' => 'ROUND((`k`.`shots`-`k`.`missed`)/`k`.`shots`*100, 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'savedPerMatch' => [ 'expression' => 'ROUND((`k`.`shots`-`k`.`missed`)/`k`.`matches`, 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'reliability' => [ 'expression' => 'ROUND((`k`.`missed`*45)/`k`.`multy`, 2)', 'type' => 'decimal', 'raw' => 'none' ],
            'zero' => [ 'expression' => '`k`.`zero`', 'type' => 'integer', 'raw' => '`zero`' ],
            'playTime' => [ 'expression' => '`k`.`playTime`', 'type' => 'integer', 'raw' => '`playTime`' ],
            'multy' => [ 'expression' => '`k`.`multy`', 'type' => 'decimal', 'raw' => '`multy`' ]
        ];
        $this->modelClass = "KeeperStat";
    }

    public function search ( $params )
    {
//        $query = $this->createSelectQuery ( $params, "", "`t`.`name` ASC, `p`.`name` ASC" );
//        $players = SqlTools::selectObjects ( $query, $this->modelClass );
//        return $players;
//
//
//        $playerManager = new PlayerModel ( $this );
//        $amplua = $playerManager->getAmplua ();
        $whereClause = [ "`p`.`show`='Y'", "`p`.`deleted`='N'" ];
        $players = $this->doSearch ( $params, implode ( " AND ", $whereClause ), "`t`.`name` ASC, `p`.`name` ASC" );
        $ids = ArrayTools::pluck ( $players->data, "playerId" );
        $images = Starter::app ()->imager->getMainImages ( "Player", $ids );

        foreach ( $players->data as $player )
        {
            //$player->amplua = $amplua[$player->amplua];
            $player->image = $images[$player->playerId];
        }
        return $players;


    }

    public function getColumns ()
    {
        $columns =
        [
            'name' => [ 'colTitle' => 'Имя игрока', 'attr' => [ 'title' => 'Имя игрока' ] ],
            'teamName' => [ 'colTitle' => 'Команда', 'attr' => [ 'title' => 'Команда' ] ],
            'matches' => [ 'colTitle' => 'И', 'attr' => [ 'title' => 'Количество проведенных игр' ] ],
            'wins' => [ 'colTitle' => 'В', 'attr' => [ 'title' => 'Выигранные матчи' ] ],
            'loss' => [ 'colTitle' => 'П', 'attr' => [ 'title' => 'Проигранные матчи' ] ],
            'missed' => [ 'colTitle' => 'ПШ', 'attr' => [ 'title' => 'Пропущено шайб' ] ],
            //'shots' => [ 'colTitle' => 'БВ', 'attr' => [ 'title' => 'Броски по воротам' ] ],
            'shotsPerMatch' => [ 'colTitle' => 'БВ\И', 'attr' => [ 'title' => 'Среднее количество бросков за игру' ] ],
            'saved' => [ 'colTitle' => 'ОтрБВ', 'attr' => [ 'title' => 'Отраженные броски' ] ],
            'savedPercent' => [ 'colTitle' => '%ОБ', 'attr' => [ 'title' => 'Процент отраженных бросков' ] ],
            'savedPerMatch' => [ 'colTitle' => 'ОтрБВ/И', 'attr' => [ 'title' => 'Среднее количество отраженных бросков за игру' ] ],
            'reliability' => [ 'colTitle' => 'КН', 'attr' => [ 'title' => 'Коэффициент надежности' ] ],
            'zero' => [ 'colTitle' => 'И«0»', 'attr' => [ 'title' => 'Сухие игры' ] ],
            'playTime' => [ 'colTitle' => 'ВП', 'attr' => [ 'title' => 'Время на площадке' ] ],
            'multy' => [ 'colTitle' => 'Кэф', 'attr' => [ 'title' => 'Коэффициент' ] ]
        ];
        return $columns;
    }

    private function simulakr()
    {
        $str = "1,Шлыков Антон,Романтик,20,15,5,51,507,25.35,89.94,	456,	22.80,	2.55,	1,	  900:00,	900;
            2,	Кондратьев Кирилл,	Черная Жемчужина,	19,	13	,5	,60,	545,	29.60,	88.99,	485,	25.53	,3.26,	2	 , 828:38	,828.63;
            3	,Силин Кирилл	,Сибирские Песцы	,19,	10	,8	,60	,635	,34.80,	90.55,	575	,30.26,	3.29,	0	,  821:01,	821.02;
            4	,Тарасюк Алексей	,Сибирский Антрацит	,17,	9,	8	,51,	379	,22.23	,86.54	,328	,19.29,	2.99	,0	,  767:16	,767.27;
            5	,Грязин Дмитрий,Комета	,17	,16	,0	,33	,393	,24.38,	91.60,	360,	21.18,	2.05,	2,	  725:30	,725.5;
            6,	Володин Владимир,	НВИ ВВ МВД,	16	,3	,13,	96	,472	,29.75	,79.66	,376	,23.50,	6.05	,0	,  714:00	,714;
            7,	Чернаков Юрий,	Авиатор,	14	,7,	7,	53	,488,	34.86,	89.14,	435,	31.07,	3.79,	1	 , 630:00	,630;
            8	,Каюров Никита	,ЮКОС	,12	,7	,5	,41	,277	,23.08,	85.20,	236,	19.67,	3.42,	1,	  540:00,	540;
            9	,Леонов Вячеслав,	Новосибирск,	13,	5	,7,	46	,334	,28.15	,86.23,	288	,22.15,	3.88,	0	, 533:55,	533.92;
            10,	Рыбаков Дмитрий,	Вымпел	,10	,8,	2,	33,	306,	30.60,	89.22,	273,	27.30,	3.30	,0,	  450:00	,450;
            11,	Ткаченко Никита,	Общее Дело	,9,	1,	8,	70,	327,	36.33,	78.59,	257,	28.56,	7.78,	0,	  405:00,	405;
            12,	Паровенко Владислав,	Райво,	9,	1,	8,	62,	310	,35.77,	80.00,	248,	27.56	,7.15,	0,	  390:00,	390;
            13,	Федоров Алексей,	Новосибирск	,13,	3,	4,	44	,271,	32.38	,83.76,	227,	17.46,	5.26,	0,	  376:36,	376.6;
            14,	Андрикенус Игорь	,ЮКОС,	8	,3,	5,	28,	206,	25.05	,86.41,	178	,22.25,	3.41,	1,	  370:00,	370;
            15,	Берестов Алексей,	Авиатор,	8,	3,	5,	28,	238,	29.75,	88.24,	210,	26.25,	3.50	,0	,  360:00,	360;
            16,	Максимов Станислав,	Вымпел,	7,	3,	3,	31,	166,	26.21,	81.33,	135	,19.29,	4.89,	0,	  285:00,	285;
            17	,Поваляев Денис,	Райво	,7,	1,	5,	40,	188,	29.68,	78.72,	148,	21.14,	6.32	,0,	  285:00,	285;
            18,	Александров Кирилл,	Общее Дело,	6,	0,	6,	44,	195,	32.50,	77.44,	151,	25.17,	7.33,	0,	  270:00	,270;
            19,	Кишов Павел,	Вымпел,	5,	3	,2,	22,	131,	28.07,	83.21,	109,	21.80	,4.71,	0,	  210:00,	210;
            20,	Зимин Максим,	Сибирский Антрацит,	8,	2,	2,	20,	115	,29.18,	82.61,	95,	11.88,	5.08	,0	  ,177:44,	177.33;
            21,	Колмогоров Александр,	Комета,	6,	3,	1,	9,	105,	27.08,	91.43,	96,	16.00,	2.32,	1,	 174:30,	174.5";

        $keys = [ "id", "name", "teamName", "matches","wins","loss","missed","shots","shotsPerMatch","saved","savedPercent","savedPerMatch",
                "reliability", "zero","playTime","multy"];

        $array = preg_split('/\s*;\s*/', $str);
        $res = [];
        foreach($array as $row)
        {
            $temp = preg_split('/\s*,\s*/', $row);
            $stat = (object) array_combine($keys, $temp);
            $stat->teamId = 8;
            $res[] = $stat;
        }
        return $res;
    }
}

class KeeperStat
{
    public $id;
    public $playerId;
    public $name;
    public $num;
    public $image;
    public $teamId;
    public $teamName;
    public $matches;
    public $wins;
    public $loss;
    public $missed;
    public $shots;
    public $shotsPerMatch;
    public $saved;
    public $savedPercent;
    public $savedPerMatch;
    public $reliability;
    public $zero;
    public $playTime;
    public $multy;
    public $date;
    public $view;
    public $deleted;
    public $created;
    public $modified;
}
