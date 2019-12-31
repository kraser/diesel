<?php

class Catalog extends AdminModule
{
    const name = 'Каталог товаров';
    const order = 4;
    const icon = 'shopping-cart';

    protected $table_topics = "products_topics";
    protected $table_products = "products";
    protected $table_brands = "products_brands";
    //protected $table_topics_tags = "products_topics_tags";
    protected $table_tags_values = "tags_values";
    protected $table_sorting = "sorting";
    protected $table_currencies = "products_currencies";
    public $submenu = array
    (
        'Info' => '<i class="glyph-icon icon-shopping-cart"></i>&nbsp;Каталог',
        'allTags' => '<i class="glyph-icon icon-tasks"></i>&nbsp;Характеристики',
        'Currencies' => '<i class="glyph-icon icon-usd"></i>&nbsp;Валюты',
        'Brands' => '<i class="glyph-icon icon-apple"></i>&nbsp;Бренды',
        'Sorting' => '<i class="glyph-icon icon-sort-amount-desc"></i>&nbsp;Сортировка',
        'MakeYML' => '<i class="glyph-icon icon-refresh"></i>&nbsp;Обновить YML'
    );

    function Info ()
    {
        //Таблица товаров
        if ( isset ( $_GET['top'] ) )
        {
            $i = SqlTools::selectRow ( "SELECT * FROM `prefix_$this->table_topics` WHERE `id`=" . ( int ) $_GET['top'], MYSQL_ASSOC );

            $this->title = '<a href="' . $this->GetLink () . '">Каталог</a> → ' . $i['name'];

            $this->content = $this->DataTableAdvanced ( $this->table_products, array
            (
                //options
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text'  // TEXT
                ),
                //Отображение контролов
                'controls' => array
                (
                    'add',
                    'edit',
                    'no_edit_text',
                    'del'
                ),
                //Табы (методы этого класса)
                'tabs' => array
                (
                    'productImages' => 'Изображения продукции',
                    '_Seo' => 'SEO'/*,
                    '_Regions' => 'Регионы'*/
                )
                ),
                array
                (
                    //fields
                    'id' => array
                    (
                        'name' => '№',
                        'class' => 'hide',
                    ),
                    'name' => array
                    (
                        'name' => 'Наименование',
                        'length' => '1-128',
                        'link' => $this->GetLink ( "productTagsValues", array ( 'top' => '{id}', 'cat' => $_GET['top'] ) ),
                    ),
                    'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
                    'shortName' => array ( 'name' => 'Артикул', 'length' => '0-64' ),
                    'nav' => array
                    (
                        'name' => 'Опциональная URI ссылка',
                        'length' => '0-100',
                        'hide_from_table' => true
                    ),
                    'brand' => array
                    (
                        'name' => 'Бренд',
                        'hide_from_table' => true,
                        'select' => array
                        (
                            //Обязательные
                            'table' => $this->table_brands,
                            'name' => 'name',
                            //Необязательные
                            'id' => 'id',
                            'order' => 'order',
                            'allow_null' => true,
                            'deleted' => 'deleted'
                        )
                    ),
                    'price' => array
                    (
                        'name' => 'Цена',
                        'length' => '1-16',
                        'regex' => '/^[0-9]*\.?[0-9]*$/i',
                        'regex_error' => 'Цена может быть только числовой и положительной',
                        'default' => 0
                    ),
                    'unit' => array
                    (
                        'name' => 'Единица измерения',
                        'length' => '0-28',
                        'default' => "шт.",
                        'hide_from_table' => true,
                    ),
                    'currency' => array
                    (
                        'name' => 'Валюта',
                        'select' => array
                        (
                            //Обязательные
                            'table' => $this->table_currencies,
                            'name' => 'name',
                            //Необязательные
                            'id' => 'id',
                            // 'symbol'		 	=> 'symbol',
                            'allow_null' => true,
                            'deleted' => 'deleted',
                        )
                    ),
                    'anons' => array ( 'name' => 'Анонс товара', 'hide_from_table' => true, 'edit_text' => 1 ),
                    'text' => array ( 'name' => 'Описание товара', 'hide_from_table' => true, 'edit_text' => 1 ),
                    'show' => array ( 'name' => 'Показывать', 'shortLabel' => 'Пок.', 'class' => 'min', 'default' => 'Y' ),
                    'is_action' => array ( 'name' => 'Акция', 'class' => 'min', 'default' => 'N' ),
                    'is_featured' => array ( 'name' => 'Рекомендуемый', 'shortLabel' => 'Реком.', 'class' => 'min', 'default' => 'N' ),
                    'is_exist' => array ( 'name' => 'В наличии', 'shortLabel' => 'Нал.', 'class' => 'min', 'default' => 'Y' ),
                    'noIndex' => array ( 'name' => 'noIndex', 'hide_from_table' => true, 'class' => 'min', 'default' => 'N' ),
                    'top' => array
                    (
                        'name' => 'Раздел',
                        'default' => ( int ) $_GET['top'],
                        'hide_from_table' => true,
                        'select' => array
                        (
                            //Обязательные
                            'table' => $this->table_topics,
                            'name' => 'name',
                            //Необязательные
                            'id' => 'id',
                            'order' => 'order',
                            'top' => 'top',
                            'deleted' => 'deleted'
                        )
                    ),
                    'content_top' => array
                    (
                        'name' => 'Относится к разделам (для примера, не рабочее поле)',
                        'multiselect' => array
                        (
                            //Обязательные
                            'table' => 'content',
                            'name' => 'name',
                            //Необязательные
                            'id' => 'id',
                            'order' => 'order',
                            'top' => 'top',
                            'deleted' => 'deleted'
                        )
                    ),
                    'rate' => array ( 'name' => 'Рейтинг (количество просмотров)', 'hide_from_table' => true ),
                    'relations' => array
                    (
                        'name' => 'С этим товаром также покупают',
                        'length' => '0-250',
                        'regex' => '/^([\d]+,?)*$/i',
                        'regex_error' => 'Зависимые товары должны быть перечислены по их номерам (id товара) через запятую!',
                        'hide_from_table' => true
                    ),
                    'discount' => array ( 'name' => 'Скидка, %', 'hide_from_table' => true )
                ), '`top` = ' . ( int ) $_GET['top']
            );
        }
        //Дерево разделов
        else
        {
            $this->title = 'Каталог';
            $this->content = $this->DataTree (
                $this->table_topics, array
                (
                    //Имена системных полей
                    'nouns' => array
                    (
                        'id' => 'id',
                        'name' => 'name',
                        'order' => 'order',
                        'deleted' => 'deleted',
                        'created' => 'created',
                        'modified' => 'modified',
                        'text' => 'text',
                        'top' => 'top',
                        'leaf' => 'isModel'
                    ),
                    //Отображение контролов
                    'controls' => array
                    (
                        'add_root',
                        'add_sub',
                        'edit',
                        'list' => $this->GetLink ( '', array ( 'top' => '{id}' ) ),
                        'del'
                    ),
                    //Зависимая таблица (напрмер товары или новости по рубрикам)
                    'inner' => array
                    (
                        'table' => $this->table_products, //Имя таблицы
                        'top_key' => 'top', //Ключ соответствия категории товарам
                        'deleted' => 'deleted' //Поле «удалено»
                    ),
                    //Табы (методы этого класса)
                    'tabs' => array
                    (
                        'categoryImages' => 'Изображения категорий',
                        '_Seo' => 'SEO'
                    )
                ),
                array
                (
                    'id' => array ( 'name' => '№', 'class' => 'min' ),
                    'name' => array
                    (
                        'name' => 'Наименование',
                        'length' => '1-128',
                        'link' => $this->GetLink ( '', array ( 'top' => '{id}' ) )
                    ),
                    'nav' => array
                    (
                        'name' => 'URI ссылка',
                        'length' => '0-32',
                        'regex' => '/^([a-z0-9-_]+)?$/i',
                        'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                        'if_empty_make_uri' => 'name'
                    ),
                    'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
                    'text' => array ( 'name' => 'Детальное описание категории', 'hide_from_table' => true, 'edit_text' => 1 ),
                    'cases' => array ( 'name' => 'Краткое описание категории', 'hide_from_table' => true, 'edit_text' => 1 ),
                    'isModel' => array ( 'name' => 'Модельный ряд', 'class' => 'min', 'default' => 'N', "hide" => $this->hasChildren() ),

                    'content_top' => array
                    (
                        'name' => 'Относится к разделам (для примера, не рабочее поле)',
                        'multiselect' => array
                        (
                            //Обязательные
                            'table' => 'content',
                            'name' => 'name',
                            //Необязательные
                            'id' => 'id',
                            'order' => 'order',
                            'top' => 'top',
                            'deleted' => 'deleted',
                            'size' => 5
                        )
                    ),
                    'rate' => array ( 'name' => 'Рейтинг (количество просмотров)' )
                )
            );
        }
    }

    function Brands ()
    {
        $this->title = 'Бренды';
        $this->content = $this->DataTableAdvanced ( $this->table_brands, array
        (
            //Имена системных полей
            'nouns' => array
            (
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'order' => 'order', // INT
                'deleted' => 'deleted', // ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified', // DATETIME
                'text' => 'text', // TEXT
            ),
            //Отображение контролов
            'controls' => array
            (
                'add',
                'edit',
                'del'
            )
        ),
        array
        (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Имя бренда', 'length' => '1-128' ),
            'nav' => array
            (
                'name' => 'Адрес',
                'length' => '0-32',
                'regex' => '/^([a-z0-9-_]+)?$/i',
                'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                'if_empty_make_uri' => 'name'
            ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min', 'transform' => 'YesNo' ),
            'order' => array ( 'name' => 'Порядок', 'class' => 'min' )
        ) );
    }

    function Currencies ()
    {
        $this->title = 'Валюты';
        $this->content = $this->DataTableAdvanced ( $this->table_currencies, array
        (
            //Имена системных полей
            'nouns' => array
            (
                'id' => 'id', // INT
                'code' => 'code', // VARCHAR
                'symbol' => 'symbol', // VARCHAR
                'symbol_position' => 'symbol_position', // ENUM('rigth','left')
                'value' => 'value', // FLOAT
                'is_main' => 'is_main', // ENUM('Y','N')
                'show' => 'show', // ENUM('Y','N')
                'deleted' => 'deleted', // ENUM('Y','N')
            ),
            //Отображение контролов
            'controls' => array
            (
                'add',
                'edit',
                'del'
            )
        ),
        array
        (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Название', 'length' => '1-100', 'class' => 'min' ),
            'code' => array ( 'name' => 'Код валюты', 'length' => '1-5', 'class' => 'min', 'hide_from_table' => true ),
            'symbol' => array ( 'name' => 'Символ', 'class' => 'min' ),
            'symbol_position' => array ( 'name' => 'Позиция символа', 'hide_from_table' => true ),
            'value' => array ( 'name' => 'Курс к валюте по умолчанию' ),
            'is_main' => array ( 'name' => 'Использовать по-умолчанию' )
        ) );
    }

    function Tags ()
    {
        if ( false && isset ( $_GET['top'] ) )
        {
            $i = SqlTools::selectRow ( "SELECT * FROM `prefix_$this->table_topics` WHERE `id`=" . ( int ) $_GET['top'], MYSQL_ASSOC );
            $cats = implode ( ",", Tools::getParentCategories ( ( int ) $_GET['top'] ) );

            $this->title = '<a href="' . $this->GetLink ( 'Info' ) . '">Каталог</a> → Характеристики для "' . $i['name'] . '"';
            $this->content = $this->DataTableAdvanced ( $this->table_topics_tags, array
            (
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id',
                    'name' => 'name',
                    'tagType' => 'tagType',
                    'order' => 'order',
                    'unit' => 'unit',
                    'created' => 'created',
                    'modified' => 'modified',
                    'text' => 'text'
                ),
                //Отображение контролов
                'controls' => array
                (
                    'add',
                    'edit',
                    'del',
                    "editIf" => array
                        ( // предусматривает вывод контролов редактирования только для строк, у которых, например, поле topic_id равно $_GET['top']
                        array ( "field" => "topic_id", "value" => $_GET['top'] )
                    )
                )
            ),
            array
            (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'topic_id' => array
                (
                    'name' => 'Категория',
                    'default' => $_GET['top'],
                    'hide_from_table' => true,
                    'hide_from_form' => true,
                ),
                'name' => array
                (
                    'name' => 'Наименование характеристики',
                    'length' => '1-128',
                    'autocomplete' => 'this',
                ),
                'tagType' => array
                (
                    'name' => 'Тип характеристики',
                    'class' => 'min',
                    'hide_from_table' => true,
                ),
                'unit' => array
                (
                    'name' => 'Единица измерения',
                    'length' => '0-10',
                    'autocomplete' => 'this',
                ),
                'default' => array
                (
                    'name' => 'Значение по умолчанию для категории',
                    'length' => '0-255',
                    'hide_from_table' => true,
                    'regex' => '($("input:radio[name=tagType][checked=checked]").val()=="INTERVAL") '
                    . ' ? /^[0-9]*\.?[0-9]*$/i '
                    . ' : ($("input:radio[name=tagType][checked=checked]").val()=="ENUM") '
                    . ' ? /^(Есть|Нет|)$/i'
                    . ' : /^.*$/i ',
                    'regex_error' => '"+('
                    . '($("input:radio[name=tagType][checked=checked]").val()=="INTERVAL") '
                    . ' ? "Должно быть число." '
                    . ' : ($("input:radio[name=tagType][checked=checked]").val()=="ENUM") '
                    . ' ? "Должно быть Есть или Нет или пустое значение." '
                    . ' : "Ошибка" '
                    . ')+"',
                ),
                'show' => array ( 'name' => 'Показывать', 'class' => 'min', 'transform' => 'YesNo', 'default' => 'Y' )
            ), "`topic_id` IN ($cats)" );
        }

        $top = filter_input ( INPUT_GET, "top", FILTER_SANITIZE_NUMBER_INT );
        if ( $top )
        {
            $row = ArrayTools::head ( SqlTools::selectObjects ( "SELECT * FROM `prefix_$this->table_topics` WHERE `id`=" . ( int ) $top ) );
            $cats = implode ( ",", Tools::getParentCategories ( ( int ) $top ) );

            $this->title = '<a href="' . $this->GetLink ( 'Info' ) . '">Каталог</a> → Характеристики для "' . $row->name . '"';
            $this->content = $this->DataTableAdvanced ( "tags_values", array
            (
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id', // INT
                    'module' => "module",
                    "moduleId" => "moduleId",
                    'order' => 'order', // INT
                    'tagId' => 'tagType', // VARCHAR
                    'value' => 'value',
                    'unit' => 'unit',
                    'created' => 'created',
                    'modified' => 'modified'
                ),
                //Отображение контролов
                'controls' => array
                (
                    'add',
                    'edit',
                    'del',
                    "editIf" => array
                    ( // предусматривает вывод контролов редактирования только для строк, у которых, например, поле topic_id равно $_GET['top']
                        array ( "field" => "moduleId", "value" => $top )
                    ),
                )
            ),
            array
            (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'order' => array ( 'name' => 'Порядок использования', 'class' => 'min' ),
                'module' => array ( 'name' => 'Модуль', "hide_from_form" => true, "hide_from_table" => true, 'default' => "Catalog" ),
                'moduleId' => array ( 'name' => 'ID Модуля', "hide_from_form" => true, "hide_from_table" => true, 'default' => $top ),
                'tagId' => array
                (
                    'name' => 'Характеристика',
                    'select' => array
                    (
                        //Обязательные
                        'table' => "catalog_tags", //$this->table_topics_tags,
                        'name' => array
                        (
                            "fields" => array ( "name" ),
                        //"delim" => ", ",
                        ),
                        //Необязательные
                        'id' => 'id',
                        "order" => "name",
                    )
                ),
                'unit' => array
                (
                    'name' => 'Единица измерения',
                    'length' => '0-10',
                    'class' => 'min',
                ),
                'where' => "module='Catalog'",
                'value' => array
                (
                    'name' => 'Значение по умолчанию для категории',
                    'length' => '0-255',
                ),
                'show' => array ( 'name' => 'Показывать', 'class' => 'min', 'transform' => 'YesNo', 'default' => 'Y' )
            ), "`module`='Catalog' AND `moduleId` IN ($cats)" );
        }
    }

    function productTagsValues ()
    {
        $top = filter_input ( INPUT_GET, "top", FILTER_SANITIZE_NUMBER_INT );
        if ( $top )
        {
            $cat = filter_input ( INPUT_GET, "cat", FILTER_SANITIZE_NUMBER_INT );
            $category = SqlTools::selectRow ( "SELECT * FROM `prefix_$this->table_topics` WHERE `id`=" . ( int ) $cat, MYSQL_ASSOC );
            $cats = implode ( ",", Tools::getParentCategories ( $cat ) );
            $prod = SqlTools::selectRow ( "SELECT * FROM `prefix_products` WHERE `id`=" . ( int ) $top, MYSQL_ASSOC );

            $this->title = "<a href='" . $this->GetLink ( 'Info' ) . "'>Каталог</a>"
                . "<a href='" . $this->GetLink ( 'Info', array ( 'top' => $category['id'] ) ) . "'> → Категория '" . $category['name'] . "'</a>'"
                . " → Значения для '" . $prod['name'] . "'";
            $this->content = $this->DataTableAdvanced ( $this->table_tags_values, array
            (
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id',
                    'module' => "module",
                    "moduleId" => "moduleId",
                    'order' => 'order',
                    'tagId' => 'tagType',
                    'value' => 'value',
                    'unit' => 'unit',
                    'created' => 'created',
                    'modified' => 'modified',
                ),
                //Отображение контролов
                'controls' => array
                (
                    'add',
                    'edit',
                    'del',
                    "editIf" => array
                    ( // предусматривает вывод контролов редактирования только для строк, у которых, например, поле topic_id равно $_GET['top']
                        array ( "field" => "moduleId", "value" => $top ),
                        array ( "field" => "module", "value" => "" )
                    ),
                )
            ),
            array
            (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'module' => array ( 'name' => 'Модуль', "hide_from_form" => true, "hide_from_table" => true, 'default' => "" ),
                'moduleId' => array ( 'name' => 'Товар', "hide_from_form" => true, "hide_from_table" => true, 'default' => $top ),
                'tagId' => array
                (
                    'name' => 'Характеристика',
                    'select' => array
                    (
                        //Обязательные
                        'table' => "catalog_tags", //$this->table_topics_tags,
                        'name' => array
                        (
                            "fields" => array ( "name" ),
                        //"delim" => ", ",
                        ),
                        //Необязательные
                        'id' => 'id',
                        "order" => "name",
                    ),
                ),
                'unit' => array
                (
                    'name' => 'Единица измерения',
                    'length' => '0-10',
                    'class' => 'min',
                ),
                //'where' => "module='Catalog'",
                'value' => array
                (
                    'name' => 'Значение характеристики товара',
                    'length' => '0-255',
                ),
                'show' => array ( 'name' => 'Показывать', 'class' => 'min', 'transform' => 'YesNo', 'default' => 'Y' ),
            ), "(`module`!='Catalog' AND `moduleId`= " . ( int ) $_GET['top'] . ") OR ( `module`='Catalog' AND `moduleId` IN ($cats))" );
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

    function Images ( $isNoEcho = false )
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

        if ( $isNoEcho )
        {
            return $result;
        }
        else
        {
            echo $result;
            exit ();
        }
    }

    function MakeYML ()
    {
        $useShortUrl = Tools::getSettings ( __CLASS__, "useShortUrl", "N" ) != "N";
        $img = new Images();
        $yml_file = '/yandex.xml';
        $topics_table = $this->table_topics;
        $brands_table = $this->table_brands;
        $products_table = $this->table_products;
        //Модели
        $query = "
            SELECT
                t.`id` AS id,
                t.`top` AS top,
                t.`name` AS name,
                b.`name` AS brandName,
                IF(c.`value`, ROUND(MIN(p.`price`)*c.`value`, 2), MIN(p.`price`)) AS price,
                MAX(p.`brand`) brandId,
                t.`text`
            FROM `prefix_$topics_table` t
            LEFT JOIN `prefix_$products_table` p ON p.`top`=t.id
            LEFT JOIN `prefix_$this->table_currencies` c ON c.`id`=p.`currency`
            LEFT JOIN `prefix_$brands_table` b ON p.`brand`=b.`id`
            WHERE t.`isModel`='Y' AND p.`brand`>0 AND t.`show`='Y' AND t.`deleted`='N' GROUP BY t.`id` HAVING brandId>0";
        $models = SqlTools::selectObjects ( $query, null, "id" );
        $exclude = array();
        $topicIds = array ();
        foreach ( $models as $id => $model )
        {
            $exclude[] = $id;
            $topicIds[] = $model->top;
            $model->link = $_SERVER['SERVER_NAME'] . admLinkByModule ( __CLASS__ ). admLinkById ( $model->id, $topics_table );
            $image = $img->GetMainImage ( "Topic", $model->id );
            $model->image = $image ? 'http://' . $_SERVER['SERVER_NAME'] . $image['src'] : "";

        }

        //Товары
        $query = "
            SELECT
                p.`id` AS id,
                p.`top` AS top,
                p.`name` AS name,
                p.`nav` AS nav,
                b.`name` AS brandName,
                IF(c.`value`, ROUND(p.`price`*c.`value`, 2), p.`price`) AS price,
                p.`text` AS text
            FROM `prefix_$products_table` AS p
            LEFT JOIN `prefix_$this->table_currencies` c ON c.`id`=p.`currency`
            LEFT JOIN `prefix_$brands_table` b ON p.`brand`=b.`id`
            WHERE p.`deleted`='N' AND p.`show`='Y' AND p.`top` NOT IN (" . ArrayTools::numberList($exclude) . ") AND p.`brand`>0 ORDER BY p.`top`,p.`order`";

        $products = SqlTools::selectObjects ( $query, null, "id" );
        foreach ( $products as $product )
        {
            $product->link = $_SERVER['SERVER_NAME'] . admLinkByModule ( __CLASS__ );
            if(!$useShortUrl)
            {
                $product->link .= admLinkById ( $product->top, $topics_table );
            }
            $product->link .= "/" . ( empty ( $product->nav ) ? $product->id : $product->nav );
            $image = $img->GetMainImage ( __CLASS__, $model->id );
            $model->image = $image ? 'http://' . $_SERVER['SERVER_NAME'] . $image['src'] : "";
        }
        $topicsIds = array_unique ( array_merge ( $topicIds, ArrayTools::pluck ( $products, "top" ) ) );
        $parents = array ();
        foreach ( $topicsIds as $topicId )
        {
            $parents = array_unique ( array_merge ( $parents, Tools::getParentCategories ( $topicId ) ) );
        }
        $allProducts = array_merge ( $models, $products );


        $yml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">
<yml_catalog date=\"" . date ( 'Y-m-d H:i' ) . "\">
<shop>
	<name>" . $_SERVER['SERVER_NAME'] . "</name>
	<company>" . Starter::app ()->title . "</company>
	<url>http://" . $_SERVER['SERVER_NAME'] . "/</url>

	<currencies>
		<currency id=\"RUR\" rate=\"1\"/>
	</currencies>

	<categories>
            ";

        //Категории
        $topics = SqlTools::selectRows ( "SELECT * FROM `prefix_$topics_table` WHERE `deleted`='N' AND `id` IN (" . ArrayTools::numberList($parents) . ")ORDER BY `top`,`order`", MYSQL_ASSOC, "id" );
        foreach ( $topics as $id => $topic )
        {
            if ( $topic['top'] != 0 && !array_key_exists ( $topic['top'], $topics ) )
            {
                unset ( $topics[$id] );
                continue;
            }
            if ( $topic['top'] == 0 )
            {
                $yml .= "<category id=\"$id\">" . $topic['name'] . "</category>
            ";
            }
            else
            {
                $yml .= "<category id=\"$id\" parentId=\"" . $topic['top'] . "\">" . $topic['name'] . "</category>
            ";
            }
        }

        $yml .= "
	</categories>
	<offers>
            ";

        foreach ( $allProducts as $product )
        {
            if ( !array_key_exists ( $product->top, $topics ) || !$product->price )
            {
                unset ( $allProducts[$product->id] );
                continue;
            }

            //Находим бренд
            if ( isset ( $product->brandName ) )
                $brand = $product->brandName;
            else
                $brand = '';

            $text = trim ( str_replace ( array ( "&nbsp;" ), array ( " " ), strip_tags ( $product->text) ) );
            $yml .= "<offer id=\"$product->id\" type=\"vendor.model\" available=\"true\">
            <url>http://$product->link</url>
            <price>$product->price</price>
            <currencyId>RUR</currencyId>
            <categoryId>$product->top</categoryId>
            <picture>$product->image</picture>
            <typePrefix>" . htmlspecialchars_decode ( $topics[$product->top]['name'] ) . "</typePrefix>
            <vendor>" . htmlspecialchars_decode ( $brand ) . "</vendor>
            <model>" . htmlspecialchars_decode ( $product->name ) . "</model>
            <description>" . html_entity_decode ( htmlspecialchars_decode ( $text ) ) . "</description>
            <sales_notes>предоплата</sales_notes>
        </offer>
        ";
        }

        $yml .= "
    </offers>
</shop>
</yml_catalog>";


        if ( is_file ( DOCROOT . $yml_file ) )
        {
            if ( is_writable ( DOCROOT . $yml_file ) )
            {
                if ( file_put_contents ( DOCROOT . $yml_file, $yml ) )
                {
                    $this->content = 'Файл ' . $yml_file . ' успешно обновлен.';
                }
                else
                {
                    $this->content = 'Неведомая ошибка записи';
                }
            }
            else
            {
                $this->content = 'Невозможно записать файл ' . $yml_file . ', нет прав!';
            }
        }
        else
        {
            if ( is_writable ( DOCROOT ) )
            {
                if ( file_put_contents ( DOCROOT . $yml_file, $yml ) )
                {
                    $this->content = 'Файл ' . $yml_file . ' успешно создан.';
                }
                else
                {
                    $this->content = 'Неведомая ошибка записи';
                }
            }
            else
            {
                $this->content = 'Невозможно создать файл ' . $yml_file . ', нет прав! Попробуйте создать его самостоятельно и назначьте права 0777.';
            }
        }
    }
    private $currency;

