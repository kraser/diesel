<?php

/**
 *
 * @author knn
 */
class Slider extends Component
{
    private $data;
    public $table = 'slider';

    function __construct ()
    {
        $this->data = Starter::app ()->data;
    }

    public function Run ()
    {
        //$slides = $this->data->GetData(self::$table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
        //$slides = array_slice($slides, 0, 4);
        //$slides = SqlTools::selectObjects ( "SELECT * FROM `prefix_$this->table` WHERE `show`='Y' AND deleted='N' ORDER BY `date` DESC" );
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
            $imageSource = DS . SITE . DS . Starter::app ()->getTheme() . "/images/img1.png"; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $images = $this->data->GetData ( 'images', " AND `module_id`=$slide->id AND `main`='Y' AND `module`='Slider'" );
            /* knn для работы с хранилищем изображений
              $imgs = getImagesStorageByModuleId(__CLASS__, $v['id'], true);
             */
            if ( $images )
            {
                foreach ( $images as $image )
                {
                    /* knn для работы с хранилищем изображений
                      $imageSource = $arr['path'];
                     */
                    $imageSource = $image['src'];
                    //берём только первую
                    break;
                }
            }

            $slideList[] =
            [
                'name' => $slide->name,
                'text' => $slide->text,
                'image' => $imageSource,
                'link' => isset ( $slide->link ) ? $slide->link : '',
            ];
        }

        return TemplateEngine::view ( 'sliderblock', array ( 'slideList' => $slideList ), "Slider" );
    }
}
