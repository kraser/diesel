<?php
/**
 * Description of Menu
 *
 * @author kraser
 */
class Menu extends AdminModule
{
    const name = 'Меню';
    const order = 1;
    const icon = 'sitemap';

    public function Info ()
    {
        $this->content = $this->DataTree ( "menu",
        [
            //Имена системных полей
            'nouns' => array
            (
                'id' => 'id',
                'name' => 'title',
                'deleted' => 'deleted',
                'created' => 'created',
                'modified' => 'modified',
                'top' => 'parentId',
                'order' => 'order'
            ),
            //Отображение контролов
            'controls' => array
            (
                'add_root',
                'add_sub',
                'edit',
                'list' => $this->GetLink ( '', array ( 'top' => '{id}' ) ),
                'del'
            ),
            //Зависимая таблица (напрмер товары или новости по рубрикам)
//            'inner' => array
//            (
//                'table' => $this->table_products, //Имя таблицы
//                'top_key' => 'top', //Ключ соответствия категории товарам
//                'deleted' => 'deleted' //Поле «удалено»
//            ),
            //Табы (методы этого класса)
//            'tabs' => array
//            (
//                'categoryImages' => 'Изображения категорий',
//                '_Seo' => 'SEO'
//            )
        ],
        [
            'id' => array ( 'name' => '№', 'class' => 'min' ),
            'title' => array
            (
                'name' => 'Наименование',
                'length' => '1-128',
                'link' => $this->GetLink ( '', array ( 'top' => '{id}' ) )
            ),
            'alias' => array
            (
                'name' => 'Псевдо',
                'length' => '0-32',
                'regex' => '/^([a-z0-9-_]+)?$/i',
                'regex_error' => 'Псевдоним может быть только из цифр, латинских букв и дефиса',
                'if_empty_make_uri' => 'name'
            ),
            'link' => array
            (
                'name' => 'URI ссылка',
                'length' => '0-32',
                'regex' => '/^(\\/[a-z0-9-_]*)?$/i',
                'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
                'if_empty_make_uri' => 'name'
            ),
            //'order' => array ( 'name' => 'Порядок', 'class' => 'min' ),
            //'text' => array ( 'name' => 'Детальное описание категории', 'hide_from_table' => true, 'edit_text' => 1 ),
            //'cases' => array ( 'name' => 'Краткое описание категории', 'hide_from_table' => true, 'edit_text' => 1 ),
            //'isModel' => array ( 'name' => 'Модельный ряд', 'class' => 'min', 'default' => 'N', "hide" => $this->hasChildren() ),

//            'content_top' => array
//            (
//                'name' => 'Относится к разделам (для примера, не рабочее поле)',
//                'multiselect' => array
//                (
//                    //Обязательные
//                    'table' => 'content',
//                    'name' => 'name',
//                    //Необязательные
//                    'id' => 'id',
//                    'order' => 'order',
//                    'top' => 'top',
//                    'deleted' => 'deleted',
//                    'size' => 5
//                )
//            ),
//            'rate' => array ( 'name' => 'Рейтинг (количество просмотров)' )
        ] );
    }
}