<?php

class Forms extends AdminModule
{
    const name = 'Формы';
    const order = 3;
    const icon = 'envelope';

    function Info ()
    {
        //Топ
        if ( !isset ( $_GET['top'] ) )
        {
            $this->title = 'Формы';
            $this->hint['text'] = 'Для вставки формы в шаблон используйте php-синтаксис — <code>&lt;?php form(0); ?&gt;</code><br /> Для вставки в любой текст — <code>{form:0}</code><br /><br /> Где 0 — № нужной формы, он находится в первом столбце таблицы';
            $this->content = $this->DataTable ( 'forms', array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified'
                ),
                //Отображение контролов
                'controls' => array (
                    'add',
                    'edit'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array ( 'name' => 'Название формы', 'length' => '1-128', 'link' => $this->GetLink () . '&top={id}' ),
                'callname' => array ( 'name' => 'Имя для вызова', 'length' => '0-128', 'if_empty_make_uri' => 'name' ),
                'template' => array ( 'name' => 'Шаблон', 'hide_from_table' => true, 'length' => '0-128' ),
                'email' => array ( 'name' => 'Почта назначения', 'length' => '1-128', 'regex' => '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', 'regex_error' => 'Заполните почту верно', 'default' => Starter::app ()->adminMail ),
                'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
                'order' => array ( 'name' => 'Порядок', 'class' => 'min' )
            ) );
        }
        //Поля формы
        else
        {
            $i = SqlTools::selectRows ( "SELECT * FROM `prefix_forms` WHERE `id`=" . ( int ) $_GET['top'] );
            $this->title = '<a href="' . $this->GetLink () . '">Формы</a> → ' . $i[0]['name'];
            $this->content = $this->DataTable ( 'forms_fields', array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'order' => 'order', // INT
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'created', // DATETIME
                    'modified' => 'modified'
                ),
                //Отображение контролов
                'controls' => array (
                    'add',
                    'edit',
                    'del'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'form' => array (
                    'name' => 'Форма',
                    //'class'		=> 'min',
                    'default' => $_GET['top'],
                    'select' => array (
                        //Обязательные
                        'table' => 'forms',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order'
                    )
                ),
                'type' => array ( 'name' => 'Тип поля', 'class' => 'min' ),
                'label' => array ( 'name' => 'Название', 'length' => '1-128' ),
                'name' => array ( 'name' => 'POST-имя поля', 'length' => '0-128', 'if_empty_make_uri' => 'label', 'hide_from_table' => true ),
                'regex' => array ( 'name' => 'Регулярное выражения для проверки поля', 'length' => '0-128', 'hide_from_table' => true ),
                'regex_error' => array ( 'name' => 'Текст ошибки проверки поля', 'length' => '0-128', 'hide_from_table' => true ),
                'default' => array ( 'name' => 'Значение поля по-умолчанию', 'length' => '0-128', 'hide_from_table' => true ),
                'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
                'required' => array ( 'name' => 'Обязательное поле', 'class' => 'min' ),
                'show' => array ( 'name' => 'Показывать поле', 'class' => 'min' )
                ), 'form=' . ( int ) $_GET['top'] );
        }
    }
}
