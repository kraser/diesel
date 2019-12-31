<?php

/**
 * <pre>Базовый класс пользователя системы</pre>
 *
 * @author kraser
 */
class CmsUser
{
    /**
     * <p>Id пользлвателя</p>
     * @var Integer
     */
    public $id;

    /**
     * <p>Логин пользователя</p>
     * @var String
     */
    public $login;

    /**
     * <p>MD5 пароля пользователя</p>
     * @var String
     */
    public $passwd;

    /**
     *
     * @var type
     */
    public $status;

    /**
     *
     * @var String
     */
    public $firstName;

    /**
     *
     * @var String
     */
    public $lastName;
    public $company;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $deleted;
    public $anons;
    public $description;
    public $hash;
    public $authorization;
    public $lastEnter;

}
