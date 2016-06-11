<?php
/**
 * Description of NominantModel
 *
 * @author kraser
 */
class NominantModel
{
    private $parent;
    private $nominations;
    private $quantity;
    public function __construct ( $parent )
    {
        $this->parent = $parent;
        $this->nominations =
        [
            'goalscorers' =>
            [
                'title' => 'Бомбардиры',
                'result' => 'набранные очки',
                'explain' => 'Хоккеисты, набравшие наибольшее количество результативных баллов (очков), определяемых как сумма заброшенных шайб (голов) и результативных передач',
                'criteria' => 'scores.desc',
                'extended' =>
                [
                    [ 'explain' => 'Набранные очки', 'result' => 'scores' ],
                    [ 'explain' => 'Заброшенные шайбы', 'result' => 'goals' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Плюс/минус', 'result' => 'utility' ]
                ]
            ],
            'snipers' =>
            [
                'title' => 'Снайперы',
                'result' => 'заброшенные шайбы',
                'explain' => 'Хоккеисты, имеющие на своем счету наибольшее количество заброшенных шайб (голов)',
                'criteria' => 'goals.desc',
                'extended' =>
                [
                    [ 'explain' => 'Заброшенные шайбы', 'result' => 'goals' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Плюс/минус', 'result' => 'utility' ],
                    [ 'explain' => 'Минут штрафа', 'result' => 'penaltyTime' ]
                ]
            ],
            'assistants' =>
            [
                'title' => 'Ассистенты',
                'result' => 'голевые передачи',
                'explain' => 'Хоккеисты, набравшие наибольшее количество голевых передач',
                'criteria' => 'pass.desc',
                'extended' =>
                [
                    [ 'explain' => 'Голевые передачи', 'result' => 'pass' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Плюс/минус', 'result' => 'utility' ],
                    [ 'explain' => 'Минут штрафа', 'result' => 'penaltyTime' ]
                ]
            ],
            'utility' =>
            [
                'title' => 'Плюс/Минус',
                'result' => 'плюс/минус',
                'explain' => 'Полевые игроки с наивысшим показателем полезности, который отражает разность заброшенных и пропущенных командой шайб в то время, когда хоккеист находился на льду. Учитывается при игре в равных составах (плюс или минус), а также при заброшенных шайбах в меньшинстве (плюс) и пропущенных в большинстве (минус)',
                'criteria' => 'utility.desc',
                'extended' =>
                [
                    [ 'explain' => 'Плюс/минус', 'result' => 'utility' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Набранные очки', 'result' => 'scores' ],
                    [ 'explain' => 'Минут штрафа', 'result' => 'penaltyTime' ]


                ]
            ],
            'reliability' =>
            [
                'title' => 'Вратари (КН)',
                'result' => 'коэффициент надежности',
                'explain' => 'Вратари с наименьшим средним количеством пропущенных шайб за игру, с учётом суммарного игрового времени',
                'criteria' => 'reliability.asc',
                'extended' =>
                [
                    [ 'explain' => 'Коэффициент надежности', 'result' => 'reliability' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Количество побед', 'result' => 'wins' ],
                    [ 'explain' => '% отраженных бросков', 'result' => 'savedPercent' ]
                ]
            ],
            'savedPercent' =>
            [
                'title' => "Вратари (%ОБ)",
                'result' => '%отраженных бросков',
                'explain' => 'Вратари с наибольшим процентным отношением количества отраженных бросков к общему количеству бросков по воротам',
                'criteria' => 'savedPercent.desc',
                'extended' =>
                [
                    [ 'explain' => '% отраженных бросков', 'result' => 'savedPercent' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => '% отраженных бросков', 'result' => 'savedPercent' ],
                    [ 'explain' => 'Количество побед', 'result' => 'wins' ]
                ]
            ],
            'defenederBomber' =>
            [
                'title' => "Бомбардиры-защитники",
                'result' => 'набранные очки',
                'explain' => 'Защитники, набравшие наибольшее количество результативных очков',
                'criteria' => 'scores.desc',
                'extended' =>
                [
                    [ 'explain' => 'Набранные очки', 'result' => 'scores' ],
                    [ 'explain' => 'Заброшенные шайбы', 'result' => 'goals' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Плюс/минус', 'result' => 'utility' ]
                ]
            ],
            'penaltyTime' =>
            [
                'title' => "Штраф",
                'result' => 'минут штрафа',
                'explain' => 'Игроки с наибольшим суммарным количеством штрафного времени',
                'criteria' => 'penaltyTime.desc',
                'extended' =>
                [
                    [ 'explain' => 'Минут штрафа', 'result' => 'penaltyTime' ],
                    [ 'explain' => 'Количество игр', 'result' => 'matches' ],
                    [ 'explain' => 'Плюс/минус', 'result' => 'utility' ],
                    [ 'explain' => 'Набранные очки', 'result' => 'scores' ]
                ]
            ]
        ];
        $this->quantity = 5;
    }

    public function getAllNominations ()
    {
        $playersManager = new PlayerStatModel ( $this );
        $keepersManager = new KeeperStatModel ( $this );
        $teamManager = new TeamModel ( $this );
        foreach ( $this->nominations as $alias => $nomination )
        {
            $where =
            [
                'limit' => $this->quantity,
                'orderBy' => $nomination['criteria']
            ];
            switch ( $alias )
            {
                case 'reliability':
                case 'savedPercent':
                    $manager = $keepersManager;
                    break;
                case 'defenederBomber':
                    $where["amplua"] = "DEFENDER";
                    $manager = $playersManager;
                    break;
                default:
                    $manager = $playersManager;
            }
            $criteriaField = ArrayTools::head ( explode ( '.', $nomination['criteria'] ) );
            $nominants = $manager->search ( $where );
            $teamsIds = ArrayTools::pluck ( $nominants->data, "teamId" );
            $teamLogos = Starter::app ()->imager->getMainImages ( $teamManager->modelClass, $teamsIds );
            $images = Starter::app ()->imager->getMainImages ( "Player", ArrayTools::pluck ( $nominants->data, "playerId" ) );
            foreach ( $nominants->data as $nomino )
            {
                $nominant = new Nominant();
                $nominant->id = $nomino->playerId;
                $nominant->name = $nomino->name;
                $nominant->amplua = $nomino->amplua;
                $nominant->num = $nomino->num;
                $nominant->teamId = $nomino->teamId;
                $nominant->teamName = $nomino->teamName;
                $nominant->logo = $teamLogos[$nomino->teamId];
                $nominant->image = $images[$nomino->playerId];
                $nominant->result = $nomino->$criteriaField;
                $nominant->extendedParams = [];
                foreach ( $this->nominations[$alias]['extended'] as $extend )
                {
                    $extension = new stdClass();
                    $extension->title = $extend['explain'];
                    $field = $extend['result'];
                    $extension->result = $nomino->$field;
                    $nominant->extendedParams[] = $extension;
                }

                $this->nominations[$alias]['nominants'][] = $nominant;
            }
        }
        return $this->nominations;
    }

}

class Nominant
{
    public $id;
    public $name;
    public $amplua;
    public $num;
    public $teamId;
    public $teamName;
    public $logo;
    public $image;
    public $result;
    public $extendedParams;
}
