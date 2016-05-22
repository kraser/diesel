<?php

/**
 *
 * @author Elena137
 */
class Question extends Component
{
    private $data;
    private $table = 'question';
    private $seo;
    private $url;
    private $part;
    private $id = 0;
    private $mode;

    /*
      function __construct() {
      $this->data = Starter::app ()->data;
      }
     */

    function __construct ()
    {
        $this->data = Starter::app ()->data;

        $this->host = $_SERVER['HTTP_HOST'];
        $request = strpos ( $_SERVER['REQUEST_URI'], '?' ) !== false ? substr ( $_SERVER['REQUEST_URI'], 0, strpos ( $_SERVER['REQUEST_URI'], '?' ) ) : $_SERVER['REQUEST_URI'];
        $this->url = $this->host . $request;
        $this->mode = Tools::getSettings ( 'Question', 'mode' );
        if ( !$this->mode )
        {
            $this->mode = 'default';
        }
    }

    public function Run ()
    {
        $this->buildModulePath ();
        return $this->startController ();
    }

    private function buildModulePath ()
    {
        if ( $this->mode == 'expert' )
        {
            $request = strpos ( $_SERVER['REQUEST_URI'], '?' ) !== false ? substr ( $_SERVER['REQUEST_URI'], 0, strpos ( $_SERVER['REQUEST_URI'], '?' ) ) : $_SERVER['REQUEST_URI'];
        }
        else
        {
            $request = $_SERVER['REQUEST_URI'];
        }

        //Выталкивает линк на текущий модуль из стека линков (запроса)
        $mlink = trim ( str_replace ( Starter::app ()->content->getLinkById ( $this->currentDocument->id ), '', $request ) );
        $pathStack = array_filter ( explode ( '/', $mlink ) );

        $path = array ();
        $a = preg_replace ( "/[^а-яa-z]+/i", "", $mlink );
        if ( !empty ( $a ) )
        {
            $path = $mlink;
        }
        else
        {
            $path = '';
        }

        $path = explode ( '/', $path );

        $this->path = $path;
        return true;
    }

    private function startController ()
    {
        if ( $this->mode == 'expert' )
        {
            if ( array_key_exists ( '0', $this->path ) && !array_key_exists ( '1', $this->path ) && !array_key_exists ( '2', $this->path ) )
            {
                return $this->MainPage ();
            }
            elseif ( array_key_exists ( '0', $this->path ) && array_key_exists ( '1', $this->path ) && array_key_exists ( '2', $this->path ) && ($this->path[2] != '') )
            {
                if ( $this->path[1] == 'user' )
                {
                    return $this->Ask ();
                }
                elseif ( $this->path[1] == 'question' )
                {
                    return $this->OneQuestion_expert ();
                }
                else
                {
                    return page404 ();
                }
            }
            elseif ( array_key_exists ( '0', $this->path ) && array_key_exists ( '1', $this->path ) )
            {
                if ( $this->path[1] == 'user' )
                {
                    return $this->UsersBlock_expert ();
                }
                elseif ( $this->path[1] == 'question' )
                {
                    return $this->QuestionsBlock_expert ();
                }
                else
                {
                    return page404 ();
                }
            }
            else
                return page404 ();
        }
        else
        {
            if ( array_key_exists ( '0', $this->path ) && !array_key_exists ( '1', $this->path ) )
            {
                return $this->MainPage ();
            }
            elseif ( array_key_exists ( '0', $this->path ) && array_key_exists ( '1', $this->path ) )
            {
                if ( $this->path[1] == 'ask' )
                {
                    return $this->Ask ();
                }
                else
                {
                    return page404 ();
                }
            }
            else
            {
                return page404 ();
            }
        }
    }

