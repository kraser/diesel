<?php

/**
 *
 */
class User
{
    private static $instance;

    private static $id;
    private static $user;
    private static $userName;

    private static $userData;
    private static $userInfo;

    private $salt;

    /**
     * Инициализирует параметры для авторизации пользователя
     */
    private function __construct ()
    {
        $this->salt = "cucumber";
    }

    /**
     * Возвращает синглтон класса User
     * @return User
     */
    public static function &getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Инициализация пользователя
     */
    public static function Init ()
    {
        $instance = self::getInstance ();
        $instance->processUser ();
    }

    public function Run ()
    {
        $method = array_shift ( Starter::app ()->urlManager->urlParts );
        if ( $method == "update" )
        {
            $html = $this->update ();
        }

        return tpl ( "update", array ( "restore" => $html ) );
    }

    /**
     * Читает параметры сессии, и проверяет авторизацию
     */
    private function processUser ()
    {
        $userInfo = null;
        if ( array_key_exists ( "authBox", $_POST ) )
        {
            $login = array_key_exists ( "login", $_POST ) ? preg_replace ( '/^[^a-zA-Z0-9]+$/', '', $_POST['login'] ) : null;
            $pwd = array_key_exists ( "password", $_POST ) ? preg_replace ( '/^[^a-zA-Z0-9]+$/', '', $_POST['password'] ) : null;
            $userInfo = array ( "login" => $login, "password" => $pwd );
        }
        else if ( array_key_exists ( "user", $_SESSION ) )
        {
            $login = $_SESSION['user'];
        }
        else if ( array_key_exists ( "user", $_COOKIE ) )
        {
            $login = $_COOKIE['user'];
        }
        else
        {
            $login = null;
        }

        if ( !$userInfo )
        {
            $authorized = array_key_exists ( 'authorized', $_COOKIE ) ? $_COOKIE['authorized'] : null;
            $userInfo = array ( "login" => $login, "authorized" => $authorized );
        }

        $this->isAuthorized ( $userInfo );
    }

    /**
     * Проверяет авторизацию пользователя и устанавливает/сбрасывает флаг авторизации
     * @param Array $userInfo массив с пользовательскими данными для авторизации
     * @return Boolean
     */
    public function isAuthorized ( $userInfo = null )
    {
        $userInfo = !$userInfo ? self::$userInfo : $userInfo;
        if ( !$userInfo )
        {
            return false;
        }

        $login = array_key_exists ( "login", $userInfo ) ? $userInfo['login'] : null;
        $password = array_key_exists ( "password", $userInfo ) ? $userInfo['password'] : null;
        $authorized = array_key_exists ( "authorized", $userInfo ) ? $userInfo['authorized'] : null;

        if ( $login && $password )
        {
            $data = SqlTools::selectObjects ( "SELECT * FROM `prefix_users` WHERE `login`='$login' AND `passwd`=MD5('$password')" );
            if ( count ( $data ) == 1 )
            {
                $this->authorize ( array_shift ( $data ) );

                return true;
            }
            else
            {
                $this->logout ();

                return false;
            }
        }
        else if ( $login && $authorized )
        {
            $data = SqlTools::selectObjects ( "SELECT * FROM `prefix_users` WHERE `login`='$login'" );
            $authorization = $this->createAuthKey ( $login );
            $authorizationKey = array_key_exists ( "userKey", $_SESSION ) ? $_SESSION['userKey'] : "none";

            if ( count ( $data ) == 1 && $authorizationKey == $authorization )
            {
                $this->authorize ( array_shift ( $data ) );

                return true;
            }
            else
            {
                $this->logout ();

                return false;
            }
        }

        return false;
    }

    /**
     * Устанавливает флаг авторизации, запоминает данные пользователя (логин, имя, телефон и т.д.)
     * @param type $userData
     */
    public function authorize ( $userData )
    {
        $authorization = $this->createAuthKey ( $userData->login );
        $_SESSION['userKey'] = $authorization;
        self::$user = $userData->login;
        $userData->name = empty ( $userData->name ) || !$userData->name ? $userData->firstName . " " . $userData->lastName : $userData->name;
        self::$userName = $userData->name ? $userData->name : $userData->login; //$_SESSION['userName'];
        self::$id = $userData->id;
        $userData->address = trim ( (empty ( $userData->city ) ? "" : $userData->city . ", ") . $userData->address );
        self::$userData = $userData;
        self::$userInfo = array ( "login" => $userData->login, "authorized" => $authorization );

        setcookie ( 'user', $userData->login, time () + 3600, '/' );
        setcookie ( 'authorized', $authorization, time () + 3600, '/' );
    }

