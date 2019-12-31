<?php

class News extends CmsModule
{
    private $data;
    private $table;
//    private $seo;
//    private $return;
//    private $year = 0;
//    private $month = 0;
//    private $id = 0;
//    private $monthesIn = array ( 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
//    private $monthes = array ( 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' );

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
        $this->template = "page";
        $this->model = "News";
        Starter::import ( "News.models.*" );
        $this->table = "news";
        $this->actions =
        [
            'default' =>
            [
                'method' => 'actionList'
            ],
            'list' =>
            [
                'method' => 'actionList'
            ],
            'view' =>
            [
                'method' => 'actionView'
            ]
        ];
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
        $newsManager = new NewsManager ( $this );
        $newsManager->pageSize = Tools::getSettings ( __CLASS__, 'onpagenews', 6 );
        $news = $newsManager->search ( $params );
        return $this->render ( 'newslist', [ 'news' => $news ] );
    }

    private function actionView ( $params )
    {
        $this->title = $this->currentDocument->title;
        $newsManager = new NewsManager ( $this );
        $newsManager->pageSize = Tools::getSettings ( __CLASS__, 'onpagenews', 6 );
        $provider = $newsManager->search ( $params );
        $news = $provider->data[0];
        $images = Starter::app()->imager->getImages ( $this->model, $news->id );
        $walkNews = $newsManager->getWalkLink ( $news->id );
        $this->breadcrumbs =  [ (object) [ 'title' => $news->title, 'link' => $this->createLink ( $news ) ] ];
        return $this->render ( 'newsone', [ 'news' => $news, 'walk' => $walkNews, 'images' => $images ] );
    }

    public function createLink ( $newsParams = [] )
    {
        $link = $this->currentDocument->link;
        if ( !array_key_exists ( 'id', $newsParams ) )
            $link .= "/list";
        else
            $link .= "/view";

        foreach ( $newsParams as $key => $value )
            if ( is_numeric ( $key ) )
                $link .= "/$value";
            else
                $link .= "/$key/$value";
        return $link;
    }

    /**
     * getPage для перехода на рядом стоящие новости
     * переход зациклен по кругу
     *
     * @param String 'next', 'prev' - направление перехода
     * returns String
     */
//    private function getPage ($param)
//    {
//        $this->id = $this->params[0];
//
//        $query = "SELECT n.id" . $select
//            . " FROM `prefix_" . $this->table . "` AS n"
//            . $join
//            . " WHERE n.`date` <= NOW() AND n.`deleted` = 'N' AND n.`show` = 'Y'" . $where
//            . " ORDER BY n.`date` DESC";
//        $total = SqlTools::selectObjects ( $query );
//
//        foreach ( $total as $link )
//            $links[] = $link->id;
//
//        $current = array_search($this->id, $links);
//
//	$next = ( $current !== (count($links) -1) ) ? $links[$current + 1] : $links[0];
//	$prev = ( $current == 0 ) ? $links[count($links) - 1] : $links[$current - 1];
//
//        if ( $param == 'prev' ) $link = $prev;
//        if ( $param == 'next' ) $link = $next;
//        return $link;
//    }



    /**
     * SEO для новостей
     * @param int $id
     */
//    private function seo ()
//    {
//        $id = ( int ) $this->id;
//
//        if ( $id == 0 )
//        {
//            $this->seo = array ();
//            return false;
//        }
//
//        Starter::app ()->headManager->Run ();
//        $sql = "SELECT `title`, `keywords`, `description`, `tagH1`
//            FROM `prefix_seo`
//            WHERE `module`='" . __CLASS__ . "'
//            AND `module_id`=$id
//            AND `module_table`='$this->table'";
//
//        $this->seo = SqlTools::selectRow ( $sql, MYSQL_ASSOC );
//        if ( !empty ( $this->seo ) )
//        {
//            //Keywords
//            if ( !empty ( $this->seo['keywords'] ) )
//                $header->addMetaText ( "<meta name='keywords' content='" . htmlspecialchars ( $this->seo['keywords'] ) . "' />" );
//            //Description
//            if ( !empty ( $this->seo['description'] ) )
//                $header->addMetaText ( "<meta name='description' content='" . htmlspecialchars ( $this->seo['description'] ) . "' />" );
//            //Title
//            if ( !empty ( $this->seo['title'] ) )
//                $this->seo['title'] = $this->seo['title'];
//            else
//                $this->seo['title'] = Starter::app ()->title . ( $this->currentDocument ? "  — " . $this->currentDocument->title : "" );
//        }
//        else
//            $this->seo['title'] = Starter::app ()->title . ( $this->currentDocument ? "  — " . $this->currentDocument->title : "" );
//
//        $header->setTitle ( $this->seo['title'] );
//    }

    /**
     * Генерация массива разделов для подменю,
     * вызывается модулем Content из метода SubMenu
     *
     * array( array('name','link','active') )
     */
