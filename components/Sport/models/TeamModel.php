<?php
/**
 * <pre>Класс Team - модель данных команда</pre>
 * @author kraser
 */
class TeamModel extends CmsModel
{
    /**
     * <pre>Конструктор</pre>
     * @param CmsComponent $parent <p>Владелец модели</p>
     */
    public function __construct ($parent )
    {
        parent::__construct ( "Team", $parent );
        $this->table = 'teams';
        $this->selectQuery = "
            SELECT
                `t`.`id` AS id,
                `t`.`alias` AS alias,
                `t`.`order` AS 'order',
                `t`.`name` AS name,
                `t`.`city` AS city,
                `t`.`rating` AS rating,
                `t`.`show` AS view,
                `t`.`deleted` AS deleted,
                `t`.`created` AS createDate,
                `t`.`modified` AS modifyDate
            FROM `prefix_" . $this->table . "` `t`
            {where}
            {order}
            ";

        $this->reference =
        [
            'id' => [ 'expression' => '`t`.`id`', 'type' => 'integer', 'raw' => '`id`' ],
            'teamId' => [ 'expression' => '`t`.`id`', 'type' => 'integer', 'raw' => '`id`' ],
            'alias' => [ 'expression' => '`t`.`alias`', 'type' => 'varchar', 'raw' => '`alias`' ],
            'order' => [ 'expression' => '`t`.`order`', 'type' => 'integer', 'raw' => '`order`' ],
            'name' => [ 'expression' => '`t`.`name`', 'type' => 'varchar', 'raw' => '`name`' ],
            'city' => [ 'expression' => '`t`.`city`', 'type' => 'integer', 'raw' => '`city`' ],
            'rating' => [ 'expression' => '`t`.`rating`', 'type' => 'integer', 'raw' => '`rating`' ],
            'view' => [ 'expression' => '`t`.`show`', 'type' => 'yesno', 'raw' => '`show`' ],
            'deleted' => [ 'expression' => '`t`.`deleted`', 'type' => 'yesno', 'raw' => '`deleted`' ]
        ];
        $this->modelClass = "Team";
    }

    /**
     * <pre>Поиск по параметрам</pre>
     * @param Array $params <p>Параметры поиска</p>
     * @return Array Of Team
     */
    public function search ( $params )
    {
        $query = $this->createSelectQuery ( $params, "`t`.`show`='Y' AND `t`.`deleted`='N'", "`t`.`name` ASC" );
        $teams = SqlTools::selectObjects($query, $this->modelClass, "id");
        return $teams;
    }

    /**
     * <pre></pre>
     * @param Team $model
     * @return Integer
     */
    public function createModelItem ( $model )
    {
        $queryParams = parent::createInsertQuery ( $model );
        $queryParams['set'][] = "`created`";
        $queryParams['values'][] = "NOW()";
        $query = "INSERT INTO `prefix_" . $this->table . "` (" . implode ( ",", $queryParams['set'] ) . ") VALUES (" . implode ( "," , $queryParams['values'] ) . ")";
        return SqlTools::insert ( $query );
    }

    /**
     * <pre>Возвращает массив настроек для построения таблицы</pre>
     * @return Array <p>Массив настроек таблицы</p>
     */
    public function getColumns ()
    {
        $columns =
        [
            'id' => 'ID',
            'alias' => 'Псевдоним',
            'order' => 'Место',
            'name' => 'Наименование',
            'city' => 'Город',
            'rating' => 'Рейтинг',
            'view' => 'Отображение',
            'deleted' => 'Удалено',
            'createDate' => 'Дата создания',
            'modifyDate' => 'Дата модификации'
        ];

        return $columns;
    }
}

class Team
{
    public $id;
    public $alias;
    public $order;
    public $name;
    public $city;
    public $rating;
    public $view;
    public $deleted;
    public $createDate;
    public $modifyDate;
    public $info = [];
}