    /**
     *
     * @param type $count - количество выводимых вопросов. Если не задано, то выводятся все вопросы.
     * @return массив вопросов $question
     *
     */
    function QuestionsBlock_expert ( $count = 0 )
    {
        $this->seo ();
        if ( $count == 0 )
        {
            $limit = '';
        }
        else
        {
            $limit = 'LIMIT ' . $count;
        }

        $query = "SELECT `id`, `question`, `answer`,`anons`, `date` FROM `prefix_{$this->table}` WHERE `show`='Y' AND `deleted`='N' ORDER BY `date` DESC " . $limit;
        $questions = SqlTools::selectRows ( $query );
        foreach ( $questions as $key => $question )
        {
            $questions[$key]['date'] = DatetimeTools::inclinedDate ( $question['date'] );
            $questions[$key]['link'] = Starter::app ()->content->getLinkByModule ( __CLASS__ ) . '/question/' . $question['id'];
        }
        if ( $count == 0 )
        {
            return tpl ( 'modules/' . __CLASS__ . '/questions_expert', array (
                'name' => 'Вопросы',
                'questions' => $questions
                ) );
        }
        else
        {
            return tpl ( 'modules/' . __CLASS__ . '/questionblock_expert', array (
                'questions' => $questions
                ) );
        }
    }

    /**
     *
     * @param type $count- количество отображаемых пользователей-экспертов на странице.
     * Если $count не задано, то выводятся все пользователи. Иначе формируется вывод пользователей с пагинацией.
     * @return массив пользователей $users
     */
    function UsersBlock_expert ( $count = 0 )
    {
        $this->seo ();

        $query = "SELECT `id`, CONCAT (`firstName`, ' ', `lastName`) AS `firstName`, `anons`, `description` FROM `prefix_users` WHERE `status`='manager' AND `deleted`='N' ";

        $users = SqlTools::selectRows ( $query, MYSQL_ASSOC, "id" );
        $usersId = ArrayTools::numberList ( array_keys ( $users ) );
        $imgs = SqlTools::selectObjects ( "SELECT * FROM `prefix_images` WHERE `module_id` IN ($usersId) AND `main`='Y' AND `module`='Users'", null, "module_id" );

        foreach ( $users as $key => $user )
        {
            $imageSource = array_key_exists ( $user['id'], $imgs ) ? $imgs[$user['id']]->src : '/images/default.png'; // картинка по умолчанию
            $users[$key]['link'] = Starter::app ()->content->getLinkByModule ( __CLASS__ ) . '/user/' . $user['id'];
            $users[$key]['img'] = $imageSource;
        }
        //Пэйджинг
        if ( $count != 0 )
        {
            $users_onpage = Tools::getSettings ( 'Question', 'usercount' );
            $users_paged = Paging ( $users, $users_onpage );
            $users = $users_paged['items'];
            $paging = $users_paged['rendered'];
            unset ( $users_paged );
        }
        if ( $count == 0 )
        {
            return tpl ( 'modules/' . __CLASS__ . '/users_expert', array (
                'name' => 'Эксперты',
                'users' => $users
                ) );
        }
        else
        {
            return tpl ( 'modules/' . __CLASS__ . '/userblock_expert', array (
                'users' => $users,
                'paging' => $paging
                ) );
        }
    }

    /**
     * Вывод информации о вопросе. Вывод текста самого вопроса и ответа на него.
     * @return type
     */
    private function OneQuestion_expert ()
    {
        $query = "SELECT `id`, `question`, `person`, `answer` FROM `prefix_" . $this->table . "` WHERE `deleted`='N' AND `show`='Y' AND `id`='" . $this->path[2] . "'";

        $questions = SqlTools::selectRow ( $query );

        if ( empty ( $questions ) )
        {
            page404 ();
        }

        $this->seo ();

        return tpl ( 'modules/' . __CLASS__ . '/questionone_expert', array (
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $users['lastName'] . ' ' . $users['firstName'] . ' — ' . Sterter::app ()->title,
            'question' => $questions['question'],
            'name' => "Вопрос",
            'answer' => $questions['answer'],
            'link' => $questions['id'],
            'users_id' => $this->id
            ) );
    }