//    public function IntegrationMenu ()
//    {
//        $dates = SqlTools::selectRows ( "
//            SELECT
//                    YEAR(`date`) AS `year`,
//                    MONTH(`date`) AS `month`
//            FROM `prefix_news`
//            WHERE `deleted`='N' AND `show`='Y' AND `date`<= NOW()
//            GROUP BY MONTH(`date`), YEAR(`date`)
//            ORDER BY `date` DESC" );
//        $data = $menu = array ();
//        foreach ( $dates as $date )
//        {
//            $data[$date['year']][] = $date['month'];
//        }
//        $i = 0;
//        foreach ( $data as $year => $monthes )
//        {
//            $menu[$i] = array (
//                'name' => 'Новости за <strong>' . $year . '</strong> год'
//            );
//            foreach ( $monthes as $month )
//            {
//                if ( $this->year == $year && $this->month == $month )
//                    $active = true;
//                else
//                    $active = false;
//                $menu[$i]['sub'][] = array (
//                    'name' => $this->monthes[$month - 1],
//                    'link' => "/press-center/list/$year/$month",
//                    'active' => $active
//                );
//            }
//            $i++;
//        }
//        return $menu;
//    }



    /**
     * Генерация и выдача RSS новостей
     */
//    private function RSS ()
//    {
//        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC', '20' );
//
//        $items = '';
//        foreach ( $news as $new )
//        {
//            $parsedDate = strtotime ( $new['date'] );
//            $newYear = date ( 'Y', $parsedDate );
//            $newMonth = date ( 'm', $parsedDate );
/*
            $items .= '
                <item>
                        <title>' . $new['name'] . '</title>
                        <description>' . $new['anons'] . '</description>
                        <link>http://' . $_SERVER['SERVER_NAME'] . $this->Link ( $newYear, $newMonth, $new['id'] ) . '</link>
                        <guid>' . $new['id'] . '</guid>
                        <pubDate>' . date ( 'r', strtotime ( $new['date'] ) ) . '</pubDate>
                </item>';
        }



        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                <rss version="2.0">
                <channel>
                        <title>Новости ' . $_SERVER['SERVER_NAME'] . '</title>
                        <description>Новости сайта ' . $_SERVER['SERVER_NAME'] . ' ' . Starter::app ()->title . '</description>
                        <link>http://' . $_SERVER['SERVER_NAME'] . '</link>
                        <lastBuildDate>' . date ( 'r' ) . '</lastBuildDate>
                        <language>ru</language>
                        <generator>Booot CMS</generator>
                        ' . $items . '
                </channel>
                </rss>';
*/
//        header ( "Content-Type: application/xml; charset=UTF-8" );
//        echo $xml;
//        exit ();
//    }

//    private function OneNews ()
//    {
//        $news = $this->data->GetDataById ( $this->table, $this->id );
//
//        if ( empty ( $news ) )
//            page404 ();
//
//        $this->seo ();
//
//        $parsedDate = strtotime ( $news['date'] );
//        $newsYear = date ( 'Y', $parsedDate );
//        $newsMonth = date ( 'm', $parsedDate );
//
//        $vars = array (
//            'title' => $news['name'],
//            'h1' => $news['name'],
//            'date' => DatetimeTools::inclinedDate ( $news['date'] ),
//            'content' => $news['text'],
//            'link' => $this->Link (),
//            'news_id' => $this->id
//        );
//
//        $content = tpl ( 'modules/' . __CLASS__ . '/newsone', $vars );
//        return tpl ( 'newsone', array (
//            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $vars['title'] . ' — ' . Starter::app ()->title,
//            'name' => $vars['title'],
//            'text' => $content
//            ) );
//    }

