<?php

/**
 * Анализатор URL
 */
class UriAnalizer extends CmsComponent
{
    private $url;

    /**
     * <pre>URL разобранный в массив на состаыне части
     * <b>scheme</b> (http/ftp), <b>host</b>, <b>port</b>, <b>user</b>, <b>pass</b>, <b>path</b>,
     * <b>query</b> (параметры после "?"), <b>fragment</b> (после "#")</pre>
     * @var Array
     */
    private $urlParts;

    /**
     * <pre>Массив из элементов url[path]</pre>
     * @var Array
     */
    private $uriPath;

    /**
     * <pre>Массив из элементов url[query]</pre>
     * @var Array
     */
    private $queries;

    /**
     * Флаг наличия path в uri
     * @var Boolean url запрос содержит path - true, иначе false
     */
    private $isMatchedUri;

    /**
     * массив контета сайта по частям uri
     * @deprecated since version 2.00.00
     * @var Array
     */
    private $docsByPath;
    private $linkPath;

    /**
     * Конструктор
     */
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $this->url = filter_input ( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
        $this->docsByPath = [];
        $this->linkPath = [];
        $siteContent = Starter::app ()->content;
        $matchesUri = [];

        $this->urlParts = parse_url ( $this->url );
        if ( !empty ( $this->urlParts['query'] ) )
        {
            $queries = array ();
            $queryParams = explode ( "&", $this->urlParts['query'] );
            foreach ( $queryParams as $param )
            {
                list($name, $value) = explode ( "=", $param );
                $queries[$name] = $value;
            }
            $this->queries = $queries;
        }
        else
            $this->queries = array ();

        $this->isMatchedUri = false;
        preg_match ( '/\/([^?#]+)?/', $_SERVER['REQUEST_URI'], $matchesUri );
        if ( isset ( $matchesUri[1] ) )
        {
            $this->isMatchedUri = true;
            $this->uriPath = array_filter ( explode ( '/', $matchesUri[1] ) );
            //$this->_urlParts = $this->uriPath;
        }
    }

    /**
     * Возвращает значение флага isMatchedUri
     * @return Boolean значение флага isMatchedUri
     */
    public function isMatchedUri ()
    {
        return $this->isMatchedUri;
    }

    /**
     * <pre>Возвращает часть URL-запроса</pre>
     * @param String $partName <p>Требуемая часть запроса</p>
     * @return String
     */
    public function getUrlPart ( $partName )
    {
        if ( array_key_exists ( $partName, $this->urlParts ) )
            $urlPart = $this->urlParts[$partName];
        else
            $urlPart = "";

        return $urlPart;
    }

    public function getCurrentUrl()
    {
        return $this->url;
    }


    /**
     * Возвращает имя компонента для обработки ajax запроса
     * @return String
     */
//    public function getUriComponent ()
//    {
//        return ucfirst ( $this->uriPath[0] );
//    }

    /**
     * Возвращает имя метода компонента для обработки ajax запроса
     * @return String
     */
//    public function getUriMethod ()
//    {
//        return $this->uriPath[1];
//    }

    /**
     * Возвращает массив контета сайта по частям uri
     * @return Array
     */
    public function getDocsByPath ()
    {
        return $this->docsByPath;
    }

//
//
//    /**
//     * <pre>Возвращает головное начальное поле (часть) URL-запроса
//     * и выталкивает его из массива частей <b>_urlParts</b></pre>
//     * @param type $partName
//     * @return null
//     */
//    public function pullUrlPart ()
//    {
//        if ( count ( $this->_urlParts ) )
//            return array_shift ( $this->_urlParts );
//        else
//            return null;
//    }
//
//    /**
//     * <pre>Возвращает головное начальное поле (часть) URL-запроса
//     * без сдвига массива частей <b>_urlParts</b></pre>
//     * @param type $partName
//     * @return null
//     */
//    public function pullUnshiftUrlPart ()
//    {
//        if ( count ( $this->_urlParts ) )
//            return $this->_urlParts[0];
//        else
//            return null;
//    }
//
    /**
     * Возвращает остаток  URL
     */
    public function getUriParts ()
    {
        return $this->uriPath;
    }

    public function getRequestParameter ( $name, $default )
    {
        return array_key_exists ( $name, $this->queries ) ? $this->queries[$name] : $default;
    }

    public function createRequestParameters ( $param = [], $not_complete = false )
    {
        $get = $this->queries;

        if ( !empty ( $param ) )
        {
            foreach ( $get as $k => $v )
            {
                if ( isset ( $param[$k] ) )
                {
                    //unset
                    if ( $param[$k] === false )
                    {
                        unset ( $get[$k] );
                        continue;
                    }

                    $get[$k] = $param[$k];
                    unset ( $param[$k] );
                }
            }

            //clean up params
            foreach ( $param as $k => $v )
            {
                if ( !$v )
                    unset ( $param[$k] );
            }

            $get = array_merge ( $get, $param );
        }

        $query = http_build_query ( $get, '', '&amp;' );
        return ( $not_complete || !$query ? '' : '?' ) . $query;
    }

    public function getParameter ( $name, $default )
    {
        $value = $this->getRequestParameter ( $name );
        if ( !$value )
            $value = array_key_exists ( $name, $_POST ) ? $_POST[$name] : $default;

        return $value;
    }

    /**
     * <pre>Определение основного модуля и составление карты пути docsByPath</pre>
     *
     * @param Integer $urinum <p>Текущий порядковый номер элемента uri (based 0)</p>
     * @param Integer $parent <p>Предыдущий порядковый номер элемента uri (при рекурсивном вызове)</p>
     */
    public function getRoute ( $urinum = 0, $parent = 0 )
    {
        if ( $urinum > ( count ( $this->uriPath ) - 1 ) || !$this->isMatchedUri )
            return 'Content';

        $contents = Starter::app ()->content;
        $docsByParent = $contents->docsByParent;
        if ( isset ( $docsByParent[$parent] ) )
        {
            foreach ( $docsByParent[$parent] as $id => $doc )
            {
                $navPath = explode ( "/", $doc->nav . "/" );
                if ( $navPath[0] == $this->uriPath[$urinum] )
                {
                    $this->docsByPath[] = $doc;
                    $this->linkPath[] = $doc->nav;

                    //Обнаружен модуль, дальше не нужно проверять URI
                    if ( !empty ( $doc->module ) && $doc->module !== "Content" )
                        return $doc->module;

                    $subModule = $this->getRoute ( $urinum + 1, $id );
                    if ( $subModule === false )
                        return $doc->module;
                    else
                        return $subModule;
                }
            }
            return false;
        }
        return false;
    }

    public function getLinkPath ()
    {
        return $this->linkPath;
    }

}
