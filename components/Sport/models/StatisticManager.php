<?php
/**
 * <pre>Класс StatisticManager загрузчик статистики из xls-файлов</pre>
 * @author kraser
 */
class StatisticManager extends CmsObject
{
    /**
     * @var Array<p>Ассоциаивный массив игроков Имя => ID  </p>
     */
    private $players;

    /**
     * @var Array<p>Ассоциаивный массив команд Имя => ID  </p>
     */
    private $teams;

    /**
     * @var Date <p>Статистическая дата</p>
     */
    private $month;

    /**
     * @var Integer <p>ID турнира</p>
     */
    private $tourneyId;

    /**
     * @var String <p>Что именно загружаем</p>
     */
    private $uploadMode;

    /**
     * @var XlsParser <p> Парсер xls-файлов</p>
     */
    private $parser;

    /**
     * @var Array of Mixed <p>Массив выходных объектов</p>
     */
    private $currentStatList;

    /**
     * <pre>Конструктор</pre>
     */
    public function __construct ()
    {
        parent::__construct();
        Starter::import ( "Sport.models.PlayerStatModel", true );
        Starter::import ( "Sport.models.KeeperStatModel", true );
        Starter::import ( "Sport.models.TeamStatModel", true );
        Starter::import ( "Sport.models.PlayerModel", true );
        Starter::import ( "Sport.models.TourneyModel", true );
        $this->players = [];
        $this->teams = [];
    }

    /**
     * <pre>Инициализирует парсер и запускает его на выполнение</pre>
     * @param String $pathName <p>Полеый путь к файлу с данными</p>
     * @param String $stat <p>Режим загрузки</p>
     */
    public function uploadStatFromFile ( $pathName, $stat, $tourneyId )
    {
        $this->tourneyId = $tourneyId;
        $this->parser = Starter::app ()->getComponent ( "xlsReader" );
        switch ( $stat )
        {
            case 'players':
                $className = 'PlayerStat';
                $header = $this->getPlayerStatHeader ();
                $statValidator = [ $this, 'playerStatValidator' ];
                $sheetNameHandler = [ $this, 'playerSheetNameHandler' ];
                $saveResult = 'updatePlayerStat';
                break;
            case 'keepers':
                $className = 'KeeperStat';
                $header = $this->getKeeperStatHeader ();
                $statValidator = [ $this, 'keeperStatValidator' ];
                $sheetNameHandler = [ $this, 'sheetNameHandler' ];
                $saveResult = 'updateKeeperStat';
                break;
            case 'teams':
                $className = 'teamStat';
                $header = $this->getTeamStatHeader ();
                $statValidator = [ $this, 'teamStatValidator' ];
                $sheetNameHandler = [ $this, 'sheetNameHandler' ];
                $saveResult = 'updateTeamStat';
                break;
            case 'prop':
                $className = 'Player';
                $header = $this->getPropHeader ();
                $statValidator = [ $this, 'propValidator' ];
                $sheetNameHandler = [ $this, 'propSheetNameHandler' ];
                $saveResult = 'updateProp';
                break;
            case 'tourney':
                $className = 'Tourney';
                $header = $this->getTourneyHeader ();
                $statValidator = [ $this, 'tourneyValidator' ];
                $sheetNameHandler = [ $this, 'tourneySheetNameHandler' ];
                $saveResult = 'updateTourney';
                break;
            default:
                return false;
        }
        $this->uploadMode = $stat;
        $this->parser->setWorkSpace ( $className, $header, $pathName, $statValidator, $sheetNameHandler );
        $this->parser->run ();
        $this->$saveResult ();
        return true;
    }

    /**
     * @return PlayerStat <p>Заголовок для разбора статистики игроков</p>
     */
    private function getPlayerStatHeader ()
    {
        $header = new PlayerStat ();
        $header->name = "A";
        $header->amplua = 'B';
        $header->teamName = "C";
        $header->matches = 'D';
        $header->goals = 'E';
        $header->pass = 'F';
        $header->utility = 'H';
        $header->penaltyTime = 'I';
        $header->winGoals = 'M';
        $header->equalGoals = 'J';
        $header->majorityGoals = 'K';
        $header->minorityGoals = 'L';
        $header->overtimeGoals = '';
        $header->winBullit = 'N';
        $header->shots = 'O';
        $header->throws = 'R';
        $header->winThrows = 'S';
        return $header;
    }

