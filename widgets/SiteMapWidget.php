<?php
/**
 * Description of SiteMapWidget
 *
 * @author kraser
 */
class SiteMapWidget extends CmsWidget
{
    public function __construct ( $parent )
    {
        parent::__construct ( "SiteMap", $parent );
    }

    public function render ()
    {
        $sitemap = Starter::app ()->content->getSiteMap ();
        return TemplateEngine::view ( "widgets/siteMap", [ 'map' => $sitemap ], null, true );
    }
}