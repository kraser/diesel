<?php
/**
 * Description of Sport
 *
 * @author kraser
 */
class Sport extends AdminModule
{
    const name = 'Спорт';
    const order = 1;
    const classIcon = 'sport-trophy53';
    public $submenu =
    [
        'Info' => '<i class="glyph-icon icon-shopping-cart"></i>&nbsp;Команды и игроки',
        'seasons' => '<i class="glyph-icon icon-shopping-cart"></i>&nbsp;Сезоны и турниры',
        'uploadPlayerStat' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Статистика полевых игроков',
        'uploadKeeperStat' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Статистика вратарей',
        'uploadTeamStat' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Статистика команд',
        'uploadPlayerProps' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Данные игроков',
        'uploadTourney' => '<i class="glyph-icon icon-apple"></i>&nbsp;Результаты турнира (таблица)',
//        'Sorting' => '<i class="glyph-icon icon-sort-amount-desc"></i>&nbsp;Сортировка',
//        'MakeYML' => '<i class="glyph-icon icon-refresh"></i>&nbsp;Обновить YML'
    ];
    protected $tableTeams = 'teams';

    public function __construct()
    {
        Starter::import("Sport.models.*");
        parent::__construct();

    }

    public function Info ()
    {
        $this->title = 'Команды';
        $this->content = $this->DataTableAdvanced ( $this->tableTeams,
        [
            //Имена системных полей
            'nouns' =>
            [
                'id' => 'id',
                'name' => 'name',
                'order' => 'order',
                'deleted' => 'deleted',
                'created' => 'created',
                'modified' => 'modified'
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit',
                'del'
            ],
            //Табы (методы этого класса)
            'tabs' =>
            [
                'teamImages' => 'Изображения',
                '_Seo' => 'SEO'/*,
                '_Regions' => 'Регионы'*/
            ]
        ],
        [
            'id' =>
            [
                'name' => '№',
                'class' => 'hide',
            ],
            'order' =>
            [
                'name' => 'Порядок'
            ],
            'alias' =>
            [
                'name' => 'Псевдоним',
                'class' => 'hide'
            ],

            'name' =>
            [
                'name' => 'Наименование',
                'length' => '1-128',
                'link' => $this->GetLink ( "players" ) . '&team={id}',
            ],
            'show' => [ 'name' => 'Показывать', 'shortLabel' => 'Пок.', 'class' => 'min', 'default' => 'Y' ]
        ]);
    }

    public function players ()
    {
        $team = SqlTools::selectObject ( "SELECT * FROM `prefix_teams` WHERE `id`=" . ( int ) $_GET['team'] );
        $this->title = "<a href='/admin/?module=" . __CLASS__ . "&method=Info'>Команды</a> → " . $team->name;
        $this->content = $this->DataTable ( 'players',
        [
            //Имена системных полей
            'nouns' =>
            [
                'id' => 'id',
                'name' => 'name',
                'order' => 'order',
                'deleted' => 'deleted',
                'created' => 'created',
                'modified' => 'modified',
                'text' => 'description'
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit',
                'del'
            ],
            'tabs' =>
            [
                'playerImages' => 'Изображения',
                '_Seo' => 'SEO'
            ]
        ],
        [
            'id' => [ 'name' => '№', 'class' => 'min' ],
            'name' => [ 'name' => 'Имя игрока', 'length' => '1-100' ],
            'alias' =>
            [
                'name' => 'URI ссылка',
                'length' => '0-32',
                'regex' => '/^([a-z0-9-_]+)?$/i',
                'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                'if_empty_make_uri' => 'name',
                'hide_from_table' => true
            ],
            'teamId' =>
            [
                'name' => 'Команда',
                'default' => ( int ) $_GET['team'],
                'hide_from_table' => true,
                'select' =>
                [
                    'table' => $this->tableTeams,
                    'name' => 'name',
                    'id' => 'id',
                    'deleted' => 'deleted'
                ]
            ],
            'birthDate' => [ 'name' => 'Дата рождения' ],
            'amplua' =>
            [
                'name' => 'Амплуа игрока',
                'hide_from_table' => true
            ],
            'num' => [ 'name' => 'Номер игрока' ],
            'weight' => [ 'name' => 'Вес' ],
            'height' => [ 'name' => 'Рост' ],
            'status' => [ 'name' => 'Статус игрока' ],
            'grip' => [ 'name' => 'Хват клюшки' ],
            'show' => [ 'name' => 'Показывать', 'class' => 'min' ],
        ], '`teamId` = ' . ( int ) $_GET['team'] );
    }

