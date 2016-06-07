<?php
/**
 * Description of BreadCrumbsWidget
 *
 * @author kraser
 */
class BreadCrumbsWidget extends CmsWidget
{
    public function __construct ( $parent )
    {
        parent::__construct ( "Menu", $parent );
    }

    public function render ()
    {
        $breadCrumbs = $this->getBreadCrumbs();
        return TemplateEngine::view ( "widgets/breadcrumbs", [ 'breadcrumbs' => $breadCrumbs ], null, true );
    }

    private function getBreadCrumbs ()
    {
        $docsByPath = Starter::app ()->urlManager->docsByPath;
        if ( empty ( $docsByPath ) )
            return [];

        $crumbs = [];
        foreach ( $docsByPath as $crumb )
        {
            $link = Starter::app ()->content->getLinkById ( $crumb->id );
            $mainPage = $mainPage || $link == "/";
            $crumbs[] = (object) [ 'title' => $crumb->title, 'link' => $link, 'id' => $crumb->id, 'active' => false ];
        }
        if ( !$mainPage )
            array_unshift ( $crumb, (object) [ 'name' => 'Главная', 'link' => '/', 'id' => '0', 'active' => false ] );

        $last = end ( $docsByPath );
        if ( $last->module !== "Content" )
        {
            if ( class_exists ( $last->module ) )
            {
                $module = Starter::app ()->getModule ( $last->module );
                if ( method_exists ( $module, 'breadCrumbs' ) )
                {
                    $moduleCrumbs = $module->breadCrumbs ();
                    $crumbs = array_merge ( $crumbs, $moduleCrumbs );
                }
            }
        }

        $crumbs = array_filter ( $crumbs );
        $crumbs[count ( $crumbs ) - 1]->active = true;

        return $crumbs;
    }
}