    /**
     * Добавление вопроса в базу данных
     * @return string
     */
    private function OneUserAction_expert ()
    {
        $result = array ();
        $errors = array ();
        $question = $this->validate ( $_POST['question'] );
        $expertId = $_POST['expert'];
        $email = $this->validate ( $_POST['email'] );
        $firstName = $this->validate ( $_POST['firstName'] );
        $lastName = $this->validate ( $_POST['lastName'] );

        if ( !$question )
        {
            $errors['question'] = "Заполните поле 'Вопрос'";
        }

        if ( !$email )
        {
            $errors['email'] = "Заполните поле 'E-mail'";
        }

        if ( !$firstName )
        {
            $errors['firstName'] = "Заполните поле 'Имя'";
        }

        if ( !intval ( $expertId ) )
        {
            $errors['expert'] = "Поле эксперт заполнено не верно";
        }

        if ( !count ( $errors ) )
        {
            $expert = ArrayTools::head ( SqlTools::selectObjects ( "SELECT `id`, CONCAT (`firstName`, ' ', `lastName`) AS name, `email` AS email FROM `prefix_users` WHERE `id`=$expertId" ) );
            $sql = "INSERT INTO  `prefix_question` (`question`, `email`, `firstName`, `lastName`, `person` ,`date` ,`show` ,`deleted` ,`created` ,`modified` ,`anons`)
            VALUES ('" . $question . "', '" . $email . "', '" . $firstName . "', '" . $lastName . "',  '" . $expertId . "', NOW(),  'N',  'N',  '',  '',  '')";
            $id = SqlTools::insert ( $sql );

            $addSysFields = array ( 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' http://ipgeobase.ru/?address=' . $_SERVER['REMOTE_ADDR'] );
            $addSysFields[] = 'Дата: ' . DatetimeTools::inclinedDate ( date ( 'c' ) ) . ' ' . date ( 'H:i' );
            $addSysFields[] = 'Страница: http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            $body = "Поступил вопрос эксперту $expert->name\r\n";
            $body .= "От $firstName $lastName\r\n\r\n";
            $body .= $question . "\r\n\r\n" . implode ( "\r\n", $addSysFields );
            $toMail = $expert->email ? : Tools::getSettings ( 'Shop', 'notify_mail', Starter::app()->adminMail );

            $mail = new ZFmail ( $toMail, 'noreply@' . $_SERVER['SERVER_NAME'], $_SERVER['SERVER_NAME'] . ': Вопрос эксперту', $body );
            $mail->send ();


            $result = array ( 'redirect' => '' );
        }
        else
        {
            $result = array ( 'error' => $errors );
        }

        return $result;
    }

    /**
     * Вывод информации о пользователе
     * @param type $contentPathFile
     * @return type
     */
    private function OneUserDescription_expert ( $contentPathFile )
    {
        $queryusers = "SELECT `id`, `firstName`, `lastName`, `description` FROM `prefix_users` WHERE `status`='manager' AND `deleted`='N' AND `id`='" . $this->path[2] . "'";
        $users = SqlTools::selectRow ( $queryusers );

        if ( empty ( $users ) )
        {
            page404 ();
        }

        $this->seo ();

        $queryexperts = "SELECT `id`, `firstName`, `lastName`, `anons`, `description` FROM `prefix_users` WHERE `status`='manager' AND `deleted`='N'";
        $experts = SqlTools::selectRows ( $queryexperts );

        return tpl ( $contentPathFile, array (
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $users['lastName'] . ' ' . $users['firstName'] . ' — ' . Sterter::app ()->title,
            'name' => $users['lastName'] . ' ' . $users['firstName'],
            'content' => $users['description'],
            'link' => $users['id'],
            'users_id' => $this->id,
            'question_url' => $this->url,
            'experts' => $experts,
            ) );
    }

    /**
     *
     * @param type $count - количество выводимых вопросов. Если не задано, то выводятся все вопросы.
     * @return массив вопросов $question
     *
     */
    function QuestionsBlock_default ()
    {
        $this->seo ();

        $query = "SELECT `id`, `question`, `answer`,`anons`, `date` FROM `prefix_{$this->table}` WHERE `show`='Y' AND `deleted`='N' ORDER BY `date` DESC ";
        $questions = SqlTools::selectRows ( $query );
        foreach ( $questions as $key => $question )
        {
            $questions[$key]['date'] = DatetimeTools::inclinedDate ( $question['date'] );
        }
        //Пэйджинг
        $questions_onpage = Tools::getSettings ( 'Question', 'questioncount' );
        $questions_paged = Paging ( $questions, $questions_onpage );
        $questions = $questions_paged['items'];
        $paging = $questions_paged['rendered'];
        unset ( $questions_paged );
        return tpl ( 'modules/' . __CLASS__ . '/questionblock_default', array (
            'questions' => $questions,
            'paging' => $paging
            ) );
    }

