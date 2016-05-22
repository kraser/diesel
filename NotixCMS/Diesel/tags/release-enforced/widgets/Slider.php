<?php

/**
 *
 * @author knn
 */
class Slider extends CmsWidget
{
    private $data;
    public $table = 'slider';

    function __construct ()
    {
        $this->data = Starter::app ()->data;
    }

    public function Run ()
    {
        $select = '';
        $join = '';
        $where = '';

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
            //$slide->text = explode("\n", $slide->text);
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
