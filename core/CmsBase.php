<?php

/**
 * Класс запуска приложения
 * Синглтон
 *
 */
class CmsBase
{
    /**
     * @var CmsBase экземпляр класса
     */
    private static $instance;

    /**
     * @var Array <p>Пул загруженных классов</p>
     */
    private static $classMap;

    /**
     * @var Array <p>Пул системных классов</p>
     */
    private static $core;

    /**
     * @var Boolean <p>Флаг включения в автозагрузку путей из include_path в php.ini</p>
     */
    private static $enableIncludePath = true;

    /**
     *
     * @var Array <p>Пути автозагрузки из include_path в php.ini</p>
     */
    private static $includePaths;

    /**
     * @var CmsApplication <p>Приложение</p>
     */
    private static $app;

    /**
     * <pre>Начальная инициализация CMS</pre>
     */
    private function __construct ()
    {
        self::setAliasPath ( "core", CORE );
        self::setAliasPath ( "tools", TOOLS );
        self::import ( "core.*" );
        self::import ( "tools.*" );

        $this->detectLanguage ();
        setlocale ( LC_ALL, _LOCALE . "." . _CHARSET );
        setlocale ( LC_NUMERIC, "C" );
    }

    /**
     * <pre>Если инициализирован возвращает объект, иначе инициализирует и возвращает</pre>
     * @return CmsBase
     */
    public static function &getInstance ()
    {
        if ( self::$instance === null )
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * <pre>Базовый метод запуска web-приложения</pre>
     */
    public static function Run ()
    {
        try
        {
            self::getInstance ();
            $config = require DOCROOT . DS . "config" . DS ."main.php";
            $app = self::createApplication ( $config );
            self::$app->Run ();
        }
        catch ( Exception $e )
        {
            if ( $e->getCode () == 404 )
                page404 ();
            else
            {
                echo $e->getFile () . ":" . $e->getLine () . " - " . $e->getMessage () . "<br>";
                foreach ( $e->getTrace() as $step => $trace )
                {
                    echo "#$step " . $trace['file'] . ":" . $trace['line'] ."<br>";
                }
                if ( $e->getCode () && ($e->getCode () == 1102 || $e->getCode () == 1146) )
                {
                    echo "<a href='/install/'>Необходима инсталляция $mode </a>";
                    //Обработка исключения
                }
            }
        }
    }

    /**
     * <pre>Определение языковых предпочтений клиента
     * для выбора соответствующих файлов сообщений</pre>
     * @todo реализовать
     *
     */
    private function detectLanguage ()
    {
        if ( !defined ( '_LANG' ) )
            define ( '_LANG', "ru" );

        if ( !defined ( '_LOCALE' ) )
            define ( '_LOCALE', "ru_RU" );
    }

    /**
     * <pre>Создание приложения</pre>
     * @param Array $config <p>Конфигурация</p>
     */
    public static function createApplication ( $config )
    {
        if ( defined ( "MODE" ) )
            $className = MODE;
        else if ( 'cli' == PHP_SAPI )
            $className = "Console";
        else
            $className = "Site";

        $rc = new ReflectionClass ( $className );
        $application = $rc->newInstance ( $className, $config );
        self::$app = $application;
        $application->init ();
    }

    /**
     * <pre>Создание произвольного компонента</pre>
     * @param Array $config <p>Массив конфигурации компонента</p>
     * @param String $componentId <p>Алиас компонента</p>
     * @param CmsComponent $owner <p>Объект владелец инициализируемого компонента</p>
     * @return CmsComponent
     * @throws CmsException
     */
    public static function createComponent ( $config, $componentId = null, $owner = null )
    {
        if ( is_string ( $config ) )
        {
            $className = $config;
            $config = [];
        }
        elseif ( isset ( $config['class'] ) )
        {
            $className = $config['class'];
            unset ( $config['class'] );
        }
        else
            throw new CmsException("Неверная конфигурация объекта");

        if ( !class_exists ( $className, false ) )
            $class = Starter::import ( $className, true );

        $rc = new ReflectionClass ( $class );
//        $fnArgs = func_get_args();
//        unset ( $fnArgs[0] );
        $alias = $componentId ? : $class;
        $object = $rc->newInstance ( $alias, $owner );

        foreach ( $config as $param => $value )
        {
			$object->$param = $value;
        }

		return $object;
    }

    /**
     * <pre>Метод автозагрузки классов</pre>
     * @param String $className <p>Имя загружаемого класса</p>
     * @return Boolean true если класс найден и подгружен, в противном случае false
     */
    public static function autoload ( $className )
	{
		if ( isset ( self::$classMap[$className] ) )
            include(self::$classMap[$className]);
        elseif ( isset ( self::$core[$className] ) )
            include( DOCROOT . self::$core[$className]);
        else
        {
            if ( strpos ( $className, '\\' ) === false )
            {
                if ( self::$enableIncludePath === false )
                {
                    foreach ( self::$includePaths as $path )
                    {
                        $classFile = $path . DIRECTORY_SEPARATOR . $className . '.php';
                        if ( is_file ( $classFile ) )
                        {
                            include ( $classFile );
                            break;
                        }
                    }
                }
                else
                    include($className . '.php');
            }
            else
            {
                $namespace = str_replace ( '\\', '.', ltrim ( $className, '\\' ) );
                if ( ($path = self::getPathOfAlias ( $namespace )) !== false )
                    include($path . '.php');
                else
                    return false;
            }
            return class_exists ( $className, false ) || interface_exists ( $className, false );
        }
        return true;
    }

    private static $aliases;
    /**
     * <pre>Устанавливает псевдоним для пути</pre>
     * @param String $alias <p>Алиас пути</p>
     * @param String $path <p>Путь</p>
     */
    public static function setAliasPath ( $alias, $path )
    {
        if ( empty ( $path ) )
            unset ( self::$aliases[$alias] );
        else
            self::$aliases[$alias] = rtrim ( $path, '\\/' );
    }

    /**
     * <pre>Возвращает путь по алиасу</pre>
     * @param String $alias <p>Алиас для которого запрашивается путь</p>
     * @return String/Boolean
     */
    public static function getAliasPath ( $alias )
    {
		if ( isset ( self::$aliases[$alias] ) )
            return self::$aliases[$alias];
        elseif ( ( $pos = strpos ( $alias, '.' ) ) !== false )
        {
            $rootAlias = substr ( $alias, 0, $pos );
            if ( isset ( self::$aliases[$rootAlias] ) )
                return self::$aliases[$alias] = rtrim ( self::$aliases[$rootAlias] . DS . str_replace ( '.', DS, substr ( $alias, $pos + 1 ) ), '*' . DS );
            elseif ( self::$app instanceof CWebApplication )
            {
                if ( self::$app->findModule ( $rootAlias ) !== null )
                    return self::getAliasPath ( $alias );
            }
        }
        return false;
    }

    /**
     * @var Array <p>Пул быстрого поиска классов</p>
     */
    private static $imports;
    /**
     * <pre>Помещает в пул класс</pre>
     * @param String $alias <p>Алиас</p>
     * @param Boolean $forceInclude <p>Флаг немедленной загрузки класса</p>
     * @return String
     * @throws CmsException
     */
    public static function import ( $alias, $forceInclude = false )
    {
        if ( isset ( self::$imports[$alias] ) )
            return self::$imports[$alias];

        if ( class_exists ( $alias, false ) || interface_exists ( $alias, false ) )
            return self::$imports[$alias] = $alias;

        if ( ($pos = strrpos ( $alias, '\\' )) !== false )
        {
            $namespace = str_replace ( '\\', '.', ltrim ( substr ( $alias, 0, $pos ), '\\' ) );
            if ( ($path = self::getAliasPath ( $namespace )) !== false )
            {
                $classFile = $path . DS . substr ( $alias, $pos + 1 ) . '.php';
                if ( $forceInclude )
                {
                    if ( is_file ( $classFile ) )
                        require ( $classFile );
                    else
                        throw new CmsException ( "Алиас $alias недействителен. Убедитесь, что файл существует и доступен для чтения." );
                    self::$imports[$alias] = $alias;
                }
                else
                    self::$classMap[$alias] = $classFile;
                return $alias;
            }
            else
            {
                if ( class_exists ( $alias, true ) )
                    return self::$imports[$alias] = $alias;
                else
                    throw new CmsException ( "Алиас $alias недействителен. Убедитесь, что файл существует и доступен для чтения." );
            }
        }

        if ( ($pos = strrpos ( $alias, '.' )) === false )
        {
            if ( $forceInclude && self::autoload ( $alias ) )
                self::$imports[$alias] = $alias;
            return $alias;
        }

        $className = ( string ) substr ( $alias, $pos + 1 );
        $isClass = $className !== '*';

        if ( $isClass && (class_exists ( $className, false ) || interface_exists ( $className, false )) )
            return self::$imports[$alias] = $className;

        if ( ($path = self::getAliasPath ( $alias )) !== false )
        {
            if ( $isClass )
            {
                if ( $forceInclude )
                {
                    if ( is_file ( $path . '.php' ) )
                        require ( $path . '.php' );
                    else
                        throw new CmsException ( "Алиас $alias недействителен. Убедитесь, что файл существует и доступен для чтения." );
                    self::$imports[$alias] = $className;
                }
                else
                    self::$classMap[$className] = $path . '.php';
                return $className;
            }
            else
            {
                if ( self::$includePaths === null )
                {
                    self::$includePaths = array_unique ( explode ( PATH_SEPARATOR, get_include_path () ) );
                    if ( ($pos = array_search ( '.', self::$includePaths, true )) !== false )
                        unset ( self::$includePaths[$pos] );
                }

                array_unshift ( self::$includePaths, $path );

                if ( self::$enableIncludePath && set_include_path ( '.' . PATH_SEPARATOR . implode ( PATH_SEPARATOR, self::$includePaths ) ) === false )
                    self::$enableIncludePath = false;

                return self::$imports[$alias] = $path;
            }
        }
        else
            throw new CmsException ( "Алиас $alias недействителен. Убедитесь, что файл или директория существует." );
    }

    /**
     * <pre>Возвращает рабочее приложение</pre>
     * @return CmsApplication <p>Приложение</p>
     */
    public static function app ()
    {
        return self::$app;
    }
}

spl_autoload_register ( array ( "CmsBase",  "autoload" ) );
require CORE . '/classes.php';
