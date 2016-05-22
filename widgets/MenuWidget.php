<?php
/**
 * Description of MenuWidget
 *
 * @author kraser
 */
class MenuWidget extends CmsWidget
{
    public function __construct ( $parent, $params )
    {
        parent::__construct ( "Menu", $parent );
    }

    public function render ()
    {
        //$sitemap = Starter::app ()->content->getSiteMap ();
        return TemplateEngine::view ( "widgets/menu", [], null, true );
    }
}