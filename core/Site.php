<?php

/**
 * Класс реализующий режим Site
 */
class Site extends CmsApplication
{

    /**
     * Конструктор режима Site
     * Сканирует директорию LIBS и подгружает библиотеки,
     * не оформленные в виде классов
     */
    public function __construct ( $alias, $config )
    {
        parent::__construct ( $alias, $config );
        require_once  DOCROOT . DS . 'system' . DS . "lib" . DS . "template_engine.php";
    }

    /**
     * Запуск режима Site
     * @return void
     * @throws Exception
     */
    public function Run ()
    {
        $moduleName = Starter::app()->getUrlManager()->getRoute ();
        if ( !$moduleName)
            $this->page404 ();
        else
        {
            $module = Starter:: app ()->getModule ( $moduleName );
            $html = $module->Run ();
            header ( "Content-Type: text/html; " . _CHARSET );
            ob_start ( "ob_gzhandler" );
            echo $html;
            ob_end_flush ();
        }
    }
}