    /**
     * Формирует вывод главной страницы компонента
     * @return type
     */
    private function MainPage ()
    {
        if ( $this->mode == 'expert' )
        {
            $template = 'mainpage_expert';
        }
        else
        {
            $template = 'mainpage_default';
        }

        return tpl ( 'modules/' . __CLASS__ . '/' . $template, array (
            'name' => $this->currentDocument->title,
            'title' => $this->currentDocument->title . ' — ' . Sterter::app ()->title,
            ) );
    }

    /**
     * Вывод формы "задать вопрос"
     * В зависимости от состояния $isAction выводится форма "задать вопрос" или происходит добавление вопроса в БД.
     * @return type
     */
    function Ask ()
    {
        $isAction = $this->isAction ();
        $contentFile = ($isAction ? 'mainpage_default' : 'ask');
        $contentPathFile = 'modules/' . __CLASS__ . '/' . $contentFile;
        $content = "";

        if ( $isAction )
        {
            if ( $this->mode == 'expert' )
            {
                $content = $this->OneUserAction_expert (); // Action.php
            }
            else
            {
                $content = $this->AskAction (); // Action.php
            }
        }
        else
        {
            if ( $this->mode == 'expert' )
            {
                $content = $this->OneUserDescription_expert ( $contentPathFile ); // Main.php
            }
            else
            {
                $content = $this->AskDescription_default ( $contentPathFile ); // Main.php
            }
        }

//        if ( defined ( "MODE" ) && MODE == "Ajax" )
//        {
//            jsonHeader ();
//            echo json_encode ( $content );
//        }
//        else
//        {
        return $content;
//        }
    }

