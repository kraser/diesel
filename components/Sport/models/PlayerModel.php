<?php
/**
 * Description of PlayerModel
 *
 * @author kraser
 */
class PlayerModel extends CmsModel
{
    private $amplua =
    [
        'GOALKEEPER' => 'Вратарь',
        'DEFENDER' => 'Защитник',
        'FORWARD' => 'Нападающий'
    ];

    public function __construct ( $parent )
    {
        parent::__construct ( "Team", $parent );
        $this->table = 'players';
        $this->selectQuery = "
            SELECT
                `p`.`id` AS id,
                `p`.`alias` AS alias,
                `p`.`teamId` AS teamId,
                `t`.`name` AS teamName,
                `p`.`amplua` AS amplua,
                `p`.`name` AS name,
                `p`.`birthDate` AS birthDate,
                `p`.`num` AS num,
                `p`.`weight` AS weight,
                `p`.`height` AS height,
                `p`.`status` AS status,
                `p`.`grip` AS grip,
                `p`.`description` AS description,
                `p`.`ratting` AS rating,
                `p`.`show` AS view,
                `p`.`deleted` AS deleted,
                `p`.`created` AS createDate,
                `p`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` `p`
            LEFT JOIN `prefix_teams` `t` ON `t`.`id`=`p`.`teamId`
            {where}
            {order}
            ";

        $this->reference =
        [
            'id' => [ 'expression' => '`p`.`id`', 'type' => 'integer', 'raw' => '`id`' ],
            'alias' => [ 'expression' => '`p`.`alias`', 'type' => 'varchar', 'raw' => '`alias`' ],
            'teamId' => [ 'expression' => '`p`.`teamId`', 'type' => 'integer', 'raw' => 'teamId' ],
            'teamName' => [ 'expression' => '`t`.`name`', 'type' => 'varchar', 'raw' => 'none' ],
            'amplua' => [ 'expression' => '`p`.`amplua`', 'type' => 'enum', 'raw' => '`amplua`' ],
            'name' => [ 'expression' => '`p`.`name`', 'type' => 'varchar', 'raw' => '`name`' ],
            'birthdate' => [ 'expression' => '`p`.`birthDate`', 'type' => 'date', 'raw' => '`birthDate`' ],
            'num' => [ 'expression' => '`p`.`num`', 'type' => 'integer', 'raw' => '`num`' ],
            'weight' => [ 'expression' => '`p`.`weight`', 'type' => 'decimal', 'raw' => '`weight`' ],
            'height' => [ 'expression' => '`p`.`height`', 'type' => 'decimal', 'raw' => '`height`' ],
            'status' => [ 'expression' => '`p`.`status`', 'type' => 'enum', 'raw' => '`status`' ],
            'grip' => [ 'expression' => '`p`.`grip`', 'type' => 'enum', 'raw' => '`grip`' ],
            'description' => [ 'expression' => '`t`.`description`', 'type' => 'text', 'raw' => '`description`' ],
            'rating' => [ 'expression' => '`p`.`rating`', 'type' => 'integer', 'raw' => '`rating`' ],
            'view' => [ 'expression' => '`p`.`show`', 'type' => 'yesno', 'raw' => '`show`' ],
            'deleted' => [ 'expression' => '`p`.`deleted`', 'type' => 'yesno', 'raw' => '`deleted`' ]
        ];
        $this->modelClass = "Player";
    }

    public function search ( $params )
    {
        $query = $this->createSelectQuery ( $params, "`p`.`show`='Y' AND `p`.`deleted`='N'", "`t`.`name` ASC, `p`.`name` ASC" );
        $players = SqlTools::selectObjects($query, $this->modelClass, "id");
        foreach ( $players as $player )
        {
            $player->amplua = $this->amplua[$player->amplua];
        }
        return $players;
    }

    public function createModelItem ( $model )
    {
        $model->amplua = array_key_exists ($model->amplua, $this->amplua )
            ? $model->amplua
            : array_search ( CharTools::upperFirst ( $model->amplua ), $this->amplua);
        $queryParams = parent::createInsertQuery ( $model );
        $queryParams['set'][] = "`created`";
        $queryParams['values'][] = "NOW()";
        $query = "INSERT INTO `prefix_" . $this->table . "` (" . implode ( ",", $queryParams['set'] ) . ") VALUES (" . implode ( "," , $queryParams['values'] ) . ")";
        return SqlTools::insert ( $query );
    }

    public function getColumns ()
    {
        $columns =
        [
            'id' => 'ID',
            'alias' => 'Псевдоним',
            'teamId' => 'Id команды',
            'teamName' => 'Название команды',
            'amplua' => 'амплуа',
            'name' => 'Имя',
            'birthdate' => 'Дата рождения',
            'num' => 'Номер',
            'weight' => 'Вес',
            'height' => 'Рост',
            'status' => 'Статус',
            'grip' => 'Хват клюшки',
            'description' => 'Описание',
            'rating' => 'рейтинг',
            'view' => 'отображение',
            'deleted' => 'удален',
            'createDate' => 'создание',
            'modifyDate' => 'модификация'
        ];

        return $columns;
    }

    public function decodeAmplua ($amplua)
    {
        return $this->amplua[$amplua];
    }

    public function getAmplua ()
    {
        return $this->amplua;
    }

    private function simulakr()
    {
        $columns = (object)
        [
            'birthdate' => '1968-12-07',
            'num' => '7',
            'weight' => '93',
            'height' => '176',
            'status' => 'любитель',
            'grip' => 'Левый',
            'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur."Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
    <blockquote>Nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Ut enim ad minima veniam, quis nostrum exercitationem </blockquote>
    <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modification..</p>
',
        ];

        return $columns;
    }
}

class Player
{
    public $id;
    public $alias;
    public $teamId;
    public $teamName;
    public $amplua;
    public $name;
    public $birthdate;
    public $num;
    public $weight;
    public $height;
    public $status;
    public $grip;
    public $description;
    public $rating;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;
}