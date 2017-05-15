<?php

/**
 *
 * @author knn
 */
class Gallery
{
    public static $table = 'gallery';

    function __construct ()
    {

    }

    function Run ()
    {
        //$pictures = SqlTools::selectRows ( "SELECT * FROM `prefix_" . self::$table . "` WHERE `show`='Y' AND deleted='N' ORDER BY `date` DESC", MYSQLI_ASSOC );
        $select = '';
        $join = '';
        $where = '';
//        if(_REGION !== null)
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (g.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//            . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $sql = "SELECT g.*" . $select
        . " FROM `prefix_" . self::$table . "` AS g"
        . $join
        . " WHERE g.`deleted` = 'N' AND g.`show` = 'Y'" . $where
        . "ORDER BY g.`date` DESC";
        $pictures = SqlTools::selectRows($sql, MYSQLI_ASSOC);
        $picturesList = array ();
        foreach ( $pictures as $picture )
        {

            $imageSource = "/themes/" . Starter::app()->theme . "/images/img1.png"; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $imgs = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module_id`={$picture['id']} AND `main`='Y' AND `module`='Gallery'", MYSQLI_ASSOC );

            if ( $imgs )
            {
                foreach ( $imgs as $arr )
                {
                    /* knn для работы с хранилищем изображений
                      $imageSource = $arr['path'];
                     */
                    $imageSource = $arr['src'];
                    //берём только первую
                    break;
                }
            }
            $picturesList[] = array (
                'name' => $picture['name'],
                'text' => $picture['text'],
                'image' => $imageSource
            );
        }

        $content = TemplateEngine::view ( 'galleryblock', array ( 'picturesList' => $picturesList ), __CLASS__ );
        return TemplateEngine::view ( 'page', array
                (
                'title' => "Галерея изображений",
                'name' => "Галерея",
                'text' => $content
                ), "Content" );
    }

    function GalleryBlock ()
    {

        //$pictures = SqlTools::selectRows ( "SELECT * FROM `prefix_" . self::$table . "` WHERE `show`='Y' AND deleted='N' ORDER BY `date` DESC", MYSQLI_ASSOC );
        $select = '';
        $join = '';
        $where = '';
//        if(_REGION !== null)
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (g.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//            . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $sql = "SELECT g.*" . $select
        . " FROM `prefix_" . self::$table . "` AS g"
        . $join
        . " WHERE g.`deleted` = 'N' AND g.`show` = 'Y'" . $where
        . "ORDER BY g.`date` DESC";
        $pictures = SqlTools::selectRows($sql, MYSQLI_ASSOC);
        $picturesList = array ();
        foreach ( $pictures as $k => $v )
        {

            $imageSource = "/themes/" . Starter::app()->theme . "/images/img1.png"; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $imgs = SqlTools::selectRows ( "SELECT * FROM `prefix_images` WHERE `module_id`={$v['id']} AND `main`='Y' AND `module`='Gallery'", MYSQLI_ASSOC );

            if ( $imgs )
            {
                foreach ( $imgs as $arr )
                {
                    /* knn для работы с хранилищем изображений
                      $imageSource = $arr['path'];
                     */
                    $imageSource = $arr['src'];
                    $imageSmall = dirname ( $arr['src'] ) . '/small_' . basename ( $arr['src'] );
                    //берём только первую
                    break;
                }
            }
            $picturesList[] = array
                (
                'name' => $v['name'],
                'text' => $v['text'],
                'image' => $imageSource,
                'small' => (file_exists ( DOCROOT . $imageSmall ) ? $imageSmall : $imageSource)
            );
        }

        return tpl ( 'modules/Gallery/block', array (
            'items' => $picturesList
            ) );
    }
}