    /**
     * Возвращает объект с регистрационными данными пользователя или с пустыми полями,
     * если пользователь не зарегистрирован
     */
    public function getUserInfo ()
    {
        if ( is_null ( self::$userData ) )
        {
            $userData = new stdClass();
            $userData->id = 0;
            $userData->status = "";
            $userData->name = "";
            $userData->email = "";
            $userData->phone = "";
            $userData->address = "";
        }
        else
        {
            $userData = self::$userData;
        }

        return $userData;
    }

    /**
     * Сбрасывает флаг авторизации ипользовательские данные
     */
    public function logout ()
    {
        unset ( $_SESSION['userKey'] );
        self::$user = null;
        self::$userName = null;
        self::$userData = null;
        $authorization = md5 ( uniqid () );
        setcookie ( 'authorized', $authorization, time () + 3600, '/' );
        setcookie ( 'user', '', time (), '/' );
    }

    /**
     * Возвращает html-текст с контейнером регистрации/авторизации/выхода
     * @param String $type определяет тип возвращаемого контейнера
     * @return String
     */
    public function userBox ( $type = "view", $error = "" )
    {
        $box = tpl ( 'parts/userBox', array ( 'user' => self::$userName, "type" => $type, "error" => $error ) );

        return $box;
    }

    /**
     * Сверяет пользовательские данные из формы с регистрационными
     * и возвращает true при совпадении или false в противном случае
     * @param Mixed $userData
     */
    public function checkUser ( $userData )
    {

    }

    public function loginAction ()
    {
        $login = array_key_exists ( "login", $_POST ) ? preg_replace ( '/^[^a-zA-Z0-9]+$/', '', $_POST['login'] ) : null;
        $pwd = array_key_exists ( "password", $_POST ) ? preg_replace ( '/^[^a-zA-Z0-9]+$/', '', $_POST['password'] ) : null;

        $userInfo = array ( "login" => $login, "password" => $pwd );
        $authorized = $this->isAuthorized ( $userInfo );
        $error = "";

        if ( $authorized )
        {
            $type = "view";
        }
        else
        {
            if ( array_key_exists ( "login", $_POST ) || array_key_exists ( "password", $_POST ) )
            {
                $error = "Неверный логин или пароль";
            }

            $type = "loginAction";
        }

        htmlHeader ();
        echo $this->userBox ( $type, $error );
    }

    public function logoutAction ()
    {
        $this->logout ();
        htmlHeader ();
        echo $this->userBox ( "logoutAction" );
    }

    public function registerAction ()
    {
        $authorized = null;
        $login = array_key_exists ( "login", $_POST ) ? preg_replace ( '/^[^a-zA-Z0-9]+$/', '', $_POST['login'] ) : null;
        $pwd = array_key_exists ( "password", $_POST ) ? preg_replace ( '/^[^a-zA-Z0-9]+$/', '', $_POST['password'] ) : null;

        $error = "";
        if ( $login && $pwd )
        {
            $exists = SqlTools::selectValue ( "SELECT 1 FROM `prefix_users` WHERE `login`='$login'" );
            if ( $exists )
            {
                $error = "Пользователь с такими данными существует";
            }
            else
            {
                SqlTools::insert ( "INSERT INTO `prefix_users` (`login`, `passwd`, `status`) VALUES ('$login', md5('$pwd'), 'client')" );
            }

            $userInfo = array ( "login" => $login, "password" => $pwd );
            $authorized = $this->isAuthorized ( $userInfo );
        }

        if ( $authorized )
        {
            $type = "view";
        }
        else
        {
            $type = "registerAction";
        }

        htmlHeader ();
        echo $this->userBox ( $type, $error );
    }

    private function createAuthKey ( $login )
    {
        return $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $login . $this->salt;
    }

    public function authorizationBox ()
    {
        return tpl ( "parts/authBox", array () );
    }

    public function getRegistrationCard ()
    {
        $userInfo = $this->getUserInfo ();
        return tpl ( "modules/PrivateOffice/shortProfile", array ( "userData" => $userInfo, "cardTitle" => "Регистрационные данные" ) );
    }

