<?php
/**
 * Description of GalleryWidget
 *
 * @author kraser
 */
class GalleryWidget extends CmsWidget
{
    private $template;
    private $type;
    public function __construct ( $parent )
    {
        parent::__construct ( "Gallery", $parent );
        $this->template = 'gallery';
        $this->type = 'single';
    }

    public function render ()
    {
        $gallery = $this->getGallery ();

        return TemplateEngine::view ( "widgets/$this->template", [ 'gallery' => $gallery ], null, true );
    }

    private function getGallery ()
    {
        if ( !$this->galleryAlias )
            return null;

        if ( $this->type === 'tab' )
            $query = "SELECT * FROM `prefix_gallery` WHERE `alias` LIKE '$this->galleryAlias%' AND `show`='Y' AND `deleted`='N'";
        else
            $query = "SELECT * FROM `prefix_gallery` WHERE `alias`='$this->galleryAlias' AND `show`='Y' AND `deleted`='N'";

        $galleries = SqlTools::selectObjects ( $query );

        if ( !count ( $galleries ) )
            return null;
        foreach ( $galleries as $gallery )
        {
            $gallery->images = Starter::app ()->imager->getImages ( "Gallery", $gallery->id );
        }
        if ( $this->type === 'single')
            return ArrayTools::head ( $galleries );
        else
            return $galleries;
    }

    private $galleryAlias;
    public function setGalleryAlias ( $alias )
    {
        $this->galleryAlias = $alias;
    }

    public function setTemplate ( $template )
    {
        $this->template = $template;
    }

    public function setType ( $type )
    {
        $this->type = $type;
    }
}