<?php

/**
 * @deprecated since version number
 */
class UserManager extends CmsComponent
{

    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
    }

    public function init ()
    {
        parent::init();
    }

    private function find ( $params = null )
    {
        if ( !$params )
            return array ();

        $conditions = array ();

        foreach ( $params as $field => $value )
        {
            if ( !$value )
                continue;

            switch ( $field )
            {
                case "id":
                    $conditions[] = "u.`id` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;

                case "login":
                    $conditions[] = "`login`='" . SqlTools::escapeString ( $value ) . "'";
                    break;

                case "password":
                    $conditions[] = "`password`='" . md5 ( SqlTools::escapeString ( $value ) ) . "'";
                    break;

                case "status":
                    $conditions[] = "`status` IN (" . ArrayTools::stringList ( $value ) . ")";
                    break;

                case "name":
                    $nameParts = explode ( " ", $value );
                    //$user->name = $user->name ? : ( $user->firstName && $user->lastName ? $user->firstName . " " . $user->lastName : $user->login );
                    if ( count ( $nameParts ) == 2 )
                    {
                        $conditions[] = "`firstName`='" . SqlTools::escapeString ( $nameParts[0] ) . "'";
                        $conditions[] = "`lastName`='" . SqlTools::escapeString ( $nameParts[1] ) . "'";
                    }
                    else
                    {
                        $conditions[] = "`login`='" . SqlTools::escapeString ( $value ) . "'";
                    }
                    break;

                case "address":
                    break;
                default:
            }
        }

        $whereClause = count ( $conditions ) ? " WHERE " . implode ( " AND ", $conditions ) : "";
        $query = "SELECT
            u.`id` AS id,
            u.`login` AS login,
            u.`passwd` AS passwd,
            u.`status` AS status,
            u.`firstName` AS firstName,
            u.`lastName` AS lastName,
            u.`email` AS email,
            u.`phone` AS phone,
            u.`address` AS address,
            u.`deleted` AS deleted,
            u.`anons` AS anons,
            u.`description` AS description,
            u.`lastEnter` AS lastEnter
        FROM `prefix_users` u
        $whereClause";
        $users = SqlTools::selectObjects ( $query, "CmsWebUser", "id" );
        foreach ( $users as $user )
        {
            $user->name = $user->name ?
                : ( $user->firstName && $user->lastName
                ? $user->firstName . " " . $user->lastName : $user->login );
        }

        return $users;
    }

    public function getUsers ( $params )
    {
        return $this->find ( $params );
    }

    public function setUser ( $user )
    {
        $this->user = $user;
    }

    public function setData ()
    {
        $error = array ();
        $id = filter_input ( INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT );
        $login = filter_input ( INPUT_POST, "login", FILTER_SANITIZE_STRING );
        if ( !$login )
            $error[] = "Поле 'Логин' должно быть заполнено";
        $passwd = filter_input ( INPUT_POST, "passwd", FILTER_SANITIZE_STRING );
        if ( !$passwd && ( array_key_exists ( "passwd", $_POST ) || $id == 0 ) )
            $error[] = "Поле 'Пароль' должно быть заполнено";
        $firstName = filter_input ( INPUT_POST, "firstName", FILTER_SANITIZE_STRING );
        if ( !$firstName )
            $error[] = "Поле 'Имя' должно быть заполнено";
        $lastName = filter_input ( INPUT_POST, "lastName", FILTER_SANITIZE_STRING );
        if ( !$lastName )
            $error[] = "Поле 'Фаимилия' должно быть заполнено";
        $address = filter_input ( INPUT_POST, "address", FILTER_SANITIZE_STRING );
        if ( !$address )
            $error[] = "Поле 'Адрес' должно быть заполнено";
        $phone = filter_input ( INPUT_POST, "phone", FILTER_SANITIZE_STRING );
        $email = filter_input ( INPUT_POST, "email", FILTER_SANITIZE_EMAIL );
        if ( !$phone && !$email )
            $error[] = "Хотя бы одно из полей 'Телефон' или 'E-mail' должно быть заполнено";
        if ( count ( $error ) )
            return array ( "error" => 1, "msg" => implode ( "\n", $error ) );

        $status = array_key_exists ( "status", $_POST ) ? filter_input ( INPUT_POST, "status", FILTER_SANITIZE_STRING ) : "client";
        $description = array_key_exists ( "description", $_POST ) ? filter_input ( INPUT_POST, "description", FILTER_SANITIZE_STRING ) : null;
        if ( $id )
        {
            $user = ArrayTools::head ( $this->find ( array ( "id" => $id ) ) );
            $update = array ();
            if ( $user->login != $login )
                $update[] = "`login`='" . SqlTools::escapeString ( $login ) . "'";
            if ( $passwd && $user->passwd != md5 ( $passwd ) )
                $update[] = "`passwd`=MD5('$passwd')";
            if ( $status && $status != $user->status )
                $update[] = "`status`='$status'";
            if ( $firstName != $user->firstName )
                $update[] = "`firstName`='" . SqlTools::escapeString ( $firstName ) . "'";
            if ( $lastName != $user->lastName )
                $update[] = "`lastName`='" . SqlTools::escapeString ( $lastName ) . "'";
            if ( $email != $user->email )
                $update[] = "`email`='" . SqlTools::escapeString ( $email ) . "'";
            if ( $phone != $user->phone )
                $update[] = "`phone`='" . SqlTools::escapeString ( $phone ) . "'";
            if ( $address != $user->address )
                $update[] = "`address`='" . SqlTools::escapeString ( $address ) . "'";
            if ( $description != $user->description )
                $update[] = "`description`='" . SqlTools::escapeString ( $description ) . "'";

            if ( count ( $update ) )
            {
                $updateSet = implode ( ",", $update );
                SqlTools::execute ( "UPDATE `prefix_users` SET " . $updateSet . ", `modified`=NOW() WHERE `id`=$id" );
            }

            return array("success" => $id);
        }
        else
        {
            $query = "
                INSERT INTO `prefix_users` (`login`, `passwd`, `status`, `firstName`, `lastName`, `email`, `phone`, `address`, `description`, `created`, `deleted`)
                VALUES (
                    '" . SqlTools::escapeString ( $login ) . "',
                    MD5('$passwd'),
                    '$status',
                    '" . SqlTools::escapeString ( $firstName ) . "',
                    '" . SqlTools::escapeString ( $lastName ) . "',
                    '" . SqlTools::escapeString ( $email ) . "',
                    '" . SqlTools::escapeString ( $phone ) . "',
                    '" . SqlTools::escapeString ( $address ) . "',
                    '" . SqlTools::escapeString ( $description ) . "',
                    NOW(),
                    'N'
                )";
            $userId = SqlTools::insert ( $query );
            return array("success" => $userId);
        }
    }

    public function renderTab ( $user, $userId )
    {
        if ( !$userId )
        {
            $data = new CmsWebUser();
            $data->id = 0;
        }
        else
        {
            $data = ArrayTools::head ( $this->find ( array ( "id" => $userId ) ) );
        }

        $html = TemplateEngine::view ( "modules/User/userCard", array ( "client" => $data, "user" => $user ), true );
        return $html;
    }

    public function renderList ( $user, $params )
    {
        return "khskfksfhskh";
    }
}
