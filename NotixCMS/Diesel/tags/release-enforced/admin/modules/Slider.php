<?php

/**
 *
 * @author knn
 */
class Slider extends AdminModule
{
    const name = 'Слайдер';
    const order = 20;
    const icon = 'sliders';

    public static $table = 'slider';

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
                $info = "Ошибка - размер файла должен быть меньше " . ini_get ( "upload_max_filesize" );

            $images->AddImage ( $_FILES['image']['tmp_name'], __CLASS__, $id, $_FILES['image']['name'] );
        }

        //Задание картинки по-умолчанию
        if ( isset ( $_GET['star'] ) )
        {
            $images->StarImage ( $_GET['star'] );
        }

        //Удаление
        if ( isset ( $_GET['del'] ) )
            $images->DelImage ( $_GET['del'] );

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $images->GetImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id,
            'info' => "Здесь лучше добавлять изображения разрешением 1000х300 (ШхВ) пикселей, плюс-минус 50.",
        ) );
        if ( $isNoEcho )
            return $result;
        else
            echo $result;
            exit ();
    }

    function Info ()
    {
        $this->title = 'Слайдер';
        $_GET['orderd'] = 'DESC';
        $this->content = $this->DataTable (
            'slider', array (
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
                'no_edit_text', // такой флаг означает, что не будет выводиться контрол редактирования поля text редактором tinyMCE
                'del'
            ),
            //Табы (методы этого класса)
            'tabs' => array (
                'Images' => 'Изображения'/*,
                '_Regions' => 'Регионы',*/
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Заголовок', 'length' => '0-200' ),
            'text' => array ( 'name' => 'Строки текста', 'length' => '0-500', 'edit_text' => true ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            'link' => array ( 'name' => 'Ссылка (вида http://... - из адресной строки)', 'length' => '0-200', 'hide_from_table' => true ),
            'date' => array ( 'name' => 'Дата создания', 'transform' => function($str)
                {
                    return DatetimeTools::inclinedDate ( $str );
                } )
            ), '', 'date' );

        $this->hint['text'] = 'Вы можете добавить заголовок, изображение или изменить строки текста в свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" />';
    }
}
