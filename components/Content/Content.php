<?php

class Content extends CmsModule
{
    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->model = "Content";
        $this->template = "mainlayout";
        $this->actions =
        [
            'default' =>
            [
                'method' => 'view'
            ],
            'map' =>
            [
                'method' => 'siteMap'
            ],
            'contacts' =>
            [
                'method' => 'contacts'
            ],
            'send' =>
            [
                'method' => 'sendForm'
            ]
        ];
        $this->table = 'content';

    }

    public function Run ()
    {
        $action = $this->createAction ();
        if ( !$action )
            page404 ();
        return $action->run ();
    }

    public function startController ( $method, $params )
    {
        if ( $this->currentDocument && array_key_exists ( $this->currentDocument->nav, $this->actions ) )
        {
            $method = $this->actions[$this->currentDocument->nav]['method'];
            return $this->$method ();
        }
        else
            return $this->$method ( $params );
    }

    public function view ( $params )
    {
        if ( !$this->currentDocument )
        {
            Starter::app ()->headManager->addMetaText ( Tools::getSettings ( "Content", "customMeta", "" ) );
            return $this->render ( 'mainpage' );
        }

        $this->template = ( $this->currentDocument->template ? basename ( $this->currentDocument->template, '.php' ) : $this->template );
        return $this->render ( 'content', [ 'content' => $this->currentDocument ] );
    }

    public function contacts ()
    {
        return $this->render ( "contacts" );
    }

    public function MainMenu ()
    {

    }

//    function Godmode ()
//    {
//        $suspend = false;
//        if ( $_POST['suspend'] )
//            $suspend = true;
//
//        $_SESSION['godmode_suspended'] = $suspend;
//
//        sendJSON ( array ( 'suspended' => $_SESSION['godmode_suspended'] ) );
//    }

    /**
     * Получение дерева меню
     * @param type $categories
     * @param type $id
     * @return type
     */
//    function Tree ( $categories, $id = 0 )
//    {
//        foreach ( $categories as $category )
//        {
//            $category->link = Starter::app ()->content->getLinkById ( $category->id );
//            $category->subCategories = array ();
//            if ( in_array ( $category->nav, Starter::app ()->urlManager->linkPath ) )
//            {
//                $category->active = true;
//                $activeSet = true;
//            }
//            else
//                $category->active = false;
//
//            $subcategories = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_content` WHERE `top`='" . $category->id . "' AND `show`='Y' AND `deleted`='N' ORDER BY `order`" ), "id" );
//            if ( $subcategories )
//            {
//                $category->subCategories = $subcategories;
//                $this->Tree ( $subcategories, $category->id );
//            }
//        }
//        return ( $categories );
//    }

//    function Menu ( $template = 'mainmenu' )
//    {
//        $select = '';
//        $join = '';
//        $where = '';
////        if ( _REGION !== null )
////        {
////            $select .= ', r.`id` AS `region`';
////            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (c.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
////                . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
////            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
////        }
//
//        $sql = "SELECT c.*" . $select
//            . " FROM `prefix_" . $this->table . "` AS c"
//            . $join
//            . " WHERE c.`deleted` = 'N' AND c.`show` = 'Y'" . $where
//            . " ORDER BY c.`order`, c.`name`";
//
//        $pages = SqlTools::selectRows ( $sql, MYSQL_ASSOC );
//
//        $menuByTop = array ();
//        $activeSet = false;
//        foreach ( $pages as $id => $i )
//        {
//            if ( $i['showmenu'] == 'N' )
//                continue;
//            //Отмечаем текущую страницу
//            if ( in_array ( $i['nav'], Starter::app ()->urlManager->linkPath ) )
//            {
//                $i['active'] = true;
//                $activeSet = true;
//            }
//            else
//                $i['active'] = false;
//
//            $menuByTop[$i['top']][] = $i;
//        }
//
//        if ( empty ( $menuByTop[0] ) )
//            return tpl ( 'parts/' . $template, array ( 'menu' => array () ) );
//
//        //if(!$activeSet) $menuByTop[0][0]['active'] = true;
//
//        foreach ( $menuByTop[0] as $top => $i )
//        {
//            $item['root'] = $i;
//            $item['sub'] = array ();
//
//            //Проверка на подменю из модуля
//            if ( !empty ( $i['module'] ) && $i['active'] )
//            {
//                $obj = Starter::app ()->getModule ( $i['module'] );
//                if ( method_exists ( $obj, 'SubMenu' ) )
//                    $item['sub'] = $obj->SubMenu ();
//            }
//            else
//            {
//                if ( isset ( $menuByTop[$i['id']] ) )
//                {
//                    foreach ( $menuByTop[$i['id']] as $id => $j )
//                    {
//                        $item['sub'][] = $j;
//                    }
//                }
//            }
//
//            $menu[] = $item;
//        }
//        return tpl ( 'parts/' . $template, array ( 'menu' => $menu ) );
//    }
//
//    function getMenuTree ()
//    {
//        $categories = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_content` WHERE `top`='0' AND `show`='Y' AND `deleted`='N' ORDER BY `order`" ), "id" );
//        $tree = ArrayTools::index ( $this->Tree ( $categories ), "id" );
//        //print_r ( $tree );
//        $treeids_ = $this->bread ();
//
//        $treeids = Array ();
//        if ( $treeids_ )
//        {
//            if ( array_key_exists ( 'id', end ( $treeids_ ) ) )
//            {
//                foreach ( $treeids_ as $treeid )
//                {
//                    $treeids['id'][] = $treeid['id'];
//                }
//            }
//            else
//                $treeids['id'] = 0;
//        }
//        else
//            $treeids['id'] = 0;
//
//        $public = $this->TreeHtml ( $tree, 0, 'menu', $treeids );
//
//        print_r ( $public );
//    }

