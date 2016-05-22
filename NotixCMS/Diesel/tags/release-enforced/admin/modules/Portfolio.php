<?php

class Portfolio extends AdminModule
{
    const name = 'Портфолио';
    const order = 11;
    const icon = 'briefcase';

    function Info ()
    {
        //Таблица товаров
        if ( isset ( $_GET['top'] ) )
        {
            $i = SqlTools::selectRows ( "SELECT * FROM `prefix_portfolio_topics` WHERE `id`=" . ( int ) $_GET['top'] );

            $this->title = '<a href="' . $this->GetLink () . '">Портфолио</a> → ' . $i[0]['name'];

            $this->content = $this->DataTableAdvanced ( 'portfolio', array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text'  // TEXT
                ),
                //Отображение контролов
                'controls' => array (
                    'add',
                    'edit',
                    'no_edit_text',
                    'del'
                ),
                //Табы (методы этого класса)
                'tabs' => array (
                    'PortfolioPages' => 'Страницы проекта',
                    '_Seo' => 'SEO',
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array ( 'name' => 'Наименование', 'length' => '1-128' ),
                'shortName' => array ( 'name' => 'Краткое наименование', 'length' => '0-64', 'hide_from_table' => true ),
                'nav' => array ( 'name' => 'Опциональная URI ссылка', 'length' => '0-100', 'hide_from_table' => true ),
                'anons' => array ( 'name' => 'Анонс проекта', 'hide_from_table' => true ), //, 'edit_text'=>1), // elFinder не дружит с tinyMCE
                'text' => array ( 'name' => 'Описание проекта', 'hide_from_table' => true ), //, 'edit_text'=>1),
                'worked' => array ( 'name' => 'Над проектом работали', 'hide_from_table' => true ), //, 'edit_text'=>1),
                'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
                'top' => array (
                    'name' => 'Раздел',
                    //'class'		=> 'min',
                    'default' => ( int ) $_GET['top'],
                    'hide_from_table' => true,
                    'select' => array (
                        //Обязательные
                        'table' => 'portfolio_topics',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order',
                        //'allow_null'=> true,
                        'top' => 'top',
                        'deleted' => 'deleted'
                    )
                ),
                'content_top' => array (
                    'name' => 'Относится к разделам (для примера, не рабочее поле)',
                    //'class'		=> 'min',
                    //'default'	=> (int)$_GET['top'],
                    'multiselect' => array (
                        //Обязательные
                        'table' => 'content',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order',
                        //'allow_null'=> true,
                        'top' => 'top',
                        'deleted' => 'deleted'
                    )
                ),
                ), '`top` = ' . ( int ) $_GET['top'] );
        }
        //Дерево разделов
        else
        {

            //Менюшечка
            /*
              $this->hint['text'] = '
              <ul>
              <li><a href="'.$this->GetLink('MakeYML').'">Обновить YML</a></li>
              </ul>
              ';
             */

            $this->title = 'Портфолио';
            $this->content = $this->DataTree ( 'portfolio_topics', array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified', // DATETIME
                    'text' => 'text', // TEXT
                    'top' => 'top'
                ),
                //Отображение контролов
                'controls' => array (
                    'add_root',
                    'add_sub',
                    'edit',
                    'list' => $this->GetLink () . '&top={id}',
                    'del'
                ),
                //Зависимая таблица (напрмер товары или новости по рубрикам)
                'inner' => array (
                    'table' => 'portfolio', //Имя таблицы
                    'top_key' => 'top', //Ключ соответствия категории товарам
                    'deleted' => 'deleted' //Поле «удалено»
                ),
                //Табы (методы этого класса)
                'tabs' => array (
                    'Images' => 'Изображения',
                    '_Seo' => 'SEO'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array ( 'name' => 'Наименование', 'length' => '1-128', 'link' => $this->GetLink () . '&top={id}' ),
                'nav' => array ( 'name' => 'URI ссылка', 'length' => '0-32', 'regex' => '/^([a-z0-9-_]+)?$/i', 'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса', 'if_empty_make_uri' => 'name' ),
                'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
                'content_top' => array (
                    'name' => 'Относится к разделам (для примера, не рабочее поле)',
                    //'class'		=> 'min',
                    //'default'	=> (int)$_GET['top'],
                    'multiselect' => array (
                        //Обязательные
                        'table' => 'content',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order',
                        //'allow_null'=> true,
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
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);

        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $info = null;
        $images = new Images();

        //Добавление картинки
        if ( !empty ( $_FILES ) )
        {
            if ( $_FILES['image']['error'] == 1 )
            {
                $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );
            }
            $images->AddImage ( $_FILES['image']['tmp_name'], __CLASS__, $id, $_FILES['image']['name'] );
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
        {
            $images->StarImage ( $_GET['star'] );
        }

        //Удаление
        if ( isset ( $_GET['del'] ) )
        {
            $images->DelImage ( $_GET['del'] );
        }

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $images->GetImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
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

    /**
     * Таб с страницами проекта
     * title, keywords, description
     */
    function PortfolioPages ( $isNoEcho = false )
    {

        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);

        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $info = null;
        $images = new Images();

        $data = array ();
        $error = false;
        $files = array ();

        //Добавление картинки
        if ( !empty ( $_POST['add'] ) )
        {



            foreach ( $_FILES as $file )
            {
                if ( empty ( $file ) )
                {
                    $data = array ( 'error' => 'Файл не загружен' );
                }
                if ( $file['error'] == 1 )
                {
                    $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );
                    $data = array ( 'error' => 'Ошибка - размер файла должен быть меньше ' . ini_get ( "upload_max_filesize" ) );
                }
                $imageId = $images->AddImage ( $file['tmp_name'], __CLASS__, $id, $file['name'] );
                $data = ($imageId === false) ? array ( 'error' => 'Файл не загружен на сервер' ) : array ( 'files' => $files );

                $data = array ( 'success' => 'Запись успешно добавлена!', 'formData' => $_POST );
                $order = (!empty ( $_POST['sort'] )) ? $_POST['sort'] : 0;
                $text = (!empty ( $_POST['text'] )) ? $_POST['text'] : '';

                SqlTools::insert ( "INSERT INTO `prefix_portfolio_images` (`image_id`, `order`, `description`) VALUES ('" . $imageId . "', '" . $order . "', '" . $text . "')" );
            }

            echo json_encode ( $data );
            exit ();
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
        {
            $images->StarImage ( $_GET['star'] );
        }

        //Удаление
        if ( isset ( $_POST['del'] ) )
        {
            // exit('23');
            $deleted = $images->DelImage ( $_POST['item_id'] );
            SqlTools::execute ( "DELETE FROM `prefix_portfolio_images` WHERE `image_id`='" . $_POST['item_id'] . "'" );

            $data = ($deleted === true) ? array ( 'success' => 'Запись удалена!', 'formData' => $_POST ) : array ( 'error' => 'Ошибка при удалении' );

            echo json_encode ( $data );
            exit ();
        }

        //Редактирование
        if ( isset ( $_POST['update'] ) && isset ( $_POST['item_id'] ) )
        {
            $order = (!empty ( $_POST['sort'] )) ? $_POST['sort'] : 0;
            $text = (!empty ( $_POST['text'] )) ? $_POST['text'] : '';
            $item_id = ( int ) $_POST['item_id'];

            $updated = SqlTools::execute ( "UPDATE `prefix_portfolio_images` SET `order`='" . $order . "', `description`='" . $text . "' WHERE `image_id`='" . $item_id . "' LIMIT 1" );

            $data = ($updated === true) ? array ( 'success' => 'Запись успешно обновлена!', 'formData' => $_POST ) : array ( 'error' => 'Ошибка при обновлении!' );

            echo json_encode ( $data );
            exit ();
        }

        $items = array ();

        $items = SqlTools::selectRows ( "SELECT *
                          FROM `prefix_portfolio_images` AS pi
                          LEFT JOIN `prefix_images` AS i ON i.`id`=pi.`image_id`
                          WHERE i.`module_id`='" . $id . "'
                          ORDER BY i.`main`, pi.`order`" );

        // exit(var_dump($items));

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $items,
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id,
            'info' => $info
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
}
