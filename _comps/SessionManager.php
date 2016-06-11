<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SessionManager
 *
 * @author kraser
 */
class SessionManager extends CmsComponent
{

    private $lifeTime;

    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
    }

    public function start ()
    {
        session_set_cookie_params ( $this->lifeTime );
        session_name ( $this->sessionName );
        session_start ();
    }

    public function getParameter ( $name, $default=null )
    {
        return array_key_exists ( $name, $_SESSION ) ? $_SESSION[$name] : $default;
    }

    public function setParameter ( $name, $value )
    {
        $_SESSION[$name] = $value;
    }

    public function clearParameter ( $name )
    {
        unset ( $_SESSION[$name] );
    }

    private $sessionName;
    public function setSessionName ( $name )
    {
        $this->sessionName = $name;
    }

    public function setLifeTime ( $time )
    {
        $this->lifeTime = $time;
    }
}