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
        parent::__construct ( "Breadcrumbs", $parent );
        $this->separator = " | ";
    }

    public function run ()
    {
        $breadCrumbs = $this->buildCrumbs();
        return $this->renderPart ( "widgets/breadcrumbs", [ 'breadcrumbs' => $breadCrumbs ] );
    }

    private function buildCrumbs ()
    {
        $docsByPath = Starter::app ()->urlManager->docsByPath;
        $crumbs = [];
        $mainPage = false;
        if ( !empty ( $docsByPath ) )
        {
            foreach ( $docsByPath as $crumb )
            {
                $link = Starter::app ()->content->getLinkById ( $crumb->id );
                $mainPage = $mainPage || $link == "/";
                $crumbs[] = (object) [ 'title' => $crumb->title, 'link' => $link, 'id' => $crumb->id, 'active' => false ];
            }
        }
        if ( !$mainPage )
            array_unshift ( $crumbs, (object) [ 'title' => 'Главная', 'link' => '/', 'id' => '0', 'active' => false ] );

        $last = end ( $docsByPath );
        if ( $last->module !== "Content" )
        {
            //array_pop ( $crumbs );
            if ( class_exists ( $last->module ) )
            {
                $module = Starter::app ()->getModule ( $last->module );
                $crumbs = array_merge ( $crumbs, $module->breadcrumbs );
            }
        }

        $crumbs = array_filter ( $crumbs );
        $crumbs[count ( $crumbs ) - 1]->active = true;

        return $crumbs;
    }

    private $separator;
    public function getSeparator ()
    {
        return $this->separator;
    }

    public function setSeparator  ( $separator )
    {
        $this->separator = $separator;
    }
}