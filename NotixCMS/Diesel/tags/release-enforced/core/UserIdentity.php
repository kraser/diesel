<?php

/**
 * <pre>Класс UserIdentity</pre>
 *
 * @author kraser
 */
class UserIdentity
{
    private static $instance;
    private $authorizer;

    private function __construct ()
    {
        Starter::import ( "core.UserIdentity.*" );
        $userAgent = filter_input ( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING );

        if ( 'cli' == PHP_SAPI )
        {
            $this->authorizer = new TerminalAuthorization();
        }
        else if ( $userAgent && $this->isBrowser ( $userAgent ) )
        {
            $this->authorizer = new HtmlAuthorization();
        }
        else
        {
            $this->authorizer = new HttpAuthorization();
        }
    }

    public static function &getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function init ()
    {
        $ident = self::getInstance ();
        $ident->processUser ();
    }

    public function processUser ()
    {
        $this->authorizer->processUser ();
    }

    /**
     * Читает параметры сессии, и проверяет авторизацию
     */
    public function isAuthorized ()
    {
        return $this->authorizer->isAuthorized ();
    }

    /**
     * <pre></pre>
     */
    public static function login ()
    {
        $ident = self::getInstance ();
        $fields = $ident->authorizer->login ();
        $fields['error'] = isset ( $_SESSION['authCount'] ) ? "Ошибка! Неверные данные: логин или пароль" : null;
        return $fields;
    }

    private function isBrowser ( $userAgent )
    {
        // для определения, что User-Agent от браузера, используется набор ключевых слов,
        // присущий подавляющему большинству браузеров
        return preg_match ( "/(Mozilla|Opera|MSIE|Safari|Chrome|Chromium|ELinks|Links|Lynx|Dillo|amaya|Gecko|KHTML|AppleWebKit|WebKit|libwww)/", $userAgent );
    }

    public static function getUser ()
    {
        $ident = self::getInstance ();
        return $ident->authorizer->getUser ();
    }

    public function logout()
    {
        $ident = self::getInstance ();
        $ident->authorizer->logout ();
    }
}
