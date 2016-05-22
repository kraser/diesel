<?php
/**
 * <pre>Класс CmsObject - базовый класс для всех объектов CMS</pre>
 * @author kraser
 */
class CmsObject
{
    /**
     * @var Array <p>Ассоциативный массив произвольных параметров</p>
     */
    private $paramSet;
    /**
     * <pre>Конструктор</pre>
     */
    public function __construct ()
    {
        $this->paramSet = [];
    }

    public function __set ( $name, $value )
    {
        $method = "set" . ucwords ( $name );
        if ( method_exists ( $this, $method ) )
            $this->$method ($value);
        else
            $this->paramSet[$name] = $value;
    }

    public function __get ( $name )
    {
        $method = "get" . ucwords ( $name );
        if ( method_exists ( $this, $method ) )
            return $this->$method ();
        else if ( array_key_exists ($name, $this->paramSet ) )
            return $this->paramSet[$name];
        else
            return null;//CmsException
    }
}