<?php

/**
 * @todo заменить topic на category
 * @todo заменить top на parentId
 */
class Portfolio extends Component
{
    private $data;
    private $seo;
    public $path, $topic, $product, $menuOpened;
    private $localCacheLink, $productsFilter, $currentTopicTypes;
    private $alter_pages = array (
        'seen' => array ( 'method' => 'Seen', 'name' => 'Вы смотрели' ),
        'search' => array ( 'method' => 'Search', 'name' => 'Поиск по каталогу' ),
        'compare' => array ( 'method' => 'Compare', 'name' => 'Сравнение товаров' )
    );

    /**
     * Конструктор модуля
     */
    function __construct ()
    {
        if ( !session_id () )
        {
            session_start ();
        }
        $this->data = Starter::app ()->data;
    }

    /**
     * Метод вызываемый для работы модуля
     */
    public function Run ()
    {
        $this->buildModulePath ();
        return $this->startController ();
    }

    /**
     * SEO для каталога
     * @param int $id
     */
    private function seo ( $table, $id, $module = __CLASS__ )
    {
        $header = Starter::app ()->headManager;
        $sql = "SELECT `title`, `keywords`, `description` "
            . "FROM `prefix_seo` "
            . "WHERE `module`='" . $module . "' "
            . "AND `module_id`=" . ( int ) $id . " "
            . "AND `module_table`='" . $table . "' "
            . "AND (`title`!='' OR `keywords`!='' OR `description`!='')";
        $this->seo = SqlTools::selectRow ( $sql, MYSQL_ASSOC );
        if ( !empty ( $this->seo ) )
        {
            //Keywords
            if ( !empty ( $this->seo['keywords'] ) )
            {
                $header->addMetaText ("<meta name='keywords' content='" . htmlspecialchars ( $this->seo['keywords'] ) . "' />");
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
                $this->seo['title'] = Sterter::app ()->title . ( $this->currentDocument ? "  — " . $this->currentDocument['name'] : "" );
            }
            $header->setTitle ( $this->seo['title'] );
        }
    }

    /**
     * Создает и проверяет путь модуля на валидность
     * заполняет необходимые для работы модуля переменные
     * $this->currentDocument, $this->path, $this->topic, $this->product
     *
     * При случае неверной адресации отдает 404
     *
     * Валидный адрес:
     * [группа](/.../[группа](/[товар]))
     *
     */
    private function buildModulePath ()
    {
        $request = strpos ( $_SERVER['REQUEST_URI'], '?' ) !== false ? substr ( $_SERVER['REQUEST_URI'], 0, strpos ( $_SERVER['REQUEST_URI'], '?' ) ) : $_SERVER['REQUEST_URI'];

        //Выталкивает линк на текущий модуль из стека линков (запроса)
        $mlink = trim ( str_replace ( Starter::app ()->content->getLinkById ( $this->currentDocument->id ), '', $request ) );
        $pathStack = array_filter ( explode ( '/', $mlink ) );
        //Альтернативные страницы
        //todo посмотреть аналоги кода в других модулях и перенести в родительский объект
        $check_alter = current ( $pathStack );
        if ( $check_alter && array_key_exists ( $check_alter, $this->alter_pages ) )
        {
            if ( !method_exists ( $this, $this->alter_pages[$check_alter]['method'] ) )
                page404 ();

            $params_path = array ();
            $params_path[] = array (
                'type' => 'alter_page',
                'method' => $this->alter_pages[$check_alter]['method'],
                'data' => $check_alter
            );

            $skip = false;
            foreach ( $pathStack as $ppath )
            {
                if ( !$skip )
                {
                    $skip = true;
                    continue;
                }
                $params_path[] = array (
                    'type' => 'param',
                    'data' => $ppath
                );
            }
            $this->path = $params_path;
            return true;
        }

        $topics = $this->data->GetData ( 'portfolio_topics', "AND `show` = 'Y'" );
        $topicsByTop = array ();
        foreach ( $topics as $topic )
            $topicsByTop[$topic['top']][] = $topic;

        $top = 0;
        $path = array ();
        //По пути модуля
        // exit(var_dump($topicsByTop));

        foreach ( $pathStack as $k => $chunk )
        {
            //По топикам
            if ( isset ( $topicsByTop[$top] ) )
                foreach ( $topicsByTop[$top] as $topic )
                {
                    if ( $topic['nav'] == $chunk )
                    {
                        $path[$k]['type'] = 'topic';
                        $path[$k]['data'] = $topic;
                        $this->topic = $topic;
                        $top = $topic['id'];
                        unset ( $pathStack[$k] );
                        break;
                    }
                }
        }

        //Если остался неизвестный трэшняк, то сори, 404
        if ( count ( $pathStack ) > 1 )
            page404 ();

        //А так это возможно товар
        if ( count ( $pathStack ) == 1 )
        {
            $product = 1; //current( $pathStack );
            $curTopic = end ( $path );
            $curTopic = $curTopic['data'];
            $curProduct = end ( $pathStack );
            if ( is_numeric ( $curProduct ) )
                $product_sql = "AND `id` = " . ( int ) $curProduct;
            else
                $product_sql = "AND `nav` = '" . SqlTools::escapeString ( $curProduct ) . "'";

            $productData = SqlTools::selectRows ( "SELECT * FROM `prefix_portfolio` WHERE `deleted`='N' AND `show`='Y' AND `top`=" . ( int ) $curTopic['id'] . " $product_sql", MYSQL_ASSOC );

            $productCount = count ( $productData );
            if ( $productCount == 0 )
                page404 ();
            elseif ( $productCount > 1 )
                $productData = current ( $productData );
            else
                $productData = current ( $productData );

            $this->product = $productData;
            /*
              $path = array(array (
              'type' => 'product',
              'data' => $productData
              ));
             */
            $path[] = array (
                'type' => 'portfolio',
                'data' => $productData
            );
        }

        $this->path = $path;
        return true;
    }

