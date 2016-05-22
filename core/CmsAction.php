<?php
/**
 * <pre>Класс CmsAction для универсального запуска контроллеров</pre>
 * @author kraser
 */
class CmsAction extends CmsComponent
{
    /**
     * @var CmsComponent <p>Контроллер</p>
     */
    private $owner;

    /**
     * @var String <p>ID (алиас) действия</p>
     */
    private $id;

    /**
     * @var Array <p>Параметры (аргументы) действия</p>
     */
    private $data;

    /**
     * <pre>Конструктор действия</pre>
     * @param String $actionId <p></p>
     * @param CmsComponent $owner <p>Владелец действия</p>
     */
    public function __construct ( $actionId, $owner )
    {
        parent::__construct ( $actionId, $owner );
        $this->owner = $owner;
        $this->id = $actionId;
    }

    /**
     * <pre>Запуска метода-действия контроллера</pre>
     * @return Mixed
     */
    public function run ()
    {
        //$methodName = "action" . ucfirst ( $this->id );
        return $this->owner->startController ( $this->id, $this->data );
    }

    /**
     * <pre>Устанавливает параметры запуска действия</pre>
     * @param Array $data <p>Параметры запуска действия</p>
     */
    public function setData ( $data )
    {
        $this->data = $data;
    }

    /**
     * <pre>Возвращает массив параметров запуска действия</pre>
     * @return Array
     */
    public function getData ()
    {
        return $this->data;
    }

}