<?php
/**
 * Description of TextBlockWidget
 *
 * @author kraser
 */
class TextBlockWidget extends CmsWidget
{
    private $table;
    public function __construct ( $parent, $params )
    {
        parent::__construct ( "TextBlock", $parent );
        $this->table = "blocks";
    }

    public function render ()
    {
        $block = $this->getBlock ();
        return TemplateEngine::view ( "widgets/textBlock", [ 'slides' => $slides ], null, true );
    }

    private function getBlock ()
    {

    }
}