    public function seasons ()
    {
        $this->title = "Сезоны";
        $this->content = $this->DataTable ( 'seasons',
        [
            'nouns' =>
            [
                'id' => 'id',
                'name' => 'title',
                //'order' => 'order',
                'deleted' => 'deleted',
                'created' => 'created',
                'modified' => 'modified',
                'text' => 'description'
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit',
                'del'
            ],
            'tabs' =>
            [
                'playerImages' => 'Изображения',
                '_Seo' => 'SEO'
            ]
        ],
        [
            'id' => [ 'name' => '№', 'class' => 'min' ],
            'title' =>
            [
                'name' => 'Название сезона', 'length' => '1-255',
                'link' => $this->GetLink ( "tourneys" ) . '&season={id}',
            ],
            'alias' =>
            [
                'name' => 'URI ссылка',
                'length' => '0-32',
                'regex' => '/^([a-z0-9-_]+)?$/i',
                'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                'if_empty_make_uri' => 'name',
                'hide_from_table' => false
            ],
//            'teamId' =>
//            [
//                'name' => 'Команда',
//                'default' => ( int ) $_GET['team'],
//                'hide_from_table' => true,
//                'select' =>
//                [
//                    'table' => $this->tableTeams,
//                    'name' => 'name',
//                    'id' => 'id',
//                    'deleted' => 'deleted'
//                ]
//            ],
            'startDate' => [ 'name' => 'Дата начала' ],
            'endDate' => [ 'name' => 'Дата завершения' ],
//            'amplua' =>
//            [
//                'name' => 'Амплуа игрока',
//                'hide_from_table' => true
//            ],
//            'num' => [ 'name' => 'Номер игрока' ],
//            'weight' => [ 'name' => 'Вес' ],
//            'height' => [ 'name' => 'Рост' ],
//            'status' => [ 'name' => 'Статус игрока' ],
//            'grip' => [ 'name' => 'Хват клюшки' ],
//            'show' => [ 'name' => 'Показывать', 'class' => 'min' ],
        ]/*, '`teamId` = ' . ( int ) $_GET['team'] */);
    }

    public function tourneys ()
    {
        $season = SqlTools::selectObject ( "SELECT * FROM `prefix_seasons` WHERE `id`=" . ( int ) $_GET['season'] );
        $this->title = "<a href='/admin/?module=" . __CLASS__ . "&method=seasons'>Сезоны</a> → " . $season->title;
        $this->content = $this->DataTable ( 'tourneys',
        [
            'nouns' =>
            [
                'id' => 'id',
                'name' => 'title',
                //'order' => 'order',
                'deleted' => 'deleted',
                'created' => 'created',
                'modified' => 'modified',
                'text' => 'description'
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit',
                'del'
            ],
            'tabs' =>
            [
                'tourneyImages' => 'Изображения',
                '_Seo' => 'SEO'
            ]
        ],
        [
            'id' => [ 'name' => '№', 'class' => 'min' ],
            'title' =>
            [
                'name' => 'Название турнира', 'length' => '1-255'
            ],
            'alias' =>
            [
                'name' => 'URI ссылка',
                'length' => '0-32',
                'regex' => '/^([a-z0-9-_]+)?$/i',
                'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                'if_empty_make_uri' => 'name',
                'hide_from_table' => false
            ],
            'type' => [ 'name' => 'Тип турнира' ],
            'seasonId' =>
            [
                'name' => 'Сезон',
                'default' => ( int ) $_GET['season'],
                'hide_from_table' => true,
                'select' =>
                [
                    'table' => "seasons",
                    'name' => 'title',
                    'id' => 'id',
                    'deleted' => 'deleted'
                ]
            ],
            'startDate' => [ 'name' => 'Дата начала' ],
            'endDate' => [ 'name' => 'Дата завершения' ],
//            'amplua' =>
//            [
//                'name' => 'Амплуа игрока',
//                'hide_from_table' => true
//            ],
//            'num' => [ 'name' => 'Номер игрока' ],
//            'weight' => [ 'name' => 'Вес' ],
//            'height' => [ 'name' => 'Рост' ],
//            'status' => [ 'name' => 'Статус игрока' ],
//            'grip' => [ 'name' => 'Хват клюшки' ],
//            'show' => [ 'name' => 'Показывать', 'class' => 'min' ],
        ], '`seasonId` = ' . ( int ) $_GET['season'] );
    }

    public function uploadPlayerProps ()
    {
        $this->title = "Загрузка физических данных игроков";
        if ( count ( $_POST ) )
            $this->processPlayerStat ( 'prop' );
        else
            $this->content = TemplateEngine::view ( '/modules/Sport/uploadForm', [ 'mode' => 'prop', 'function' => __FUNCTION__ ], __CLASS__ );
    }

