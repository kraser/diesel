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
        $parser = Starter::app()->urlManager;
        $matchesUri = array ();
        preg_match ( '%/([^?#]+)?%i', $_SERVER['REQUEST_URI'], $matchesUri );
        $route = $parser->validateURI ();
        switch ( $route )
        {
            case 'content':
                $moduleName = "Content";
                break;

            case 'module':
                $docsByPath = Starter::app ()->urlManager->getDocsByPath ();
                $moduleName = $docsByPath[count ( $docsByPath ) - 1]->module;
                break;

            default:
                throw new Exception ( "Страница не найдена", 404 );
        }

        $module = Starter:: app ()->getModule ( $moduleName );
        $html = $module->Run ();
        header ( "Content-Type: text/html; " . _CHARSET );
        ob_start ( "ob_gzhandler" );
        echo $html;
//        $smarty = SmartyTools::getSmarty ();
//        $smarty->assign ( 'model', $html );
//
//        $smarty->display ( TEMPL . DS . 'html.tpl' );
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
