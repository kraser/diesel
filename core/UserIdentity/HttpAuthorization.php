<?php

/**
 * <pre>Класс для http-аутентификации роботов</pre>
 * @author kraser
 */
class HttpAuthorization extends Authorization
{

    /**
     * Основная логика проверки/авторизации при входе в систему
     */
    public function processUser ()
    {
        $login = array_key_exists ( 'PHP_AUTH_USER', $_SERVER ) ? $_SERVER['PHP_AUTH_USER'] : null;
        $password = array_key_exists ( 'PHP_AUTH_PW', $_SERVER ) ? $_SERVER['PHP_AUTH_PW'] : null;
        if ( $login && $password )
        {
            $user = new CmsUser();
            $user->login = $login;
            $user->passwd = $password;
        }
        else if ( array_key_exists ( "user", $_SESSION ) )
        {
            $user = unserialize ( $_SESSION['user'] );
        }
        else
        {
            $user = null;
        }

        if ( !$this->isAuthorized ( $user ) )
            $this->logout ();
    }

    /**
     * <pre>Посылаем заголовок 401 и выходим из программы</pre>
     */
    public function login ()
    {
        header ( 'WWW-Authenticate: Basic realm="diesel"' );
        header ( 'HTTP/1.0 401 Unauthorized' );
        echo 'Вы не зарегистрированы в системе';
        exit;
    }
}
