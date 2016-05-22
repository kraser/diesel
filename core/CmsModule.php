<?php
/**
 * Description of CmsModule
 *
 * @author kraser
 */
class CmsModule extends CmsComponent
{
    private $component;
    private $componentsMap;
    public $controllerMap;
    private $modulesConfig;
    private $module;
    private $controllerPath;
    private $currentController;
    private $defaultController = "default";

    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $this->module = [];
    }

    public function init ()
    {
        parent::init();
        $installer = Starter::app ()->getComponent ( "installer" );
        $installer->Run ();
    }

    public function configure ( $config )
    {
        foreach ( $config as $param => $value )
        {
            $this->$param = $value;
            unset ( $config[$param] );
        }
    }

    public function getComponent ( $alias, $forceCreate = true )
    {
        if ( isset ( $this->component[$alias] ) )
            return $this->component[$alias];
        elseif ( isset ( $this->componentsMap[$alias] ) && $forceCreate )
        {
            $config = $this->componentsMap[$alias];
            if ( !isset ( $config['enabled'] ) || $config['enabled'] )
            {
                $component = Starter::createComponent ( $config );
                $component->init ();
                $this->component[$alias] = $component;
                return $component;
            }
        }
    }

    public function setAliases ( $aliases )
    {
        foreach ( $aliases as $alias => $path )
        {
            Starter::setAliasPath ( $alias, $path );
        }
    }

    public function setImport ( $aliases )
    {
        foreach ( $aliases as $alias )
        {
            Starter::import ( $alias );
        }
    }

    public function getModule ( $id )
    {
        if ( isset ( $this->module[$id] ) || array_key_exists ( $id, $this->module ) )
            return $this->module[$id];
        elseif ( isset ( $this->modulesConfig[$id] ) )
        {
            $config = $this->modulesConfig[$id];
            if ( !isset ( $config['enabled'] ) || $config['enabled'] )
            {
//                $class = $config['class'];
//                unset ( $config['class'], $config['enabled'] );
                if ( $this === Starter::app () )
                    $module = Starter::createComponent ( $config, $id, null );
                else
                    $module = Starter::createComponent ( $config, $this->getId () . '/' . $id, $this );

                $this->module[$id] = $module;
                return $this->module[$id];
            }
        }
    }

    public function setModules ( $modules )
    {
        foreach ( $modules as $key => $module )
        {
            if ( is_numeric ( $key ) )
            {
                $alias = $module;
                $module = [];
            }
            else
                $alias = $key;

            if ( !isset ( $module['class'] ) )
			{
				Starter::setAliasPath ( $alias, $this->getModulePath () . DS . $alias );
				$module = [ 'class' => $this->getModuleClass ( $alias ) ];
			}

            if ( isset($this->modulesConfig[$alias] ) )
				$this->modulesConfig[$alias] = ArrayTools::merge ( $this->modulesConfig[$alias], $module );
			else
				$this->modulesConfig[$alias] = $module;
        }
    }

    public function getModules ()
    {
        return $this->modulesConfig;
    }

    public function setComponents ( $components, $merge = true )
	{
		foreach ( $components as $id => $component )
        {
			$this->setComponent ( $id, $component, $merge );
        }
	}

    public function setComponent ( $id, $component, $merge = true )
    {
        if ( $component === null )
        {
            unset ( $this->component[$id] );
            return;
        }
        elseif ( $component instanceof CmsComponent )
        {
            $this->component[$id] = $component;

            if ( !$component->isInit )
                $component->init ();

            return;
        }
        elseif ( isset ( $this->component[$id] ) )
        {
            if ( isset ( $component['class'] ) && get_class ( $this->component[$id] ) !== $component['class'] )
            {
                unset ( $this->component[$id] );
                $this->componentsMap[$id] = $component;
                return;
            }

            foreach ( $component as $key => $value )
            {
                if ( $key !== 'class' )
                    $this->component[$id]->$key = $value;
            }
        }
        elseif ( isset ( $this->componentsMap[$id]['class'], $component['class'] ) && $this->componentsMap[$id]['class'] !== $component['class'] )
        {
            $this->componentsMap[$id] = $component;
            return;
        }

        if ( isset ( $this->componentsMap[$id] ) && $merge )
            $this->componentsMap[$id] = ArrayTools::merge ( $this->componentsMap[$id], $component );
        else
            $this->componentsMap[$id] = $component;
    }

    public function getModuleClass ($module)
    {
        return ucfirst ( $module ) . '.' . ucfirst ( $module );
    }

    //private $modulePath;
    public function setModulePath ( $value )
    {
        $path = realpath ( Starter::getAliasPath ( "app" ) . DS . $value );
        if ( $path === false || !is_dir ( $path ) )
            throw new CmsException ( "The module path $value is not a valid directory." );

        Starter::setAliasPath ( 'modules', $path );
        //$this->modulePath = realpath ( $value );
    }

    public function getModulePath ()
    {
        $path = Starter::getAliasPath ( "modules" );
        if ( $path )
            return $path;
        else
        {
            $modulePath = $this->getBasePath () . DS . 'modules';
            Starter::setAliasPath ( 'modules', $modulePath );
            return $modulePath;
        }
    }

