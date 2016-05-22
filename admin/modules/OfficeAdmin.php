<?php

/**
 * Description of OfficeAdmin
 *
 * @author kraser
 */
class OfficeAdmin extends AdminModule
{
    const name = 'Кабинет';
    const order = 10;
    const icon = 'group';

    public $submenu = array
        (
        'Orders' => 'Заказы',
        'Users' => 'Клиенты'
    );

    function Info ()
    {
        $this->Orders ();
    }

    function Orders ()
    {
        //Топ
        if ( !isset ( $_GET['top'] ) )
        {
            $this->title = 'Список заказов';
            //Так проще задать сортировку
            if ( !isset ( $_GET['orderd'] ) )
            {
                $_GET['orderd'] = 'DESC';
            }

            $this->content = $this->DataTableAdvanced (
            'shop_orders', array
             (
                //Имена системных полей
                'nouns' => array
                (
                    'id' => 'id', // INT
                    'name' => 'name', // VARCHAR
                    'userId' => 'userId',
                    'deleted' => 'deleted', // ENUM(Y,N)
                    'created' => 'date', // DATETIME
                    'modified' => 'modified' // DATETIME
                ),
                //Отображение контролов
                'controls' => array ( 'add', 'edit', 'del' ),
                'tabs' => array ()
            ),
            array
            (
                'id' => array ( 'name' => '№', 'class' => 'min', 'link' => $this->GetLink () . '&top={id}' ),
                'name' => array ( 'name' => 'Имя покупателя на момент заказа', "link" => '?module=OfficeAdmin&method=Users&top={userId}', 'length' => '0-255' ),
                'mail' => array ( 'name' => 'Почта', 'length' => '0-100', 'hide_from_table' => true ),
                'phone' => array ( 'name' => 'Телефон', 'length' => '1-100' ),
                'address' => array ( 'name' => 'Адрес', 'length' => '0-120', 'hide_from_table' => true ),
                'date' => array ( 'name' => 'Дата заказа' ),
                'status' => array
                (
                    'name' => 'Статус',
                    'select' => array
                    (
                        //Обязательные
                        'table' => 'shop_statuses',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id'
                    )
                ),
                'paymethod' => array
                (
                    'name' => 'Метод оплаты',
                    'select' => array
                    (
                        //Обязательные
                        'table' => 'shop_paymethods',
                        'name' => 'name',
                        //Необязательные
                        'id' => 'id',
                        'order' => 'order'
                    ),
                    'hide_from_table' => true
                )
            ), '', 'date' );
        }
        else
        {
            $this->title = "<a href='" . $this->GetLink () . "#open" . ( int ) $_GET['top'] . "'>Заказ №" . ( int ) $_GET['top'] . "</a>";
            $this->content = $this->DataTable (
                'shop_orders_items', array
                (
                    //Имена системных полей
                    'nouns' => array
                    (
                        'id' => 'id', // INT
                        'name' => 'name'  // VARCHAR
                    ),
                    //Отображение контролов
                    'controls' => array ( 'add', 'edit', 'del' )
                ),
                array
                (
                    'id' => array ( 'name' => '№', 'class' => 'min' ),
                    'name' => array ( 'name' => 'Имя покупателя на момент заказа', "link" => '?module=OfficeAdmin&method=Users&top={userId}', 'length' => '0-255' ),
                    'name' => array
                    (
                        'name' => 'Наименование товара на момент заказа',
                        "link" => '?module=OfficeAdmin&method=Users&top={userId}',
                        'length' => '0-255',
                        'transform' => function($v, $row)
                        {
                            return '<a target="_blank" href="http://' . $_SERVER['SERVER_NAME'] . $row['link'] . '">' . $v . '</a>';
                        }
                    ),
                    'price' => array ( 'name' => 'Цена', 'style' => 'text-align:right' ),
                    'count' => array ( 'name' => 'Количество', 'style' => 'text-align:right' ),
                    'total_fake' => array
                    (
                        'name' => 'Итого',
                        'style' => 'text-align:right',
                        'transform' => function($v, $row)
                        {
                            return priceFormat ( $row['price'] * $row['count'] );
                        }
                    ),
                    'mail' => array ( 'name' => 'Почта', 'length' => '0-100' ),
                    'phone' => array ( 'name' => 'Телефон', 'length' => '0-100' ),
                    'address' => array ( 'name' => 'Адрес', 'length' => '0-120' ),
                    'date' => array ( 'name' => 'Дата заказа' ),
                ), '`order`=' . ( int ) $_GET['top'] );
        }
    }

    function Users ()
    {
        $usersId = array_keys ( SqlTools::selectRows ( "SELECT * FROM `prefix_shop_orders`", MYSQL_ASSOC, "userId" ) );
        $listId = ArrayTools::numberList ( $usersId );
        $where = $listId ? "id IN ($listId)" : "";
        //Топ
        if ( !isset ( $_GET['top'] ) )
        {
            $this->title = 'Список покупателей';
            if ( !isset ( $_GET['orderd'] ) )
            {
                $_GET['orderd'] = 'DESC';
            }

            $this->content = $this->DataTableAdvanced (
                'users', array
                (
                    //Имена системных полей
                    'nouns' => array
                    (
                        'id' => 'id', // INT
                        'name' => 'name', // VARCHAR
                    ),
                    //Отображение контролов
                    'controls' => array ( 'add', 'edit', 'del' ),
                    'tabs' => array ( 'Items' => 'Заказ' )
                ),
                array
                (
                    'id' => array ( 'name' => '№', 'class' => 'min', 'link' => $this->GetLink () . '&top={id}' ),
                    'name' => array ( 'name' => 'Имя покупателя на текущий момент', 'link' => $this->GetLink () . '&top={id}', 'length' => '0-255' ),
                    'mail' => array ( 'name' => 'Почта', 'length' => '0-100' ),
                    'phone' => array ( 'name' => 'Телефон', 'length' => '1-100' ),
                    'address' => array ( 'name' => 'Адрес', 'length' => '0-120' ),
                    'email' => array ( 'name' => 'Почта' ),
                    'status' => array ( 'name' => 'Статус' )
                ), $where, 'id' );
        }
        else
        {
            $this->title = "<a href='" . $this->GetLink () . "#open" . ( int ) $_GET['top'] . "'>Покупатель №" . ( int ) $_GET['top'] . "</a>";
            $this->content = $this->DataTable (
                'users', array (
                //Имена системных полей
                'nouns' => array
                    (
                    'id' => 'id', // INT
                    'name' => 'name'  // VARCHAR
                ),
                //Отображение контролов
                'controls' => array ( 'add', 'edit', 'del' )
                ), array (
                'id' => array ( 'name' => '№', 'class' => 'min' ),
                'name' => array (
                    'name' => 'Имя покупателя на текущий момент',
                    'length' => '0-255',
                    'link' => $this->GetLink () . '#open{id}'
                ),
                'mail' => array ( 'name' => 'Почта', 'length' => '0-100' ),
                'phone' => array ( 'name' => 'Телефон', 'length' => '0-100' ),
                'address' => array ( 'name' => 'Адрес', 'length' => '0-120' ),
                ), '`id`=' . ( int ) $_GET['top'] );
        }
    }

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
}
