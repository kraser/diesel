<?php

/**
 * <pre>Базовый класс-заготовка для обеспечения аутентификации и авторизации
 * как пользователей, так и роботов/сервисов при входе в систему</pre>
 * @author kraser
 */
abstract class Authorization
{
    /**
     * <p>Пользователь системы</p>
     * @var CmsUser
     */
    protected $user;

    /**
     * <p>"Соль" для хэша авторизации</p>
     * @var String
     */
    protected $salt;

    /**
     * <p>Хэш авторизации</p>
     * @var String
     */
    private $hash;

    public function __construct ()
    {
        $this->user = array_key_exists ( "user", $_SESSION ) ? unserialize ( $_SESSION['user'] ) : null;
        $this->salt = "BhwmpAG556cdUCr4ber";
    }

    /**
     * <pre>Заполняет глобальные переменные свойствами
     * переданного пользователя</pre>
     *
     * @param CmsUser $user
     */
    protected function setUser ( CmsUser $user )
    {
        $user->name = $user->name ? : ( $user->firstName && $user->lastName ? $user->firstName . " " . $user->lastName : $user->login );

        $_SESSION['authKey'][$user->authorization] = $user->id;
        //SqlTools::execute ( "UPDATE `prefix_users` SET `lastEnter`=NOW() WHERE `id`=$user->id" );

        setcookie ( 'user', $user->login, time () + 3600, '/' );
        setcookie ( 'authorized', $user->authorization, time () + 3600, '/' );
        setcookie ( 'hash', $user->hash, time () + 3600, '/' );

        $this->user = $user;
        $_SESSION['user'] = serialize ( $user );
    }

    /**
     * <pre>Проверка авторизации пользователя</pre>
     *
     * @param CmsUser $checkedUser <p>Пользователь авторизация которого проверяется</p>
     * @return Boolean
     */
    public function isAuthorized ( CmsUser $checkedUser = null )
    {
        $user = $checkedUser ? : $this->user;
        if ( !$user )
            return false;

        if ( $user->authorization )
        {
            $userId = $_SESSION['authKey'][$user->authorization];
            $data = SqlTools::selectObjects ( "SELECT * FROM `prefix_users`
                WHERE `login`='" . SqlTools::escapeString ( $user->login ) . "'
                    AND `id`='" . $userId . "'
                    AND `deleted`='N'", "CmsUser" );

            if ( count ( $data ) != 1 )
                return false;

            $referUser = array_shift ( $data );
            $authorization = $this->createAuthKey ( $referUser->passwd, $user->hash );

            if ( $user->authorization == $authorization )
            {
                $referUser->hash = $user->hash;
                $referUser->authorization = $authorization;
                $this->setUser ( $referUser );

                return true;
            }
            else
                return false;
        }
        else if ( $user->login && $user->passwd )
        {
            $login = $user->login;
            $password = $user->passwd;
            $data = SqlTools::selectObjects ( "SELECT * FROM `prefix_users`
                WHERE `login`='" . SqlTools::escapeString ( $login ) . "'
                    AND `passwd`=MD5('" . SqlTools::escapeString ( $password ) . "')
                    AND `deleted`='N'", "CmsUser" );

            if ( count ( $data ) == 1 )
            {
                $user = array_shift ( $data );
                $user->authorization = $this->createAuthKey ( $user->passwd );
                $user->hash = $this->hash;
                $this->setUser ( $user );

                return true;
            }
            else
                return false;
        }

        return false;
    }

    /**
     * Удаление пользователя из сессии
     */
    public function logout ()
    {
        unset ( $_SESSION['user'] );
        unset ( $_SESSION['authKey'] );
        $this->user = null;

        $authorization = md5 ( uniqid () );
        setcookie ( 'authorized', $authorization, time () + 3600, '/' );
        setcookie ( 'user', '', time (), '/' );
    }

    public function getUser ()
    {
        return $this->user;
    }

    /**
     * <pre>Создает ключ авторизации</pre>
     * @param String $login <p>login авторизуемого пользователя</p>
     * @return String
     */
    private function createAuthKey ( $password, $hash = null )
    {
        $hash = $hash ? : uniqid ();
        $this->hash = $hash;
        $userAgent = filter_input ( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING );
        $remote = filter_input ( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING );
        return md5 ( $hash . $userAgent . $remote . $password . $this->salt );
    }

    /**
     * <p>Основная логика проверки</p>
     */
    abstract public function processUser ();

    /**
     * <p>Вывод формы авторизации</p>
     */
    abstract public function login ();
}