//    public function setControllerPath ( $value )
//	{
//        $path = realpath ( $value );
//		if ( $path === false || !is_dir ( $path ) )
//            throw new CmsException ( "The controllers path $value is not a valid directory." );
//
//        Starter::setAliasPath ( 'controllers', $path );
//    }
//
//    public function getControllerPath()
//	{
//        $path = Starter::getAliasPath ( "controllers" );
//        if ( $path )
//            return $path;
//        else
//        {
//            $controllerPath = $this->getBasePath () . DS . 'controllers';
//            Starter::setAliasPath ( 'controllers', $controllerPath );
//            return $controllerPath;
//        }
//    }

    protected function getActionParams ( $pathInfo )
    {
        if ( ( $pos = strpos ( $pathInfo, '/' ) ) !== false )
        {
            $manager = $this->getComponent ( 'urlManager' );
//            $manager->parsePathInfo ( ( string ) substr ( $pathInfo, $pos + 1 ) );
            $actionID = substr ( $pathInfo, 0, $pos );
            return $manager->caseSensitive ? $actionID : strtolower ( $actionID );
        }
        else
            return $pathInfo;
    }

    public function setDefaultController ( $controllerName )
    {
        $this->defaultController = $controllerName;
    }

    public function getDefaultController ()
    {
        return $this->defaultController;
    }

    public function setCurrentController ( $controller )
    {
        $this->currentController = $controller;
    }

    public function getCurrentController ()
    {
        return $this->currentController;
    }

    public function getControllerPath ()
    {
        if ( $this->controllerPath !== null )
            return $this->controllerPath;
        else
        {
            $this->controllerPath = $this->getBasePath () . DS . 'controllers';
            return $this->controllerPath;
        }
    }

    public function setControllerPath ( $path )
    {
        $this->controllerPath = $path;
    }

    public function setControllers ( $controllers )
    {
        $this->controllerMap = $controllers;
    }

    public function getControllers ()
    {
        return $this->controllerMap;
    }

    /* @todo -- Методы будущего класса CmsController. Это надо будет перенести ----------------------------------------*/
    private $title;
    public function setTitle ( $title )
    {
        $this->title = $title;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    private $template;
    public function setTemplate ( $template )
    {
        $this->template = $template;
    }

    public function getTemplate ()
    {
        return $this->template;
    }

    protected function render ( $template, $params = [], $return = true )
    {
        if ( $this->befoRerender () )
        {
            $content = $this->renderPart ( $template, $params );
            $output = $this->output ( $this->template, [ 'content' => $content, 'title' => $this->title ] );
            if ( $return)
                return $output;
            else
                echo $output;
        }
    }

    protected function renderPart ( $template, $params = [], $return = true )
    {
        $output = $this->output ( $template, $params );
        if ( $return )
            return $output;
        else
            echo $output;
    }

    private function output ( $template, $params )
    {
        $templateFile = $this->resolveTemplateFile ( $template );
        if ( file_exists ( $templateFile . ".tpl" ) )
        {
            $smarty = SmartyTools::getSmarty ();
            $smarty->assign ( "this", $this );
            foreach ( $params as $varName => $varvalue )
            {
                $smarty->assign ( $varName, $varvalue );
            }
            $output = $smarty->fetch ( $templateFile . ".tpl" );
        }
        else
        {
            extract ( $params );
            ob_start ();
            include $templateFile . EXT;
            $output = ob_get_contents ();
            ob_end_clean ();
        }
        return $output;
    }

    private function resolveTemplateFile ( $templateName, $moduleName )
    {
        $moduleName = get_class ( $this );
        $customCompPath = $moduleName ? "modules" . DS . $moduleName . DS : "";
        $templateRoot = TEMPL . DS . Starter::app ()->getTheme ();
        $templatePath = $templateRoot . DS . "templates" . DS . $customCompPath;
        $componentPath = $moduleName ? Starter::getAliasPath ( $moduleName ) . DS . "templates" . DS : "";

        if ( defined ( "MODE" ) && MODE == "Admin" )
            $templateFile = DOCROOT . DS . 'admin/tpls/' . $templateName;
        else if ( file_exists ( $templatePath . $templateName . ".tpl" ) || file_exists ( $templatePath . $templateName . EXT ) )
            $templateFile = $templatePath . $templateName;
        else if ( file_exists ( $componentPath . $templateName . ".tpl" ) || file_exists ( $componentPath . $templateName . EXT ) )
            $templateFile = $componentPath . $templateName;
        else
            throw new Exception ( "Не найден шаблон <code style='font-weight:bold'>" . $templateName . "</code>" );

        return $templateFile;
    }

    private $currentDoc;
    public function getCurrentDocument ()
    {
        if ( !$this->currentDoc )
        {
            $docByPath = Starter::app ()->urlManager->docsByPath;
            $this->currentDoc = end ( $docByPath );
        }
        return $this->currentDoc;
    }

    public function createAction ()
    {
        $requestUri = filter_input ( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );
        $request = strpos ( $requestUri, '?' ) !== false ? substr ( $requestUri, 0, strpos ( $requestUri, '?' ) ) : $requestUri;
        if ( !$this->currentDocument->id )
            $actionAlias = 'default';
        else
        {

            $mlink = trim ( str_replace ( Starter::app ()->content->getLinkById ( $this->currentDocument->id ), '', $request ) );
            $pathStack = array_filter ( explode ( '/', $mlink ) );
            $actionAlias = current ( $pathStack ) ? : 'default';
        }

        $actionMethod = $this->actions[$actionAlias]['method'];
        if ( !array_key_exists ( $actionAlias, $this->actions ) || !method_exists ( $this, $actionMethod )  )
            return null;

        $action = new CmsAction ( $actionMethod, $this );

        $data = [];
        $isValue = false;
        while ( $next = next ($pathStack))
        {
            if ( !$isValue )
            {
                $name = $next;
                $isValue = true;
            }
            else
            {
                $value = $next;
                $data[$name] = $value;
                $isValue = false;
            }
        }
        $action->data = $data;
        return $action;
    }

    public function beforeRender ()
    {
        Starter::app ()->headManager->seoSettings ( $this );
        return true;
    }

    private $actions;
    public function getActions ()
    {
        return $this->actions;
    }

    public function setActions ( $actions )
    {
        $this->actions = $actions;
    }

    private $currentModelName;
    public function getModel ()
    {
        return $this->currentModelName;
    }

    public function setModel ( $modelName )
    {
        $this->currentModelName = $modelName;
    }
//    public function startController ( $action )
//    {
//        return $this->$method();
//    }
    /*---- Методы будущего класса CmsController. Это надо будет перенести ----------------------------------------*/

    public function widget ( $className, $properties = [], $return = false )
    {
        $widget = Starter::app ()->widgetFactory->createWidget ( $this, $className, $properties );
        $output = $widget->render();
        if ( $return )
            return $output;
        else
            echo $output;
    }
}