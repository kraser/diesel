<?php

/**
 * <pre>Класс реализующий приложение Admin</pre>
 */
class Admin extends CmsApplication
{
    /**
     * Информация об административных модулях (компонентах)
     * в виде ассоциированного массива, для построения меню админ.панели
     *
     * @var Array
     */
    private $adminModules;

    public function __construct ( $alias, $config )
    {
        parent::__construct ( $alias, $config );
    }

    public function init ()
    {
        parent::init ();
        $user = AdminUsers::getInstance ();
        //Загрузка библиотек админки
        $lib = scandir ( DOCROOT . '/admin/lib', 1 );
        foreach ( $lib as $file )
        {
            if ( substr ( $file, -3, 3 ) == 'php' )
                require_once ( DOCROOT . '/admin/lib/' . $file );
        }

        require_once ( TOOLS . DS . 'additionalFunctions.php' );

        //Подключение модулей
        $user = AdminUsers::getInstance ();
        $mods = scandir ( DOCROOT . '/admin/modules', 1 );
        Starter::setAliasPath ( 'adminModules', DOCROOT . '/admin/modules' );
        Starter::import ( 'adminModules.*' );
        $modulesConfig = $this->modules;
        foreach ( $mods as $file )
        {
            if ( substr ( $file, -3, 3 ) == 'php' )
            {
                $moduleName = substr ( $file, 0, -4 );
                if ( !$user->isAllowed ( $moduleName ) || !array_key_exists ( $moduleName, $modulesConfig ) )
                    continue;

                include DOCROOT . '/admin/modules/' . $file;

                $modules[] =
                [
                    'module' => $moduleName,
                    'name' => $moduleName::name,
                    'icon' => defined ( "$moduleName::icon" ) ? $moduleName::icon : false,
                    'hide' => $moduleName::hide ? ($moduleName::hide) : false
                ];
                $modules_sort[] = $moduleName::order;
            }
        }

        array_multisort ( $modules_sort, $modules );
        $this->adminModules = $modules;
    }

    public function getAdminModules ()
    {
        return $this->adminModules;
    }

    public function getModuleClass ( $module )
    {
        return ucfirst ( $module );
    }

    public function Run ()
    {
        $modules = $this->adminModules;
        $parser = Starter::app ()->urlManager;
        $route = $parser->getParameter ( "module", "About" );

        $rc = new ReflectionClass ( $route );
        $module = $rc->newInstance ();
        header ( "Content-Type: text/html; " . _CHARSET );
        ob_start ( "ob_gzhandler" );
        echo TemplateEngine::view ( 'index', array (
            'title' => ( !empty ( $module->title ) ? strip_tags ( $module->title ) . ' — ' : '' ) . 'Нотикс CMS',
            'h1' => $module->title,
            'content' => $module->content,
            'menu' => menu ( array ( 'modules' => $modules ) ),
            'hint' => $module->Hint (),
            'sidemenu' => $module->SubMenu (),
            'userbar' => AdminUsers::getInstance ()->userBar ()
        ) );
        ob_end_flush ();
        if ( $_SESSION['godmode_ref'] && isset ( $_GET['return'] ) )
        {
            header ( "Location: {$_SESSION['godmode_ref']}" );
            unset ( $_SESSION['godmode_ref'] );
            exit;
        }
    }
    /*Костыль*/
    public function getModule ( $id )
    {
        $module = new $id();
        return $module;
    }
}