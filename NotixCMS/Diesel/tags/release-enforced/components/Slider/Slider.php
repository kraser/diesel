<?php

/**
 *
 * @author knn
 */
class Slider extends CmsModule
{
    private $data;
    public $table = 'slider';

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
    }

    public function Run ()
    {
        $select = '';
        $join = '';
        $where = '';
//        if(_REGION !== null)
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (s.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//            . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $sql = "SELECT s.*" . $select
        . " FROM `prefix_" . $this->table . "` AS s"
        . $join
        . " WHERE s.`deleted` = 'N' AND s.`show` = 'Y'" . $where
        . "ORDER BY s.`date` DESC";
        $slides = SqlTools::selectObjects($sql);
        $slideList = array ();
        foreach ( $slides as $slide )
        {
            $imageSource = DS . SITE . DS . Starter::app ()->theme . "/images/img1.png"; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $images = $this->data->GetData ( 'images', " AND `module_id`=$slide->id AND `main`='Y' AND `module`='Slider'" );
            if ( $images )
            {
                foreach ( $images as $image )
                {
                    $imageSource = $image['src'];
                    //берём только первую
                    break;
                }
            }
            // в поле text вводятся строки, разделённые переносом строки ("\n"),
            // преобразуем их в массив для дальнейшего окружения тегами <li> в шаблоне
            $slideList[] = array
            (
                'name' => $slide->name,
                'text' => $slide->text,
                'image' => $imageSource,
                'link' => isset ( $slide->link ) ? $slide->link : '',
            );
        }

        return TemplateEngine::view ( 'sliderblock', array ( 'slideList' => $slideList ), "Slider" );
    }
}
