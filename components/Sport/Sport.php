<?php
/**
 * Description of Sport
 *
 * @author kraser
 */
class Sport extends CmsModule
{
    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->defaultController = 'team';
        $this->template = "page";
        Starter::import ( "Sport.controllers.*" );
        Starter::import ( "Sport.models.*" );
        $this->actions =
        [
            'default' =>
            [
                'method' => 'teamsList'
            ],
            'teams' =>
            [
                'method' => 'teamsList'
            ],
            'team' =>
            [
                'method' => 'teamView'
            ],
            'players' =>
            [
                'method' => 'playersList'
            ],
            'player' =>
            [
                'method' => 'playerView'
            ],
            'playerstat' =>
            [
                'method' => 'playerStatView'
            ],
            'keeperstat' =>
            [
                'method' => 'keeperStatView'
            ],
            "results" =>
            [
                'method' => 'tourneyResults'
            ],
            'leaders' =>
            [
                'method' => 'leaders'
            ]
        ];
    }

    public function Run ()
    {
        $action = $this->createAction ();
        if ( !$action )
            page404 ();
        $content = $action->run ();
        return $content;
    }

    public function startController ( $method, $params )
    {
        return $this->$method ( $params );
    }

    public function beforeRender ()
    {
        if ( parent::beforeRender () )
        {
            $assetPath = str_replace ( Starter::getAliasPath('webroot'), "", $this->basePath ) . "/assets" ;
            $assets = DS . SITE . DS . Starter::app ()->getTheme () . "/assets" ;
            $header = Starter::app ()->headManager;
            $header->addJs ( $assetPath . '/js/sortManager.js');
            $header->addJs ( $assets . '/js/jquery.bracket.js');
            //$header->addJs ( $assets . '/js/jquery.bracket.min.js');
            return true;
        }
        else
            return false;
    }

    private function teamsList( $search )
    {
        $this->title = $this->currentDocument->title;
        $this->model = "Team";
        $manager = new TeamModel ( $this );
        $teams = $manager->search ( $search );
        $teamIds = ArrayTools::pluck ( $teams, "id" );
        $images = Starter::app ()->imager->getMainImages ( "Team", $teamIds );
        $params =
        [
            'teams' => $teams,
            'images' => $images,
            'columns' => $manager->getColumns (),
        ];
        return $this->render ( 'teamslist', $params );
    }

    private function playersList ( $search )
    {
        $teamManager = new TeamModel ( $this );
        $team = ArrayTools::head ( $teamManager->search ( $search ) );
        $this->title = $team->name;
        $this->model = "Player";
        $manager = new PlayerModel ();
        $players = $manager->search ( $search );
        $playersIds = ArrayTools::pluck ( $players, "id" );
        $images = Starter::app ()->imager->getMainImages ( "Player", $playersIds );
        $params =
        [
            'images' => $images,
            'players' => $players,
            'columns' => $manager->getColumns ()
        ];
        return $this->render ( 'playerslist', $params );
    }

    private function playerView ( $search )
    {
        $manager = new PlayerModel ();
        $player = ArrayTools::head ( $manager->search ( $search ) );
        $this->title = $player->name;
        $this->model = "Player";
        $this->template = "playerPage";
        $images = Starter::app ()->imager->getMainImages ( "Player", [ $player->id ] );
        $statManager = $player->amplua !== 'Вратарь' ? new PlayerStatModel ( $this ) : new KeeperStatModel ( $this );
        $stata = $statManager->search ( [ 'playerId' => $player->id ] );
        $params =
        [
            'images' => $images,
            'player' => $player,
            'stata' => $stata,
            'columns' => $manager->getColumns (),
            'statColumns' => $statManager->getColumns ()
        ];
        return $this->render ( 'playerview', $params );
    }

    private function playerStatView ( $search )
    {
        $manager = new PlayerStatModel ( $this );
        $tourneysManager = new TourneyModel ( $this );
        if ( array_key_exists ( "tourney", $search ) )
            $seasonId = $search['tourney'];
        else
        {
            $seasonId = 0;
            $search['tourney'] = $tourneysManager->defaultTourneyId ();
        }
        $stat = $manager->search ( $search );
        $this->title = "Статистика полевых игроков";
        $this->model = "PlayerStat";
        $params =
        [
            'stata' => $stat,
            'seasons' => $tourneysManager->getTourneys ( $seasonId ),
            'columns' => $manager->getColumns ()
        ];
        return $this->render ( 'playersStat', $params );
    }

    private function keeperStatView ( $search )
    {
        $manager = new KeeperStatModel ( $this );
        $tourneysManager = new TourneyModel ( $this );
        if ( array_key_exists ( "tourney", $search ) )
            $seasonId = $search['tourney'];
        else
        {
            $seasonId = 0;
            $search['tourney'] = $tourneysManager->defaultTourneyId ();
        }
        $keepers = $manager->search ( $search );
        $this->title = "Статистика вратарей";
        $this->model = "KeeperStat";
        $params =
        [
            'keepers' => $keepers,
            'seasons' => $tourneysManager->getTourneys ( $seasonId ),
            'columns' => $manager->getColumns ()
        ];
        return $this->render ( 'keepersStat', $params );
    }

    private function tourneyResults ( $search )
    {
        $manager = new TourneyModel ( $this );
        if ( array_key_exists ( "tourney", $search ) )
            $seasonId = $search['tourney'];
        else
        {
            $seasonId = 0;
            $search['tourney'] = $manager->defaultTourneyId ();
        }
        $tourneys = $manager->getTourneys ( $seasonId );
        $type = $manager->selectedTourney->type;
        $result = $manager->search ( $search );
        $this->title = $type == "regular" ? "Регулярный чемпионат" : "Плэйофф";
        $this->model = "Tourney";
        $this->template = "regularView";
        $params =
        [
            'type' => $type,
            'result' => $result['rows'],
            'seasons' => $tourneys,
            'columns' => $result['columns']
        ];
        return $this->render ( 'regular', $params );
    }

    private function leaders ()
    {
        $this->title = "Лидеры";
        $nominationManager = new NominantModel ( $this );
        $leaders = $nominationManager->getAllNominations ();
        return $this->render ( 'leaders', [ 'leaders' => $leaders ] );
    }

    public function createLink ( $action, $params )
    {
        return Starter::app ()->content->getLinkById ( $this->currentDocument->id );
    }
}
