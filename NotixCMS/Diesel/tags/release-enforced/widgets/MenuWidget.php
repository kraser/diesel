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

    public function render ()
    {
        return TemplateEngine::view ( "widgets/menu", [], null, true );
    }
}