    public function uploadTourney ()
    {
        $this->title = "Загрузка результатов турнира";
        if ( count ( $_POST ) )
            $this->processPlayerStat ( 'tourney' );
        else
            $this->content = TemplateEngine::view ( '/modules/Sport/uploadForm', [ 'mode' => 'tourney', 'function' => __FUNCTION__ ], __CLASS__ );
    }

    public function uploadPlayerStat ()
    {
        $this->title = "Загрузка статистики полевых игроков";
        if ( count ( $_POST ) )
            $this->processPlayerStat ( 'players' );
        else
            $this->content = TemplateEngine::view ( '/modules/Sport/uploadForm', [ 'mode' => 'players', 'function' => __FUNCTION__ ], __CLASS__ );
    }

    public function uploadKeeperStat ()
    {
        $this->title = "Загрузка статистики вратарей";
        if ( count ( $_POST ) )
            $this->processPlayerStat ( 'keepers' );
        else
            $this->content = TemplateEngine::view ( '/modules/Sport/uploadForm', [ 'mode' => 'keepers', 'function' => __FUNCTION__ ], __CLASS__ );
    }

    public function uploadTeamStat ()
    {
        $this->title = "Загрузка статистики команд";
        if ( count ( $_POST ) )
            $this->processPlayerStat ( 'teams' );
        else
            $this->content = TemplateEngine::view ( '/modules/Sport/uploadForm', [ 'mode' => 'teams', 'function' => __FUNCTION__ ], __CLASS__ );
    }

    private function processPlayerStat ( $mode )
    {
        $file = $_FILES[$mode];
        if ( $file['error'] )
            $this->content = "Ошибка " . FileTools::getPhpUploadError ( $file['error'] );
        else
        {
            $fileName = rtrim ( $file['name'] );
            $tmpname = $file['tmp_name'];
            $pathName = dirname ( $tmpname ) . DS . $fileName;
            copy ( $tmpname, $pathName );
            $processor = new StatisticManager();
            $statAlias = filter_input ( INPUT_POST, 'mode', FILTER_SANITIZE_STRING );
            $tourneyId = filter_input ( INPUT_POST, 'tourney', FILTER_SANITIZE_NUMBER_INT );
            $result = $processor->uploadStatFromFile ( $pathName, $statAlias, $tourneyId );
            $this->content = $result ? "Загрузка $fileName успешно завершена" : "Загрузка $fileName завершена неудачно";
        }
    }

    protected function teamImages ()
    {
        setcookie ( "moduleName", "Team", 0, "/" );
        setcookie ( "method", __FUNCTION__, 0, "/" );
        $this->Images ();
    }

    protected function playerImages ()
    {
        setcookie ( "moduleName", "Player", 0, "/" );
        setcookie ( "method", __FUNCTION__, 0, "/" );
        $this->Images ();
    }

    protected function tourneyImages ()
    {
        setcookie ( "moduleName", "Tourney", 0, "/" );
        setcookie ( "method", __FUNCTION__, 0, "/" );
        $this->Images ();
    }

    function Images ( $output = false )
    {
        $moduleName = filter_input ( INPUT_COOKIE, "moduleName", FILTER_SANITIZE_STRING );
        $method = filter_input ( INPUT_COOKIE, "method", FILTER_SANITIZE_STRING );
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);

        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $info = null;
        $imager = Starter::app ()->imager;
        //Добавление картинки
        if ( !empty ( $_FILES ) )
        {
            if ( $_FILES['image']['error'] == 1 )
            {
                $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );
            }
            $imager->addImage ( $_FILES['image']['tmp_name'], $moduleName, $id, $_FILES['image']['name'] );
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
        {
            $imager->starImage ( $_GET['star'] );
        }

        //Удаление
        if ( isset ( $_GET['del'] ) )
        {
            $imager->delImage ( $_GET['del'] );
        }

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $imager->getImages ( $moduleName, $id ),
            'link' => $this->GetLink ( $method ),
            'module' => $moduleName,
            'module_id' => $id,
            'info' => $info,
        ) );

        if ( $output )
            return $result;
        else
        {
            echo $result;
            exit ();
        }
    }

    public function ajaxImages ()
    {
        $result = "";
        if ( isset ( $_GET['files'] ) && empty ( $_FILES ) )
        {
            $files = $_GET['files'];
            foreach ( $files as $file )
            {
                $_FILES['image'] = array ( "tmp_name" => DOCROOT . DS . DATA . DS . $file["path"], "name" => $file["name"] );
                $result = $this->Images ( true );
            }
        }
        htmlHeader ();
        echo $result;
        exit ();
    }
}


