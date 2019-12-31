<?php

/**
 * Description of Golos
 *
 * @author knn
 */
class Golos extends AdminModule
{
    const name = 'Голосование';
    const order = 9;
    const icon = 'bullhorn';

    private $table = 'golos';
    private $table_detail = 'golos_detail';

    function Info ()
    {
        //Топ
        if ( !isset ( $_GET['top'] ) )
        {
            $this->title = 'Голосование';
            $_GET['orderd'] = 'DESC';
            $this->hint['text'] = 'Вы можете добавить голосование ...';
            $this->content = $this->DataTable (
                $this->table, array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'enabled' => 'enabled', // INT(1)
                ),
                //Отображение контролов
                'controls' => array (
                    'add',
                    'edit',
                    'del'
                ),
                //Табы (методы этого класса)
                'tabs' => array (
//                    '_Regions' => 'Регионы'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array ( 'name' => 'Название голосования', 'length' => '1-128', 'link' => $this->GetLink () . '&top={id}' ), // VARCHAR
                'enabled' => array ( 'name' => 'Активное', 'length' => '0-2' )
                ), '', 'id'
            );
        }
        //Поля формы
        else
        {
            $i = SqlTools::selectRows ( "SELECT * FROM `prefix_golos` WHERE `id`=" . ( int ) $_GET['top'] );
            $this->title = '<a href="' . $this->GetLink () . '">Голосование</a> → ' . $i[0]['name'];
            $this->content = $this->DataTable (
                $this->table_detail, array (
                //Имена системных полей
                'nouns' => array (
                    'id' => 'id', // INT
                    'golos_id' => 'golos_id', // INT
                    'name' => 'name', // VARCHAR
                    'enabled' => 'enabled', // INT(1)
                ),
                //Отображение контролов
                'controls' => array (
                    'add',
                    'edit',
                    'del'
                )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'golos_id' => array (
                    'name' => 'Голосование',
                    'default' => $_GET['top'],
                    'hide_from_table' => true,
                    'hide_from_form' => true,
                ),
                'order' => array ( 'name' => 'Пункт голосования по порядку', 'class' => 'min' ),
                'quest' => array ( 'name' => 'Вариант ответа', 'length' => '1-350' ),
                'answers' => array ( 'name' => 'Ответов', 'legth' => '0-10' ),
                ), '`golos_id`= ' . ( int ) $_GET['top']
            );
        }
    }
}
