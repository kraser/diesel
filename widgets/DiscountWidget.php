<?php
/**
 * Description of DiscountWidget
 *
 * @author kraser
 */
class DiscountWidget extends CmsWidget
{
    public function __construct ( $parent, $params )
    {
        parent::__construct ( "Discount", $parent );
    }

    public function render ()
    {
        $discount = Tools::getSettings ("Catalog", "discount", 0 );
        return TemplateEngine::view ( "widgets/discount", [ 'discount' => $discount ], null, true );
    }
}