    /**
     * Запуск нужного метода модуля
     */
    private function startController ()
    {
        //Альтернативные страницы
        $check_alter = current ( $this->path );
        if ( $check_alter['type'] == 'alter_page' )
            return $this->$check_alter['method'] ();

        $last = end ( $this->path );
        if ( empty ( $this->path ) )
            return $this->MainPage (); // Главная страница портфолио
        elseif ( $last['type'] == 'portfolio' )
            return $this->Project (); // Проект
        else
            return $this->CategoryPage ( $this->topic['id'] ); // Категория
    }

    /**
     * Главная страница портфолио
     */
    private function MainPage ()
    {
        $this->seo ( 'content', $this->currentDocument->id, 'Content' );

        $categories = $this->getCatalogTree ();
        $projects = array ();

        foreach ( $categories as $cat )
        {
            $projects[$cat->id] = $this->ProjectsList ( $cat->id );
        }

        return tpl ( 'modules/' . __CLASS__ . '/mainpage', array (
            'name' => $this->currentDocument->title,
            'text' => $this->currentDocument->html,
            'anons' => nl2br ( $this->currentDocument->anons ),
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $this->currentDocument->title . ' — ' . Sterter::app ()->title,
            'categories' => $categories,
            'projects' => $projects,
        ) );
    }

    /**
     * Список проектов из категории
     */
    private function ProjectsList ( $topic_id )
    {
        $query = "SELECT *, p.`id` AS proj_id FROM `prefix_portfolio` AS p"
            . " LEFT JOIN `prefix_images` As i ON (i.module_id=p.id AND i.module='Portfolio' AND i.`main`='Y')"
            . " WHERE p.`top`='" . $topic_id . "' AND p.`deleted`='N' AND p.`show`='Y'";
        $projects = SqlTools::selectRows ( $query, MYSQL_ASSOC );

        foreach ( $projects as $k => $project )
        {
            $projects[$k]['link'] = $this->Link ( $project['top'], $project['nav'] ? $project['nav'] : $project['proj_id']  );
        }
        return $projects;
    }

