<?php

/**
 * @todo поместить операции с содержимым из админ
 * @todo rename to ContentManager
 */
class SiteContent extends CmsComponent
{
    /**
     * <pre>Массив объектов SiteDoc индексированный по ID</pre>
     * @var Array Of SiteDoc
     */
    private $docs;

    /**
     * <pre>Массив объектов SiteDoc индексированный по ID</pre>
     * @var Array Of SiteDoc
     */
    private $byId;

    /**
     * <pre>Массив объектов SiteDoc индексированный по parentID</pre>
     * @var Array Of SiteDoc
     */
    private $byParent;

    /**
     * <pre>Массив объектов SiteDoc индексированный по module</pre>
     * @var Array Of SiteDoc
     */
    private $byModule;

    /**
     * <pre>Дерево документов сайта</pre>
     * @var Array Of SiteDoc
     */
    private $docTree;

    /**
     * <pre>Инициализирует массивы внутренних данных</pre>
     * @param String $alias
     * @param type $parent
     */
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );

        $this->docs = $this->find ( array ( "view" => "Y", "deleted" => "N" ) );
        $this->byId = ArrayTools::index ( $this->docs, "id" );
        foreach ( $this->byId as $doc )
        {
            $doc->docs = [];
            $this->byParent[$doc->parentId][$doc->id] = $doc;
            $this->byModule[$doc->module][$doc->id] = $doc;
            if ( $doc->parentId == 0 )
            {
                $doc->link = "/" . preg_replace ( '/\\/*/', '', $doc->nav );
                $this->docTree[$doc->id] = $doc;
            }
            else
            {
                $parent = $this->byId[$doc->parentId];
                $moreParent = $parent;
                $links = array();
                while ( $moreParent )
                {
                    array_unshift ( $links, $moreParent->nav );
                    $moreParent = $moreParent->parentId ? $this->byId[$moreParent->parentId] : null;
                }
                $doc->link = "/" . implode ( "/", $links ) . "/" . $doc->nav;
                $parent->docs[$doc->id] = $doc;
            }
        }
    }

    /**
     * <pre>Возвращает плоский список документов сайта</pre>
     * @return Array Of SiteDoc
     */
    public function getDocs ()
    {
        return $this->docs;
    }

    /**
     * <pre>Возвращает массивов SiteDoc, индексированный по parentID</pre>
     * @return Array Of SiteDoc
     */
    public function getDocsByParent ()
    {
        return $this->byParent;
    }

    /**
     * <pre>Возвращает массив объектов SiteDoc индексированный по module</pre>
     * @return Array
     */
    public function getDocsByModule ()
    {
        return $this->byModule;
    }

    private function find ( $params = null )
    {
        if ( !$params )
            return array ();

        $conditions = array ();
        $orderBy = array ();
        foreach ( $params as $field => $value )
        {
            if ( !$value )
                continue;

            switch ( $field )
            {
                case "id":
                    $conditions[] = "`id` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "parent":
                    $conditions[] = "`top` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "nav":
                    $conditions[] = "`nav` IN (" . ArrayTools::stringList ( $value ) . ")";
                    break;
                case "name":
                    $conditions[] = "`name` IN (" . ArrayTools::stringList ( $value ) . ")";
                    break;
                case "view":
                    $value = $value == "Y" ? "Y" : "N";
                    $conditions[] = "`show`='$value'";
                    break;
                case "deleted":
                    $value = $value == "Y" ? "Y" : "N";
                    $conditions[] = "`deleted`='$value'";
                    break;
            }
        }

        $whereClause = count ( $conditions ) ? " WHERE " . implode ( " AND ", $conditions ) : "";
        $query = "SELECT
                `id` AS id,
                `top` AS parentId,
                `order` AS 'order',
                `nav` AS nav,
                `name` AS title,
                `text` AS html,
                IF(`module`='','Content',`module`) AS module,
                `template` AS template,
                `showmenu` AS showMenu,
                `show` AS view,
                `deleted` AS deleted,
                `created` AS dateCreate,
                `modified` AS dateModify
            FROM `prefix_content`
            $whereClause ORDER BY `order` ASC";
        $docs = SqlTools::selectObjects ( $query, "SiteDoc" );

        return $docs;
    }

    /**
     * <pre>Возвращает ссылку по ID записи</pre>
     * @param Integer $id <p>ID записи</p>
     * @return String <p>Ссылка</p>
     */
    public function getLinkById ( $id )
    {
        if ( $id == 0 || !array_key_exists ( $id, $this->byId ) )
            return;

        $link = $this->getLinkById ( $this->byId[$id]->parent ) . '/' . $this->byId[$id]->nav;
        return $link;
    }

    function getLinkByModule ( $module )
    {
        $module = ArrayTools::head ( $this->byModule[$module] );
        $link = $this->getLinkById ( $module->id );
        return $link;
    }

    public function getSiteMap ()
    {
        $siteMap = array ();
        $skippedModules = array( "Basket" );
        foreach ( $this->docTree as $doc )
        {
            $clone = clone $doc;
            $clone->link = "/" . preg_replace ( '/\\/*/', "", $clone->nav );
            if ( $clone->module && $clone->module != "Content" )
            {
                if(  in_array ( $clone->module, $skippedModules ))
                    continue;
                $componentName = $clone->module;
                $methodName = "getModuleMap";
                if ( method_exists ( $componentName, $methodName ) )
                {
                    $reflectMethod = new ReflectionMethod ( $componentName, $methodName );
                    $moduleMap = $reflectMethod->invoke ( Starter::app ()->getModule ( $componentName ) );
                    $clone->docs = array_merge ( $clone->docs, $moduleMap );
                }
            }

            if ( !$clone->parentId )
                $siteMap[$clone->id] = $clone;
        }
        return $siteMap;
    }
}

class CmsSiteDoc
{
    public $id;
    public $parentId;
    public $order;
    public $nav;
    public $link;
    public $tagH1;
    public $title;
    public $view;
    public $deleted;

    public $docs;
    public $children;
}

class SiteDoc extends CmsSiteDoc
{
    public $brief;
    public $anons;
    public $html;
    public $module;
    public $template;
    public $showMenu;
    public $dateCreate;
    public $dateModify;
}

class SiteTree
{
    public $id;
    public $link;
    public $title;
    public $docs;

    public function __construct ()
    {
        $this->docs = array ();
    }
}
