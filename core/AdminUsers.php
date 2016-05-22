<?php

class AdminUsers
{
    private static $instance;
    public $user;
    private $allAlowed = array ( 'About' );

    private function __construct ()
    {
        if ( !session_id () )
            session_start ();

        if ( isset ( $_GET['logout'] ) )
            $this->logout ();

        if ( $this->isLogged () )
            $this->auth ();
        else
            $this->login ();
    }

    public static function &getInstance ()
    {
        if ( self::$instance === null )
            self::$instance = new self();

        return self::$instance;
    }

    function isLogged ()
    {
        if ( isset ( $_SESSION['admin'] ) )
            return true;
        else
            return false;
    }

    function isAllowed ( $module )
    {
        if ( $_SESSION['admin']['type'] == 'a' )
            return true;

        if ( isset ( $_SESSION['admin']['access'][$module] ) || in_array ( $module, $this->allAlowed ) )
            return true;
        else
            return false;
    }

    /**
     * Выводит блок пользователя
     */
    function userBar ()
    {
        return TemplateEngine::view ( 'widgets/userbar', array (
                'login' => $this->user['login'],
                'user' => $this->user
        ) );
    }

    /**
     * Логин
     */
    function login ()
    {
        if ( @!$_SESSION['godmode_ref'] )
            $_SESSION['godmode_ref'] = @$_SERVER['HTTP_REFERER'];

        if ( !empty ( $_POST['send'] ) )
        {
            $login = SqlTools::escapeString ( $_POST['login'] );
            $user = SqlTools::selectRow ( "SELECT * FROM `prefix_admin_users` WHERE `login`='$login' AND `password`='" . md5 ( $_POST['password'] ) . "'", MYSQL_ASSOC );
            if ( !empty ( $user ) )
            {
                $_SESSION['admin'] = $user;
                $_SESSION['useradminkey'] = md5 ( $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SESSION['admin']['password'] . 'douglas' );
                $this->auth ();
                return true;
            }
            else
                $this->errorLogin ();
        }
        else
        {
            $title = 'Система администрирования сайта';
            $login = TemplateEngine::view ( 'widgets/login', array () );
            echo TemplateEngine::view ( 'login', array (
                'title' => $title,
                'h1' => $title,
                'menu' => '',
                'userbar' => '',
                'hint' => '',
                'sidemenu' => '',
                'content' => $login
            ) );
            exit ();
        }
    }

    /**
     * Авторизация
     */
    function auth ()
    {
        if ( $_SESSION['useradminkey'] != md5 ( $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SESSION['admin']['password'] . 'douglas' ) )
            $this->logout ();

        $admin = SqlTools::selectRow ( "SELECT * FROM `prefix_admin_users` WHERE `id`=" . (( int ) $_SESSION['admin']['id']) . " AND `login`='" . SqlTools::escapeString ( $_SESSION['admin']['login'] ) . "' AND `password`='" . SqlTools::escapeString ( $_SESSION['admin']['password'] ) . "'", MYSQL_ASSOC );
        $_SESSION['admin'] = $admin;

        if ( empty ( $_SESSION['admin'] ) )
            $this->logout ();

        $_SESSION['admin']['access'] = unserialize ( $_SESSION['admin']['access'] );
        $this->user = $_SESSION['admin'];
    }

    /**
     * Указание браузеру авторизоваться по ложным данным
     */
    function logout ()
    {
        unset ( $_SESSION['admin'] );
        unset ( $_SESSION['useradminkey'] );
        session_destroy ();
        header ( 'Location: /' );
        exit ();
    }

    /**
     * Если пользователь авторизовался, но в базе такого нет
     */
    function errorLogin ()
    {
        $title = 'Система администрирования сайта';
        echo TemplateEngine::view ( 'login', array (
            'title' => $title,
            'error' => 'Ошибка авторизации',
            'post' => $_POST
        ) );
        exit ();
    }
}
