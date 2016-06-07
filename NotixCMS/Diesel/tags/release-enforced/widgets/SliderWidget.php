<?php

/**
 *
 * @author knn
 */
class SliderWidget extends CmsWidget
{
    private $table;
    public function __construct ( $parent )
    {
        parent::__construct ( "Slider", $parent );
        $this->table = "slider";
    }

    public function render ()
    {
        $slides = $this->getSlideList ();
        return TemplateEngine::view ( "widgets/slider", [ 'slides' => $slides ], null, true );
    }

    private function getSlideList ()
    {
        $query = "SELECT s.*
            FROM `prefix_" . $this->table . "` AS s WHERE s.`deleted`='N' AND s.`show`='Y' ORDER BY s.`order` ASC";
        $slideList = SqlTools::selectObjects ( $query );
        $slideIds = ArrayTools::pluck ( $slideList, "id" );
        $images = Starter::app ()->imager->getMainImages ( "Slider", $slideIds );
        foreach ( $slideList as $slide )
        {
            $slide->image = $images[$slide->id]['src'];
        }
        return $slideList;
    }
}