    /**
     * Добавление вопроса в базу данных при настройке default
     * @return string
     */
    private function AskAction ()
    {
        $result = array ();
        $errors = array ();

        if ( $_POST['question'] == 'Поле для вашего вопроса' )
        {
            $_POST['question'] = '';
        }

        if ( $_POST['email'] == 'E-mail' )
        {
            $_POST['email'] = '';
        }

        if ( $_POST['firstName'] == 'Имя' )
        {
            $_POST['firstName'] = '';
        }

        if ( $_POST['lastName'] == 'Фамилия' )
        {
            $_POST['lastName'] = '';
        }

        $question = $this->validate ( $_POST['question'] );
        $email = $this->validate ( $_POST['email'] );
        $firstName = $this->validate ( $_POST['firstName'] );
        $lastName = $this->validate ( $_POST['lastName'] );

        require_once(LIBS . DS . 'recaptchalib.php');
        $privatekey = SqlTools::selectValue ( "SELECT `value` FROM `prefix_settings` WHERE `callname`='privateKey'" );
        if ( $privatekey )
        {
            $resp = recaptcha_check_answer ( $privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"] );
            if ( !$resp->is_valid )
            {
                $errors['capt'] = "Картинка подтверждения была введена не правильно. Повторите ввод.";
                $result = array ( 'error' => $errors );
            }
            else
            {
                if ( !$question )
                {
                    $errors['question'] = "Заполните поле 'Вопрос'";
                }

                if ( !$email )
                {
                    $errors['email'] = "Заполните поле 'E-mail'";
                }

                if ( !$firstName )
                {
                    $errors['firstName'] = "Заполните поле 'Имя'";
                }

                if ( !$lastName )
                {
                    $errors['lastName'] = "Заполните поле 'Фамилия'";
                }

                if ( !count ( $errors ) )
                {
                    $sql = "INSERT INTO  `prefix_question` (`question`, `email`, `firstName`, `lastName`, `date` ,`show` ,`deleted` ,`created` ,`modified` ,`anons`)
            VALUES ('" . $question . "', '" . $email . "', '" . $firstName . "', '" . $lastName . "', NOW(),  'N',  'N',  '',  '',  '')";

                    $id = SqlTools::insert ( $sql );
                    $result = array ( 'redirect' => '' );
                }
                else
                {
                    $result = array ( 'error' => $errors );
                }
            }
        }
        return $result;
    }

    /**
     * Вывод формы "задать вопрос"
     * @param type $contentPathFile
     * @return type
     */
    private function AskDescription_default ( $contentPathFile )
    {

        return tpl ( $contentPathFile, array (
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : Sterter::app ()->title,
            'name' => 'Задать вопрос',
            ) );
    }

    /**
     * Проверяет состояние кнопки отправлен вопрос или нет
     * @return type
     */
    function isAction ()
    {
        $request = strpos ( $_SERVER['REQUEST_URI'], '?' ) !== false ? substr ( $_SERVER['REQUEST_URI'], 0, strpos ( $_SERVER['REQUEST_URI'], '?' ) ) : $_SERVER['REQUEST_URI'];
        $pathStack = array_filter ( explode ( '/', $request ) );
        $last = end ( $pathStack );

        return ($last == "action");
    }

    /**
     * Проверка данных, вводимых пользователем
     * @param type $input_text
     * @return type
     */
    private function validate ( $input_text )
    {
        $input_text = strip_tags ( $input_text );
        $input_text = htmlspecialchars ( $input_text );
        $input_text = mysql_escape_string ( $input_text );
        return $input_text;
    }

    /**
     * SEO для Question
     * @param type $id
     * @return boolean
     */
    private function seo ( $id = false )
    {
        if ( $id === false )
        {
            $id = $this->currentDocument->id;
        }
        else
        {
            $id = ( int ) $id;
        }

        if ( $id == 0 )
        {
            $this->seo = array ();
            return false;
        }

        $header = Starter::app ()->headManager;
        $this->seo = SqlTools::selectRow ( "SELECT * FROM `prefix_seo` WHERE `module`='" . __CLASS__ . "' AND `module_id`=" . $id . " AND `module_table`='" . $this->table . "'", MYSQL_ASSOC );
        if ( !empty ( $this->seo ) )
        {
            //Keywords
            if ( !empty ( $this->seo['keywords'] ) )
            {
                $header->addMetaText ("<meta name='keywords' content='" . htmlspecialchars ( $this->seo['keywords'] ) . "' />");
            }
            //Description
            if ( !empty ( $this->seo['description'] ) )
            {
                $header->addMetaText ( "<meta name='description' content='" . htmlspecialchars ( $this->seo['description'] ) . "' />" );
            }
            //Title
            if ( !empty ( $this->seo['title'] ) )
            {
                $this->seo['title'] = $this->seo['title'];
            }
            else
            {
                $this->seo['title'] = $this->currentDocument->title; // . ' — ' . Sterter::app ()->title;
            }
        }
        else
        {
            $this->seo['title'] = $this->currentDocument->title;
        }
        $header->setTitle ( $this->seo['title'] );
    }

    /**
     * Хлебные крошки для Question
     * @return type
     */
    public function breadCrumbs ()
    {
        if ( $this->mode == 'expert' )
        {
            return $this->breadCrumbs_expert ();
        }
        else
        {
            return $this->breadCrumbs_default ();
        }
    }

    public function breadCrumbs_expert ()
    {
        $uri = explode ( '/', $_SERVER['REQUEST_URI'] );
        if ( array_key_exists ( '0', $this->path ) && array_key_exists ( '1', $this->path ) && array_key_exists ( '2', $this->path ) && ($this->path[2] != '') )
        {
            if ( $this->path[1] == 'user' )
            {
                return array
                (
                    array ( 'name' => 'Эксперты', 'link' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/user' ),
                    array ( 'name' => 'Эксперт', 'link' => '' )
                );
            }
            elseif ( $this->path[1] == 'question' )
            {
                return array
                (
                    array ( 'name' => 'Вопросы', 'link' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/question' ),
                    array ( 'name' => 'Вопрос', 'link' => '' )
                );
            }
        }
        elseif ( array_key_exists ( '0', $this->path ) && array_key_exists ( '1', $this->path ) )
        {
            if ( $this->path[1] == 'user' )
            {
                return array ( array ( 'name' => 'Эксперты', 'link' => '' ) );
            }
            elseif ( $this->path[1] == 'question' )
            {
                return array ( array ( 'name' => 'Вопросы', 'link' => '' ) );
            }
        }
        return array ();
    }

    public function breadCrumbs_default ()
    {
        if ( array_key_exists ( '0', $this->path ) && array_key_exists ( '1', $this->path ) )
        {
            if ( $this->path[1] == 'ask' )
            {
                return
                    array (
                        array ( 'name' => 'Форма - задать вопрос', 'link' => '' ) );
            }
            elseif ( $this->path[1] == 'question' )
            {
                return
                    array (
                        array ( 'name' => 'Вопросы', 'link' => '' ) );
            }
        }
        return array ();
    }
}
