<?php
/**
 * <pre>Класс CmsComponent - базовый класс построения компонентов</pre>
 * @author kraser
 */
class CmsComponent extends CmsObject
{
    /**
     * @var String <p>Алиас компонента</p>
     */
    private $alias;

    /**
     * @var CmsComponent <p>Компонент-владелец</p>
     */
    private $parent;

    /**
     * @var String <p>Путь к классу объекта</p>
     */
    private $basePath;

    /**
     * @var Boolean <p>Флаг инициализации компонента</p>
     */
    private $isInit = false;

    /**
     * <pre>Конструктор</pre>
     * @param String $alias <p>Алиас компонента</p>
     * @param CmsComponent $parent <p>Компонент-владелец</p>
     */
    public function __construct ( $alias, $parent )
    {
        parent::__construct ();
        $this->alias = $alias;
        $this->parent = $parent;
    }

    /**
     * <pre>Инициализация компонента</pre>
     */
    public function init()
    {
        $this->isInit = true;
    }

    /**
     * <pre>Возвращает алиас компонента</pre>
     * @return String
     */
    public function getAlias ()
	{
		return $this->alias;
	}

    /**
     * <pre>Устанавливает алиас компонента</pre>
     * @param String $alias
     */
	public function setAlias ( $alias )
    {
        $this->alias = $alias;
    }

    /**
     * <pre>Устанавливает путь к классу компонента</pre>
     * @param String $path
     */
    public function setBasePath ($path)
    {
        $this->basePath = $path;
    }

    /**
     * <pre>Возвращает путь к классу компонента</pre>
     * @return String
     */
    public function getBasePath()
	{
		if ( $this->basePath === null )
		{
			$class = new ReflectionClass ( get_class ( $this ) );
			$this->basePath = dirname ( $class->getFileName () );
		}
		return $this->basePath;
	}

    /**
     * <pre>Возвращает значение флага инициализации</pre>
     * @return Boolean
     */
    public function getIsInit()
    {
        return $this->isInit;
    }

    /**
     * <pre>Возвращает владельца компонента</pre>
     * @return CmsComponent
     */
    public function getParent ()
    {
        return $this->parent;
    }

    /**
     * <pre>Устанавливает владельца компонента</pre>
     * @param CmsComponent $module <p>Владелец компонента</p>
     */
    public function setParent ( $module )
    {
        $this->parent = $module;
    }


}