    /**
     * Страинца категории
     */
    private function CategoryPage ( $topic_id )
    {
        $this->seo ( 'content', $this->currentDocument->id, 'Portfolio' );

        $query = "
            SELECT p.id AS proj_id, p.*, i.*
            FROM `prefix_portfolio` AS p
            LEFT JOIN `prefix_images` As i ON (i.module_id=p.id AND i.module='Portfolio' AND i.`main`='Y')
            WHERE p.`top`='" . $topic_id . "' AND p.`deleted`='N' AND p.`show`='Y'";

        $projects = SqlTools::selectRows ( $query, MYSQL_ASSOC );

        foreach ( $projects as $k => $project )
        {
            $projects[$k]['link'] = $this->Link ( $project['top'], $project['nav'] ? $project['nav'] : $project['proj_id']  );
        }

        $query = "SELECT * FROM `prefix_portfolio_topics` WHERE `id`='" . $topic_id . "' LIMIT 1";
        $category = SqlTools::selectRow ( $query, MYSQL_ASSOC );

        return tpl ( 'modules/' . __CLASS__ . '/list', array (
            'name' => $this->currentDocument->title,
            'text' => $this->currentDocument->html,
            'anons' => nl2br ( $this->currentDocument->anons ),
            'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $this->currentDocument->title . ' — ' . Sterter::app ()->title,
            'category' => $category,
            'projects' => $projects,
            ) );
    }

    /**
     *
     * @param type $topic_id
     * @param type $product_id
     * @param array $topicsByTop
     * @param type $topics
     * @param type $linkById
     * @return type
     */
    function Link ( $topic_id = 0, $product_id = 0 )
    {
        $topic_id = ( int ) $topic_id;

        if ( $topic_id != 0 || $product_id != 0 )
        {
            $topics = $this->data->GetData ( 'portfolio_topics', "AND `show` = 'Y'" );

            if ( $product_id !== 0 )
            {
                if ( $topic_id == 0 )
                {
                    $product = $this->data->GetDataById ( 'portfolio', $product_id );
                    $topic_id = $product['top'];
                }
                $product_link = '/' . $product_id;
            }
            else
                $product_link = '';

            $topicsByTop = array ();
            foreach ( $topics as $i )
            {
                $topicsByTop[$i['top']][$i['id']] = $i;
            }

            $linkById = function($topic_id, $topicsByTop, $topics, $linkById)
            {
                if ( $topic_id == 0 )
                    return;

                return $linkById ( $topics[$topic_id]['top'], $topicsByTop, $topics, $linkById ) . '/' . $topics[$topic_id]['nav'];
            };

            $topic_link = $linkById ( $topic_id, $topicsByTop, $topics, $linkById );
            $prepared_link = $topic_link . $product_link;
        }

        if ( empty ( $this->localCacheLink ) )
            $this->localCacheLink = Starter::app ()->content->getLinkByModule ( __CLASS__ );

        return $this->localCacheLink . $prepared_link;
    }

    /**
     * Массив для хлебных крошек модуля
     *
     * array( array('name','link') )
     */
    public function breadCrumbs ()
    {
        if ( empty ( $this->path ) )
        {
            $this->buildModulePath ();
        }
        if ( !empty ( $this->path ) )
        {
            $ret = array ();
            foreach ( $this->path as $i )
            {
                if ( $i['type'] == 'alter_page' )
                {
                    $ret[] = array (
                        'name' => $this->alter_pages[$i['data']]['name'],
                        'link' => Starter::app ()->content->getLinkByModule ( 'Catalog' ) . '/' . $i['data']
                    );
                    break;
                }
                $link = '';
                if ( $i['type'] == 'topic' )
                    $link = $this->Link ( $i['data']['id'] );
                if ( $i['type'] == 'product' )
                    $link = $this->Link ( $i['data']['top'], $i['data']['id'] );

                $ret[] = array ( 'name' => $i['data']['name'], 'link' => $link );
            }

            return $ret;
        }
        else
            return array ();
    }