//    function Price ( $price, $discount = 0 )
//    {
//        if ( $price > 0 )
//        {
//            //Расчет курса (выполняется один раз!)
//            if ( empty ( $this->currency ) )
//            {
//                $currencies = unserialize ( getVar ( 'currency' ) );
//                $selectedCurrency = admGetSet ( 'Catalog', 'inner_currency' );
//                if ( $currencies && isset ( $currencies[$selectedCurrency] ) && $currencies[$selectedCurrency] > 0 )
//                {
//                    $this->currency = $currencies[$selectedCurrency];
//                }
//                else
//                    $this->currency = 1;
//
//                //Надбавка к конвертации
//                if ( $this->currency > 1 )
//                {
//                    $this->currency = $this->currency * ( 1 + (admGetSet ( 'Catalog', 'currency_margin' ) / 100) );
//                }
//            }
//            //Конвертация цены товара от валюты
//            $priceVal = $price * $this->currency;
//
//            //Расчет скидки на товар (акции и подобное)
//            $priceDiscount = $priceVal * (1 - $discount / 100);
//
//            //Округление
//            $finalPrice = round ( $priceDiscount, admGetSet ( 'Catalog', 'price_round' ) );
//        }
//
//        return $finalPrice;
//    }

    protected function categoryImages ()
    {
        setcookie ( "moduleName", "Topic", 0, "/" );
        setcookie ( "method", __FUNCTION__, 0, "/" );
        $this->Images ();
    }

    protected function productImages ()
    {
        setcookie ( "moduleName", "Catalog", 0, "/" );
        setcookie ( "method", __FUNCTION__, 0, "/" );
        $this->Images ();
    }

    /** @Override
     * Генерация ссылки для админки
     *
     * @param string $method
     * @param array $data
     * @param string $module
     *
     * @return string
     */
    protected function GetLink ( $method = '', $data = array (), $module = '' )
    {
        $level = 1;
        $debug_info = debug_backtrace ();
        if ( $debug_info[$level]['class'] == 'AdminModule' && isset ( $debug_info[$level + 1] ) )
            $level++;

        $urlParts = array (
            'module' => empty ( $module ) ? $this->called_class : $module,
            'method' => empty ( $method ) ? $debug_info[$level]['function'] : $method
        );

        ////?? костыль для Catalog - Tags
        $prm = "cat";
        $prms = array ();
        parse_str ( parse_url ( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ), $prms );
        if ( isset ( $prms[$prm] ) && !isset ( $data[$prm] ) )
        {
            $data[$prm] = $prms[$prm];
        }
        ////
        $url = array_merge ( $urlParts, $data );
        return '?' . urldecode ( http_build_query ( $url ) );
    }
    /*
      function OnDataTableAdd($post, $id = 0)
      {
      if( empty($id))
      {
      return false;
      }
      if( isset($_GET["method"]) )
      {
      $mthd = $_GET["method"];
      if( $mthd == "Info" ) // добавляется товар или категория
      {
      if( isset($_GET["top"])) // добавляется товар
      {
      $ret = $this->addDefaultTagsValues($id, $_GET["top"]);
      return $ret;
      }
      else // добавляется категория
      {

      }
      }
      elseif( $mthd == "Tags" ) // добавляется характеристика
      {
      }
      elseif( $mthd == "productTagsValues" ) // добавляется значение характеристики
      {
      }
      }
      return false;
      }


      function addDefaultTagsValues($id, $top)
      {
      //        if( empty($id) || empty($top) )
      //        {
      //            return false;
      //        }
      //
      //        $tags = Tools::getParentCategoriesTags( $top );
      //
      //        foreach( $tags as $tag)
      //        {
      //            $query = "INSERT `prefix_products_tags_values` "
      //                    . " (`product_id`, `tag_id`, `value`) "
      //                    . " VALUES ($id, {$tag['id']}, '{$tag['default']}')";
      //
      //            $valId = SqlTools::insert($query); //?? проверка успешности
      //        }
      //        return true;
      }
     */

    function Sorting ()
    {
        $this->title = 'Сортировка';
        $this->hint['text'] = "Первой будет применяться сортировка с наименьшим значением поля 'Порядок использования'";
        $this->content = $this->DataTableAdvanced (
            $this->table_sorting, array
            (
            //Имена системных полей
            'nouns' => array
                (
                'id' => 'id',
                'name' => 'name',
                'order' => 'order', // INT
                'direction' => 'direction', // ENUM(Y,N)
            ),
            //Отображение контролов
            'controls' => array
                (
                'add',
                'edit',
                'del'
            )
            ), array
            (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'type' => array
                (
                'name' => 'Поле сортировки',
                'enum' => array
                    (
                    'name' => array ( 'type' => 'name', 'name' => 'Сортировка по наименованию', 'substitute' => 'названию' ),
                    'rate' => array ( 'type' => 'rate', 'name' => 'Сортировка по рейтингу', 'substitute' => 'рейтингу' ),
                    'price' => array ( 'type' => 'price', 'name' => 'Сортировка по цене', 'substitute' => 'цене' ),
                    'shortName' => array ( 'type' => 'shortName', 'name' => 'Сортировка по артикулу', 'substitute' => 'артикулу' )
                )
            ),
            'order' => array ( 'name' => 'Порядок использования', 'class' => 'min' ),
            'direction' => array ( 'name' => 'По возрастанию', 'class' => 'min', 'transform' => 'YesNo' )
            )
        );
    }

    public function allTags ()
    {
        $table = 'catalog_tags';
        $this->content = $this->DataTableAdvanced ( $table, array
        (
            //Имена системных полей
            'nouns' => array
            (
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'alias' => 'order', // INT
                'tagType' => 'tagType', // ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified' // DATETIME
            ),
            //Отображение контролов
            'controls' => array
            (
                'add',
                'edit',
                'del'
            )
        ),
        array
        (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Наименование' ),
            'alias' => array
            (
                'name' => 'Имя для вызова',
                'length' => '1-128'
            ),
            'tagType' => array
            (
                'name' => 'Тип характеристики',
                'class' => 'min'
            )
        ) );
    }

    private function hasChildren()
    {
        $currentCategory = filter_input(INPUT_GET, "getFieldsById", FILTER_SANITIZE_NUMBER_INT);
        if($currentCategory)
        {
            $hasSub = SqlTools::selectValue ( "SELECT COUNT(*) FROM `prefix_products_topics` WHERE `top`=$currentCategory AND `show`='Y' AND `deleted`='N'" );
        }
        else
        {
            $hasSub = 0;
        }

        return $hasSub > 0;
    }
    /*Костыль*/
    public function getModuleMap ()
    {
        $whereClause = "WHERE `deleted`='N' AND `show`='Y'";

        $categoryQuery = "
            SELECT
                t.`id` AS id,
                t.`top` AS parentId,
                t.`order` AS 'order',
                t.`nav` AS nav,
                t.`name` AS title,
                t.`show` AS view,
                t.`deleted` AS deleted
            FROM `prefix_products_topics` t
            $whereClause
            ORDER BY `order` ASC
            ";
        $categories = SqlTools::selectObjects ( $categoryQuery, "CmsSiteDoc", "id" );

        $productQuery = "
            SELECT
                p.`id` AS id,
                p.`top` AS parentId,
                p.`order` AS 'order',
                p.`nav` AS nav,
                p.`name` AS title,
                p.`show` AS view,
                p.`deleted` AS deleted
            FROM `prefix_products` AS p
            $whereClause
            ORDER BY `order`";

        $products = SqlTools::selectObjects ( $productQuery, "CmsSiteDoc", "id" );
        foreach ( $products as $product )
        {
            $product->link = $this->Link ( $product->parentId, $product->id );
        }

        $docsTree = array ();
        foreach ( $categories as $doc )
        {
            $doc->link = $this->Link ( $doc->id );
            $doc->children = ArrayTools::select ( $products, "parentId", $doc->id );
            if ( $doc->parentId == 0 )
            {
                $docsTree[$doc->id] = $doc;
            }
            else
            {
                if ( !array_key_exists ( $doc->parentId, $categories ) )
                    continue;
                $parentDoc = $categories[$doc->parentId];
                $parentDoc->docs[$doc->id] = $doc;
            }
        }

        return $docsTree;
    }

    public function Link ( $topicId = 0, $productId = 0 )
    {
        $topicId = ( int ) $topicId;
        $productId = ( int ) $productId;

        if ( $topicId != 0 || $productId != 0 )
        {
            if ( $productId != 0 )
            {
                $product = ArrayTools::head ( SqlTools::selectObjects ( "SELECT `id`, `top`, `nav` FROM `prefix_products` WHERE `id`=$productId" ) );
                $productLink = "/" . ( $product->nav ? : $product->id );
                $topicId = $product->top;
            }
            else
            {
                $productLink = "";
            }

//            $parents = Tools::getParentCategories ( $topicId );
//            array_shift ( $parents );
//            $parents = array_reverse ( $parents );
            $parents[] = $topicId;
            $topics = SqlTools::selectObjects ( "SELECT `id`, `top`, `nav` FROM `prefix_products_topics` WHERE `id` IN (" . ArrayTools::numberList ( $parents ) . ")", null, "id" );
            $topicsLinkChain = array ();
            foreach ( $parents as $id )
            {
                $topic = $topics[$id];
                $topicsLinkChain[] = $topic->nav ? : $topic->id;
            }
            $topicsLink = implode ( "/", $topicsLinkChain );

            $url = "/$topicsLink" . $productLink;
        }
        return Starter::app ()->content->getLinkByModule ( __CLASS__ ) . $url;
    }
}
