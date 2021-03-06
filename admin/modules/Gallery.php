<?php

/**
 *
 * @author knn
 */
class Gallery extends AdminModule
{
    const name = 'Галерея';
    const order = 30;
    const icon = 'picture-o';

    public static $table = 'gallery';
    public $submenu = array (
        'Photo' => 'Обработать фото',
    );

    public function Photo ()
    {
        $notification = array ();
        if ( isset ( $_GET['action'] ) )
        {
            $images = new Images();
            $notification[] = $images->ProccessImages ( __CLASS__ );
        }

        $this->title = 'Обработка фото';
        $this->content = tpl ( '/modules/' . __CLASS__ . '/photo', array (
            'link' => $this->GetLink (),
            'notification' => $notification,
        ) );
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

        //Добавление видео
        if ( isset ( $_GET['add'] ) )
            $imager->addVideo ( $_GET['add'], $_GET['url'], $_GET['title'] );

        //Удаление
        if ( isset ( $_GET['del'] ) )
            $imager->delImage ( $_GET['del'] );

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $imager->getImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id
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
        $this->title = 'Галерея';
        $_GET['orderd'] = 'DESC';
        $this->content = $this->DataTable (
            'gallery', array (
            //Имена системных полей
            'nouns' => array (
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'deleted' => 'deleted', // ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified', // DATETIME
                'text' => 'text', // TEXT
                'alias' => 'alias', // VARCHAR
            ),
            //Отображение контролов
            'controls' => array (
                'add',
                'edit',
                'no_edit_text', // такой флаг означает, что не будет выводиться контрол редактирования поля text редактором tinyMCE
                'del'
            ),
            //Табы (методы этого класса)
            'tabs' => array (
                'Images' => 'Изображения'/*,
                '_Regions' => 'Регионы'*/
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Заголовок', 'length' => '0-200' ),
            'text' => array ( 'name' => 'Строки текста', 'length' => '0-500' ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            'date' => array ( 'name' => 'Дата создания', 'transform' => function ($str)
                {
                    return DatetimeTools::inclinedDate ( $str );
                } ),
            'alias' => array ( 'name' => 'Алиас', 'length' => '0-200' ),
            ), '', 'date'
        );

        $this->hint['text'] = 'Вы можете добавить заголовок, изображение или изменить строки текста в свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" />';
    }
}
