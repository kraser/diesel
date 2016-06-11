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
        parent::__construct ( 'Tourney', $parent );
        $this->table = 'tourneyResults';
        $this->modelClass = "Tourney";;
    }

    public function search ( $params )
    {
        $searcherName = $this->selectedTourney->type == 'regular' ? 'RegularModel' : 'PlayoffModel';
        $searcher = new $searcherName ( $this );
        $response = [];
        $response['rows'] = $searcher->search ( $params );
        $response['columns'] = $searcher->getColumns ();
        return $response;
    }

    private $tourneys;
    public function getTourneys ( $selected )
    {
        if ( $this->tourneys)
            return $this->tourneys;

        if ( $selected && $selected != 0 )
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
        $images = Starter::app ()->imager->getMainImages ( $this->modelClass, array_keys ( $this->tourneys ) );
        foreach ( $this->tourneys as $tourney )
        {
            $tourney->image = $images[$tourney->id];
        }
        return $this->tourneys;
    }

    private $selectedTourney;
    public function defaultTourneyId ()
    {
        $this->getTourneys( 0 );
        $selected = ArrayTools::head ( ArrayTools::select ( $this->tourneys, 'selected', 1 ) );
        if ( !$selected )
        {
            $selected = ArrayTools::tail ( $this->tourneys );
            $selected->selected = 1;
        }
        $this->selectedTourney = $selected;
        return $selected->id;
    }

    public function getSelectedTourney ()
    {
        if ( !$this->selectedTourney )
            $this->selectedTourney = ArrayTools::head ( ArrayTools::select ( $this->tourneys, 'selected', 1 ) );

        return $this->selectedTourney;
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