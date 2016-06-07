<?php

class Blog extends CmsModule
{
//    private $data;
//    private $table = 'blog';
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
        $this->model = "Blog";
        $this->template = "page";
        $this->actions =
        [
//            'default' =>
//            [
//                'method' => 'view'
//            ],
//            'map' =>
//            [
//                'method' => 'siteMap'
//            ],
//            'contacts' =>
//            [
//                'method' => 'contacts'
//            ],
//            'send' =>
//            [
//                'method' => 'sendForm'
//            ]
        ];
        $this->table = 'blog';
    }

    /** Вывод блока новостей, в зависимости от URI
     * @return type
     */
    function Run ()
    {
        $action = $this->createAction ();
        //RSS
        $path = Starter::app ()->urlManager->getUriParts ();
        if ( $action )
            return $action->run ();
        else
        {

            if ( !count($path))
                page404 ();
            else if ( count($path) == 1)
                return $this->viewTopic ( $path );
            else
                return $this->viewRecord ( $path );
        }
//        array_shift ( $path );
//        if ( count ( $path ) === 0 || $path[0] === "list" )
//        {
//            $this->params = count ( $path ) ? $path : null;
//            return $this->actionList ();
//        }
//        else if ( count ( $path ) === 1 )
//        {
//            $this->params = $path;
//            return $this->actionView ();
//        }
//        else
//        {
//            page404 ();
//        }
    }

    private function viewTopic ( $param )
    {
        $alias = array_shift ( $param );
        $sql = "SELECT n.* FROM `prefix_" . $this->table . "_topics` AS n
            WHERE n.`deleted`='N' AND n.`show`='Y' AND `top`=0 AND `nav`='$alias'";
        $topic = SqlTools::selectObject ( $sql );
        if ( $topic )
            $blogList = SqlTools::selectObjects ("SELECT * FROM `prefix_blog` WHERE `top`=" . $topic->id . " AND `deleted`='N' AND `show`='Y'" );
        else
            $blogList = [];
        $template = $topic && $topic->template ? : $alias . "List";
        $this->title =  [ $topic->name ];
        return $this->render ( $template, [ 'blogs' => $blogList ] );
    }

    private function viewRecord ( $param )
    {
        $alias = array_shift ( $param );
        $sql = "SELECT n.* FROM `prefix_" . $this->table . "_topics` AS n
            WHERE n.`deleted`='N' AND n.`show`='Y' AND `top`=0 AND `nav`='$alias'";
        $topic = SqlTools::selectObject ( $sql );
        $blogAlias = array_shift ( $param );
        if ( $topic )
            $blog = SqlTools::selectObject ("SELECT * FROM `prefix_blog` WHERE `top`=" . $topic->id . " AND `link`='$blogAlias'" );
        else
            $blog = null;
        $template = $topic && $topic->template ? : $alias;
        $this->title =  [ $topic->name, str_replace ( "|", " ", $blog->name ) ];
        return $this->render ( $template, [ 'blog' => $blog ] );
    }

    private function actionList ()
    {
        if ( count ( $this->params ) && $this->params[0] == "list" )
        {
            array_shift ( $this->params );
        }

        $sql = "SELECT n.*" . $select
            . " FROM `prefix_" . $this->table . "_topics` AS n"
            . $join
            . " WHERE n.`created` <= NOW() AND n.`deleted` = 'N' AND n.`show` = 'Y' AND `top` = 0 " . $where
            . " ORDER BY n.`created` ASC";
        $categories = SqlTools::selectObjects ( $sql );

        Starter::app ()->headManager->setTitle ( Starter::app ()->title . " - Команды " );

        foreach( $categories as $category )
        {
            $sql = "SELECT n.*" . $select
                . " FROM `prefix_" . $this->table . "_topics` AS n"
                . $join
                . " WHERE n.`created` <= NOW() AND n.`deleted` = 'N' AND n.`show` = 'Y' AND `top` = {$category->id}" . $where
                . " ORDER BY n.`created` ASC";
            $cats = SqlTools::selectObjects ( $sql );

            foreach ( $cats as $cat )
            {
                $imageSource = '/images/default.png'; // картинка по умолчанию
                // извлекаем привязанные картинки из prefix_images
                $imgs = $this->data->GetData ( 'images', " AND `module_id` = {$cat->id} AND `main` = 'Y' AND `module` = '". __CLASS__ ."'" );
                if ( $imgs )
                {
                    foreach ( $imgs as $arr )
                    {
                        if ( array_key_exists ( 'src', $arr ) && file_exists ( DOCROOT . $arr['src'] ) )
                        {
                            $imageSource = $arr['src'];
                            //берём только первую
                            break;
                        }
                    }
                }

                $sql = "SELECT n.*"
                    . " FROM `prefix_" . $this->table . "` AS n"
                    . " WHERE n.`top` = '{$cat->id}' AND n.`deleted` = 'N' AND n.`show` = 'Y' ";

                $newsList[$category->name][] = array (
                    //'category' => $this->getParentCategories($cat->id),
                    //'child' => $this->getChildCategories($cat->id),
                    'name' => $cat->name,
                    'link' => $this->Link ( array (), SqlTools::selectValue($sql) ),
                    'date' => DatetimeTools::inclinedDate ( $cat->created ),
                    'img' => $imageSource
                );
            }
        }

        //Пэйджинг
        $news_onpage = Tools::getSettings ( __CLASS__, 'onpagenews', 100 );
        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
        $newsList = $news_paged['items'];
        $paging = $news_paged['rendered'];
        unset ( $news_paged );

        $vars = array (
            'name' => 'Все команды',
            'title' => 'Команды',
            'news' => $newsList,
            'link' => $this->Link (),
            'paging' => $paging
        );
        $content = tpl ( 'modules/' . __CLASS__ . '/bloglist', $vars );
        return TemplateEngine::view ( 'page', array (
            'title' => $vars['title'],
            'name' => $vars['title'],
            'paging' => $paging,
            'text' => $content,
            'news' => $newsList
            ), __CLASS__, true );
    }


    private function actionView ()
    {
        $this->id = $this->params[0];
        //$news = ArrayTools::head ( SqlTools::selectObjects ( "SELECT * FROM `prefix_news` WHERE id=" . $this->id ) );

        $select = '';
        $join = '';
        $where = '';

        $sql = "SELECT n.*"
            . " FROM `prefix_" . $this->table . "` AS n"
            . " WHERE n.`id` = '" . $this->id . "' AND n.`deleted` = 'N' AND n.`show` = 'Y' ";
        $team = ArrayTools::head ( SqlTools::selectObjects ( $sql ) );

        if ( empty ( $team ) )
        {
            page404 ();
        }

        foreach ( $team as $one )
        {
            $imageSource = '/images/default.png'; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $imgs = $this->data->GetData ( 'images', " AND `module_id` = {$this->id} AND `main` = 'Y' AND `module` = '". __CLASS__ ."'" );
            if ( $imgs )
            {
                foreach ( $imgs as $arr )
                {
                    if ( array_key_exists ( 'src', $arr ) && file_exists ( DOCROOT . $arr['src'] ) )
                    {
                        $imageSource = $arr['src'];
                        //берём только первую
                        break;
                    }
                }
            }
        }


        foreach( $this->getChildCategories($team->top) as $category )
        {
            $categoryName =  SqlTools::selectObjects ( "SELECT `id`, `name` FROM `prefix_".$this->table."_topics` WHERE `top`=" . $category );
            if ( !empty($categoryName) )
                $categories =  $categoryName;
        }

        foreach ( $categories as $category )
        {
            $playerName  =  SqlTools::selectObjects ( "SELECT * FROM `prefix_".$this->table."` WHERE `top`=" . $category->id );
            $amplua[$category->name]= $playerName;
        }

        //$this->seo ();
        Starter::app ()->headManager->setTitle ( Starter::app ()->title . " - Команда " . " - " . $team->name );

        $parsedDate = strtotime ( $team->date );
        $newsYear = date ( 'Y', $parsedDate );
        $newsMonth = date ( 'm', $parsedDate );

        $vars = array (
            'title' => $team->name,
            'h1' => $team->name,
            'date' =>  DatetimeTools::inclinedDate ( $team->date ),
            'content' => $team->text,
            'link' => $this->Link ( array (), $team->id ),
            'news_id' => $this->id,
            'img' => $imageSource,
            'amplua' =>  $amplua
        );

        $content = TemplateEngine::view ( 'blogone', $vars, __CLASS__ );
        return TemplateEngine::view ( 'page', array (
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $vars['title'] . ' — ' . Starter::app ()->title,
            'name' => $vars['title'],
            'date' => $vars['date'],
            'text' => $content
            ), __CLASS__, true );
    }


    /**
     * <p>Строит и возвращает массив дочерних категорий от текущей (заданной) категории </p>
     * @param Integer $parentId <p>Id категории от которой находятся дочерние</p>
     * @param Integer $level <p>Уровень рекурсии</p>
     * @return Array
     */
    private function getChildCategories ( $parentId = null, $level = 0 )
    {
        $categoryId = $parentId ? : $this->currentCategoryId;

        if ( !$categoryId )
            return;

        $query = "SELECT `id` FROM `prefix_".$this->table."_topics` WHERE `top`=$categoryId AND `deleted`='N'";
        $childCategories = array_keys ( SqlTools::selectRows ( $query, MYSQL_ASSOC, "id" ) );
        foreach ( $childCategories as $subCatId )
        {
            $moreChildCategories = $this->getChildCategories ( $subCatId, $level + 1 );
            $childCategories = array_merge ( $childCategories, $moreChildCategories );
        }

        if ( $level === 0 && $categoryId == $this->currentCategoryId )
        {
            $this->childCategories = $childCategories;
        }

        return $childCategories;
    }

    /**
     * <p>Строит и возвращает массив родительских, относительно текущей, категорий</p>
     * @param Integer $categoryId <p>Id категоории относительно которой строится массив родительских</p>
     * @param Integer $level <p>Уровень рекурсии</p>
     * @return Array
     */
    public function getParentCategories ( $categoryId = null )
    {
        $parentCategory = $categoryId ? : $this->currentCategoryId;

        if ( !$parentCategory )
        {
            return;
        }

        $parentCategories = array ();

        while ( $parentCategory )
        {
            $query = "SELECT `top` FROM `prefix_".$this->table."_topics` WHERE `id`=$parentCategory AND `deleted`='N'";
            //$q = "SELECT `name` FROM `prefix_".$this->table."_topics` WHERE `id`=$parentCategory AND `deleted`='N'";
            $parentCategory = SqlTools::selectValue ( $query );
            //$parent = SqlTools::selectValue ( $q );
            if ( $parentCategory )
                $parentCategories[] = $parentCategory;
            //$parentCategories[parent] = $parent;

        }

        $this->parentCategories = $parentCategories;
        return $parentCategories;
    }

    /**
     * SEO для новостей
     * @param int $id
     */
    private function seo ()
    {
        $id = ( int ) $this->id;

        if ( $id == 0 )
        {
            $this->seo = array ();
            return false;
        }

        $header = Starter::app ()->headManager->Run ();
        $sql = "SELECT `title`, `keywords`, `description`, `tagH1`
            FROM `prefix_seo`
            WHERE `module`='" . __CLASS__ . "'
            AND `module_id`=$id
            AND `module_table`='$this->table'";

        $this->seo = SqlTools::selectRow ( $sql, MYSQL_ASSOC );
        if ( !empty ( $this->seo ) )
        {
            //Keywords
            if ( !empty ( $this->seo['keywords'] ) )
            {
                $header->addMetaText ( "<meta name='keywords' content='" . htmlspecialchars ( $this->seo['keywords'] ) . "' />" );
            }
            //Description
            if ( !empty ( $this->seo['description'] ) )
            {
                $header->addMetaText ( "<meta name='description' content='" . htmlspecialchars ( $this->seo['description'] ) . "' />" );
            }
            //Title
            if ( !empty ( $this->seo['title'] ) )
            {
                $this->seo['title'] = $this->seo['title'];
            }
            else
            {
                $this->seo['title'] = Starter::app ()->title . ( $this->currentDocument ? "  — " . $this->currentDocument->title : "" );
            }
        }
        else
        {
            $this->seo['title'] = Starter::app ()->title . ( $this->currentDocument ? "  — " . $this->currentDocument->title : "" );
        }

        $header->setTitle ( $this->seo['title'] );
    }

    /**
     * Генерация массива разделов для подменю,
     * вызывается модулем Content из метода SubMenu
     *
     * array( array('name','link','active') )
     */
    public function IntegrationMenu ()
    {
        $dates = SqlTools::selectRows ( "
            SELECT
                    YEAR(`date`) AS `year`,
                    MONTH(`date`) AS `month`
            FROM `prefix_blog`
            WHERE `deleted`='N' AND `show`='Y' AND `date`<= NOW()
            GROUP BY MONTH(`date`), YEAR(`date`)
            ORDER BY `date` DESC" );
        $data = $menu = array ();
        foreach ( $dates as $date )
        {
            $data[$date['year']][] = $date['month'];
        }
        $i = 0;
        foreach ( $data as $year => $monthes )
        {
            $menu[$i] = array (
                'name' => 'Новости за <strong>' . $year . '</strong> год'
            );
            foreach ( $monthes as $month )
            {
                if ( $this->year == $year && $this->month == $month )
                    $active = true;
                else
                    $active = false;
                $menu[$i]['sub'][] = array (
                    'name' => $this->monthes[$month - 1],
                    'link' => "/press-center/list/$year/$month",
                    'active' => $active
                );
            }
            $i++;
        }
        return $menu;
    }

    /**
     * Массив для хлебных крошек модуля
     *
     * array( array('name','link') )
     */
    public function breadCrumbs ()
    {
        if ( $this->id != 0 )
        {
            $news = $this->data->GetDataById ( $this->table, $this->id );
            return array ( array ( 'name' => $news['name'], 'link' => $this->Link ( array (), $this->id ) ) );
        }
        elseif ( $this->year != 0 && $this->month != 0 )
        {
            return array ( array ( 'name' => 'Новости за ' . mb_strtolower ( $this->monthes[$this->month - 1] ) . ' ' . $this->year, 'link' => $this->Link ( $this->year, $this->month ) ) );
        }

        return array ();
    }

    /**
     * Генерация и выдача RSS новостей
     */
    private function RSS ()
    {
        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC', '20' );

        $items = '';
        foreach ( $news as $new )
        {
            $parsedDate = strtotime ( $new['date'] );
            $newYear = date ( 'Y', $parsedDate );
            $newMonth = date ( 'm', $parsedDate );

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

        header ( "Content-Type: application/xml; charset=UTF-8" );
        echo $xml;
        exit ();
    }

    private function OneNews ()
    {
        $news = $this->data->GetDataById ( $this->table, $this->id );

        if ( empty ( $news ) )
            page404 ();

        $this->seo ();

        $parsedDate = strtotime ( $news['date'] );
        $newsYear = date ( 'Y', $parsedDate );
        $newsMonth = date ( 'm', $parsedDate );

        $vars = array (
            'title' => $news['name'],
            'h1' => $news['name'],
            'date' => DatetimeTools::inclinedDate ( $news['date'] ),
            'content' => $news['text'],
            'link' => $this->Link (),
            'news_id' => $this->id
        );

        $content = tpl ( 'modules/' . __CLASS__ . '/newsone', $vars );
        return tpl ( 'newsone', array (
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $vars['title'] . ' — ' . Starter::app ()->title,
            'name' => $vars['title'],
            'text' => $content
            ) );
    }

    private function MonthNews ()
    {
        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );

        if ( empty ( $news ) )
            page404 ();

        foreach ( $news as $k => $v )
        {
            $parsedDate = strtotime ( $v['date'] );
            $newsYear = date ( 'Y', $parsedDate );
            $newsMonth = date ( 'm', $parsedDate );

            if ( $newsMonth != $this->month )
                continue;
            if ( $newsYear != $this->year )
                continue;

            $image = SqlTools::selectValue ( "SELECT `src` FROM `prefix_images` WHERE `module`='Blog' AND `module_id`='" . $v['id'] . "' AND `main`='Y' LIMIT 1" );

            $newsList[] = array (
                'name' => $v['name'],
                'anons' => $v['anons'],
                'link' => $this->Link ( $newsYear, $newsMonth, $v['id'] ),
                'date' => DatetimeTools::inclinedDate ( $v['date'] ),
                'text' => $v['text'],
                'image' => $image
            );
        }

        $news_onpage = Tools::getSettings ( __CLASS__, 'onpagenews', 3 );
        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
        $newsList = $news_paged['items'];
        $paging = $news_paged['rendered'];
        unset ( $news_paged );

        $vars = array (
            'title' => 'Новости за ' . mb_strtolower ( $this->monthes[$this->month - 1] ) . ' ' . $this->year,
            'news' => $newsList,
            'paging' => $paging,
            'link' => $this->Link (),
        );
        $GLOBALS['sidebar'] = $this->Menu ();

        $content = tpl ( 'modules/' . __CLASS__ . '/bloglist', $vars );

        return tpl ( 'bloglist', array (
            'title' => $vars['title'],
            'name' => $vars['title'],
            'text' => $content
            ) );
    }

    private function YearNews ()
    {
        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );

        if ( empty ( $news ) )
            page404 ();

        foreach ( $news as $k => $v )
        {
            $parsedDate = strtotime ( $v['date'] );
            $newsYear = date ( 'Y', $parsedDate );
            if ( $newsYear == $this->year )
            {
                $newsMonth = date ( 'm', $parsedDate );
                break;
            }
        }

        if ( !isset ( $newsMonth ) )
            page404 ();

        header ( 'Location: ' . $this->Link ( $this->year, $newsMonth ) );
    }

    private function MainPage ()
    {
        return $this->allNews ();
        /*
          $news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');

          if(empty($news)) return tpl('page', array('title'=>'Новости', 'name'=>'Новости', 'text'=>tpl('modules/'.__CLASS__.'/nonews')));

          $lastNews = current($news);

          $parsedDate = strtotime($lastNews['date']);
          $newsYear = date('Y',$parsedDate);
          $newsMonth = date('m',$parsedDate);

          header('Location: '.$this->Link($newsYear, $newsMonth));
         *
         */
    }

    private function allNews ()
    {
        $news = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );
        if ( empty ( $news ) )
        {
            return tpl ( 'bloglist', array (
                'title' => 'Новости',
                'name' => 'Новости',
                'text' => tpl ( 'modules/' . __CLASS__ . '/nonews' )
                ) );
        }

        foreach ( $news as $k => $v )
        {
            $imageSource = '/images/default.png'; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $imgs = $this->data->GetData ( 'images', " AND `module_id` = {$v['id']} AND `main` = 'Y' AND `module` = 'Blog'" );
            if ( $imgs )
            {
                foreach ( $imgs as $arr )
                {
                    $imageSource = $arr['src'];
                    //берём только первую
                    break;
                }
            }

            $parsedDate = strtotime ( $v['date'] );
            $newsYear = date ( 'Y', $parsedDate );
            $newsMonth = date ( 'm', $parsedDate );

            $newsList[] = array (
                'name' => $v['name'],
                'anons' => $v['anons'],
                'link' => $this->Link ( $newsYear, $newsMonth, $v['id'] ),
                'date' => DatetimeTools::inclinedDate ( $v['date'] ),
                'text' => $v['text'],
                'img' => $imageSource
            );
        }
        //Пэйджинг
        $news_onpage = Tools::getSettings ( __CLASS__, 'onpagenews', 3 );
        $show_pages = Tools::getSettings ( __CLASS__, 'show_pagenews', 5 );
        $news_paged = Paging ( $newsList, $news_onpage, $show_pages );
        $newsList = $news_paged['items'];
        $paging = $news_paged['rendered'];
        unset ( $news_paged );

        $GLOBALS['sidebar'] = $this->Menu ();

        $vars = array (
            'name' => 'Все новости',
            'title' => 'Новости',
            'news' => $newsList,
            'link' => $this->Link (),
            'paging' => $paging
        );
        $content = tpl ( 'modules/' . __CLASS__ . '/bloglist', $vars );
        return tpl ( 'page', array (
            'title' => $vars['title'],
            'name' => $vars['title'],
            'paging' => $paging,
            'text' => $content
            ) );
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

    function LastNewsBlock ($topic = null, $limit = null)
    {
        if($limit)
        {
            $count = $limit;
        }
        else
        {
            $count = Tools::getSettings ( 'News', 'last_news' );
            if ( !$count )
                $count = 3;
        }

        $link = Starter::app ()->content->getLinkByModule ( __CLASS__ );
        $title = 'Новости';
        if($topic)
        {
            $topic = SqlTools::selectObject("SELECT * FROM `prefix_blog_topics` WHERE `id` = '".(int)$topic."'");
            $link = Starter::app ()->content->getLinkByModule ( __CLASS__ ) . '?topic=' . (int)$topic->id;
            $title = $topic->name;
            $where = " AND `topic` = '".$topic->id."' ";
        }

        $queryNews = "SELECT * FROM `prefix_blog` WHERE `deleted`='N' ".$where." ORDER BY `created` DESC LIMIT " . ( int ) $count;
        $newsList = SqlTools::selectObjects ( $queryNews, null, "id" );
        if ( count ( $newsList ) === 0 )
            return "";

        $ids = ArrayTools::numberList ( array_keys ( $newsList ) );
        $images = SqlTools::selectObjects ( "SELECT * FROM `prefix_images` WHERE `main`= 'Y' AND `module`='Blog' AND `module_id` IN ($ids)", null, "module_id" );


        foreach ( $newsList as $k => $news )
        {
            $parsedDate = strtotime ( $news->date );
            $news->date = date ( 'Y-m-d', $parsedDate );
            $newsYear = date ( 'Y', $parsedDate );
            $newsMonth = date ( 'm', $parsedDate );

            $news->link = '/news/' . $news->id;
            $news->image = array_key_exists ( $news->id, $images ) ? $images[$news->id] : null;
        }

        return tpl ( 'modules/'. __CLASS__ .'/lastnewsblock', array (
            'link' => $link,
            'title' => $title,
            'news' => $newsList
            ) );
    }

    function BlockMain ( $limit = 5 )
    {
        $rows = SqlTools::selectRows ( "SELECT * FROM `prefix_blog` WHERE `show`='Y' AND `deleted`='N' ORDER BY `date` DESC, `id` DESC LIMIT {$limit}" );
        return tpl ( 'modules/'. __CLASS__ .'/blockmain', array ( 'items' => $rows ) );
    }
}