    public function breadCrumbsCategory ( $arr )
    {
        if ( count ( $arr ) <= 2 )
            $crumbscats = array ();

        if ( isset ( $arr[3] ) )
        {
            $crumbs = explode ( "/", substr ( $arr[3]['link'], 1 ) );
            $crumbscats = SqlTools::selectRows ( "SELECT b.`id`, b.`name` as `name`, b.`nav` as `nav2`, a.`nav` as `nav1` FROM  `prefix_products_topics` AS a
                JOIN  `prefix_products_topics` AS b ON a.id = b.top WHERE ( a.`deleted`='N') AND ( b.`deleted`='N') AND ( a.`nav`='" . $crumbs[2] . "') ORDER BY b.`id`" );

            foreach ( $crumbscats as $k => $crumbscat )
            {
                $crumbscats[$k]['link'] = $crumbscat['nav1'] . '/' . $crumbscat['nav2'];
                $imgSrc = SqlTools::selectValue ( "SELECT `src` FROM  `prefix_images` WHERE ( `module`='Topic') AND ( `module_id`='" . $crumbscat['id'] . "') AND ( `main`='Y')" );
                $crumbscats[$k]['img'] = $imgSrc;
            }
        }

        if ( isset ( $arr[4] ) )
        {
            $crumbs = explode ( "/", substr ( $arr[4]['link'], 1, strlen ( $arr[4]['link'] ) ) );
            $crumbscats = SqlTools::selectRows ( "SELECT b.`id`, b.`name` as `name`, b.`nav` as `nav2`, a.`nav` as `nav1` FROM  `prefix_products_topics` AS a
                JOIN  `prefix_products_topics` AS b ON a.id=b.top WHERE ( a.`deleted`='N') AND ( b.`deleted`='N') AND ( a.`nav`='" . $crumbs[2] . "') ORDER BY b.`id`" );

            foreach ( $crumbscats as $k => $crumbscat )
            {
                $crumbscats[$k]['link'] = $crumbscat['nav1'] . '/' . $crumbscat['nav2'];
                $imgSrc = SqlTools::selectValue ( "SELECT `src` FROM  `prefix_images` WHERE ( `module`='Topic') AND ( `module_id`='" . $crumbscat['id'] . "') AND ( `main`='Y')" );
                $crumbscats[$k]['img'] = $imgSrc;
            }
        }

        return ($crumbscats);
    }

    /**
     *
     * @param type $products
     * @return type
     * @todo refactoring to OOP
     */
    function Paging ( $products )
    {
        $products_count = count ( $products );
        $products_onpage = Tools::getSettings ( 'Catalog', 'onpage', 8 );
        $pages_count = ceil ( $products_count / $products_onpage );
        if ( isset ( $_GET['page'] ) )
            $page_current = abs ( ( int ) $_GET['page'] );
        else
            $page_current = 1;
        $products_from = ($page_current - 1) * $products_onpage;

        if ( $products_count <= $products_onpage )
        {
            $rendered = '';
        }
        else
        {
            $rendered = tpl ( 'modules/' . __CLASS__ . '/paging', array (
                'pages_count' => $pages_count,
                'page_current' => $page_current,
                'products_count' => $products_count,
                'products_from' => $products_from + 1,
                'products_to' => $products_onpage * $page_current > $products_count ? $products_count : $products_onpage * $page_current,
            ) );
        }

        $url = "?limit=" . $products_onpage . "&page=1";
        $url_page = "?limit=" . $products_onpage . "&page=";
        $count_show_pages = Tools::getSettings ( 'Catalog', 'show_pages', 8 );



        $left = $page_current - 1;
        $right = $pages_count - $page_current;
        if ( $left < floor ( $count_show_pages / 2 ) )
            $start = 1;
        else
            $start = $page_current - floor ( $count_show_pages / 2 );
        $end = $start + $count_show_pages - 1;
        if ( $end > $pages_count )
        {
            $start -= ($end - $pages_count);
            $end = $pages_count;
            if ( $start < 1 )
                $start = 1;
        }


        $rendered = tpl ( 'modules/' . __CLASS__ . '/paging', array (
            'pages_count' => $pages_count,
            'page_current' => $page_current,
            'products_count' => $products_count,
            'products_from' => $products_from + 1,
            'products_to' => $products_onpage * $page_current > $products_count ? $products_count : $products_onpage * $page_current,
            'products_onpage' => $products_onpage,
            'url' => $url,
            'url_page' => $url_page,
            'count_show_pages' => $count_show_pages,
            'start' => $start,
            'end' => $end,
        ) );


        $this->paging_rendered = $rendered;

        return array (
            'products' => array_slice ( $products, $products_from, $products_onpage ),
            'rendered' => $rendered
        );
    }

    function GetPaging ()
    {
        if ( isset ( $this->paging_rendered ) )
        {
            return $this->paging_rendered;
        }
        else
        {
            return false;
        }
    }

    function Project ()
    {
        // SEO
        $this->seo ( 'portfolio', $this->product['id'] );

        // Проект
        $query_project = "SELECT p.*
            FROM `prefix_portfolio` AS p
            WHERE p.`deleted`='N' AND p.`show`='Y' AND p.`id`='" . $this->product['id'] . "'
            LIMIT 1";
        $project = SqlTools::SelectRow ( $query_project, MYSQL_ASSOC );

        $query_image = "SELECT *
            FROM `prefix_portfolio_images` AS pi
            LEFT JOIN `prefix_images` AS i ON ( i.`id`=pi.`image_id` )
            WHERE i.`module`='Portfolio' AND i.`module_id`='" . $project['id'] . "'
            ORDER BY i.`main`, pi.`order`";

        $project['images'] = SqlTools::SelectRows ( $query_image, MYSQL_ASSOC );

        // Категория
        $query_topic = "SELECT *
            FROM `prefix_portfolio_topics`
            WHERE `id`='" . $project['top'] . "'
            LIMIT 1";
        $topic = SqlTools::selectRow ( $query_topic, MYSQL_ASSOC );

        // Следующий проект
        $query_next = "SELECT p.*
            FROM `prefix_portfolio` AS p
            WHERE p.`id`=(SELECT MIN(pn.`id`)
                FROM `prefix_portfolio` AS pn
                WHERE pn.`id`>'" . $project['id'] . "' AND pn.`top`='" . $topic['id'] . "' AND `deleted`='N' AND `show`='Y')";

        $project_next = SqlTools::SelectRow ( $query_next, MYSQL_ASSOC );
        $project_next = ($project_next !== false) ? $this->Link ( $project_next['top'], $project_next['nav'] ? $project_next['nav'] : $project_next['id']  ) : null;

        // Предыдущий проект
        $query_previous = "SELECT p.*
            FROM `prefix_portfolio` AS p
            WHERE p.`id`=(SELECT MAX(pp.`id`)
                FROM `prefix_portfolio` AS pp
                WHERE pp.`id`<'" . $project['id'] . "' AND pp.`top`='" . $topic['id'] . "' AND `deleted`='N' AND `show`='Y')";

        $project_previous = SqlTools::SelectRow ( $query_previous, MYSQL_ASSOC );
        $project_previous = ($project_previous !== false) ? $this->Link ( $project_previous['top'], $project_previous['nav'] ? $project_previous['nav'] : $project_previous['id']  ) : null;

        // Генерация заголовка страницы (meta title)
        if ( isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) )
        {
            $head_title = $this->seo['title'];
        }
        else
        {
            $backpath = array_reverse ( $this->path );
            $head_title = array ();
            $head_title[] = $project['name'];
            foreach ( $backpath as $i )
            {
                if ( $i['type'] == 'topic' )
                {
                    $head_title[] = $i['data']['name'];
                }
            }
            $head_title = implode ( ' — ', $head_title ) . ' — ' . Sterter::app ()->title;
        }

        return tpl ( 'modules/' . __CLASS__ . '/one', array (
            'title' => $head_title,
            'name' => $this->currentDocument->title,
            'text' => $this->currentDocument->html,
            'anons' => nl2br ( $this->currentDocument->anons ),
            'project' => $project,
            'project_previous' => (isset ( $project_previous ) && !empty ( $project_previous )) ? $project_previous : null,
            'project_next' => (isset ( $project_next ) && !empty ( $project_next )) ? $project_next : null,
        ) );
    }

    public function getFullCatalog ()
    {
        $tree = array ();
        $cat = new stdClass();
        $cat->subCategories = array ();
        $cat->name = "zero";
        $tree[] = $cat;
        $cat1 = new stdClass();
        $cat1->subCategories = array ();
        $cat1->name = "one";
        $cat->subCategories[] = $cat1;
        $cat2 = new stdClass();
        $cat2->subCategories = array ();
        $cat2->name = "two";
        $cat1->subCategories[] = $cat2;
        $cat3 = new stdClass();
        $cat3->subCategories = array ();
        $cat3->name = "three";
        $tree[] = $cat3;

        return $this->getCatalogTree ();
    }

    public function getCatalogTree ( $id = null )
    {
        $categories = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_portfolio_topics` WHERE `deleted`='N' AND `show`='Y'" ), "id" );
        $productsInTop = ArrayTools::index ( SqlTools::selectObjects ( "SELECT `top`, count(`top`) AS `count` FROM `prefix_portfolio` WHERE `deleted`='N' AND `show`='Y' GROUP BY `top`" ), "top" );
        $categoriesTree = array ();
        foreach ( $categories as $category )
        {
            $productsCount = array_key_exists ( $category->id, $productsInTop ) ? $productsInTop[$category->id] : null;
            $category->productsCount = $productsCount && $productsCount->count ? $productsCount->count : 0;
            $category->link = $this->Link ( $category->id );
            $category->subCategories = array ();
            if ( $category->top == 0 )
                $categoriesTree[$category->id] = $category;
            else
            {
                $parentCategory = $categories[$category->top]; /*
                  if ( is_null( $parentTopic->subCategories ) )
                  $parentCategory->subCategories = array ( ); */

                $parentCategory->subCategories[$category->id] = $category;
            }
        }

        if ( $id && is_numeric ( $id ) )
        {
            if ( array_key_exists ( $id, $categories ) )
                $categoriesBranch = $categories[$id]->subCategories;
            else
                $categoriesBranch = array ();

            return $categoriesBranch;
        }
        else
            return $categoriesTree;
    }

    public function Block ( $cat_id = null, $template = null, $limit = null )
    {
        $projects = array ();
        $categories = $this->getCatalogTree ( $cat_id );

        $title = SqlTools::selectValue ( "SELECT name FROM `prefix_portfolio_topics` WHERE `id` = '" . $cat_id . "' AND `deleted`='N' AND `show`='Y'" );

        $sql = " ORDER BY RAND()";

        if ( !empty ( $categories ) )
        {
            foreach ( $categories as $cat )
            {
                $query = "SELECT p.`id` AS proj_id, p.`name` AS name, p.`anons` AS anons, p.`text` AS text, t.`name` AS category, i.`src` AS src "
                    . "FROM `prefix_portfolio` AS p"
                    . " LEFT JOIN `prefix_images` AS i ON (i.module_id=p.id AND i.module='Portfolio' AND i.`main`='Y')"
                    . " LEFT JOIN `prefix_portfolio_topics` AS t ON (p.top=t.id)"
                    . " WHERE p.`top`='" . $cat->id . "' AND p.`deleted`='N' AND p.`show`='Y'" . $sql;
                $result = SqlTools::selectRows ( $query, MYSQL_ASSOC );

                foreach ( $result as $k => $project )
                {
                    $result[$k]['link'] = $this->Link ( $project['top'], $project['nav'] ? $project['nav'] : $project['proj_id']  );
                }
                $projects = array_merge ( $projects, $result );
            }
            if ( $limit )
            {
                $projects = array_slice ( $projects, 0, $limit );
            }
        }
        else
        {
            if ( $limit )
            {
                $sql .= " LIMIT " . $limit;
            }
            $query = "SELECT p.`id` AS proj_id, p.`name` AS name, p.`anons` AS anons, p.`text` AS text, t.`name` AS category, i.`src` AS src "
                . "FROM `prefix_portfolio` AS p"
                . " LEFT JOIN `prefix_images` AS i ON (i.module_id=p.id AND i.module='Portfolio' AND i.`main`='Y')"
                . " LEFT JOIN `prefix_portfolio_topics` AS t ON (p.top=t.id)"
                . " WHERE p.`top`='" . $cat_id . "' AND p.`deleted`='N' AND p.`show`='Y'" . $sql;
            $result = SqlTools::selectRows ( $query, MYSQL_ASSOC );

            foreach ( $result as $k => $project )
            {
                $result[$k]['link'] = $this->Link ( $project['top'], $project['nav'] ? $project['nav'] : $project['proj_id']  );
            }
            $projects = $result;
        }

        return tpl ( 'modules/' . __CLASS__ . '/block' . $template, array (
            'title' => $title,
            'projects' => $projects,
        ) );
    }
}
