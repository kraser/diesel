<?php
/**
 * Description of CmsApplication
 *
 * @author kraser
 */
class CmsApplication extends CmsModule
{
    public $data;

    private $config;

    public function __construct ( $alias, $config )
    {
        parent::__construct ( $alias, null );
        $this->defaultController = "Content";
        if(isset($config['basePath']))
		{
			$this->setBasePath ( $config['basePath'] );
			unset ( $config['basePath'] );
		}
		else
			$this->setBasePath ( 'cms' );

        $aliases =
        [
            'app' => $this->getBasePath (),
            'webroot' => DOCROOT,
            'cache' => DOCROOT .DS . 'cache',
            "libs" => LIBS,
            //'modules' => DOCROOT . DS . "modules",
            "components" => $this->getBasePath () . DS . "_comps",
            "controllers" => DOCROOT . DS . 'controllers',
            'widgets'=> DOCROOT . DS . 'widgets',
            'behaviors' => DOCROOT . DS . "behaviors",
            'site' => DOCROOT . DS . "theme"
        ];
		if ( isset ( $config['aliases'] ) )
        {
            $aliases = ArrayTools::merge ( $aliases, $config['aliases'] );
            unset ( $config['aliases'] );
        }
        $this->setAliases ( $aliases );

        $imports =
        [
            "libs.*",
            "controllers.*",
            "widgets.*",
            "behaviors.*",
        ];
        if ( isset ( $config['imports'] ) )
        {
            $aliases = ArrayTools::merge ( $imports, $config['imports'] );
            unset ( $config['imports'] );
        }
        $config['components'] =
        [
            'installer' => 'Install',
            'urlManager' => "UriAnalizer",
            'xlsReader' => 'components.XlsParser.XlsParser',
            "headManager" => 'components.Header',
            'content' => 'components.SiteContent',
            'imager' => "components.Imager",
            'widgetFactory' => "components.WidgetFactory",
            'session' =>
            [
                'class' => "components.SessionManager",
                'sessionName' => "CMS_SESSION",
                'lifeTime' => 36000
            ]
        ];
//        $config['controllers'] =
//        [
//            'Content' => 'controllers.Content'
//        ];
        $this->setImport ( $imports );
        $this->data = new SimpleData ();
        $this->configure ( $config );
        FileTools::init ();
    }

    public function init ()
    {
        parent::init ();
        UserIdentity::init ();
    }

    public function runController ( $route )
    {
        if ( ( $ca = $this->createController ( $route ) ) !== null )
        {
            list ( $controller, $actionId ) = $ca;
            $oldController = $this->currentController;
            $this->currentController = $controller;
            $controller->init ();
            $controller->Run ( $actionId );
            $this->currentController = $oldController;
        }
        else
            throw new CmsException ( 404, "Unable to resolve the request $route" );
    }

    public function createController ( $route, $owner = null )
    {
        if ( $owner === null )
            $owner = $this;
        if ( ($route = trim ( $route, '/' )) === '' )
            $route = $owner->defaultController;
        $caseSensitive = $this->getComponent('url')->caseSensitive;

        $route.='/';
        while ( ($pos = strpos ( $route, '/' )) !== false )
        {
            $id = substr ( $route, 0, $pos );
            if ( !preg_match ( '/^\w+$/', $id ) )
                return null;
            if ( !$caseSensitive )
                $id = strtolower ( $id );
            $route = ( string ) substr ( $route, $pos + 1 );
            if ( !isset ( $basePath ) )
            {
                if ( isset ( $owner->controllerMap[$id] ) )
                {
                    return
                    [
                        $controller = Starter::createComponent ( $owner->controllerMap[$id], $owner ),
                        $this->getActionParams ( $route ),
                    ];
                }

                if ( ( $module = $owner->getModule ( $id ) ) !== null )
                    return $this->createController ( $route, $module );

                $basePath = $owner->getControllerPath ();
                $controllerID = '';
            }
            else
                $controllerID.='/';
            $className = ucfirst ( $id ) . 'Controller';
            $classFile = $basePath . DS . $className . '.php';

//            if ( $owner->controllerNamespace !== null )
//                $className = $owner->controllerNamespace . '\\' . $className;

            if ( is_file ( $classFile ) )
            {
                if ( !class_exists ( $className, false ) )
                    require ( $classFile );
                if ( class_exists ( $className, false ) && is_subclass_of ( $className, 'CmsController' ) )
                {
                    $id[0] = strtolower ( $id[0] );
                    return array (
                        new $className ( $controllerID . $id, $owner === $this ? null : $owner ),
                        $this->getActionParams ( $route ),
                    );
                }
                return null;
            }
            $controllerID .= $id;
            $basePath .= DS . $id;
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    private $theme;
    public function setTheme ( $theme )
    {
        $this->theme = $theme;
    }

    public function getTheme ()
    {
        return $this->theme;
    }

    private $adminMail;
    public function setAdminMail ( $mail )
    {
        $this->adminMail = $mail;
    }

    public function getAdminMail ()
    {
        return $this->adminMail;
    }

    public function setParameter ( $parameter, $value )
    {
        $this->$parameter = $value;
    }

    public function getUrlManager ()
    {
        return $this->getComponent ( "urlManager" );
    }

    public function getHeadManager ()
    {
        return $this->getComponent ( "headManager" );
    }

    public function getContent ()
    {
        return $this->getComponent ( "content" );
    }

    public function getImager ()
    {
        return $this->getComponent ( "imager" );
    }

    public function getWidgetfactory ()
    {
        return $this->getComponent ( "widgetFactory" );
    }

    public function getSession ()
    {
        return $this->getComponent ( "session" );
    }
}
