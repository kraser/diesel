<?php

class Gallery extends CmsModule
{
    private $data;
    private $table = 'gallery';
    private $seo;
    private $return;
    private $year = 0;
    private $month = 0;
    private $id = 0;
    private $currentCategoryId = 0;
    private $monthesIn = array ( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
    private $monthes = array ( 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' );

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
        $this->actions = array
        (
            'siteMap' => array ( 'method' => 'siteMap', 'name' => 'Карта сайта' ),
            'galery' => array ( 'method' => 'Galery', 'name' => 'Галерея' )
        );
    }

    function Run ()
    {
        //RSS
        if ( isset ( $_GET['rss'] ) )
        {
            $this->RSS ();
        }

        $template = $this->currentDocument->template ? basename ( $this->currentDocument->template, '.php' ) : 'page';


        $path = Starter::app ()->urlManager->getUriParts ();
        array_shift ( $path );
        if ( count ( $path ) === 1 || $path[0] === "list" )
        {
            $this->params = count ( $path ) ? $path : null;
            return $this->actionList ();
        }
        else if ( count ( $path ) === 2 )
        {
            $this->params = $path;
            return $this->actionView ();
        }
        else
        {
            page404 ();
        }
    }

    private function actionList ()
    {
        if ( count ( $this->params ) && $this->params[0] == "list" )
        {
            array_shift ( $this->params );
        }

        $sql = "SELECT n.*" . $select
            . " FROM `prefix_" . $this->table . "` AS n"
            . $join
            . " WHERE n.`date` <= NOW() AND n.`deleted` = 'N' AND n.`show` = 'Y' AND n.`alias` = '".$this->params[0]."' ORDER BY id DESC " . $where;
        $galleries = SqlTools::selectObjects ( $sql );

        Starter::app ()->headManager->setTitle ( Starter::app ()->title . " - " . $galleries[0]->name);

        foreach( $galleries as $gallery )
        {
            $sql = "SELECT n.*" . $select
                . " FROM `prefix_images` AS n"
                . $join
                . " WHERE n.`module_id` = {$gallery->id} AND `module` = '". __CLASS__ ."' " . $where
                . " ORDER BY n.`id` DESC";
            $images = SqlTools::selectObjects ( $sql );

            foreach ( $images as $image )
            {
                $newsList[] = array (
                    'src' => $image->src,
                    'video' => $image->video,
                    'title' => $image->title
                );

                $gal[$gallery->id] = Array(
                    'title' => $gallery->name,
                    'src' => $image->src,
                    'link' => $this->Link ( array (), $gallery->id ),
                    'total' => '('. sizeof($images). ' шт )'
                );
            }
        }

        if ($this->params[0] == 'videos')
        {
            $news_onpage = Tools::getSettings ( __CLASS__, 'onpage'.$this->params[0], 3 );
            $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
            $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
            $newsList = $news_paged['items'];
            $paging = $news_paged['rendered'];
            unset ( $news_paged );

            $vars = array (
                'name' => $galleries[0]->name,
                'title' => $galleries[0]->name,
                'images' => $newsList,
                'paging' => $paging,
            );
        } else {
            $news_onpage = Tools::getSettings ( __CLASS__, 'onpage'.$this->params[0], 3 );
            $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
            $news_paged = Paging ( $gal, $news_onpage, $show_pages );
            $gal = $news_paged['items'];
            $paging = $news_paged['rendered'];
            unset ( $news_paged );

            $vars = array (
                'name' => 'Фотогалереи',
                'title' => 'Фотогалереи',
                'galleries' => $gal,
                'paging' => $paging,
            );
        }


        //$template = $this->current_document['template'] ? basename ( $current_document['template'], '.php' ) : 'page' ;

        $content = tpl ( 'modules/' . __CLASS__ . '/'. $this->params[0], $vars );
        return TemplateEngine::view ( 'page', array (
            'title' => $vars['title'],
            'name' => $vars['name'],
            'paging' => $paging,
            'text' => $content,
            ), __CLASS__ );
    }

     private function actionView ()
    {
        $path = Starter:: app ()->urlManager->getUriParts ();

        $select = '';
        $join = '';
        $where = '';

        $sql = "SELECT n.*" . $select
                . " FROM `prefix_images` AS n"
                . $join
                . " WHERE n.`module_id` = ".end($path)." AND `module` = '". __CLASS__ ."' " . $where
                . " ORDER BY n.`id` DESC";
        $images = SqlTools::selectObjects ( $sql );

        if ( empty ( $images ) )
        {
            page404 ();
        }

        foreach ( $images as $image )
        {
            $newsList[] = array (
                'src' => $image->src,
                'video' => $image->video
            );
        }

        $news_onpage = Tools::getSettings ( __CLASS__, 'onpageforgallery', 9 );
        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
        $newsList = $news_paged['items'];
        $paging = $news_paged['rendered'];
        unset ( $news_paged );

        $vars = array (
            'name' => 'Фотогалереи',
            'title' => 'Фотогалереи',
            'galleries' => $newsList,
            'paging' => $paging,
        );

        $content = tpl ( 'modules/' . __CLASS__ . '/gallery', $vars );
        return TemplateEngine::view ( 'page', array (
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $vars['title'] . ' — ' . Starter::app ()->title,
            'name' => $news->name,
            'date' => $news->date,
            'text' => $content,

            ), __CLASS__ );
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
