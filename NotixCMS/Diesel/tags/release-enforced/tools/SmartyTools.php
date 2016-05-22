<?php

class SmartyTools
{
    private static $instance;
    public $smarty;

    private function __construct ()
    {
        $folders = Starter::app ()->folders;
        $this->smarty = new Smarty;
        $this->smarty->compile_dir = $folders['SMARTY_TEMPLATES'];
        $this->smarty->cache_dir = $folders['SMARTY_CACHE'];
        $this->smarty->force_compile = true;

        return $this->smarty;
    }

    public static function &getInstance ()
    {
        if ( self::$instance === null )
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public static function getSmarty ()
    {
        $instance = self::getInstance ();

        return $instance->smarty;
    }
}