//    private function MonthNews ()
//    {
//        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );
//
//        if ( empty ( $news ) )
//            page404 ();
//
//        foreach ( $news as $k => $v )
//        {
//            $parsedDate = strtotime ( $v['date'] );
//            $newsYear = date ( 'Y', $parsedDate );
//            $newsMonth = date ( 'm', $parsedDate );
//
//            if ( $newsMonth != $this->month )
//                continue;
//            if ( $newsYear != $this->year )
//                continue;
//
//            $image = SqlTools::selectValue ( "SELECT `src` FROM `prefix_images` WHERE `module`='News' AND `module_id`='" . $v['id'] . "' AND `main`='Y' LIMIT 1" );
//
//            $newsList[] = array (
//                'name' => $v['name'],
//                'anons' => $v['anons'],
//                'link' => $this->Link ( $newsYear, $newsMonth, $v['id'] ),
//                'date' => DatetimeTools::inclinedDate ( $v['date'] ),
//                'text' => $v['text'],
//                'image' => $image
//            );
//        }
//
//        $news_onpage = Tools::getSettings ( __CLASS__, 'onpagenews', 3 );
//        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
//        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
//        $newsList = $news_paged['items'];
//        $paging = $news_paged['rendered'];
//        unset ( $news_paged );
//
//        $vars = array (
//            'title' => 'Новости за ' . mb_strtolower ( $this->monthes[$this->month - 1] ) . ' ' . $this->year,
//            'news' => $newsList,
//            'paging' => $paging,
//            'link' => $this->Link (),
//        );
//        $GLOBALS['sidebar'] = $this->Menu ();
//
//        $content = tpl ( 'modules/' . __CLASS__ . '/newslist', $vars );
//
//        return tpl ( 'page', array (
//            'title' => $vars['title'],
//            'name' => $vars['title'],
//            'text' => $content
//            ) );
//    }
//
//    private function YearNews ()
//    {
//        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );
//
//        if ( empty ( $news ) )
//            page404 ();
//
//        foreach ( $news as $k => $v )
//        {
//            $parsedDate = strtotime ( $v['date'] );
//            $newsYear = date ( 'Y', $parsedDate );
//            if ( $newsYear == $this->year )
//            {
//                $newsMonth = date ( 'm', $parsedDate );
//                break;
//            }
//        }
//
//        if ( !isset ( $newsMonth ) )
//            page404 ();
//
//        header ( 'Location: ' . $this->Link ( $this->year, $newsMonth ) );
//    }

//    private function MainPage ()
//    {
//        return $this->allNews ();
//        /*
//          $news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
//
//          if(empty($news)) return tpl('page', array('title'=>'Новости', 'name'=>'Новости', 'text'=>tpl('modules/'.__CLASS__.'/nonews')));
//
//          $lastNews = current($news);
//
//          $parsedDate = strtotime($lastNews['date']);
//          $newsYear = date('Y',$parsedDate);
//          $newsMonth = date('m',$parsedDate);
//
//          header('Location: '.$this->Link($newsYear, $newsMonth));
//         *
//         */
//    }

