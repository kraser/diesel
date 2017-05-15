<?php
/**
 * Description of CategoryListWidget
 *
 * @author kraser
 */
class CategoryListWidget extends CmsWidget
{
    public function __construct ( $parent )
    {
        parent::__construct ( "CategoryList", $parent );
        Starter::import ( "modules.Catalog.models.*" );
    }

    public function run ()
    {
        $manager = new CategoryModel ( $this );
        $categories = $manager->getTree ();
        $template = "categories";
        return $this->renderPart ( "widgets/$template", [ 'categories' => $categories ] );
    }
}