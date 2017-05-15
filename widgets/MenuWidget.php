<?php
/**
 * Description of MenuWidget
 *
 * @author kraser
 */
class MenuWidget extends CmsWidget
{
    public function __construct ( $parent )
    {
        parent::__construct ( "Menu", $parent );
    }

    public function run ()
    {
        $template = "menu" . $this->location;
        return $this->renderPart ( "widgets/$template", [ 'root' => $this->createMenu () ], null, true );
    }

    private function createMenu ()
    {
        $query = "
            SELECT
                id AS id,
                parentId AS parentId,
                alias AS alias,
                link AS link,
                title AS title,
                module AS module,
                root AS root
            FROM `prefix_menu`
            WHERE `show`='Y' AND `deleted`='N'
            ORDER BY `order` ASC";
        $routes = SqlTools::selectObjects ( $query, null, "id" );
        $roots = [];

        foreach ( $routes as $route )
        {
            if ( empty ( $route->menu ) )
                $route->menu = [];

            if ( $route->parentId == 0 )
                $roots[$route->id] = $route;
            else
            {
                $parent = $routes[$route->parentId];
                $parent->menu[$route->id] = $route;
            }

        }
        $root = ArrayTools::head ( ArrayTools::select ( $roots, "alias", $this->location ) );
        return $root;
    }

    private $location;
    public function setLocation ( $location )
    {
        $this->location = ucfirst ( $location );
    }
}