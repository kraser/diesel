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
        $moduleName = Starter::app()->urlManager->getRoute ();
        if ( !$moduleName)
            page404 ();
        else
        {
            $module = Starter:: app ()->getModule ( $moduleName );
            $html = $module->Run ();
            header ( "Content-Type: text/html; " . _CHARSET );
            ob_start ( "ob_gzhandler" );
            echo $html;
            ob_end_flush ();
            /* super god-mode check */
            if ( Tools::getSettings ( 'Blocks', 'godmode_suspended', true ) == true && array_key_exists ( "admin", $_SESSION ) && $_SESSION['admin']['type'] == 'a' )
            {
                $bGodmodeSuspended = array_key_exists ( 'godmode_suspended', $_SESSION ) && $_SESSION['godmode_suspended'] ? 'true' : ' false';
                echo '<script type="text/javascript">var g_bGodmode = true; g_bGodmodeSuspended = ' . $bGodmodeSuspended . '</script>';
                echo '<script type="text/javascript" src="/js/godmode.js"></script>';
            }
        }
    }
}
