<?php

class Blocks extends AdminModule
{
    const name = 'Блоки';
    const order = 2;
    const icon = 'cubes';

    function Info ()
    {
        $this->title = 'Блоки';
        $this->hint['text'] = 'Для вставки блока в в любой текст — <code>{block:0}</code><br /><br /> Где 0 — Id или callname нужного блока';
        $this->content = $this->DataTable ( 'blocks',
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
                'text' => 'text'
            ],
            //Отображение контролов
            'controls' =>
            [
                'add',
                'edit'
            ],
            //Табы (методы этого класса)
            'tabs' =>
            [
                'Images' => 'Изображения'
            ]
        ],
        [
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Название блока', 'length' => '1-128' ),
            'callname' => array ( 'name' => 'Имя для вызова', 'length' => '0-128' ),
            'template' => array ( 'name' => 'Шаблон', 'length' => '0-255' ),
            'text' => array ( 'name' => 'HTML код блока', 'hide_from_table' => true ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            'order' => array ( 'name' => 'Порядок', 'class' => 'min' )
        ] );
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
            'module_id' => $id
        ) );
        if ( $isNoEcho )
            return $result;
        else
            echo $result;
            exit ();
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