    public function registration ( $regData )
    {
        $errors = array ();

        if ( !$regData['email'] )
        {
            $errors['email'] = "Поле не может быть пустым";
        }

        if ( !$regData['login'] )
        {
            $errors['login'] = "Поле не может быть пустым";
        }

        if ( !$regData['password'] )
        {
            $errors['password'] = "Поле не может быть пустым";
        }

        if ( $regData['password'] AND $regData['confirm'] != $regData['password'] )
        {
            $errors['confirm'] = "Подтверждение не совпадает с паролем";
        }

        if ( !$regData['telephone'] )
        {
            $errors['telephone'] = "Поле не может быть пустым";
        }

        require_once(LIBS . DS . 'recaptchalib.php');
        $privatekey = SqlTools::selectValue ( "SELECT `value` FROM `prefix_settings` WHERE `callname`='privateKey'" );
        if ( $privatekey )
        {
            $host = $_SERVER["REMOTE_ADDR"];
            $challenge = $_POST["recaptcha_challenge_field"];
            $response = $_POST["recaptcha_response_field"];
            $resp = recaptcha_check_answer ( $privatekey, $host, $challenge, $response );

            if ( !$resp->is_valid )
            {
                $errors['captcha'] = "Неверно введено проверочное выражение";
            }
        }
        if ( !array_key_exists ( "login", $errors ) && !array_key_exists ( "email", $errors ) && !array_key_exists ( "telephone", $errors ) )
        {
            if ( $this->isUnique ( $regData ) )
            {
                $errors['login'] = "Пользователь с такими данными уже существует";
            }
        }

        if ( count ( $errors ) )
            return array ( "error" => $errors );

        if ( !array_key_exists ( "name", $regData ) )
        {
            $regData['name'] = $regData['firstname'] . " " . $regData['lastname'];
        }

        $address = $regData['address1'] . " " . $regData['address2'];
        $query = "INSERT INTO `prefix_users` (`login`, `passwd`, `status`, `firstName`, `lastName`, `name`, `company`, `email`, `phone`, `address`) VALUES (";
        $query .= "'" . $regData['login'] . "', md5('" . $regData['password'] . "'), 'client', '" . $regData['firstname'] . "', '" . $regData['lastname'] . "', '" . $regData['name'] . "', '" . $regData['company'] . "',";
        $query .= "'" . $regData['email'] . "', '" . $regData['telephone'] . "', '$address')";
        SqlTools::insert ( $query );

        $result = array ( 'redirect' => "/privateOffice/order" );
        $userInfo = array ( "login" => $regData['login'], "password" => $regData['password'] );
        $authorized = $this->isAuthorized ( $userInfo );


        return $result;
    }

    public function getShortProfile ()
    {
        return tpl ( "parts/shortProfile", array ( "userData" => $this->getUserInfo () ) );
    }

    public function updateAccount ()
    {
        $errors = array ();
        $firstname = filter_input ( INPUT_POST, "name", FILTER_SANITIZE_STRING );
        $phone = filter_input ( INPUT_POST, "phone", FILTER_SANITIZE_STRING );

        if ( !$phone )
        {
            $errors['phone'] = "Поле Телефон не может пустым";
        }

        $mail = filter_input ( INPUT_POST, "mail", FILTER_SANITIZE_EMAIL );
        if ( !$mail )
        {
            $errors['mail'] = "Поле Email не может пустым";
        }

        $address = filter_input ( INPUT_POST, "address", FILTER_SANITIZE_STRING );

        if ( count ( $errors ) )
        {
            return array ( "error" => $errors );
        }

        $update = array ();
        $info = $this->getUserInfo ();
        if ( $firstname && $info->name != $firstname )
        {
            $update[] = "`name`='$firstname'";
        }

        if ( $phone && $info->phone != $phone )
        {
            $update[] = "`phone`='$phone'";
        }

        if ( $info->email != $mail )
        {
            $update[] = "`email`='$mail'";
        }

        if ( $address && $info->address != $address )
        {
            $update[] = "`address`='$address'";
        }

        if ( count ( $update ) )
        {
            $query = "UPDATE `prefix_users` SET " . implode ( ',', $update ) . " WHERE `id`=$info->id";
            SqlTools::execute ( $query );
        }

        return array ( "success" => 1 );
    }

    private function isUnique ( $regData )
    {
        $exists = SqlTools::selectValue ( "SELECT 1 FROM `prefix_users` WHERE `login`='" . $regData['login'] . "' OR `email`='" . $regData['email'] . "' OR `phone`='" . $regData['telephone'] . "'" );
        return $exists;
    }






}
