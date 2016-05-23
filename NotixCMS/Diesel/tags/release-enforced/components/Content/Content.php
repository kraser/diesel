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
        $docs = Starter::app ()->content->docs;
        $menuByTop = array ();
        $menu = array ();
        $activeSet = false;
        foreach ( $docs as $dox )
        {
            $i = (array) $dox;
            if ( $i['showMenu'] == 'N' )
                continue;
            //Отмечаем текущую страницу
            if ( in_array ( $i['nav'], Starter::app ()->urlManager->linkPath ) )
            {
                $i['active'] = true;
                $activeSet = true;
            }
            else
                $i['active'] = false;

            $menuByTop[$i['parentId']][] = $i;
        }
        if ( empty ( $menuByTop[0] ) )
            return tpl ( 'parts/mainmenu', array ( 'menu' => array () ) );

        foreach ( $menuByTop[0] as $top => $i )
        {
            $item['root'] = $i;
            $item['sub'] = [];

                //Проверка на подменю из модуля
            if ( $i['module'] !== "Content" )
            {
                $obj = Starter::app ()->getModule ( $i['module'] );
                if ( method_exists ( $obj, 'SubMenu' ) )
                    $item['sub'] = $obj->SubMenu ();
            }
            else
            {
                if ( isset ( $menuByTop[$i['id']] ) )
                {
                    foreach ( $menuByTop[$i['id']] as $id => $j )
                    {
                        $item['sub'][] = $j;
                    }
                }
            }
            $menu[] = $item;
        }
        return tpl ( 'parts/mainmenu', array ( 'menu' => $menu ) );
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
        $id = ArrayTools::head ( $params );
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

    /**
     * <pre>Создает форму из модуля «Формы»</pre>
     *
     * @param int $id <p>ID формы</p>
     * @param bool $return <p>Флаг управления выводом true - вернуть результат/ false - вывести результат в поток вывода</p>
     */
    public function form ( $id, $return = false )
    {
        if ( $id == '0' )
            return;

        if ( is_array ( $id ) )
            $id = array_shift ( $id );

        $cutValueLength = 10240;
        $emptyValueAlert = 'Пожалуйста, заполните поле «{label}»';

        $form = $this->getForm ( $id );

        if ( !$form )
            error ( 'Не найдена форма ' . $id );

        $fields = $this->getFormFields ( $form->id );
        $js = "";
        $as_js = "";

        //$ajax = filter_input ( INPUT_POST, "ajax_form" );
        //$return = ($ajax) ? true : false;

        $submittedForm = filter_input ( INPUT_POST, "formSubmitted" );
        $mailFields = array ();
        $p = ( $submittedForm /*|| $ajax */) ? true : false;
        $error = false;

        $templateName = $form->template ? : "form";
        foreach ( $fields as $field )
        {
            if ( $field->required == "Y" )
            {
                $js .= '
                    if($("#autoform_' . $field->name . '").val() == "" || $("#autoform_' . $field->name . '").val() == "' . $field->value . '") {
                            alert("' . str_replace ( '{label}', $field->label, $emptyValueAlert ) . '");
                            $("#autoform_' . $field->name . '").focus();
                            return false;
                    }';
            }
        }

        $html = TemplateEngine::view ( $templateName, array ( "form" => $form, "fields" => $fields, "js" => $js ), __CLASS__, true );

        $formSuccessMsg = TemplateEngine::view ( "formSuccessMsg", array ( "form" => $form ), __CLASS__, true );

        //Если админ
        if ( !session_id () )
            session_start ();
        if ( isset ( $_SESSION['admin']['type'] ) )
        {
            $is_admin = ($_SESSION['admin']['type'] == 'a');
            if ( $is_admin && @!$_SESSION['godmode_suspended'] )
            {
                $edit_block_link = '/admin/?module=Forms&method=Info&top=' . $form->id;
                $html = '<div style="border:dashed 1px grey; position:relative;">' . $html . '<a target="_blank" style="position:absolute; top:0; right:-8px; z-index:100;" href="' . $edit_block_link . '" title="Редактировать"><img src="/admin/images/icons/pencil.png" alt="Редактировать" /></a></div>';
            }
        }

        //Отправка письма
        if ( $p && !$error )
        {
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
            if ( $send )
            {
                if ( $return )
                    return array ( 'success' => 1, 'message' => $formSuccessMsg );
                else
                {
                    echo $formSuccessMsg;
                    return true;
                }
            }
        }

        if ( $form->view == 'Y' )
        {
            if ( $return )
                return $html;
            else
                echo $html;
        }
        else
            return false;
    }
    /**
     * <p>Массив форм индексированный по Id</p>
     * @var ArrayOfForm
     */
    private $formsById;

    /**
     * <p>Массив форм индексированный по системному имени alias</p>
     * @var ArrayOfForm
     */
    private $formsByAlias;

    /**
     * <pre>Выбирает по alias/id форму и возвращает её</pre>
     * @param Integer/String $id <p>Индекс для поиска формы</p>
     * @return Form
     */
    private function getForm ( $id )
    {
        $form = null;
        if ( is_null ( $this->formsById ) || is_null ( $this->formsByAlias ) )
        {
            $query = "SELECT
                `id` AS id,
                `order` AS 'order',
                `name` AS title,
                `callname` AS alias,
                `template` AS template,
                `email` AS email,
                `show` AS view
            FROM `prefix_forms`
            WHERE `show`='Y' AND `deleted`='N'";

            $forms = SqlTools::selectObjects ( $query, "Form" );
            $this->formsById = ArrayTools::index ( $forms, "id" );
            $this->formsByAlias = ArrayTools::index ( $forms, "alias" );
        }

        if ( is_numeric ( $id ) && array_key_exists ( $id, $this->formsById ) )
            $form = $this->formsById[$id];
        elseif ( array_key_exists ( $id, $this->formsByAlias ) )
            $form = $this->formsByAlias[$id];

        return $form;
    }

    /**
     * <pre>Возвращает поля для формы с заданным Id<pre>
     * @param Integer $formId <p>Id формы</p>
     * @return ArrayOfFormFields
     */
    private function getFormFields ( $formId )
    {
        $query = "SELECT
            `id` AS id,
            `form` AS formId,
            `type` AS fieldType,
            `label` AS label,
            `name` AS name,
            `regex` AS 'regExp',
            `regex_error` AS regExpError,
            `default` AS value,
            `required` AS required,
            `order` AS 'order',
            `show` AS view
        FROM `prefix_forms_fields`
        WHERE `form`=$formId AND `show`='Y' ORDER BY `order`";
        $fields = SqlTools::selectObjects ( $query, "FormField" );
        return $fields;
    }

    public function siteMap()
    {
        $this->template = "page";
        $this->title = "Карта сайта";
        $sitemap = Starter::app ()->content->getSiteMap ();
        return $this->render ( "siteMap", [ "map" => $sitemap ] );
    }
}

class Form
{
    /**
     * <p>ID формы</p>
     * @var Integer
     */
    public $id;
    /**
     * <p>Системное имя формы</p>
     * @var String
     */
    public $alias;
    /**
     * <p>Название формы</p>
     * @var String
     */
    public $title;
    /**
     * <p>имя шаблона для вывода формы</p>
     * @var String
     */
    public $template;
    /**
     * <p>E-mail обратной связи</p>
     * @var String
     */
    public $email;
    /**
     * <p>Флаг отображения формы</p>
     * @var Boolean
     */
    public $view;
    public $order;

}

class FormField
{
    public $id;
    public $formId;
    public $fieldType;
    public $label;
    public $name;
    public $regExp;
    public $regEexpError;
    public $value;
    public $required;
    public $order;
    public $view;

}