//    function TreeHtml ( $root, $level, $Class, $tree, $idd = '' )
//    {
//        $html = "";
//        $class = "level" . $level;
//        $menu_count = count ( $root );
//        $i = 1;
//        if ( $level >= 1 )
//        {
//            $categoryClass = "sub_" . $Class . "_list";
//            $id = "id='sub_" . $Class . "_List" . $idd . "'";
//
//
//            if ( ($level) < count ( $tree['id'] ) )
//            {
//                if ( ($idd) && $idd == $tree['id'][$level] )
//                    $style = "style='display: block;'";
//                else
//                    $style = "style='display: none;'";
//            }
//            else
//                $style = "style='display: none;'";
//        }
//        else
//        {
//            $categoryClass = "index_" . $Class . "_list";
//            $id = "id='index_" . $Class . "_List'";
//            $style = "";
//        }
//        $html .= "<ul " . $id . " class='$categoryClass' " . $style . ">";
//        foreach ( $root as $branch )
//        {
//            if ( $branch->active )
//                $active = 'active';
//            else
//                $active = '';
//
//            if ( $i == $menu_count )
//            {
//                if ( count ( $branch->subCategories ) )
//                {
//                    $html .= "<li class='sub_item' id='sub_item_id_" . $branch->id . "'><a class='hide'>" . $branch->name . "</a><div class='stripe-next'></div>";
//                    $html .= $this->TreeHtml ( $branch->subCategories, $level + 1, $Class, $tree, $branch->id );
//                    $html .= "</li>";
//                }
//                else
//                    $html .= "<li class='sub_item'><a class='" . $active . "' href='" . $branch->link . "'>" . $branch->name . "</a></li>";
//            }
//            else
//            {
//                if ( count ( $branch->subCategories ) )
//                {
//                    $html .= "<li class='sub_item' id='sub_item_id_" . $branch->id . "'><a class='hide'>" . $branch->name . "</a><div class='stripe-next'></div>";
//                    $html .= $this->TreeHtml ( $branch->subCategories, $level + 1, $Class, $tree, $branch->id );
//                    $html .= "</li>";
//                }
//                else
//                    $html .= "<li class='sub_item'><a class='" . $active . "' href='" . $branch->link . "'>" . $branch->name . "</a></li>";
//            }
//            $i++;
//        }
//        $html .= "</ul>";
//        return $html;
//    }

    public function sendForm ( $params )
    {
        $id = $_POST['form_id'];//ArrayTools::head ( $params );
        $form = $this->getForm ( $id );
        $fields = $this->getFormFields ( $form->id );
        $cutValueLength = 10240;
//        $submittedForm = filter_input ( INPUT_POST, "formSubmitted" );
//        if ( $p && !$error )
//        {
            $mailFields = array ();
            foreach ( $fields as $field )
            {
                //Заполнение полей
                $checked = $value = '';
                if ( isset ( $_POST[$field->name] ) )
                {
                    if ( $field->fieldType == 'checkbox' )
                        $checked = 'checked="checked"';
                    else
                        $value = htmlspecialchars ( substr ( trim ( $_POST[$field->name] ), 0, $cutValueLength ) );
                }
                else
                    $value = htmlspecialchars ( $field->default );


                switch ( $field->fieldType )
                {
                    case 'text':
                    case 'hidden':
                        $mailFields[] = $field->label . ': ' . $value;
                        break;

                    case 'textarea':
                        $mailFields[] = $field->label . ": \r\n" . str_repeat ( '—', 10 ) . "\r\n" . $value . "\r\n" . str_repeat ( '—', 10 );
                        break;

                    case 'checkbox':
                        if ( !empty ( $checked ) )
                            $mailFields[] = $field->label . ': Да';
                        else
                            $mailFields[] = $field->label . ': Не указано';
                        break;

                    default: break;
                }
            }
            $addSysFields[] = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' http://ipgeobase.ru/?address=' . $_SERVER['REMOTE_ADDR'];
            $addSysFields[] = 'Дата: ' . DatetimeTools::inclinedDate ( date ( 'c' ) ) . ' ' . date ( 'H:i' );
            $addSysFields[] = 'Страница: http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            $body = filter_input ( INPUT_POST, "comment", FILTER_SANITIZE_STRING );
            $mailText = $body . "\r\n\r\n" . implode ( "\r\n", $mailFields ) . "\r\n\r\n" . implode ( "\r\n", $addSysFields );

            $mail = new ZFmail ( $form->email, 'noreply@' . $_SERVER['SERVER_NAME'], "Письмо из формы"/* $_SERVER['SERVER_NAME'] */ . ': ' . $form->title, $mailText );

            $send = $mail->send ();
            //if ( $send )
                return json_encode ( [ 'success' => 1, 'message' => $this->renderPart ( "formSuccessMsg", [ "form" => $form ] ) ] );
//        }
    }

    public function siteMap()
    {
        $this->template = "page";
        $this->title = "Карта сайта";
        $sitemap = Starter::app ()->content->getSiteMap ();
        return $this->render ( "siteMap", [ "map" => $sitemap ] );
    }
}
