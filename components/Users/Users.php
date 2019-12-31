<?php

/**
 *
 */
class Users implements OfficeService
{
    private $actions;

    public function __construct ()
    {
        $this->actions = array
        (
            'login' => array ( 'method' => 'authorize', 'name' => 'Авторизация' ),
            "registration" => array ( "method" => "registration", 'name' => 'Регистрация' ),
            'order' => array ( 'method' => 'order'),
            'logout' => array ( 'method' => 'logout'),
            'remind' => array ( 'method' => 'remind'),
            'restore' => array ( 'method' => 'restore')
        );
    }

    public function Run()
    {
        $uri = Starter::app ()->urlManager->getUrlPart ( "path" );
        $uriParts = array_filter ( explode ( "/", $uri ) );
        array_shift ( $uriParts );
        if(count ( $uriParts ))
        {
            $alias = array_shift ( $uriParts );
            $method = array_key_exists($alias, $this->actions) ? $this->actions[$alias]['method'] : "indexAction";
        }
        else
        {
            $method = "indexAction";
        }

        $model = $this->$method ();

        return TemplateEngine::view( "mainpage", $model, __CLASS__);
    }

    public function findUsers ( $params )
    {
        if ( $params)
        {
            return array();
        }

        $conditions = array();
        foreach($params as $field => $value)
        {
            if(!$value)
            {
                continue;
            }

            switch ($field)
            {
                case "id":
                    $conditions[] = "u.`id` IN (".ArrayTools::numberList($value).")";
                    break;
                case "login":
                    $conditions[] = "u.`login` IN (".ArrayTools::stringList($value).")";
                    break;
                case "password":
                    $conditions[] = "u.`passwd`=MD5('$value')";
                    break;
                case "status":
                    $conditions[] = "u.`status` IN (".ArrayTools::stringList($value).")";
                    break;
                case "name":
                    break;
                case "email":
                    $conditions[] = "u.`email` IN (".ArrayTools::stringList($value).")";
                    break;
                case "phone":
                    $conditions[] = "u.`phone` IN (".ArrayTools::stringList($value).")";
                    break;
                default:
            }
        }

        $whereClause = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $query = "SELECT
            u.`id`,
            u.`login`,
            u.`passwd`,
            u.`status`,
            u.`firstName`,
            u.`lastName`,
            u.`company`,
            u.`name`,
            u.`email`,
            u.`phone`,
            u.`address`,
            u.`deleted`,
            u.`anons`,
            u.`description
        FROM `prefix_users`
        $whereClause";
        $users = SqlTools::selectObjects($query, "CmsUsers");
        return $users;
    }

    public function getFormField ()
    {
        $fields = array
        (
            'login' => array ( 'name' => 'login', 'type' => 'text', 'value' => '', 'label' => 'Логин', 'required' => true ),
            'password' => array ( 'name' => 'password', 'type' => 'password', 'label' => 'Пароль', 'required' => true ),
            'confirm' => array ( 'name' => 'confirm', 'type' => 'password', 'label' => 'Подтвердить', 'required' => true ),
            'regBox' => array ( 'name' => 'regBox', 'type' => 'hidden', 'value' => 'REGISTR' ),
            'firstName' => array ('name'=> 'firstName', 'type'=>'text', 'label'=>'Имя', 'required' => true),
            'lastName' => array ('name'=>'lastName', 'type'=>'lastName', 'label'=>'Фамилия'),
            'email' => array('name'=>'email', 'type'=>'text', 'label'=>'E-mail'),
            'phone' => array('name'=>'phone', 'type'=>'text', 'label'=>'Телефон')
        );
        return $fields;
    }

    public function remind ()
    {
        $errors = array ();
        $mail = filter_input ( INPUT_POST, "mail", FILTER_SANITIZE_EMAIL );
        if ( !$mail )
        {
            $errors['msg'] = "Поле Email не может пустым";
        }

        if ( count ( $errors ) )
        {
            return array ( "error" => 1, "msg" => $errors );
        }

        $account = ArrayTools::head ( SqlTools::selectObjects ( "SELECT * FROM `prefix_users` WHERE `email`='$mail'" ) );

        if ( !$account )
        {
            return array ( "error" => 1, "msg" => "Не найден пользователь с таким Email" );
        }
        $serverName = filter_input ( INPUT_SERVER, 'SERVER_NAME' );
        $salt = uniqid($serverName, true);
        $_SESSION['restoreSalt'] = $salt;
        $accountHash = $this->accountHash ( $account, $salt );
        $moduleLink = "users";//linkByModule(__CLASS__);
        $body = "На Ваш e-mail было запрошено восстановление пароля к кабинету на $serverName
Если Вы получили это письмо по ошибке, скорее всего, другой пользователь
случайно указал Ваш адрес, пытаясь изменить пароль. Если Вы не отправляли
запрос, ничего не делайте и не обращайте внимания на это сообщение.

Для смены пароля перейдите по ссылке: http://$serverName/$moduleLink/restore/$accountHash \n
Ссылка действительна в течение часа\n
Примечание. Отвечать на этот адрес электронной почты не следует.";

        $mailSender = new ZFmail ( $mail, "noreply@$serverName", "Восстановление пароля к $serverName", $body );
        $mailSender->send ();
        //Tools::dump("http://$serverName/$moduleLink/restore/$accountHash");

        return array ( "msg" => "На ваш E-mail отправлено письмо с описанием дальнейших шагов по восстановлению доступа" );
    }

    public function restore ()
    {
        $hash = end ( Starter::app ()->urlManager->getUriParts () );
        if ( !$hash )
        {
            throw new Exception ( "Страница не существует", 404 );
        }

        $check = filter_input ( INPUT_POST, 'check', FILTER_SANITIZE_STRING );
        if ( !$check )
        {
            return array( 'title' => 'Восстановление пароля', 'content' => TemplateEngine::view ( 'restore', array ( 'hidden' => time () ), __CLASS__, true ) );
        }

        $salt = array_key_exists("restoreSalt", $_SESSION) ? $_SESSION["restoreSalt"] : null;

        if ( !$salt )
        {
            throw new Exception ( "Страница недействительна или не существует", 404 );
        }

        $query = "SELECT u.`id` AS id, MD5(CONCAT(u.`id`, u.`login`, u.`passwd`, u.`email`)) AS hash FROM `prefix_users` u WHERE MD5(CONCAT(u.`id`, u.`login`, u.`passwd`, u.`email`, '$salt'))='$hash'";
        $account = ArrayTools::head ( SqlTools::selectObjects ( $query ) );
        if ( !$account )
        {
            throw new Exception ( "Страница недействительна или не существует", 404 );
        }

        $errors = array ();
        $pwd = filter_input ( INPUT_POST, "passwd", FILTER_SANITIZE_STRING );
        if ( !$pwd )
        {
            $errors['passwd'] = "Поле Пароль не может быть пустым";
        }

        $confirm = filter_input ( INPUT_POST, "confirm", FILTER_SANITIZE_STRING );
        if ( $pwd && $confirm !== $pwd )
        {
            $errors['confirm'] = "Подтверждение не совпадает с паролем";
        }

        if ( $errors )
        {
            return array ( "error" => $errors );
        }

        $query = "UPDATE `prefix_users` SET `passwd`=MD5('$pwd') WHERE `id`=$account->id";
        $success = SqlTools::execute ( $query );
        $toSend = array ();
        if ( $success )
        {
            $toSend["success"] = 1;
            $toSend['html'] = "Пароль сменен. Сейчас страница будет перезагружена.";
        }
        else
        {
            $toSend["warn"] = "Попытка окончилась неудачей";
        }
        return $toSend;
    }

    public function getRegistrationForm( $data = null )
    {
        if(!$data)
        {
            $data = new stdClass();
            $data->errors = array();
            $data->firstname = "";
            $data->lastname = "";
            $data->email = "";
            $data->phone = "";
            $data->login = "";
            $data->password = "";
            $data->confirm = "";
            $data->company = "";
            $data->address = "";
        }

        return TemplateEngine::view('registration', array ( 'data' => $data ), __CLASS__, true);
    }

    private function accountHash ( $account, $salt )
    {
        return md5 ( $account->id . $account->login . $account->passwd . $account->email . $salt );
    }

    public function getData ( $params )
    {

    }

    public function renderList ( $params )
    {

    }

    public function renderTab ( $params )
    {
        $user = UserIdentity::getUser();
        return TemplateEngine::view("profile", array("user" => $user), __CLASS__, true);
    }

    public function setData ( $params )
    {

    }

    public function createRecord ( $params )
    {
        $regBox = filter_input ( INPUT_POST, 'regBox', FILTER_SANITIZE_STRING );
        if ( !$regBox )
        {
            return $this->getRegistrationForm ();
        }

        $data = new stdClass();
        $data->errors = array();
        $login = filter_input (INPUT_POST, "login", FILTER_SANITIZE_STRING );
        $records = SqlTools::selectValue("SELECT COUNT(*) FROM `prefix_users` WHERE `login`='$login'");
        if ( $records )
        {
            $data->errors[] = "Пользователь $login уже зарегистрирован в системе";
        }
        $data->login = $login;
        $firstName = filter_input (INPUT_POST, "firstname", FILTER_SANITIZE_STRING );
        $data->firstName = $firstName ? : "";
        $lastName  = filter_input (INPUT_POST, "lastname", FILTER_SANITIZE_STRING );
        $data->lastName = $lastName ? : "";
        $email = filter_input (INPUT_POST, "email", FILTER_SANITIZE_EMAIL );
        if ( !$email )
        {
            $data->errors[] = "Не заполнено обязательное поле E-mail";
        }
        $data->email = $email ? : "";

        $phone = filter_input ( INPUT_POST, "phone", FILTER_SANITIZE_STRING );
        if ( !$phone )
        {
            $data->errors[] = "Не заполнено обязательное поле Телефон";
        }
        $data->phone = $phone ? : "";

        $password = filter_input (INPUT_POST, "password", FILTER_SANITIZE_STRING );
        if ( !$password )
        {
            $data->errors[] = "Не заполнено обязательное поле Пароль";
        }
        $data->password = $password ? : "";

        $confirm = filter_input (INPUT_POST, "confirm", FILTER_SANITIZE_STRING );
        if ( $password && $password != $confirm )
        {
            $data->errors[] = "Подтверждение не совпадает с паролем";
        }
        $data->confirm = $confirm ? : "";

        $company = filter_input (INPUT_POST, "company", FILTER_SANITIZE_STRING );
        $data->company = $company ? : "";
        $address = filter_input (INPUT_POST, "address", FILTER_SANITIZE_STRING );
        $data->address = $address ? : "";
//        $city = filter_input (INPUT_POST, "city", FILTER_SANITIZE_STRING );
//        $postcode = filter_input (INPUT_POST, "postcode", FILTER_SANITIZE_STRING );

        if(count($data->errors))
        {
            return $this->getRegistrationForm ( $data );
        }
        $data->name = $data->firstName . " " . $data->lastName;
        $query = "INSERT INTO `prefix_users` (
                `login`,
                `passwd`,
                `status`,
                `firstName`,
                `lastName`,
                `company`,
                `name`,
                `email`,
                `phone`,
                `address`,
                `deleted`)
            VALUES (
                '$data->login',
                MD5('$data->password'),
                'client',
                '$data->firstName',
                '$data->lastName',
                '$data->company',
                '$data->name',
                '$data->email',
                '$data->phone',
                '$data->address',
                'N')";

        $id = SqlTools::insert($query);
        if($id)
        {
            return TemplateEngine::view ( "registrSuccess", array (), __CLASS__, true );
        }
        else
        {
            return $this->getRegistrationForm ( array("Неизвестная ошибка при регистрацииs") );
        }
    }
}
