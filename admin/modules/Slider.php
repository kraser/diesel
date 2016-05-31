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
        {
            $imager->starImage ( $_GET['star'] );
        }

        //Удаление
        if ( isset ( $_GET['del'] ) )
            $imager->delImage ( $_GET['del'] );

        $result = tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $imager->getImages ( __CLASS__, $id ),
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
        $this->content = $this->DataTable ( 'slider',
        [
            //Имена системных полей
            'nouns' =>
            [
                'id' => 'id',
                'name' => 'name',
                'deleted' => 'deleted',
                'created' => 'created',
                'modified' => 'modified',
                'text' => 'text',
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit',
                'no_edit_text',// флаг означает, что не будет выводиться контрол редактирования поля text редактором tinyMCE
                'del'
            ],
            //Табы (методы этого класса)
            'tabs' =>
            [
                'Images' => 'Изображения'
            ]
        ],
        [
            'id' => [ 'name' => '№', 'class' => 'min' ],
            'order' =>
            [
                'name' => 'Порядковый номер',
                'length' => '0-200',
                'regex' => '/^[\d]+$/i',
                'regex_error' => 'Порядковый номер может быть только числом'
            ],
            'name' => [ 'name' => 'Заголовок', 'length' => '0-200' ],
            'text' => [ 'name' => 'Строки текста', 'length' => '0-1500', 'edit_text' => true ],
            'show' => [ 'name' => 'Показывать', 'class' => 'min' ],
            'link' => [ 'name' => 'Ссылка', 'length' => '0-200', 'hide_from_table' => true ],
            'date' => [ 'name' => 'Дата создания', 'transform' => function($str)
            {
                return DatetimeTools::inclinedDate ( $str );
            } ]
        ], '', 'order' );

        $this->hint['text'] = 'Вы можете добавить заголовок, изображение или изменить строки текста в свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" />';
    }
}
