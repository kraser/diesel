<?php

/**
 * <pre>Класс для авторизации пользователей с вводом логина-пароля через html-форму</pre>
 * @author kraser
 */
class HtmlAuthorization extends Authorization
{

    public function processUser ()
    {
        $authBox = filter_input ( INPUT_POST, "authBox" );
        $submit = filter_input ( INPUT_POST, "submit" );
        if ( $authBox || $submit )
        {
            $login = filter_input ( INPUT_POST, "login", FILTER_SANITIZE_STRING );
            $pwd = filter_input ( INPUT_POST, "password", FILTER_SANITIZE_STRING );
            $user = new CmsUser;
            $user->login = $login;
            $user->passwd = $pwd;
        }
        else if ( isset ( $this->user ) )
        {
            $user = $this->user;
        }
        else if ( array_key_exists ( "user", $_COOKIE ) )
        {
            $user = new CmsUser;
            $user->login = filter_input ( INPUT_COOKIE, "user", FILTER_SANITIZE_STRING );
            $user->authorization = filter_input ( INPUT_COOKIE, "authorized", FILTER_SANITIZE_STRING );
            $user->hash = filter_input ( INPUT_COOKIE, "hash", FILTER_SANITIZE_STRING );
        }
        else
        {
            $user = null;
        }

        if ( !$this->isAuthorized ( $user ) )
        {
            $this->logout ();
        }
    }

    /**
     * <pre>Форму авторизации не выводим, а отдаем поля для формирования формы модулем</pre>
     * @return Array
     */
    public function login ()
    {
        return array
        (
            'authBox' => array ( 'name' => 'authBox', 'type' => 'hidden', 'value' => 'LOGIN' ),
            'login' => array ( 'name' => 'login', 'type' => 'text', 'label' => 'Логин' ),
            'password' => array ( 'name' => 'password', 'type' => 'password', 'label' => 'Пароль' )
        );
    }
}