    /**
     * <pre>Обработчик объектов статистики игроков</pre>
     * @param PlayerStat $item <p></p>
     * @return void
     */
    public function playerStatValidator ( $item )
    {
        if ( !$item->name || !$item->teamName || $item->name == 'Игрок' || $item->name == 'Общие показатели' )
            return;

        $item->date = $this->month;
    }

    /**
     * <pre>Обработчик имение листа<pre>
     * @param String $sheetName <p>Имя листа</p>
     * @return Boolean
     */
    public function playerSheetNameHandler ( $sheetName )
    {
        if ( $sheetName === "Общее")
            return false;
        else
            return $this->sheetNameHandler ( $sheetName );
    }

    /**
     * <pre>Запись статистики игроков в БД</pre>
     */
    private function updatePlayerStat ()
    {
        $playersManager = new PlayerModel ( $this );
        $teamsManager = new TeamModel ( $this );
        $teams = $teamsManager->search ( [] );
        foreach ( $teams as $team )
        {
            $this->teams[$team->name] = $team->id;
        }
        $players = $playersManager->search ( [] );
        foreach ( $players as $player )
        {
            $this->players[$player->name] = $player->id;
        }
        foreach ( $this->currentStatList as $record )
        {
            if ( array_key_exists ( $record->teamName, $this->teams ) )
                $record->teamId = $this->teams[$record->teamName];
            else
            {
                $teamModel = new stdClass();
                $teamModel->name = $record->teamName;
                $teamId = $teamsManager->createModelItem ( $teamModel );
                $this->teams[$record->teamName] = $teamId;
                $record->teamId = $teamId;
            }

            if ( array_key_exists ( $record->name, $this->players ) )
                $record->playerId = $this->players[$record->name];
            else
            {
                $playerModel = new stdClass();
                $playerModel->name = $record->name;
                $playerModel->teamId = $record->teamId;
                $playerModel->amplua = $record->amplua;
                $playerId = $playersManager->createModelItem ( $playerModel );
                $this->players[$record->name] = $playerId;
                $record->playerId = $playerId;
            }

            $query = "INSERT INTO `prefix_playerStat`
                (
                    `playerId`,
                    `matches`,
                    `goals`,
                    `pass`,
                    `utility`,
                    `penaltyTime`,
                    `winGoals`,
                    `equalGoals`,
                    `majorityGoals`,
                    `minorityGoals`,
                    `overtimeGoals`,
                    `winBullit`,
                    `shots`,
                    `throw`,
                    `winThrow`,
                    `date`,
                    `seasonId`
                    `created`,
                    `modified`
                )
                VALUES
                (

                    $record->playerId,
                    " . intval ( $record->matches ) . ",
                    " . intval ( $record->goals ) . ",
                    " . intval ( $record->pass ) . ",
                    " . intval ( $record->utility ) . ",
                    " . intval ( $record->penaltyTime ) . ",
                    " . intval ( $record->winGoals ) . ",
                    " . intval ( $record->equalGoals ) . ",
                    " . intval ( $record->majorityGoals ) . ",
                    " . intval ( $record->minorityGoals ) . ",
                    " . intval ( $record->overtimeGoals ) . ",
                    " . intval ( $record->winBullit ) . ",
                    " . intval ( $record->shots ) . ",
                    " . intval ( $record->throw ) . ",
                    " . intval ( $record->winThrow ) . ",
                    '$record->date',
                    " . intval ( $this->tourneyId ) . ",
                    NOW(),
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    `matches`=" . intval ( $record->matches ) . ",
                    `goals`=" . intval ( $record->goals ) . ",
                    `pass`=" . intval ( $record->pass ) . ",
                    `utility`=" . intval ( $record->utility ) . ",
                    `penaltyTime`=" . intval ( $record->penaltyTime ) . ",
                    `winGoals`=" . intval ( $record->winGoals ) . ",
                    `equalGoals`=" . intval ( $record->equalGoals ) . ",
                    `majorityGoals`=" . intval ( $record->majorityGoals ) . ",
                    `minorityGoals`=" . intval ( $record->minorityGoals ) . ",
                    `overtimeGoals`=" . intval ( $record->overtimeGoals ) . ",
                    `winBullit`=" . intval ( $record->winBullit ) . ",
                    `shots`=" . intval ( $record->shots ) . ",
                    `throw`=" . intval ( $record->throw ) . ",
                    `winThrow`=" . intval ( $record->winThrow ) . ",
                    `modified`=NOW()";

            SqlTools::insert ( $query );
        }
    }

    /**
     * @return KeeperStat <p>Заголовок для разбора статистики вратарей</p>
     */
    private function getKeeperStatHeader ()
    {
        $header = new KeeperStat ();
        $header->name = "C";
        $header->teamName = "D";
        $header->matches = 'E';
        $header->wins = "F";
        $header->loss = "G";
        $header->missed = "H";
        $header->shots = "I";
        $header->saved = "L";
        $header->zero = "O";
        $header->playTime = "P";
        $header->multy = "Q";

        return $header;
    }

    /**
     * <pre>Обработчик объектов статистики вратарей</pre>
     * @param KeeperStat $item <p></p>
     * @return void
     */
    public function keeperStatValidator ( $item )
    {
        if ( !$item->name || !$item->teamName || $item->name == 'Игрок' || !$item->teamName == 'Клуб' || is_numeric ( $item->teamName ) )
            return;

        $item->date = $this->month;
    }

    /**
     * <pre>Обработчик имение листа<pre>
     * @param String $sheetName <p>Имя листа</p>
     * @return Boolean
     */
    public function sheetNameHandler ( $sheetName )
    {
        $monthNum = DatetimeTools::monthNum ( $sheetName );
        if ( is_numeric ( $monthNum ) )
        {
            if ( $monthNum <= date('n') )
                $this->month = date ( "Y-m-d", mktime(0, 0, 0, $monthNum, 1, date('Y') ) );
            else
                $this->month = date ( "Y-m-d", mktime(0, 0, 0, $monthNum, 1, date('Y') - 1 ) );
        }
        else
            $this->month = date ("Y-m-d");

        return true;
    }

    /**
     * <pre>Запись статистики вратарей в БД</pre>
     */
    private function updateKeeperStat ()
    {
        $playersManager = new PlayerModel ( $this );
        $teamsManager = new TeamModel ( $this );
        $teams = $teamsManager->search ( [] );
        foreach ( $teams as $team )
        {
            $this->teams[$team->name] = $team->id;
        }
        $players = $playersManager->search ( [ 'amplua' => 'GOALKEEPER' ] );
        foreach ( $players as $player )
        {
            $this->players[$player->name] = $player->id;
        }
        foreach ( $this->currentStatList as $record )
        {
            if ( array_key_exists ( $record->teamName, $this->teams ) )
                $record->teamId = $this->teams[$record->teamName];
            else
            {
                $teamModel = new stdClass();
                $teamModel->name = $record->teamName;
                $teamId = $teamsManager->createModelItem ( $teamModel );
                $this->teams[$record->teamName] = $teamId;
                $record->teamId = $teamId;
            }

            if ( array_key_exists ( $record->name, $this->players ) )
                $record->playerId = $this->players[$record->name];
            else
            {
                $playerModel = new stdClass();
                $playerModel->name = $record->name;
                $playerModel->teamId = $record->teamId;
                $playerModel->amplua = 'Вратарь';
                $playerId = $playersManager->createModelItem ( $playerModel );
                $this->players[$record->name] = $playerId;
                $record->playerId = $playerId;
            }

            $query = "INSERT INTO `prefix_keeperStat`
                (
                    `playerId`,
                    `matches`,
                    `wins`,
                    `loss`,
                    `missed`,
                    `shots`,
                    `saved`,
                    `zero`,
                    `playTime`,
                    `multy`,
                    `tourneyId`
                    `created`,
                    `modified`
                )
                VALUES
                (
                    " . intval ( $record->playerId ) . ",
                    " . intval ( $record->matches ) . ",
                    " . intval ( $record->wins ) . ",
                    " . intval ( $record->loss ) . ",
                    " . intval ( $record->missed ) . ",
                    " . intval ( $record->shots ) . ",
                    " . intval ( $record->saved ) . ",
                    " . intval ( $record->zero ) . ",
                    '" . $record->playTime . "',
                    '" . floatval ( $record->multy ) . "',
                    " . intval ( $this->tourneyId ) . ",
                    NOW(),
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    `matches`=" . intval ( $record->matches ) . ",
                    `wins`=" . intval ( $record->wins ) . ",
                    `loss`=" . intval ( $record->loss ) . ",
                    `missed`=" . intval ( $record->missed ) . ",
                    `shots`=" . intval ( $record->shots ) . ",
                    `saved`=" . intval ( $record->saved ) . ",
                    `zero`=" . intval ( $record->zero ) . ",
                    `playTime`='" . $record->playTime . "',
                    `multy`=" . floatval ( $record->multy ) . ",
                    `modified`=NOW()
                    ";
            SqlTools::insert ( $query );
        }
    }

    /**
     * @return TeamStat <p>Заголовок для разбора статистики команд</p>
     */
    private function getTeamStatHeader ()
    {
        $header = new TeamStat ();
        $header->id = 'B';
        $header->name = 'C';
        $header->matches = 'D';
        $header->majority = 'E';
        $header->majorityGoals = 'F';
        $header->majorityMisses = 'G';
        $header->minority = 'I';
        $header->minorityGoals = 'J';
        $header->minorityMisses = 'K';
        $header->penaltyTime = 'M';
        $header->contestPenaltyTime = 'O';
        $header->goalless = 'Q';
        $header->missless = 'R';
        $header->win = 'U';

        return $header;
    }

    /**
     * <pre>Обработчик объектов статистики команд</pre>
     * @param TeamStat $item <p></p>
     * @return void
     */
    public function teamStatValidator ( $item )
    {
        if ( !$item->name || $item->name == 'Команда' )
            return;

        if ( stripos ( 'первом', mb_strtolower ( $item->id ) ) )
            $item->period = 1;
        else if ( stripos ( 'втором', mb_strtolower ( $item->id ) ) )
            $item->period = 2;
        else if ( stripos ( 'третьем', mb_strtolower ( $item->id ) ) )
            $item->period = 3;

        unset ( $item->id );
        list ( $win, $draw, $loss ) = explode ( "-", $item->win );
        $item->win = $win;
        $item->draw = $draw;
        $item->loss = $loss;
        $item->date = $this->month;
        $this->currentStatList[] = $item;
    }

    /**
     * <pre>Запись статистики команд в БД</pre>
     */
    public function updateTeamStat ()
    {
        $teamsManager = new TeamModel ( $this );
        $teams = $teamsManager->search ( [] );
        foreach ( $teams as $team )
        {
            $this->teams[$team->name] = $team->id;
        }
        foreach ( $this->currentStatList as $record )
        {
            if ( array_key_exists ( $record->name, $this->teams ) )
                $record->teamId = $this->teams[$record->name];
            else
            {
                $teamModel = new stdClass();
                $teamModel->name = $record->name;
                $teamId = $teamsManager->createModelItem ( $teamModel );
                $this->teams[$record->name] = $teamId;
                $record->teamId = $teamId;
            }
            $query = "INSERT INTO `prefix_byPeriodStat` SET
                    `teamId`=" . intval ( $record->teamId ) . ",
                    `period`=" . intval ( $record->period ) . ",
                    `majority`=" . intval ( $record->majority ) . ",
                    `majorityGoals`=" . intval ( $record->majorityGoals ) . ",
                    `majorityMisses`=" . intval ( $record->majorityMisses ) . ",
                    `minority`=" . intval ( $record->minority ) . ",
                    `minorityGoals`=" . intval ( $record->minotiryGoals ) . ",
                    `minorityMisses`=" . intval ( $record->minorityMisses ) . ",
                    `penaltyTime`=" . intval ( $record->penaltyTime ) . ",
                    `contestPenaltyTime`=" . intval ( $record->contestPenaltyTime ) . ",
                    `goalless`=" . intval ( $record->goalless ) . ",
                    `missless`=" . intval ( $record->missless ) . ",
                    `win`=" . intval ( $record->win ) . ",
                    `draw`=" . intval ( $record->draw ) . ",
                    `loss`=" . intval ( $record->loss ) . ",
                    `date`='" . $record->date . "',
                    `created`=NOW(),
                    `modified`=NOW()
                ON DUPLICATE KEY UPDATE
                    `majority`=" . intval ( $record->majority ) . ",
                    `majorityGoals`=" . intval ( $record->majorityGoals ) . ",
                    `majorityMisses`=" . intval ( $record->majorityMisses ) . ",
                    `minority`=" . intval ( $record->minority ) . ",
                    `minorityGoals`=" . intval ( $record->minorityGoals ) . ",
                    `minorityMisses`=" . intval ( $record->minorityMisses ) . ",
                    `penaltyTime`=" . intval ( $record->penaltyTime ) . ",
                    `contestPenaltyTime`=" . intval ( $record->contestPenaltyTime ) . ",
                    `goalless`=" . intval ( $record->goalless ) . ",
                    `missless`=" . intval ( $record->missless ) . ",
                    `win`=" . intval ( $record->win ) . ",
                    `draw`=" . intval ( $record->draw ) . ",
                    `loss`=" . intval ( $record->loss ) . ",
                    `modified`=NOW()";

            SqlTools::insert ( $query );
        }
    }

    /**
     * @return Player <p>Заголовок для разбора параметров игроков</p>
     */
    private function getPropHeader ()
    {
        $header = new Player ();
        $header->teamName = 'A';
        $header->amplua = 'D';
        $header->name = 'B';
        $header->birthdate = 'C';
        $header->num = 'E';
        $header->weight = 'G';
        $header->height = 'F';
        $header->status = 'H';
        $header->grip = 'I';

        return $header;
    }

    /**
     * <pre>Обработчик объектов физических данных игроков</pre>
     * @param Player $item <p></p>
     * @return void
     */
    public function propValidator ( $item )
    {
        if ( !$item->name || $item->name == 'Фамилия Имя Отчество' )
            return;

        list ( $family, $name, $surname ) = explode ( " ", $item->name );
        $item->name = $family . " " . $name;
        $item->teamName = $this->team;
        list ( $day, $month, $year ) = explode ( '.', $item->birthdate );
        $item->birthdate = date ( "Y-m-d", mktime ( 0, 0, 0, $month, $day, $year ) );

        $this->currentStatList[] = $item;
    }

    /**
     * <pre>Обработчик имени листа<pre>
     * @param String $sheetName <p>Имя листа</p>
     * @return Boolean
     */
    public function propSheetNameHandler ( $sheetName )
    {
        $this->team = $sheetName;

        return true;
    }

    /**
     * <pre>Запись параметров игроков в БД</pre>
     */
    public function updateProp ()
    {
        $teamsManager = new TeamModel ( $this );
        $playersManager = new PlayerModel ( $this );
        $teams = $teamsManager->search ( [] );
        foreach ( $teams as $team )
        {
            $this->teams[$team->name] = $team->id;
        }
        $players = $playersManager->search ( [] );
        foreach ( $players as $player )
        {
            $this->players[$player->name] = $player->id;
        }
        foreach ( $this->currentStatList as $record )
        {
            if ( array_key_exists ( $record->teamName, $this->teams ) )
                $record->teamId = $this->teams[$record->teamName];
            else
            {
                $teamModel = new stdClass();
                $teamModel->name = $record->teamName;
                $teamId = $teamsManager->createModelItem ( $teamModel );
                $this->teams[$record->teamName] = $teamId;
                $record->teamId = $teamId;
            }

            if ( array_key_exists ( $record->name, $this->players ) )
                $record->id = $this->players[$record->name];
            else
            {
                $playerModel = new stdClass();
                $playerModel->name = $record->name;
                $playerModel->teamId = $record->teamId;
                $playerModel->amplua = $record->amplua;
                $playerId = $playersManager->createModelItem ( $playerModel );
                $this->players[$record->name] = $playerId;
                $record->id = $playerId;
            }

            $query = "UPDATE `prefix_players` SET
                    `birthDate`='" . $record->birthdate . "',
                    `num`=" . intval ( $record->num ) . ",
                    `weight`=" . floatval ( $record->weight ) . ",
                    `height`=" . floatval ( $record->height ) . ",
                    `status`='" . CharTools::upperFirst ( $record->status ) . "',
                    `grip`='" . mb_strtolower ( $record->grip ) . "',
                    `modified`=NOW()
                WHERE `id`=$record->id";

            SqlTools::execute ( $query );
        }
    }

    /**
     * @return Player <p>Заголовок для разбора итоговой таблицы</p>
     */
    private function getTourneyHeader ()
    {
        $header = new Tourney ();
        $header->teamName = 'E';
        $header->matches = 'F';
        $header->wins = 'G';
        $header->winsByBullit = 'H';
        $header->lossByBullit = 'I';
        $header->loss = 'J';
        $header->goals = 'K';
        $header->score = 'L';
        $header->place = 'C';

        return $header;
    }


    /**
     * <pre>Обработчик объектов строк турнирной таблицы</pre>
     * @param Torney $item <p></p>
     * @return void
     */
    public function tourneyValidator ( $item )
    {
        if ( !$item->teamName || $item->teamName == 'Клуб' )
            return;

        list ( $goals, $misses ) = explode ( "-", $item->goals );
        $item->goals = $goals;
        $item->misses = $misses;

        $this->currentStatList[] = $item;
    }

    /**
     * <pre>Обработчик имение листа<pre>
     * @param String $sheetName <p>Имя листа</p>
     * @return Boolean
     */
    public function tourneySheetNameHandler ( $sheetName )
    {
        if ( $sheetName == "Лист1" )
            return true;
        else
            return false;
    }

    /**
     * <pre>Запись турнирной таблицы в БД</pre>
     */
    public function updateTourney ()
    {
        $teamsManager = new TeamModel ( $this );
        $teams = $teamsManager->search ( [] );
        foreach ( $teams as $team )
        {
            $this->teams[$team->name] = $team->id;
        }
        foreach ( $this->currentStatList as $record )
        {
            if ( array_key_exists ( $record->teamName, $this->teams ) )
                $record->teamId = $this->teams[$record->teamName];
            else
            {
                $teamModel = new stdClass();
                $teamModel->name = $record->teamName;
                $teamId = $teamsManager->createModelItem ( $teamModel );
                $this->teams[$record->teamName] = $teamId;
                $record->teamId = $teamId;
            }

            $query = "INSERT INTO `prefix_tourneyResults` SET
                    `teamId`=" . intval ( $record->teamId ) . ",
                    `tourneyId`=" . intval ( $this->tourneyId ) . ",
                    `matches`=" . intval ( $record->matches ) . ",
                    `wins`=" . intval ( $record->wins ) . ",
                    `winsByBullit`=" . intval ( $record->winsByBullit ) . ",
                    `lossByBullit`=" . intval ( $record->lossByBullit ) . ",
                    `loss`=" . intval ( $record->loss ) . ",
                    `goals`=" . intval ( $record->goals ) . ",
                    `misses`=" . intval ( $record->misses ) . ",
                    `scores`=" . intval ( $record->score ) . ",
                    `place`=" . intval ( $record->place ) . ",
                    `created`=NOW(),
                    `modified`=NOW()
                ON DUPLICATE KEY UPDATE
                    `matches`=" . intval ( $record->matches ) . ",
                    `wins`=" . intval ( $record->wins ) . ",
                    `winsByBullit`=" . intval ( $record->winsByBullit ) . ",
                    `lossByBullit`=" . intval ( $record->lossByBullit ) . ",
                    `loss`=" . intval ( $record->loss ) . ",
                    `goals`=" . intval ( $record->goals ) . ",
                    `misses`=" . intval ( $record->misses ) . ",
                    `scores`=" . intval ( $record->score ) . ",
                    `place`=" . intval ( $record->place ) . ",
                    `modified`=NOW()";

            SqlTools::insert ( $query );
        }
    }
}