//    private function allNews ()
//    {
//        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );
//        if ( empty ( $news ) )
//        {
//            return tpl ( 'page', array (
//                'title' => 'Новости',
//                'name' => 'Новости',
//                'text' => tpl ( 'modules/' . __CLASS__ . '/nonews' )
//                ) );
//        }
//
//        foreach ( $news as $k => $v )
//        {
//            $imageSource = '/images/default.png'; // картинка по умолчанию
//            // извлекаем привязанные картинки из prefix_images
//            $imgs = $this->data->GetData ( 'images', " AND `module_id` = {$v['id']} AND `main` = 'Y' AND `module` = 'News'" );
//            if ( $imgs )
//            {
//                foreach ( $imgs as $arr )
//                {
//                    $imageSource = $arr['src'];
//                    //берём только первую
//                    break;
//                }
//            }
//
//            $parsedDate = strtotime ( $v['date'] );
//            $newsYear = date ( 'Y', $parsedDate );
//            $newsMonth = date ( 'm', $parsedDate );
//
//            $newsList[] = array (
//                'name' => $v['name'],
//                'anons' => $v['anons'],
//                'link' => $this->Link ( $newsYear, $newsMonth, $v['id'] ),
//                'date' => DatetimeTools::inclinedDate ( $v['date'] ),
//                'text' => $v['text'],
//                'img' => $imageSource
//            );
//        }
//        //Пэйджинг
//        $news_onpage = Tools::getSettings ( __CLASS__, 'onpagenews', 3 );
//        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
//        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
//        $newsList = $news_paged['items'];
//        $paging = $news_paged['rendered'];
//        unset ( $news_paged );
//
//        $GLOBALS['sidebar'] = $this->Menu ();
//
//        $vars = array (
//            'name' => 'Все новости',
//            'title' => 'Новости',
//            'news' => $newsList,
//            'link' => $this->Link (),
//            'paging' => $paging
//        );
//        $content = tpl ( 'modules/' . __CLASS__ . '/newslist', $vars );
//        return tpl ( 'page', array (
//            'title' => $vars['title'],
//            'name' => $vars['title'],
//            'paging' => $paging,
//            'text' => $content
//            ) );
//    }

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
            $link .= "/view/" . ( $id == 0 ? '' : $id );
        return $link;
    }

    function Menu ()
    {
        $news = $this->data->GetData ( $this->table, "AND `show`='Y' AND `date` <= NOW()", "`date` DESC" );

        $menu = array ();

        foreach ( $news as $v )
        {
            $parsedDate = strtotime ( $v['date'] );
            $newsYear = date ( 'Y', $parsedDate );
            $newsMonth = date ( 'm', $parsedDate );
            //$newsDay = (int)date('d',$parsedDate);

            if ( $newsMonth == $this->month )
                $active = true;
            else
                $active = false;

            $menu[$newsYear][$newsMonth] = array (
                'number' => $newsMonth,
                'name' => $this->monthes[$newsMonth - 1],
                'link' => $this->Link (),
                'active' => $active
            );
        }

        return tpl ( 'modules/' . __CLASS__ . '/menu', array ( 'menu' => $menu ) );
    }
    /*
     * Блок вывода последних новостей
     */

//    function LastNewsBlock ($topic = null, $limit = null)
//    {
//        if($limit)
//            $count = $limit;
//        else
//        {
//            $count = Tools::getSettings ( 'News', 'last_news' );
//            if ( !$count )
//                $count = 3;
//        }
//
//        $link = Starter::app ()->content->getLinkByModule ( __CLASS__ );
//        $title = 'Новости';
//        if($topic)
//        {
//            $topic = SqlTools::selectObject("SELECT * FROM `prefix_news_topics` WHERE `id` = '".(int)$topic."'");
//            $link = Starter::app ()->content->getLinkByModule ( __CLASS__ ) . '?topic=' . (int)$topic->id;
//            $title = $topic->name;
//            $where = " AND `topic` = '".$topic->id."' ";
//        }
//
//        $queryNews = "SELECT * FROM `prefix_news` WHERE `deleted`='N' ".$where." ORDER BY `created` DESC LIMIT " . ( int ) $count;
//        $newsList = SqlTools::selectObjects ( $queryNews, null, "id" );
//        if ( count ( $newsList ) === 0 )
//            return "";
//
//        $ids = ArrayTools::numberList ( array_keys ( $newsList ) );
//        $images = SqlTools::selectObjects ( "SELECT * FROM `prefix_images` WHERE `main`= 'Y' AND `module`='News' AND `module_id` IN ($ids)", null, "module_id" );
//
//
//        foreach ( $newsList as $k => $news )
//        {
//            $parsedDate = strtotime ( $news->date );
//            $news->date = date ( 'Y-m-d', $parsedDate );
//            $newsYear = date ( 'Y', $parsedDate );
//            $newsMonth = date ( 'm', $parsedDate );
//
//            $news->link = '/news/' . $news->id;
//            $news->image = array_key_exists ( $news->id, $images ) ? $images[$news->id] : null;
//        }
//
//        return tpl ( 'modules/News/lastnewsblock', array (
//            'link' => $link,
//            'title' => $title,
//            'news' => $newsList
//            ) );
//    }
//
//    function BlockMain ( $limit = 5 )
//    {
//        $rows = SqlTools::selectRows ( "SELECT * FROM `prefix_news` WHERE `show`='Y' AND `deleted`='N' ORDER BY `date` DESC, `id` DESC LIMIT {$limit}" );
//        return tpl ( 'modules/News/blockmain', array ( 'items' => $rows ) );
//    }
}
