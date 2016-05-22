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
                'explain' => 'Хоккеисты, набравшие наибольшее количество результативных баллов (очков), определяемых как сумма заброшенных шайб (голов) и результативных передач',
                'criteria' => 'scores.desc',
            ],
            'snipers' =>
            [
                'title' => 'Снайперы',
                'explain' => 'Хоккеисты, имеющие на своем счету наибольшее количество заброшенных шайб (голов)',
                'criteria' => 'goals.desc'
            ],
            'assistants' =>
            [
                'title' => 'Ассистенты',
                'explain' => 'Хоккеисты, набравшие наибольшее количество голевых передач',
                'criteria' => 'pass.desc'
            ],
            'utility' =>
            [
                'title' => 'Плюс/Минус',
                'explain' => 'Полевые игроки с наивысшим показателем полезности, который отражает разность заброшенных и пропущенных командой шайб в то время, когда хоккеист находился на льду. Учитывается при игре в равных составах (плюс или минус), а также при заброшенных шайбах в меньшинстве (плюс) и пропущенных в большинстве (минус)',
                'criteria' => 'utility.desc'
            ],
            'reliability' =>
            [
                'title' => 'Вратари (КН)',
                'explain' => 'Вратари с наименьшим средним количеством пропущенных шайб за игру, с учётом суммарного игрового времени',
                'criteria' => 'reliability.asc'
            ],
            'savedPercent' =>
            [
                'title' => "Вратари (%ОБ)",
                'explain' => 'Вратари с наибольшим процентным отношением количества отраженных бросков к общему количеству бросков по воротам',
                'criteria' => 'savedPercent.desc'
            ],
            'defenederBomber' =>
            [
                'title' => "Бомбардиры-защитники",
                'explain' => 'Защитники, набравшие наибольшее количество результативных очков',
                'criteria' => 'scores.desc'
            ],
            'penaltyTime' =>
            [
                'title' => "Штраф",
                'explain' => 'Игроки с наибольшим суммарным количеством штрафного времени',
                'criteria' => 'penaltyTime.desc'
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
            $images = Starter::app ()->imager->getMainImages ( $teamManager->modelClass, $teamsIds );
            foreach ( $nominants->data as $nomino )
            {
                $nominant = new Nominant();
                $nominant->id = $nomino->playerId;
                $nominant->name = $nomino->name;
                $nominant->amplua = $nomino->amplua;
                $nominant->num = $nomino->num;
                $nominant->teamId = $nomino->teamId;
                $nominant->teamName = $nomino->teamName;
                $nominant->logo = $images[$nomino->teamId];
                $nominant->result = $nomino->$criteriaField;
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
    public $result;
}