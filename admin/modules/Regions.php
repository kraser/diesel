<?php

/**
 *
 * @author Vasyl Hebrian
 */
class Regions extends AdminModule
{
    const name = 'Регионы';
    const icon = 'globe';
    const order = 81;

    function Info ()
    {
        $this->title = 'Регионы';
        $this->content = $this->DataTable (
            'regions', array (
            //Имена системных полей
            'nouns' => array (
                'id' => 'id', // INT
                'name' => 'name', // VARCHAR
                'deleted' => 'deleted', // ENUM(Y,N)
            ),
            //Отображение контролов
            'controls' => array (
                'add',
                'edit',
                'del'
            )
            ), array (
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'name' => array ( 'name' => 'Заголовок', 'length' => '0-200' ),
            'show' => array ( 'name' => 'Показывать', 'class' => 'min' ),
            ), '', ''
        );
    }
}
