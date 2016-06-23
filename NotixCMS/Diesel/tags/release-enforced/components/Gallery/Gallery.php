<?php

class Gallery extends CmsModule
{
    private $data;
    private $table;
//    private $seo;
//    private $return;
//    private $year = 0;
//    private $month = 0;
//    private $id = 0;
//    private $currentCategoryId = 0;
//    private $monthesIn = array ( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
//    private $monthes = array ( 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' );

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
        $this->actions = array
        (
            'default' => [ 'method' => 'actionList' ],
            'list' => [ 'method' => 'actionList' ],
            'view' => [ 'method' => 'actionView' ]
        );
        $this->table = 'gallery';
        $this->model = "Gallery";
        Starter::import ( "Gallery.models.*" );
        $this->template = "mainpage";
    }

    public function Run ()
    {
        $action = $this->createAction ();
        if ( !$action )
            page404 ();
        $content = $action->run ();
        return $content;
    }

    public function startController ( $method, $params )
    {
        return $this->$method ( $params );
    }

    public function beforeRender ()
    {
        if ( parent::beforeRender () )
            return true;
        else
            return false;
    }

    private function actionList ( $params )
    {
        $this->title = $this->currentDocument->title;
        $manager = new GalleryManager ( $this );
        $manager->pageSize = Tools::getSettings ( __CLASS__, 'onpagenews', 6 );
        $galleries = $manager->search ( $params );
        $ids = ArrayTools::pluck ( $galleries->data, "id" );
        Starter::app()->imager->getImages ("Gallery", $ids);
        $template = array_key_exists ( 'alias', $params ) ? $params['alias'] : 'galleryList';
        return $this->render ( $template, [ 'galleries' => $galleries ] );
//        $sql = "SELECT n.* FROM `prefix_" . $this->table . "` AS n
//            WHERE n.`deleted` = 'N' AND n.`show` = 'Y'";
//        $galleries = SqlTools::selectObjects ( $sql );
//        foreach( $galleries as $gallery )
//        {
//            $gallery->images = Starter::app ()->imager->getImages( $this->model, $gallery->id );
//        }

//        if ($this->params[0] == 'videos')
//        {
//            $news_onpage = Tools::getSettings ( __CLASS__, 'onpage'.$this->params[0], 3 );
//            $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
//            $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
//            $newsList = $news_paged['items'];
//            $paging = $news_paged['rendered'];
//            unset ( $news_paged );
//
//            $vars = array (
//                'name' => $galleries[0]->name,
//                'title' => $galleries[0]->name,
//                'images' => $newsList,
//                'paging' => $paging,
//            );
//        } else {
//            $news_onpage = Tools::getSettings ( __CLASS__, 'onpage'.$this->params[0], 3 );
//            $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
//            $news_paged = Paging ( $gal, $news_onpage, $show_pages );
//            $gal = $news_paged['items'];
//            $paging = $news_paged['rendered'];
//            unset ( $news_paged );
//
//            $vars = array (
//                'name' => 'Фотогалереи',
//                'title' => 'Фотогалереи',
//                'galleries' => $gal,
//                'paging' => $paging,
//            );
//        }


        //$template = $this->current_document['template'] ? basename ( $current_document['template'], '.php' ) : 'page' ;
//        return $this->render ('gallery', [ 'galleries' => $galleries ]);
//        $content = tpl ( 'modules/' . __CLASS__ . '/'. $this->params[0], $vars );
//        return TemplateEngine::view ( 'page', array (
//            'title' => $vars['title'],
//            'name' => $vars['name'],
//            'paging' => $paging,
//            'text' => $content,
//            ), __CLASS__ );
    }

     private function actionView ( $params )
    {
        $manager = new GalleryManager ( $this );
        $manager->pageSize = Tools::getSettings ( __CLASS__, 'onpagenews', 21 );
        $gallery = $manager->searchGallery ( $params );
        $this->title = $gallery->title;
        $template = array_key_exists ( 'alias', $params ) ? $params['alias'] : 'gallery';
        return $this->render ( 'gallery', [ 'gallery' => $gallery ] );

//        $path = Starter:: app ()->urlManager->getUriParts ();
//
//        $sql = "SELECT n.* FROM `prefix_images` AS n
//            WHERE n.`module_id`='".end($path)."' AND `module` = '$this->model' ORDER BY n.`id` ASC";
//        $images = SqlTools::selectObjects ( $sql );
//
////        if ( empty ( $images ) )
////        {
////            page404 ();
////        }
//
//        foreach ( $images as $image )
//        {
//            $newsList[] = array (
//                'src' => $image->src,
//                'video' => $image->video
//            );
//        }
//
//        $news_onpage = Tools::getSettings ( __CLASS__, 'onpageforgallery', 9 );
//        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
//        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
//        $newsList = $news_paged['items'];
//        $paging = $news_paged['rendered'];
//        unset ( $news_paged );
//
//        $vars = array (
//            'name' => 'Фотогалереи',
//            'title' => 'Фотогалереи',
//            'galleries' => $newsList,
//            'paging' => $paging,
//        );
//
//        return $this->render ('gallery', $vars);
//        $content = tpl ( 'modules/' . __CLASS__ . '/gallery', $vars );
//        return TemplateEngine::view ( 'page', array (
//            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $vars['title'] . ' — ' . Starter::app ()->title,
//            'name' => $news->name,
//            'date' => $news->date,
//            'text' => $content,
//
//            ), __CLASS__ );
    }

    /**
     *
     * @param Array $newsDate
     * @param type $id
     * @return string
     */
    function Link ( $newsDate = array (), $id = 0 )
    {
        $link = '/' . implode ( '/', Starter::app ()->urlManager->linkPath );
        if ( count ( $newsDate ) )
        {
            $month = ( $newsDate['month'] < 10 ? '0' : "" ) . ( int ) $newsDate['month'];
            $day = ( $newsDate['day'] < 10 ? '0' : "" ) . ( int ) $newsDate['day'];
            $year = ( int ) $newsDate['year'];
            $link .= "/list/$year/$month/$day";
        }
        else
        {
            $link .= "/" . ( $id == 0 ? '' : $id );
        }
        return $link;
    }

}
