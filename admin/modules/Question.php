<?php

/**
 *
 * @author knn
 */
class Question extends AdminModule
{
    const name = 'Вопрос эксперту';
    const order = 11;
    const icon = 'question';

    function Images ()
    {
        $id = ( int ) (isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0);
        if ( $id == 0 )
        {
            echo 'Сначала создайте запись';
            exit ();
        }

        $images = new Images();

        //Добавление картинки
        if ( !empty ( $_FILES ) )
        {
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

        echo tpl ( 'modules/' . __CLASS__ . '/' . __FUNCTION__, array (
            'images' => $images->GetImages ( __CLASS__, $id ),
            'link' => $this->GetLink (),
            'module' => __CLASS__,
            'module_id' => $id,
            'info' => "Здесь следует добавлять изображения разрешением 1000х300 (ШхВ) пикселей, плюс-минус 50.",
        ) );
        exit ();
    }

    function Info ()
    {
        $this->title = 'Вопрос эксперту';
        $_GET['orderd'] = 'DESC';
        $this->content = $this->DataTable ( 'question', array (
            //Имена системных полей
            'nouns' => array (
                'id' => 'id', // INT
                'question' => 'question', // VARCHAR
                'person' => 'person', // VARCHAR
                'deleted' => 'deleted', // ENUM(Y,N)
                'created' => 'created', // DATETIME
                'modified' => 'modified', // DATETIME
                'answer' => 'answer', // TEXT
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
                'Images' => 'Изображения'
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'question' => array ( 'name' => 'Вопрос', 'length' => '0-500' ),
            'person' => array (
                'name' => 'Эксперт',
                'select' => array (
                    //Обязательные
                    'table' => 'users',
                    'name' => 'name',
                    //Необязательные
                    'id' => 'id',
                    'order' => 'id',
                    'where' => ' AND `status` = "manager"',
                    'deleted' => 'deleted',
                )
            ),
            'answer' => array ( 'name' => 'Ответ', 'length' => '0-500' ),
            'anons' => array ( 'name' => 'Анонс', 'length' => '0-500' ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            'date' => array ( 'name' => 'Дата создания', 'transform' => function($str)
                {
                    return DatetimeTools::inclinedDate ( $str );
                } )
            ), '', 'date' );

        $this->hint['text'] = 'Вы можете добавить заголовок, изображение или изменить строки текста в свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" />';
    }
}
