<?php

class AdminUsers
{
    private static $instance;
    public $user;
    private $allAlowed = array ( 'About' );

    private function __construct ()
    {
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
        return !is_null ( Starter::app ()->session->getParameter ( 'admin' ) );
    }

    function isAllowed ( $module )
    {
        $admin = Starter::app ()->session->getParameter ( 'admin' );
        $allowed = $admin['type'] == 'a' || isset ( $admin['access'][$module] ) || in_array ( $module, $this->allAlowed );
        return $allowed;
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
        $godModeRef = Starter::app ()->session->getParameter ( 'godmode_ref' );
        if ( !$godModeRef && array_key_exists ( 'HTTP_REFERER', $_SERVER ) )
            Starter::app ()->session->setParameter ( 'godmode_ref', $_SERVER['HTTP_REFERER'] );

        if ( !empty ( $_POST['send'] ) )
        {
            $login = SqlTools::escapeString ( $_POST['login'] );
            $user = SqlTools::selectRow ( "SELECT * FROM `prefix_admin_users` WHERE `login`='$login' AND `password`='" . md5 ( $_POST['password'] ) . "'", MYSQLI_ASSOC );
            if ( !empty ( $user ) )
            {
                Starter::app ()->session->setParameter ( 'admin', $user );
                $adminKey = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $user['password'] . 'douglas';
                Starter::app ()->session->setParameter ( 'useradminkey',  md5 ( $adminKey ));
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
        $adminKey = Starter::app ()->session->getParameter ( 'useradminkey' );
        $admin = Starter::app ()->session->getParameter ( 'admin' );
        if ( $adminKey != md5 ( $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $admin['password'] . 'douglas' ) )
            $this->logout ();

        $admin = SqlTools::selectRow ( "SELECT * FROM `prefix_admin_users` WHERE `id`=" . (( int ) $admin['id']) . " AND `login`='" . SqlTools::escapeString ( $admin['login'] ) . "' AND `password`='" . SqlTools::escapeString ( $admin['password'] ) . "'", MYSQLI_ASSOC );
        if ( is_null ( $admin ) )
            $this->logout ();

        $admin['access'] = unserialize ( $admin['access'] );
        Starter::app ()->session->setParameter ( 'admin', $admin );
        $this->user = $admin;
    }

    /**
     * Указание браузеру авторизоваться по ложным данным
     */
    function logout ()
    {
        Starter::app ()->session->clearParameter ( 'admin' );
        Starter::app ()->session->clearParameter ( 'useradminkey' );
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
