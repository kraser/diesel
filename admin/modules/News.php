<?php

class News extends AdminModule
{
    const name = 'Новости (пресс-центр)';
    const order = 8;
    const icon = 'linecons-doc';
    public $submenu = array
    (
        'Run' => 'Новости',
        'Topics' => 'Категории',
    );

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
        $imager = Starter::app ()->imager;

        //Добавление картинки
        if ( !empty ( $_FILES ) )
        {
            if ( $_FILES['image']['error'] == 1 )
                $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );

            $imager->addImage ( $_FILES['image']['tmp_name'], __CLASS__, $id, $_FILES['image']['name'] );
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
            $imager->starImage ( $_GET['star'] );

        //Удаление
        if ( isset ( $_GET['del'] ) )
            $imager->delImage ( $_GET['del'] );

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $imager->getImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id,
            'info' => $info,
        ) );

        if ( $isNoEcho )
            return $result;
        else
        {
            echo $result;
            exit ();
        }
    }

    function Info ()
    {
        $this->title = 'Новости';
        $_GET['orderd'] = 'DESC';
        $this->content = $this->DataTableAdvanced ( 'news', array (
            //Имена системных полей
            'nouns' => array (
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'deleted' => 'deleted', // ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified', // DATETIME
                'text' => 'text', // TEXT
            ),
            //Отображение контролов
            'controls' => array (
                'add',
                'edit',
                'del'
            ),
            //Табы (методы этого класса)
            'tabs' => array (
                'Images' => 'Изображения',
                '_Seo' => 'SEO'/*,
                '_Regions' => 'Регионы'*/
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Имя новости', 'length' => '1-128' ),
            'anons' => array ( 'name' => 'Анонс новости', 'hide_from_table' => true ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            'topic' => array
                (
                    'name' => 'Категория',
                    'select' => array
                    (
                        //Обязательные
                        'table' => 'news_topics',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order',
                        'allow_null' => true,
                    )
                ),
            'date' => array ( 'name' => 'Дата публикации', 'transform' => function($str)
                {
                    return DatetimeTools::inclinedDate ( $str );
                } )
            ), '', 'date' );

        $this->hint['text'] = 'Вы можете добавить анонс новости в ее свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" /><br>или изменить саму новость в редактировании содержания <img src="/admin/images/icons/document-text-image.png" style="vertival-align:middle" />';
    }

    function Topics ()
    {
        $this->title = '<a href="' . $this->GetLink () . '">Новости</a> → Категории новостей';

        $this->content = $this->DataTable ( 'news_topics', array (
            //Имена системных полей
            'nouns'    => array (
                'id'      => 'id', // INT
                'name'    => 'name', // VARCHAR
                'deleted' => 'deleted', // ENUM(Y,N)
            ),
            //Отображение контролов
            'controls' => array (
                'add',
                'edit',
                'del'
            ),
        ), array (
            'id'   => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Имя категории', 'length' => '1-128' ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
        ) );
    }
}
