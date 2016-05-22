<?php

class Articles extends CmsModule
{
    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
        $this->defaultController = 'article';
        $this->template = "page";
        Starter::import ( "Articles.controllers.*" );
        Starter::import ( "Articles.models.*" );
        $this->actions =
        [
            'default' =>
            [
                'method' => 'articleList'
            ],
            'articles' =>
            [
                'method' => 'articleList'
            ],
            'article' =>
            [
                'method' => 'articleView'
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
        //$method = $this->path['method'];
        return $this->$method ( $params );
    }

    public function beforeRender ()
    {
        if ( parent::beforeRender () )
        {
            $header = Starter::app ()->headManager;

            return true;
        }
        else
            return false;

    }

    private function articleView ( $params )
    {
        $this->template = "page";
        $manager = new ArticleManager ();
        $article = ArrayTools::head ( $manager->find ( $params ) );
        $article->date = DatetimeTools::inclinedDate ( $article->date );
        $this->title = $article->title;
        $this->model = "article";

        return $this->render ( "article", [ 'article' => $article ] );
    }

    private function articleList ( $params )
    {
        $this->template = "page";
        $manager = new ArticleManager ();
        $articles = ArrayTools::head ( $manager->find ( $params ) );
        $this->title = $article->title;
//        $this->model = "article";

        return $this->render ( "article", [ 'article' => $article ] );
    }

    private function MainPage ()
    {
        $sql = "SELECT a.* FROM `prefix_" . $this->table . "` AS a WHERE a.`date` <= NOW() AND a.`deleted` = 'N' AND a.`show` = 'Y' ORDER BY a.`date` DESC";
        $articles = SqlTools::selectRows ( $sql, MYSQL_ASSOC );

        if ( empty ( $articles ) )
        {
            return tpl ( 'page', array (
                'title' => 'Статьи - ' . Starter::app ()->title,
                'name' => 'Статьи',
                'text' => tpl ( 'modules/' . __CLASS__ . '/noarticles' ) ) );
        }

        foreach ( $articles as $k => $v )
        {
            $imageSource = '/images/default.png'; // картинка по умолчанию
            // извлекаем привязанные картинки из prefix_images
            $imgs = $this->data->GetData ( 'images', " AND `module_id` = {$v['id']} AND `main` = 'Y' AND `module` = 'Articles'" );
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
            $articlesYear = date ( 'Y', $parsedDate );
            $articlesMonth = date ( 'm', $parsedDate );

            $articlesList[] = array (
                'name' => $v['name'],
                'anons' => $v['anons'],
                'link' => $this->Link ( $articlesYear, $articlesMonth, $v['id'] ),
                'date' => DatetimeTools::inclinedDate ( $v['date'] ),
                'text' => $v['text'],
                'img' => $imageSource
            );
        }
        //Пэйджинг
        $articles_onpage = Tools::getSettings ( __CLASS__, 'onpage', 3 );
        $show_pages = Tools::getSettings ( __CLASS__, 'show_pages', 5 );
        $articles_paged = Paging ( $articlesList, $articles_onpage, $show_pages );
        $articlesList = $articles_paged['items'];
        $paging = $articles_paged['rendered'];
        unset ( $articles_paged );

        $GLOBALS['sidebar'] = $this->Menu ();

        $vars = array (
            'name' => 'Cтатьи',
            'title' => 'Статьи - ' . Starter::app ()->title,
            'articles' => $articlesList,
            'link' => $this->Link (),
            'paging' => $paging
        );
        $content = tpl ( 'modules/' . __CLASS__ . '/articles', $vars );
        return tpl ( 'page', array (
            'title' => $vars['title'],
            'name' => $vars['name'],
            'text' => $content
            ) );
    }

    function Link ( $year = 0, $month = 0, $id = 0 )
    {
        if ( $month < 10 )
            $month = '0' . ( int ) $month;
        return '/' . implode ( '/', Starter::app ()->urlManager->linkPath ) . '/' . ($year == 0 ? '' : ($year . '/' . ($month == 0 ? '' : $month . '/' . ($id == 0 ? '' : $id))));
    }

    function Menu ()
    {
        $select = '';
        $join = '';
        $where = '';
//        if ( _REGION !== null )
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (a.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//                . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $sql = "SELECT a.*" . $select
            . " FROM `prefix_" . $this->table . "` AS a"
            . $join
            . " WHERE a.`date` <= NOW() AND a.`deleted` = 'N' AND a.`show` = 'Y'" . $where
            . " ORDER BY a.`date` DESC";
        $articles = SqlTools::selectRows ( $sql, MYSQL_ASSOC );

        //$articles = $this->data->GetData ( $this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC' );

        $menu = array ();

        foreach ( $articles as $k => $v )
        {

            $parsedDate = strtotime ( $v['date'] );
            $articlesYear = date ( 'Y', $parsedDate );
            $articlesMonth = date ( 'm', $parsedDate );
            $articlesDay = ( int ) date ( 'd', $parsedDate );

            if ( $articlesMonth == $this->month )
                $active = true;
            else
                $active = false;

            $menu[$articlesYear][$articlesMonth] = array (
                'number' => $articlesMonth,
                'name' => $this->monthes[$articlesMonth - 1],
                'link' => $this->Link ( $articlesYear, $articlesMonth ),
                'active' => $active
            );
        }

        return tpl ( 'modules/' . __CLASS__ . '/menu', array ( 'menu' => $menu ) );
    }
}
