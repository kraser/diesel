<?php

/**
 * @todo заменить topic на category
 * @todo заменить top на parentId
 */
class Catalog extends CmsModule
{
    /**
     * <pre>Конструктор модуля Catalog</pre>
     */
    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->data = Starter::app ()->data;
        $this->defaultController = 'categories';
        $this->template = "page";
        Starter::import ( "Catalog.controllers.*" );
        Starter::import ( "Catalog.models.*" );
        $this->actions =
        [
            'default' => [ 'method' => 'categoriesList' ],
            'catlist' => [ 'method' => 'categoriesList' ],
            'category' => [ 'method' => 'categoryView' ],
            'prodlist' => [ 'method' => 'productsList' ],
            'prodcard' => [ 'method' => 'productCard' ],
            'filter' => [ 'method' => 'productsByFilter' ]
        ];

        $this->data = Starter::app ()->data;
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
        {
            $header = Starter::app ()->headManager;

            return true;
        }
        else
            return false;

    }

    /**
     * <p>Возвращает Html-текст основной страницы каталога</p>
     * @return String <p>Html-текст основной страницы каталога</p>
     */
    private function categoriesList ( $searchParam )
    {
        $this->template = "mainpage";
        $manager = new CategoryManager();
        $categories = $manager->getCatalogTree ( $searchParam );
        $this->title = $categories->title;
        return $this->render ( "subcategories", [ "categoriesTree" => $categories ] );
    }


























    public function SubMenu ()
    {
        $topics = SqlTools::selectRows ( "SELECT * FROM `prefix_products_topics` WHERE `top` = '0' AND `deleted` = 'N' AND `show` = 'Y'" );
        return $topics;
    }

    /**
     * <p>Основной метод модуля</p>
     */
//    public function Run ()
//    {
//        $this->buildModulePath ();
//        return $this->startController ();
//    }

    /**
     * <p>Массив (запись) текущей категории товаров</p>
     * @var Array
     */
    private $topic;

    /**
     * <p>Массив элементов-описаний действий</p>
     * @var Array
     */
    private $path;

    /**
     * <pre>Создает и проверяет путь модуля на валидность
     * заполняет необходимые для работы модуля свойства
     * <b>path, topic, product, brand</b>
     *
     * При случае неверной адресации отдает 404
     *
     * Валидный адрес:
     * [группа](/.../[группа](/[товар]))</pre>
     *
     */
    private function buildModulePath ()
    {
        $this->getCurrentDocument ();
        $requestUri = filter_input ( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );
        $request = strpos ( $requestUri, '?' ) !== false ? substr ( $requestUri, 0, strpos ( $requestUri, '?' ) ) : $requestUri;
        $mlink = trim ( str_replace ( Starter::app ()->content->getLinkById ( $this->currentDocument->id ), '', $request ) );
        $pathStack = array_filter ( explode ( '/', $mlink ) );
        //todo посмотреть аналоги кода в других модулях и перенести в родительский объект
        $checked = current ( $pathStack );
        if ( $checked && array_key_exists ( $checked, $this->actions ) )
        {
            if ( !method_exists ( $this, $this->actions[$checked]['method'] ) )
                page404 ();

            $paramsPath[] =
            [
                'type' => 'alterPage',
                'method' => $this->actions[$checked]['method'],
                'data' => $checked
            ];
            $skip = false;
            foreach ( $pathStack as $parameterPath )
            {
                if ( !$skip )
                {
                    $skip = true;
                    continue;
                }
                $paramsPath[] = array ( 'type' => 'param', 'data' => $parameterPath );
            }
            $this->path = $paramsPath;
            return true;
        }

        $topics = $this->findCategories ( array ( "show" => "Y", "deleted" => "N" ) );
        $top = 0;
        $path = array ();
        foreach ( $pathStack as $k => $chunk )
        {
            $categories = ArrayTools::select ( $topics, "parentId", $top );
            $category = ArrayTools::head ( ArrayTools::select ( $categories, "nav", $chunk ) );
            if ( $category )
            {
                $path[$k]['type'] = 'topic';
                $path[$k]['data'] = $category;
                $this->topic = $category;
                $this->currentCategoryId = $category->id;
                $top = $category->id;
                unset ( $pathStack[$k] );
            }
        }

        $this->getChildCategories ();
        $this->getParentCategories ();

        if ( count ( $pathStack ) > 1 && $pathStack[1] == 'brands' )
        {
            $brandFind =
            [
                "deleted" => "N",
                "show" => "Y",
                "nav" => $pathStack[2]
            ];
            $brandData = $this->findBrands ( $brandFind );
            $path[] =
            [
                'type' => 'brands',
                'data' => $brandData
            ];

            $this->path = $path;
            return true;
        }

        //Если остался неизвестный мусор в URI, то 404
        if ( count ( $pathStack ) > 1 )
            page404 ();
        else if ( count ( $pathStack ) == 1 ) //А так это возможно товар
        {
            $productParam = end ( $pathStack );
            $navWhere = "";
            $productFindParam = [ "deleted" => 'N', "show" => 'Y' ];
            if ( is_numeric ( $productParam ) )
                $productFindParam["id"] = ( int ) $productParam;
            else
                $navWhere = "p.`nav`='" . SqlTools::escapeString ( $productParam ) . "'";

            $productData = $this->findProducts ( $productFindParam, $navWhere );
            $productCount = count ( $productData );
            if ( $productCount == 0 )
                page404 ();
            else
                $this->product = ArrayTools::head ( $productData );

            if ( $this->useShortUrl )
            {
                $this->currentCategoryId = $this->product->top;
                $this->topic = $this->product->topic;
                $this->getChildCategories ();
                $this->getParentCategories ();
                $parents = array_reverse ( $this->parentCategories );

                foreach ( $parents as $k => $parent )
                {
                    $category = $topics[$parent];
                    if ( $category )
                    {
                        $path[$k]['type'] = 'topic';
                        $path[$k]['data'] = $category;
                    }
                }
            }

            $path[] = array ( 'type' => 'product', 'data' => $this->product );
        }

        $this->path = $path;
        return true;
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

        $query = "SELECT `id` FROM `prefix_products_topics` WHERE `top`=$categoryId AND `deleted`='N'";
        $childCategories = array_keys ( SqlTools::selectRows ( $query, MYSQL_ASSOC, "id" ) );
        foreach ( $childCategories as $subCatId )
        {
            $moreChildCategories = $this->getChildCategories ( $subCatId, $level + 1 );
            $childCategories = array_merge ( $childCategories, $moreChildCategories );
        }

        if ( $level === 0 && $categoryId == $this->currentCategoryId )
            $this->childCategories = $childCategories;

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
            return;

        $parentCategories = array ();

        while ( $parentCategory )
        {
            $query = "SELECT `top` FROM `prefix_products_topics` WHERE `id`=$parentCategory AND `deleted`='N'";
            $parentCategory = SqlTools::selectValue ( $query );
            if ( $parentCategory )
                $parentCategories[] = $parentCategory;
        }

        $this->parentCategories = $parentCategories;
        return $parentCategories;
    }
    /**
     * <p>Массив SEO-параметров</p>
     * @var Array
     */
    private $seo;

    /**
     * SEO для каталога
     * @param int $id
     */
    private function seo ( $table, $id = 0, $module = __CLASS__ )
    {
        $header = Starter::app ()->headManager;
        $this->seo = SqlTools::selectRow ( "SELECT * FROM `prefix_seo` WHERE `module`='" . $module . "' AND `module_id`=" . ( int ) $id . " AND `module_table`='" . $table . "'", MYSQL_ASSOC );
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
                $this->seo['title'] = trim ( $this->seo['title'] );
            }
            else
            {
                $this->seo['title'] = ( Starter::app ()->title ? Starter::app ()->title . "  — " : "" ) . $this->currentDocument->title;
            }
        }
        else
        {
            $headTitle = array ( $this->moduleName );
            foreach ( $this->path as $path )
            {
                if ( $path['type'] == 'topic' )
                {
                    $headTitle[] = $path['data']->name;
                }
            }

            if ( $this->product )
            {
                $headTitle[] = $this->product->name;
            }

            $this->seo['title'] = ( Starter::app ()->title ? Starter::app ()->title . "  — " : "" ) . implode ( ' — ', $headTitle );
        }
        $header->setTitle ( $this->seo['title'] );
    }

    /**
     * <p>Запуск метода-действия модуля</p>
     * @return String <p>Html-текст</p>
     */
    private function _startController ()
    {
        $alterAction = current ( $this->path );
        if ( $alterAction['type'] == 'alterPage' )
            return $this->$alterAction['method'] ();

        $action = end ( $this->path );
        if ( empty ( $this->path ) )
            return $this->mainPageView (); //Главная каталога
        elseif ( $action['type'] == 'product' )
            return $this->productCardView (); //Товар
        else
            return $this->productsListView (); //Группа (список товаров) с подбором по х-кам
    }

    /**
     * <p>Возвращает Html-текст основной страницы каталога</p>
     * @return String <p>Html-текст основной страницы каталога</p>
     */
    private function mainPageView ()
    {
        $this->seo ( 'content', $this->currentDocument->id, 'Content' );
        $categories = $this->getCatalogTree ( is_null( $this->topic ) ? null : $this->topic->id );
        $categoriesHtml = TemplateEngine::view ( "subcategories", array ( "categoriesTree" => $categories ), __CLASS__, true );
        $tagH1 = !empty ( $this->seo['tagH1'] ) ? $this->seo['tagH1'] : $this->currentDocument->title;

        return TemplateEngine::view ( 'mainpage', array ( 'name' => $tagH1, 'tagH1' => $tagH1, "subCategories" => $categoriesHtml ), __CLASS__ );
    }

    /**
     * <pre>Формирует и возвращает Html-текст страницы с карточкой товара</pre>
     * @return String <p>Html-текст страницы с карточкой товара</p>
     */
    private function productCardView ()
    {
        $this->seo ( 'products', $this->product->id );
        if ( isset ( $_SESSION['seen'][$this->product->id] ) )
        {
            SqlTools::execute ( "UPDATE `prefix_products` SET `rate`=`rate` + 1 WHERE `id`=" . $this->product->id );
            SqlTools::execute ( "UPDATE `prefix_products_topics` SET `rate`=`rate` + 1 WHERE `id`=" . $this->topic->id );
        }

        if ( !isset ( $_SESSION['seen'] ) )
            $_SESSION['seen'] = array ();

        $_SESSION['seen'][$this->product->id] = true;

        $product = $this->product;
        $this->getTaggedFeatures ( array ( $product->id ) );
        $product->features = $this->productsFeatures[$product->id];
        $filesByGroup = files ()->GetFiles ( 'Catalog', $product->id );
        $files = array ();
        foreach ( $filesByGroup as $fileByGroup )
        {
            foreach ( $fileByGroup as $filesH )
            {
                $files[] = $filesH;
            }
        }

        $tagH1 = !empty ( $this->seo['tagH1'] ) ? $this->seo['tagH1'] : $product->name;

        $productCardHtml = TemplateEngine::view ( "productCard", array ( "product" => $product, "tagH1" => $tagH1 ), __CLASS__ );

        return TemplateEngine::view ( 'one', array
        (
            'productHtml' => $productCardHtml,
            'files' => $files,
            'show_comments' => Tools::getSettings ( __CLASS__, 'comments', 'N' ) == 'Y' ? true : false
        ), __CLASS__ );
    }

    /**
     * <p>Выводит страницы списка товаров</p>
     * @return String <p>Текст выводимого списка</p>
     */
    private function productsListView ()
    {
        if ( $this->topic )
        {
            $categoryId = $this->topic->id;
            $this->seo ( 'products_topics', $categoryId );
            $pageTitle = $this->seo["title"] ? : $this->topic->name;
        }
        else
        {
            $categoryId = 0;
            $pageTitle = $this->actions["filter"]["name"];
            Starter::app ()->headManager->setTitle ( Sterter::app ()->title . " — " . $pageTitle );
        }

        $products = $this->getProductsItems ();
        if ( $this->topic->isModel == "Y" )
            $productsHtml = $this->modelView ( $products );
        else
        {
            $models = $this->getModelsItems ();
            $items = array_merge ( $models, $products );
            $productsHtml = $this->productsItemsView ( $items );
        }
        $categoriesTree = $this->getCatalogTree ( $categoryId );
        $categoryHtml = TemplateEngine::view ( "subcategories", array ( "categoriesTree" => $categoriesTree ), __CLASS__, true );
        $tagH1 = !empty ( $this->seo['tagH1'] ) ? $this->seo['tagH1'] : ( $this->topic ? $this->topic->name : $this->actions["filter"]["name"] );

        return TemplateEngine::view ( 'list', array
        (
            "tagH1" => $tagH1,
            "isFilter" => $this->isFilter,
            'name' => $pageTitle,
            'productsItems' => $productsHtml,
            'isProducts' => count ( $this->products ),
            'currentCategory' => $categoriesTree,
            "categoryHtml" => $categoryHtml
        ), __CLASS__ );
    }

    private function modelView ( $products )
    {
        return TemplateEngine::view ( "modelCard", array ( "category" => $this->topic, "products" => $products ), __CLASS__, true );
    }

    /**
     * <pre>Возвращает список товарных позиций, удовлетворяющих условиям фильтра</pre>
     * @return Array <p>Массив-список товарных позиций</p>
     */
    private function getProductsItems ()
    {
        $this->processSorting ();
        $params = array ();
        if ( isset ( $this->sorting ) )
        {
            $sortField = '`' . $this->sorting['field'] . '`';
            if ( $this->sorting['field'] == 'price' )
                $sortField = '(`price` * (1 - `discount` / 100))';

            $params['orderBy'] = "`is_exist`, $sortField " . $this->sorting['direction'];
        }
        else
            $params['orderBy'] = "`order` ASC, `rate` DESC";

        $isFilter = filter_input ( INPUT_POST, "isFilter", FILTER_SANITIZE_NUMBER_INT ) || array_key_exists ( "productsFilter", $_SESSION );
        $isChangedCategory = $this->currentCategoryId && array_key_exists ( "currentCategory", $_SESSION ) && $_SESSION['currentCategory'] != $this->currentCategoryId;
        if ( $isChangedCategory )
            $this->clearSearchParams ();

        $search = $this->search ();

        $_SESSION['currentCategory'] = $this->currentCategoryId;
        if ( $isFilter )
        {
            $isClear = filter_input ( INPUT_POST, "isClear", FILTER_SANITIZE_NUMBER_INT );
            if ( $isClear )
                $this->clearSearchParams ();
            else
            {
                $productsFilter = $this->getFilterParamsFromPost ();
                if ( count ( $productsFilter ) )
                {
                    $_SESSION["productsFilter"] = $productsFilter;
                    $this->filters = $productsFilter;
                    $this->isFilter = true;
                }
                else
                {
                    $this->filters = $_SESSION["productsFilter"];
                    $this->isFilter = count ( $_SESSION["productsFilter"] );
                }
                $this->checkFiltersForClear ();
            }
        }

        if ( $isFilter && $this->isFilter )
        {
            $cats = $this->currentCategoryId ? array_merge ( $this->childCategories, array ( $this->currentCategoryId ) ) : null;
        }
        else
            $cats = $this->currentCategoryId ? array ( $this->currentCategoryId ) : null;

        $this->getTaggedFeatures ();
        $productFilter = $this->fitFilterToCategory ( $this->topic->id );
        $params["deleted"] = "N";
        $params["show"] = "Y";


        if ( array_key_exists ( 'paramFilter', $_SESSION ) && $_SESSION['paramFilter'] )
        {
            $filterParams = $this->filterByParams ( $productFilter );
            $params = array_merge ( $params, $filterParams );
        }
        if ( array_key_exists ( 'tagFilter', $_SESSION ) && $_SESSION['tagFilter'] )
        {
            $tagsClauses = $this->filterByTags ( $productFilter, $cats );
            $params = array_merge ( $params, array ( $tagsClauses ) );
        }

        if ( $cats )
            $params["top"] = $cats;

        $products = $this->findProducts ( $params, $search, "id" );

        return $products;
    }
    /**
     * <pre>html-текст контейнера с пэйджером</pre>
     *
     * @var String
     */
    private $pager;

    /**
     * <pre>Возвращает html-текст списка позиций</pre>
     * @param Array_Of_ProductItem $products <p>Список товарных позиций</p>
     * @return String <p>Html-текст списка позиций</p>
     */
    public function productsItemsView ( $products )
    {
        $productsPerPage = Tools::getSettings ( __CLASS__, 'onpage', 3 );
        $pagerLength = Tools::getSettings ( __CLASS__, 'show_pages', 5 );
        $processedPage = Paging ( $products, $productsPerPage, $pagerLength );
        $productsOnPage = $processedPage['items'];
        $paging = $processedPage['rendered'];
        $this->pager = $paging;
        $productIds = ArrayTools::pluck ( $productsOnPage, "id" );
        Starter::app ()->imager->prepareImages ( 'Catalog', $productIds, false );
        $link = $this->Link ( $this->topic->id );

        $productsItems = TemplateEngine::view ( 'productsItems', array
        (
            "isFilter" => $this->isFilter,
            'link' => $link,
            'exist' => $this->productsFilter['exist'],
            'products' => $productsOnPage,
            'paging' => $paging,
            'currentCategory' => $this->topic,
        ), __CLASS__ );

        return $productsItems;
    }
    /**
     * <pre>Ассоциативный массив с параметрами сортировки продукции
     * <b>field</b> поле сортировки
     * <b>direction</b> направление сортировки
     * <b>html</b> текст контейнера сортировки</pre>
     *
     * @var Array
     */
    private $sorting;

    /**
     * <pre>Создаёт массив параметров для сортировки списка продукции
     * из параметров запроса или по умолчанию</pre>
     * @return Array
     * @throws Exception
     */
    private function processSorting ()
    {
        $urlManager = Starter::app ()->urlManager;
        //Значения по-умолчанию
        try
        {
            $query = "SELECT
                    `type` AS type,
                    `name` AS name,
                    IF(`direction`='Y', 'ASC', 'DESC') AS direction
                FROM `prefix_sorting`
                ORDER BY `order` LIMIT 3";
            $options = SqlTools::selectRows ( $query, MYSQL_ASSOC, 'type' );
            if ( count ( $options ) == 0 )
                throw new Exception ( "Таблица сортировки пуста.", 1 );

            $types = array
                (
                'name' => array ( 'type' => 'name', 'name' => 'Сортировка по наименованию', 'substitute' => 'названию' ),
                'rate' => array ( 'type' => 'rate', 'name' => 'Сортировка по рейтингу', 'substitute' => 'рейтингу' ),
                'price' => array ( 'type' => 'price', 'name' => 'Сортировка по цене', 'substitute' => 'цене' ),
                'shortName' => array ( 'type' => 'shortName', 'name' => 'Сортировка по артикулу', 'substitute' => 'артикулу' )
            );
            $firstIter = true;
            foreach ( $options as $key => $option )
            {
                $option['current'] = $firstIter;
                $firstIter = false;
                $option['name'] = $types[$key]['substitute'];
                $options[$key] = $option;
            }
        }
        catch ( Exception $ex )
        {
            if ( !( $ex->getCode () == 1051 || $ex->getCode () == 1 ) )
                throw $ex;

            $defaultSort = array ();
            //Сортировка по умолчанию
            $sort_type = Tools::getSettings ( __CLASS__, "sort_type", "order" );
            $sort_direct = Tools::getSettings ( __CLASS__, "sort_direct", "ASC" );
            $defaultSort[$sort_type]['name'] = "порядку";
            $defaultSort[$sort_type]['current'] = true;
            $defaultSort[$sort_type]['direction'] = $sort_direct;

            $options = array_merge ( $defaultSort, array
                (
                'name' => array ( 'name' => 'названию', 'current' => false, 'direction' => 'ASC' ),
                'price' => array ( 'name' => 'цене', 'current' => false, 'direction' => 'ASC' ),
                ) );
        }

        //Проставляем ссылки
        foreach ( $options as $field => $sorting )
        {
            $direction = $sorting['direction'];
            if ( $sorting['current'] )
                $options[$field]['link'] = $urlManager->createRequestParameters ( array ( 'page' => false, 'order' => $field, 'orderd' => $direction == 'ASC' ? 'DESC' : 'ASC' ) );
            else
                $options[$field]['link'] = $urlManager->createRequestParameters ( array ( 'page' => false, 'order' => $field, 'orderd' => $direction ) );
        }

        //Переопределения
        $order = $urlManager->getRequestParameter ( "order", null );
        $orderDirection = $urlManager->getRequestParameter ( "orderd" );
        if ( $order && $orderDirection )
        {
            if ( isset ( $options[$order] ) && ($orderDirection == 'ASC' || $orderDirection == 'DESC') )
            {
                $_SESSION['orderd'] = $orderDirection;
                $_SESSION['order'] = $order;
                foreach ( $options as $field => $val )
                {
                    if ( $order == $field )
                    {
                        $options[$field]['current'] = true;
                        $options[$field]['direction'] = $orderDirection;
                        $options[$field]['link'] = $urlManager->createRequestParameters ( array ( 'page' => false, 'order' => $field, 'orderd' => ($orderDirection == 'ASC' ? 'DESC' : 'ASC') ) );
                    }
                    else
                        $options[$field]['current'] = false;
                }
            }
        }
        else if ( array_key_exists ( "order", $_SESSION ) )
        {
            foreach ( $options as $field => $val )
            {
                if ( $_SESSION['order'] == $field )
                {
                    $direction = array_key_exists ( "orderd", $_SESSION ) ? $_SESSION['orderd'] : "ASC";
                    $options[$field]['current'] = true;
                    $options[$field]['direction'] = $direction;
                    $options[$field]['link'] = $urlManager->createRequestParameters ( array ( 'page' => false, 'order' => $field, 'orderd' => ($direction == 'ASC' ? 'DESC' : 'ASC') ) );
                }
                else
                    $options[$field]['current'] = false;
            }
        }
        $this->sorting = array ();
        foreach ( $options as $field => $val )
        {
            if ( $val['current'] )
            {
                $this->sorting['field'] = $field;
                $this->sorting['direction'] = $val['direction'];
                break;
            }
        }

        $model = array ( 'options' => $options );
        $this->sorting['html'] = tpl ( 'modules/' . __CLASS__ . '/sortpanel', $model );

        return $this->sorting['html'];
    }

    /**
     * <p>Возвращает html-текст контейнера с управлением сортировкой</p>
     * @return String
     */
    public function sortPanel ()
    {
        if ( !$this->sorting )
            $this->processSorting ();

        return $this->sorting['html'];
    }

    /**
     * <pre>Возвращает html-текст контейнера с пэйджером</pre>
     * @return String
     */
    public function getPager ()
    {
        return $this->pager;
    }

    private function getModelsItems ()
    {
        $modelIds = array ();
        foreach ( $this->childCategories as $child )
        {
            if ( $this->getChildCategories ( $child ) )
                continue;
            $modelIds[] = $child;
        }
        $params = array
            (
            "show" => "Y",
            "deleted" => "N",
            "isModel" => "Y",
            "parentId" => $this->currentCategoryId,
            "id" => $modelIds
        );
        $models = $this->findCategories ( $params );
        if ( count ( $models ) )
        {
            $paramsItems = array
                (
                "show" => "Y",
                "deleted" => "N",
                "top" => $modelIds,
                "orderBy" => array ( "price" => "ASC" )
            );
            $products = $this->findProducts ( $paramsItems );
            foreach ( $models as $model )
            {
                $product = ArrayTools::head ( ArrayTools::select ( $products, "top", $model->id ) );
                $model->price = $product ? $product->price : null;
            }
        }
        return $models;
    }

    /**
     * <p>Возвращает массив объектов ProductItem</p>
     * @param Array $params <p>Массив параметров для выбора из таблицы прдукции</p>
     * @param String $where <p>Строка дополнительных условий для выбора из таблицы прдукции</p>
     * @param String $indexKey <p>Поле по которому необходимо проиндексировать выходной массив</p>
     * @return Array_Of_ProductItem <p>Список товаров</p>
     */
    public function findProducts ( $params = array (), $where = null, $indexKey = null )
    {
        if ( count ( $params ) )
        {
            $conditions = array ();
            $orderBy = array ();
            $limit = "";
            foreach ( $params as $field => $value )
            {
                switch ( $field )
                {
                    case "id":
                        $conditions[] = "p.`id` IN (" . ArrayTools::numberList ( $value ) . ")";
                        break;
                    case "top":
                        $conditions[] = "p.`top` IN (" . ArrayTools::numberList ( $value ) . ")";
                        break;
                    case "order":
                        $conditions[] = "p.`order` IN (" . ArrayTools::numberList ( $value ) . ")";
                        break;
                    case "name":
                        $conditions[] = "p.`name` IN (" . ArrayTools::stringList ( $value ) . ")";
                        break;
                    case "nav":
                        $conditions[] = "p.`nav` IN (" . ArrayTools::stringList ( $value ) . ")";
                        break;
                    case "brandId":
                        $conditions[] = "p.`brand` IN (" . ArrayTools::numberList ( $value ) . ")";
                        break;
                    case "brandName":
                        $conditions[] = "b.`name` IN (" . ArrayTools::stringList ( $value ) . ")";
                        break;
                    case "valuta":
                        $conditions[] = "p.`currency` IN (" . ArrayTools::numberList ( $value ) . ")";
                        break;
                    case "priceNoLess":
                        $conditions[] = "IF(c.`value`, p.`price`*c.`value`, p.`price`)>=" . floatval ( $value );
                        break;
                    case "priceNoBigger":
                        $conditions[] = "IF(c.`value`, p.`price`*c.`value`, p.`price`)<=" . floatval ( $value );
                        break;
                    case "priceEqual":
                        $conditions[] = "IF(c.`value`, p.`price`*c.`value`, p.`price`)=" . floatval ( $value );
                        break;
                    case "discountedEqual":
                        $conditions[] = "IF(c.`value`, p.`price`*c.`value`, p.`price`)*(1 - p.`discount` / 100)=" . floatval ( $value );
                        break;
                    case "unit":
                        $conditions[] = "p.`unit` IN (" . ArrayTools::stringList ( $value ) . ")";
                        break;
                    case "date":
                        $conditions[] = "p.`date`='" . SqlTools::escapeString ( $value ) . "'";
                        break;
                    case "show":
                        $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                        $conditions[] = "p.`show`='$fieldValue'";
                        break;
                    case "deleted":
                        $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                        $conditions[] = "p.`deleted`='$fieldValue'";
                        break;
                    case "created":
                        $conditions[] = "p.`created`='" . SqlTools::escapeString ( $value ) . "'";
                        break;
                    case "modified":
                        $conditions[] = "p.`modified`='" . SqlTools::escapeString ( $value ) . "'";
                        break;
                    case "anons":
                        $conditions[] = "p.`anons` LIKE '%" . SqlTools::escapeString ( $value ) . "%'";
                        break;
                    case "text":
                        $conditions[] = "p.`text` LIKE '%" . SqlTools::escapeString ( $value ) . "%'";
                        break;
                    case "action":
                        $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                        $conditions[] = "p.`is_action`='$fieldValue'";
                        break;
                    case "featured":
                        $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                        $conditions[] = "p.`is_featured`='$fieldValue'";
                        break;
                    case "lider":
                        $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                        $conditions[] = "p.`is_lider`='$fieldValue'";
                        break;
                    case "exist":
                        $fieldValue = $value == "Y" || $value == "N" ? $value : 'N';
                        $conditions[] = "p.`is_exist`='$fieldValue'";
                        break;
                    case "rate":
                        $conditions[] = "p.`rate`=" . intval ( $value );
                        break;
                    case "rateMore":
                        $conditions[] = "p.`rate`>=" . intval ( $value );
                        break;
                    case "orderBy":
                        if ( !is_array ( $value ) )
                            $orderBy[] = SqlTools::escapeString ( $value );
                        else
                        {
                            foreach ( $value as $ordered => $direcion )
                            {
                                if ( $ordered == 'price' )
                                    $orderField = '(`price` * (1 - `discount` / 100))';
                                else
                                    $orderField = $ordered;

                                $direction = $direcion ? : "ASC";
                                $orderBy[] = "$ordered $direction";
                            }
                        }
                        break;
                    case "limit":
                        $limit = "LIMIT $value";
                    default:
                }
            }

            $whereClause = ( count ( $conditions ) ? "WHERE " . implode ( " AND ", $conditions ) : "" ) . ( $where ? " AND $where" : "" );
            $orderClause = count ( $orderBy ) ? "ORDER BY " . implode ( ", ", $orderBy ) : "";
        }

        $regionField = '';
        $join = '';
//        if ( _REGION !== null )
//        {
//            $regionField .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (p.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//                . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $whereClause .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $query = "
            SELECT
                p.`id` AS id,
                p.`top` AS top,
                p.`order` AS 'order',
                p.`name` AS name,
                p.`shortName` AS shortName,
                p.`nav` AS nav,
                p.`brand` AS brandId,
                IF(c.`value`, p.`price`*c.`value`, p.`price`) AS price,
                IF(c.`value`, p.`price`*c.`value`, p.`price`)*(1 - p.`discount` / 100) AS discountPrice,
                p.`currency` AS valutaId,
                p.`unit` AS unit,
                p.`date` AS date,
                p.`show` AS view,
                p.`deleted` AS deleted,
                p.`created` AS created,
                p.`modified` AS modified,
                p.`anons` AS anons,
                p.`text` AS text,
                p.`types` AS types,
                p.`is_action` AS isAction,
                p.`is_featured` AS isFeatured,
                p.`is_lider` AS isLider,
                p.`is_exist` AS isExists,
                p.`availability` AS availability,
                p.`relations` AS relations,
                p.`rate` AS rate,
                p.`discount` AS discount,
                p.`noIndex` AS noIndex
                $regionField
            FROM `prefix_products` AS p
            LEFT JOIN `prefix_products_topics` AS t ON t.`id`=p.`top`
            LEFT JOIN `prefix_products_brands` AS b ON b.`id`=p.`brand`
            LEFT JOIN `prefix_products_currencies` c ON c.`id`=p.`currency`
            $join
            $whereClause
            $orderClause
            $limit";

        $products = SqlTools::selectObjects ( $query, "ProductItem", $indexKey );
        $brandsIds = array_unique ( ArrayTools::pluck ( $products, "brandId" ) );
        $brands = count ( $brandsIds ) ? $this->findBrands ( array ( "show" => 'Y', "deleted" => "N", "id" => $brandsIds ) ) : array ();
        $basket = Starter::app ()->getModule ( "Basket" )->getBasketList ();
        $basketItems = $basket ? $basket->products : array ();
        $categoriesIds = array_unique ( ArrayTools::pluck ( $products, "top" ) );
        $categories = count ( $categoriesIds ) ? $this->findCategories ( array ( "show" => "Y", "deleted" => "N", "id" => $categoriesIds ) ) : array ();
        $digits = Tools::getSettings ( 'Catalog', 'price_round', 0 );
        foreach ( $products as $product )
        {
            $product->types = unserialize ( $product->types );
            $product->brand = array_key_exists ( $product->brandId, $brands ) ? $brands[$product->brandId] : null;
            $product->link = $this->Link ( $product->top, $product->id );
            $product->price = round ( $product->price, $digits );
            $product->discountPrice = round ( $product->discountPrice, $digits );
            $image = Starter::app ()->imager->getMainImage ( 'Catalog', $product->id );
            $product->image = $image['src'];
            $product->images = Starter::app ()->imager->getImages ( 'Catalog', $product->id );
            $product->inCompare = array_key_exists ( "compare", $_SESSION ) && array_key_exists ( $product->id, $_SESSION['compare'] );
            //todo формирование характеристик перенести сюда
            if ( count ( $this->productsFeatures ) == 0 )
            {
                $this->getTaggedFeatures ( array ( $product->id ), $product->top );
            }
            $product->features = array_key_exists ( $product->id, $this->productsFeatures ) ? $this->productsFeatures[$product->id] : null;
            $inBasketItems = ArrayTools::select ( $basketItems, "productId", $product->id );
            $product->quantityByFeatures = array();
            foreach ($inBasketItems as $basketItem)
            {
                $quantityByFeature = new stdClass();
                $quantityByFeature->featureValue = $basketItem->featureValue;
                $quantityByFeature->quantity = $basketItem->quantity;
                $quantityByFeature->basketId = $basketItem->id;
                $product->quantityByFeatures[] = $quantityByFeature;
            }
            $product->inBasket = array_sum ( ArrayTools::pluck ( $inBasketItems, "quantity" ) );
            $product->basketId = implode ( ",", ArrayTools::pluck ( $inBasketItems, "id" ) );
            $product->topic = array_key_exists ( $product->top, $categories ) ? $categories[$product->top] : null;
        }
        $this->products = $products;

        return $products;
    }

    /**
     * <pre>Возвращаеь массив объектов BrandItem брендов</pre>
     * @param Array $params <p>Массив параметров для выбора брендов</p>
     * @return Array_Of_BrandItem <p>Массив брендов</p>
     */
    public function findBrands ( $params = array () )
    {
        $conditions = array ();
        $orderBy = array ();
        foreach ( $params as $field => $value )
        {
            if ( !$value )
                continue;

            switch ( $field )
            {
                case "id":
                    $conditions[] = "b.`id` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "nav":
                    $conditions[] = "b.`nav` IN (" . ArrayTools::stringList ( $value ) . ")";
                    break;
                case "deleted":
                    $conditions[] = "b.`deleted`='$value'";
                    break;
                case "show":
                    $conditions[] = "b.`show`='$value'";
                    break;
                case "orderBy":
                    if ( !is_array ( $value ) )
                        $orderBy[] = $value;
                    else
                    {
                        foreach ( $value as $orderField => $direction )
                        {
                            $orderBy[] = "b.`$orderField` $direction";
                        }
                    }
                default:
            }
        }

        $whereClause = count ( $conditions ) ? "WHERE " . implode ( " AND ", $conditions ) : "";
        $orderClause = count ( $orderBy ) ? "ORDER BY " . implode ( ", ", $orderBy ) : "";

        $query = "SELECT
                b.`id` AS id,
                b.`order` AS 'order',
                b.`name` AS name,
                b.`nav` AS nav,
                b.`text` AS text,
                b.`show` AS view,
                b.`deleted` AS deleted,
                b.`created` AS created,
                b.`modified` AS modified
            FROM `prefix_products_brands` b
            $whereClause
            $orderClause";

        $brands = SqlTools::selectObjects ( $query, "BrandItem", "id" );

        return $brands;
    }

    /**
     * <pre>Возвращает дерево/поддерево категорий товаров</pre>
     * @param Integer $id <p>ID категории для которой выводится поддерево</p>
     * @param Boolean $branchesOnly <p>Только ветви дерева - true, дерево с корнем - false</p>
     * @param Boolean $modelExclude <p>Только категории, исключая модели - true, включая модели - false</p>
     * @return Array <p>Дерево категорий</p>
     */

    /* -------- Блок фильтрации -------- */

    /**
     * <pre>Формирует параметры фильтров из POST-запроса
     * В обработку принимаются те элементы POST у которых индекс
     * формируется по следующим правилам:
     * <b>tf_</b> обязательный, префикс элемента
     * <b>name</b> обязательный, имя (для фильтров по цене/price или производителю/brand) или ID фильтра
     * <b>subName</b> дополнительное имя параметра (например для задания диапазона цены from или to)
     * </pre>
     * @return Array
     */
    private function getFilterParamsFromPost ()
    {
        $post = $_POST;
        $filtersFromPosts = array ();
        foreach ( $post as $filterField => $filterValue )
        {
            if ( substr ( $filterField, 0, 3 ) == "tf_" )
            {
                $filterParams = explode ( "_", substr ( $filterField, 3 ) );
                $filterParams[] = $filterValue;
                $filtersFromPosts[] = $filterParams;
            }
        }
        $finalFilters = array ();
        foreach ( $filtersFromPosts as $param )
        {
            $tag = array_shift ( $param );

            if ( !isset ( $finalFilters[$tag] ) )
                $finalFilters[$tag] = array ();

            if ( count ( $param ) > 1 )
                $finalFilters[$tag][$param[0]] = $param[1];
            else
                $finalFilters[$tag] = $param[0];
        }

        return $finalFilters;
    }

    /**
     * <p>Проверяет параметры фильтра на корректность</p>
     * @return void
     */
    private function checkFiltersForClear ()
    {
        if ( !$this->isFilter )
        {
            $this->clearSearchParams ();
            return;
        }
        $tagsIds = array ();
        $tagsParams = $_SESSION["productsFilter"];
        foreach ( array_keys ( $tagsParams ) as $id )
        {
            if ( is_numeric ( $id ) )
                $tagsIds[] = $id;
        }
        $paramFilter = array ();
        $tagFilter = array ();
        $tags = count ( $tagsIds ) > 0 ? SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags` WHERE `id` IN (" . implode ( ",", $tagsIds ) . ")", "FilterTag", "id" ) : array ();

        $needClear = true;
        foreach ( $tagsParams as $id => $val )
        {
            $clearFilter = true;
            if ( array_key_exists ( $id, $tags ) )
            {
                if ( empty ( $tags[$id]->tagType ) )
                    $tags[$id]->tagType = "";

                $type = $tags[$id]->tagType;
            }
            else
                $type = "";

            switch ( $type )
            {
                case "INTERVAL":
                    $clearFilter = ( empty ( $val["from"] ) && empty ( $val["to"] ) ) || ( $val['from'] == $val['min'] && $val['to'] == $val['max'] );
                    break;
                case "ENUM":
                    $clearFilter = !$val || count ( $val ) == 0;
                    break;
                case "SET":
                    $clearFilter = (!isset ( $val ) || $val == -1);
                    break;
                default:
                    if ( $id == "price" )
                        $clearFilter = ( empty ( $val["from"] ) && empty ( $val["to"] ) ) || ( $val['from'] == $val['min'] && $val['to'] == $val['max'] );
                    elseif ( $id == "brand" )
                        $clearFilter = !$val || count ( $val ) == 0;

                    if ( !$clearFilter )
                        $paramFilter[$id] = $val;
                    break;
            }
            if ( !$clearFilter && is_numeric ( $id ) )
                $tagFilter[$id] = $val;


            $needClear = $needClear && $clearFilter;
        }

        $this->isFilter = !$needClear;
        $this->filters = $needClear ? array () : $tagsParams;
        if ( $needClear )
            unset ( $_SESSION["productsFilter"] );
        else
            $_SESSION["productsFilter"] = $tagsParams;

        $_SESSION['paramFilter'] = count ( $paramFilter ) ? $paramFilter : null;
        $_SESSION['tagFilter'] = count ( $tagFilter ) ? $tagFilter : null;
    }

    /**
     * <p>Очистка фильтров подбора товара</p>
     */
    private function clearSearchParams ()
    {
        $this->isFilter = false;
        $this->filters = array ();
        unset ( $_SESSION["productsFilter"] );
        unset ( $_SESSION['paramFilter'] );
        unset ( $_SESSION['tagFilter'] );
    }

    /**
     * <p>Возвращает массив табличных характеристик для ряда товаров или категории</p>
     * @param Array $productIds <p>Массив ID товаров для которых строится массив характеристик</p>
     * @param Integer $categoryId <p>ID категории для которой строится массив характеристик</p>
     * @return Array_Of_ProductFeature
     */
    private function getTaggedFeatures ( $productIds = null, $categoryId = null )
    {
        $categoryId = $categoryId ? : $this->topic->id;
        if ( !$categoryId )
            return $this->productsFeatures;

        $childCategories = $this->childCategories ? : $this->getChildCategories ( $categoryId );
        $parentCategories = $this->parentCategories ? : $this->getParentCategories ( $categoryId );
        $categories = array_merge ( $parentCategories, array ( $categoryId ), $childCategories );
        $catIds = ArrayTools::numberList ( $categories );

        if ( !$productIds )
        {
            $query = "SELECT `id` FROM `prefix_products` WHERE `top` IN (" . ArrayTools::numberList ( array_merge ( array ( $this->currentCategoryId ), $childCategories ) ) . ") AND `show`='Y' AND `deleted`='N'";
            $productIds = array_keys ( SqlTools::selectRows ( $query, MYSQLI_ASSOC, "id" ) );
        }

        $ids = $productIds ? ArrayTools::numberList ( $productIds ) : null;
        if ( $ids )
            $where = "((`module`='Catalog' AND `moduleId` IN ($catIds)) OR (`module`!='Catalog' AND `moduleId` IN ($ids)))";
        else
            $where = "`module`='Catalog' AND `moduleId` IN ($catIds)";

        $query = "SELECT
                `id` AS id,
                `module` AS module,
                `moduleId` AS moduleId,
                `tagId` AS tagId,
                `value` AS value
            FROM `prefix_tags_values`
            WHERE $where
                AND `show`='Y'";

        $tagValues = SqlTools::selectObjects ( $query );
        $tags = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", "FilterTag", "id" );
        $productsFeatures = ArrayTools::select ( $tagValues, "module", "" );
        $categoryFeatures = ArrayTools::select ( $tagValues, "module", "Catalog" );
        foreach ( $productIds as $prodId )
        {
            $features = ArrayTools::select ( $productsFeatures, "moduleId", $prodId );
            $usedTags = array_unique ( array_merge ( ArrayTools::pluck ( $features, "tagId" ), ArrayTools::pluck ( $categoryFeatures, "tagId" ) ) );
            $prodFeatures = array ();
            foreach ( $usedTags as $tagId )
            {
                $taggedFeatures = array_merge ( ArrayTools::select ( $features, "tagId", $tagId ), ArrayTools::select ( $categoryFeatures, "tagId", $tagId ) );
                $tag = $tags[$tagId];
                $values = array_unique ( ArrayTools::pluck ( $taggedFeatures, "value" ) );
                asort ( $values );

                $prodFeatures[$tag->id] = new ProductFeature ( $tag->id, $tag->alias, $tag->name, $tag->tagType, $values );
            }

            $this->productsFeatures[$prodId] = $prodFeatures;
        }
        return $this->productsFeatures;
    }

    /**
     * <pre>Возвращает хар-ки текущей категории, без дочерних и родительских</pre>
     * @param Mixed $category <p>Объект представляющий категорию</p>
     * @return Array
     */
    private function fitFilterToCategory ( $category = "" )
    {
        if ( !$this->isFilter )
            return "";

        $tagsParams = $_SESSION["productsFilter"];
        $params = array ();
        $categoryTags = array ();

        if ( !empty ( $category ) )
        {
            $cats = array_merge ( $this->parentCategories, array ( $this->currentCategoryId ), $this->childCategories );
            $query = "SELECT * FROM `prefix_tags_values` "
                . " WHERE `module`='Catalog' AND `moduleId` IN (" . implode ( ",", $cats ) . ") "
                . " AND `show`='Y' "
                . " GROUP BY `tagId`"
                . " ORDER BY `moduleId` DESC";
            $categoryTags = array_keys ( SqlTools::selectRows ( $query, MYSQL_ASSOC, "tagId" ) );
        }
        // оставить только присущие данной категории х-ки в фильтре
        foreach ( $tagsParams as $id => $tagParam )
        {
            // оставляя цену и брэнды
            if ( !is_numeric ( $id ) || in_array ( $id, $categoryTags ) )
                $params[$id] = $tagParam;
        }

        return $tagsParams;
    }

    /**
     * <pre>Возвращает массив параметров для построения SQL-запроса,
     * удовлетворяющего фильтрам по цене и брэнду</pre>
     * @param Array $filters <p>Массив фильтров</p>
     * @return Array
     */
    private function filterByParams ( $filters )
    {
        $whereClause = array ();
        $namedFilters = array ();
        foreach ( $filters as $name => $filter )
        {
            if ( !is_numeric ( $name ) )
                $namedFilters[$name] = $filter;
        }

        foreach ( $namedFilters as $name => $filter )
        {
            switch ( $name )
            {
                case "price":
                    if ( isset ( $filter['from'] ) && trim ( $filter['from'] ) != "" )
                        $whereClause["priceNoLess"] = $filter['from'];

                    if ( isset ( $filter['to'] ) && trim ( $filter['to'] ) != "" )
                        $whereClause["priceNoBigger"] = $filter["to"];
                    break;
                case "brand":
                    if ( count ( $filter ) > 0 )
                        $whereClause["brandId"] = is_array ( $filter ) ? array_keys ( $filter ) : array ( $filter );
                    break;
                default:
            }
        }

        return $whereClause;
    }

    /**
     * <p>Возвращает Html-текст фильтра по брендам</p>
     * @return String <p>Html-текст фильтра по брендам</p>
     */
    public function brandFilter ()
    {
        $params = array
            (
            "show" => 'Y',
            "deleted" => "N",
            "orderBy" => "`order`, `name`"
        );
        $brands = $this->findBrands ( $params );

        if ( isset ( $this->filters['brand'] ) )
            $sBrands = is_array ( $this->filters['brand'] ) ? $this->filters['brand'] : explode ( ",", $this->filters['brand'] );
        else
            $sBrands = array ();

        foreach ( $brands as $brand )
        {
            $brand->selected = false;
            if ( !empty ( $sBrands ) )
            {
                foreach ( $sBrands as $selected )
                {
                    if ( $selected == $brand->id )
                        $brand->selected = true;
                }
            }
        }

        return TemplateEngine::view ( 'selectionBrand', array ( 'brands' => $brands ), __CLASS__ );
    }

    /**
     * <p>Возвращает Html-текст фильтра по цене</p>
     * @return String <p>Html-текст фильтра по цене</p>
     */
    public function priceFilter ()
    {
        $urlManager = Starter::app ()->urlManager;
        $filterTags = array ();
        //$prices = array_unique ( ArrayTools::pluck ( $this->products, "price" ) );
        $prices = $this->getPriceRange ();
        $filterTags["price"] = new FilterTag ( "price", "Цена", "INTERVAL", $prices );
        foreach ( $filterTags as $fTag )
        {
            if ( count ( $fTag->tagValues ) !== 0 )
            {
                $fTag->min = min ( $fTag->tagValues );
                $fTag->max = max ( $fTag->tagValues );
                $fTag->pattern = "\d+(\.\d{2})?";
            }

            if ( array_key_exists ( "price", $this->filters ) )
            {
                $fTag->from = $_SESSION["productsFilter"][$fTag->alias]["from"];
                $fTag->to = $_SESSION["productsFilter"][$fTag->alias]["to"];
            }
            else
            {
                $fTag->from = $urlManager->getParameter ( "tf_" . $fTag->alias . "_from" ) ? : $fTag->min;
                $fTag->to = $urlManager->getParameter ( "tf_" . $fTag->alias . "_to" ) ? : $fTag->max;
            }
        }

        return TemplateEngine::view ( 'priceFilter', array
                (
                'name' => "Подбор товара.",
                'link' => $this->Link ( $this->topic->id ),
                'filterTags' => $filterTags,
                'brand' => array_key_exists ( "brand", $this->filters ) ? $this->filters['brand'] : null,
                'isFilter' => $this->isFilter
                ), __CLASS__ );
    }

    /**
     * <pre>Возвращает массив уникальных цен товров текущей категории</pre>
     * @return Array
     */
    private function getPriceRange ()
    {
        $categoriesIds = ArrayTools::numberList ( Tools::getChildrenCategories ( $this->currentCategoryId ) );
        if ( $categoriesIds )
        {
            $whereClause = "p.`top` IN ($categoriesIds)";
        }
        else if ( !is_null ( $this->filters['brand'] ) )
        {
            $brandIds = is_array ( $this->filters['brand'] ) ? ArrayTools::numberList ( $this->filters['brand'] ) : $this->filters['brand'];
            $whereClause = "p.`brand` IN ($brandIds)";
        }
        else
        {
            $prices = array_unique ( ArrayTools::pluck ( $this->products, "price" ) );
            return $prices;
        }

        $hasCurrencies = SqlTools::selectObjects ( "SELECT * FROM `prefix_products_currencies`" ) > 0;
        $digits = Tools::getSettings ( 'Catalog', 'price_round', 0 );
        if ( !$hasCurrencies )
        {
            $query = "SELECT DISTINCT ROUND(`price`, $digits) FROM `prefix_products` "
                . " WHERE $whereClause AND `deleted`='N'";
        }
        else
        {
            $query = "SELECT DISTINCT ROUND(IF(c.`value`, p.`price`*c.`value`, p.`price`), $digits) AS price FROM `prefix_products` p LEFT JOIN `prefix_products_currencies` c ON c.`id`=p.`currency`"
                . " WHERE $whereClause AND p.`deleted`='N'";
        }
        $rows = SqlTools::selectRows ( $query, MYSQL_ASSOC );
        $prices = ArrayTools::pluck ( $rows, "price" );

        return $prices;
    }

    /**
     * <pre>Вычисляет ids товаров, значения характеристик, которые
     * попадают во все критерии из $tagsParams. Возвращает строку:
     *  where clauses для запроса в products, если по критериям выбраны ids;
     *  "", если ни по одному тэгу не определены критерии;
     *  "false", если по критериям не выбран ни один id.</pre>
     * @param array $tagsParams
     * @return string
     */
    function filterByTags ( $tagsParams )
    {
        $params = array ();
        $tagsIds = array ();
        $whereClauses = array ();

        // отделить от цены и брэндов
        foreach ( $tagsParams as $id => $tagParam )
        {
            if ( is_numeric ( $id ) )
            {
                $tagsIds[] = $id;
                $params[$id] = $tagParam;
            }
        }
        // выясняем типы тэгов
        if ( count ( $tagsIds ) > 0 )
        {
            $query = "SELECT * FROM `prefix_catalog_tags` WHERE `id` IN (" . ArrayTools::numberList ( $tagsIds ) . ")";
            $tags = SqlTools::selectObjects ( $query, "FilterTag", "id" );
        }

        foreach ( $params as $id => $val )
        {
            $where = array ( "tv.`tagId`=$id" );
            $noFeatures = false;
            switch ( $tags[$id]->tagType )
            {
                case "INTERVAL":
                    $from = isset ( $val['from'] ) ? intval ( $val['from'] ) : null;
                    $to = isset ( $val['to'] ) ? intval ( $val['to'] ) : null;
                    if ( $from )
                    {
                        $where[] = "tv.`value`>=$from";
                    }
                    if ( $to )
                    {
                        $where[] = "tv.`value`<=$to";
                    }
                    if ( !$val['to'] && !$val['from'] )
                    {
                        $noFeatures = true;
                    }
                    break;
                case "ENUM":
                    $values = ArrayTools::stringList ( $val );
                    $where[] = "tv.`value` IN ($values)";
                    break;
                case "SET":
                    if ( $val == -1 )
                    {
                        $noFeatures = true;
                    }
                    else
                    {
                        $where[] = "tv.`value`='$val'";
                    }
                    break;
                default:
                    break;
            }

            if ( !count ( $where ) || $noFeatures )
            {
                continue;
            }
            else
            {
                $clause = implode ( " AND ", $where );
                $query = "SELECT tv.`module` AS module, tv.`moduleId` AS moduleId FROM `prefix_tags_values` tv WHERE $clause";
                $result = SqlTools::selectObjects ( $query, null );
                $inCategories = ArrayTools::select ( $result, "module", "Catalog" );
                $inProducts = ArrayTools::select ( $result, "module", "" );
                $tagCondition = array ();
                if ( count ( $inCategories ) )
                {
                    $featuredCategories = ArrayTools::pluck ( $inCategories, "moduleId" );
                    $childFeatured = array ();
                    foreach ( $featuredCategories as $featuredCategory )
                    {
                        if ( in_array ( $featuredCategory, $this->parentCategories ) || $featuredCategory == $this->currentCategoryId )
                        {
                            continue;
                        }
                        else
                        {
                            $childFeatured[] = $featuredCategory;
                            $childFeatured = array_merge ( $childFeatured, $this->getChildCategories ( $featuredCategory ) );
                        }
                    }
                    if ( count ( $childFeatured ) )
                    {
                        $tagCondition[] = "p.`top` IN (" . ArrayTools::numberList ( $childFeatured ) . ")";
                    }
                }
                if ( count ( $inProducts ) )
                {
                    $productsIds = ArrayTools::pluck ( $inProducts, "moduleId" );
                    $tagCondition[] = "p.`id` IN (" . ArrayTools::numberList ( $productsIds ) . ")";
                }

                if ( count ( $tagCondition ) )
                {
                    $whereClauses[] = "(" . implode ( " OR ", $tagCondition ) . ")";
                }
            }
        }
        $clauses = count ( $whereClauses ) > 0 ? "(" . implode ( " AND ", $whereClauses ) . ")" : false;

        return $clauses;
    }

    /* -------- Блок виджетов -------- */
    /**
     * <p>Служит для запоминания сформированной части ссылки при рекурсии</p>
     * @var String
     */
    private $localCacheLink;

    /**
     * <pre>Возвращает ссылку на документ категории или товара</pre>
     * @param type $topicId <p>ID категории</p>
     * @param type $productId <p>ID товара</p>
     * @return String <p>текст ссылки</p>
     */
    public function Link ( $topicId = 0, $productId = 0 )
    {
        $topicId = ( int ) $topicId;
        $productId = ( int ) $productId;

        if ( empty ( $this->localCacheLink ) )
            $this->localCacheLink = Starter::app ()->content->getLinkByModule ( __CLASS__ );

        if ( $topicId != 0 || $productId != 0 )
        {
            if ( $productId != 0 )
            {
                $product = ArrayTools::head ( SqlTools::selectObjects ( "SELECT `id`, `top`, `nav` FROM `prefix_products` WHERE `id`=$productId" ) );
                $productLink = "/" . ( $product->nav ? : $product->id );
                $topicId = $product->top;
            }
            else
            {
                $productLink = "";
            }

            $parents = array_reverse ( $this->getParentCategories ( $topicId ) );
            $parents[] = $topicId;
            $topics = SqlTools::selectObjects ( "SELECT `id`, `top`, `nav` FROM `prefix_products_topics` WHERE `id` IN (" . ArrayTools::numberList ( $parents ) . ")", null, "id" );
            $topicsLinkChain = array ();
            foreach ( $parents as $id )
            {
                $topic = $topics[$id];
                $topicsLinkChain[] = $topic->nav ? : $topic->id;
            }
            $topicsLink = implode ( "/", $topicsLinkChain );

            if ( $this->useShortUrl && $productId )
            {
                $url = $productLink;
            }
            else
            {
                $url = "/$topicsLink" . $productLink;
            }
        }
        return $this->localCacheLink . $url;
    }

    /**
     * <p>Возвращает Html-текст меню из дерева категорий</p>
     * @return String <p>Html-текст меню дерева категорий</p>
     */
    public function getMenuTree ( $template = "menutree")
    {
        $menu = $this->getCatalogTree ( null, false, false );

        return TemplateEngine::view ( $template, array ( 'menu' => $menu ), __CLASS__, true );
    }

    /**
     * <p>Возвращает html-текст главного меню каталога</p>
     * @return String <p>html-текст главного меню каталога</p>
     */
    public function MainMenu ()
    {
        $counts_ = SqlTools::selectRows ( "SELECT `top` , COUNT(`id`) AS `count` FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' GROUP BY `top`", MYSQL_ASSOC );
        $counts = array ();
        foreach ( $counts_ as $count )
        {
            $counts[$count['top']] = $count['count'];
        }
        $active_ids = array ();
        if ( !empty ( $this->path ) )
        {
            foreach ( $this->path as $path )
            {
                if ( $path['type'] == 'topic' )
                {
                    $active_ids[] = $path['data']['id'];
                }
            }
        }
        $topics = $this->data->GetData ( 'products_topics', "AND `show` = 'Y'" );
        $topicsByTop = array ();
        foreach ( $topics as $topic )
        {
            $topic['link'] = $this->Link ( $topic['id'] );
            $topic['count'] = isset ( $counts[$topic['id']] ) ? $counts[$topic['id']] : 0;

            $cos_ = SqlTools::selectRows ( "SELECT i.`src`, t.`id` FROM `prefix_products_topics` AS t, `prefix_images` AS i WHERE i.`module`='Topic' AND i.`module_id`=t.`id` AND i.`main`='Y'" );

            $topic['src'] = '/data/moduleImages/Topic/no.png';
            foreach ( $cos_ as $co )
            {
                if ( $co['id'] == $topic['id'] )
                {
                    $topic['src'] = $co['src'];
                }
            }

            //set active
            if ( in_array ( $topic['id'], $active_ids ) )
            {
                $topic['active'] = true;
                $this->menuOpened = true;
            }
            else
            {
                $topic['active'] = false;
            }

            $topicsByTop[$topic['top']][$topic['id']] = $topic;
        }
        unset ( $topics );

        return tpl ( 'modules/' . __CLASS__ . '/mainmenu', array (
            'topics' => $topicsByTop
            ) );
    }

    /**
     * <pre>Возвращает html-текст меню каталога</pre>
     * @return String <p>html-текст меню</p>
     */
    public function Menu ()
    {
        $counts = ArrayTools::index ( SqlTools::selectObjects ( "SELECT `top` , COUNT(`id`) AS `count` FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' GROUP BY `top`" ), "top" );
        $active_ids = array ();

        if ( !empty ( $this->path ) )
        {
            foreach ( $this->path as $v )
            {
                if ( $v['type'] == 'topic' )
                {
                    $active_ids[] = $v['data']->id;
                }
            }
        }
        $topics = $this->data->GetData ( 'products_topics', "AND `show` = 'Y'" );
        $topicsByTop = array ();
        $newTopics = array ();
        foreach ( $topics as $topic )
        {
            $topic['link'] = $this->Link ( $topic['id'] );
            $topic['count'] = isset ( $counts[$topic['id']] ) ? $counts[$topic['id']]->count : 0;

            //set active
            if ( in_array ( $topic['id'], $active_ids ) )
            {
                $topic['active'] = true;
                $this->menuOpened = true;
            }
            else
            {
                $topic['active'] = false;
            }

            $topic = ( Object ) $topic;
            $topic->subCategories = Array ();
            $newTopics[$topic->id] = $topic;
        }

        foreach ( $newTopics as $topic )
        {
            if ( $topic->top == 0 )
                $topicsByTop[$topic->id] = $topic;
            else
            {
                if ( !array_key_exists ( $topic->top, $newTopics ) )
                    continue;

                $parentTopic = $newTopics[$topic->top];
                if ( is_null ( $parentTopic->subCategories ) )
                    $parentTopic->subCategories = array ();

                $parentTopic->subCategories[$topic->id] = $topic;
            }
        }
        unset ( $topics );

        //$tree = ArrayTools::index ( $topicsByTop, "id" );
        $breads = Starter::app ()->getModule ( 'Content' )->bread ();

        $treeIds = array ();
        $endCrumb = end ( $breads );
        if ( $endCrumb && array_key_exists ( 'id', $endCrumb ) )
        {
            foreach ( $breads as $crumb )
            {
                $treeIds['id'][] = $crumb['id'];
            }
        }

        $html = TemplateEngine::view ( 'menu', array ( 'topics' => $topicsByTop, "level" => 0, 'classType' => 'catalog', 'breadPath' => $treeIds ), __CLASS__ );
        return $html;
    }

    /**
     * <pre>Возвращает массив параметров для хлебных крошек модуля вида
     * name => название страницы в последовательности крошек
     * link => ссылка для перехода на страницу,
     * id => ID страницы,
     * disable => флаг активности ссылки
     * @return Array <p>массив параметров для хлебных крошек модуля</p>
     */
//    public function breadCrumbs ()
//    {
//        if ( empty ( $this->path ) )
//        {
//            $this->buildModulePath ();
//        }
//        if ( !empty ( $this->path ) )
//        {
//            $query = "SELECT `top` AS top, COUNT(`id`) AS count FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' GROUP BY `top`";
//            $productsInTop = SqlTools::selectRows ( $query, MYSQL_ASSOC, "top" );
//
//            $ret = array ();
//            foreach ( $this->path as $i )
//            {
//                $data = $i['data'];
//                if ( $i['type'] == 'alterPage' )
//                {
//                    $ret[] = array
//                        (
//                        'name' => $this->actions[$data]['name'],
//                        'link' => Starter::app ()->content->getLinkByModule ( 'Catalog' ) . '/' . $i['data'],
//                    );
//                    break;
//                }
//                $link = '';
//                $disable = false;
//                if ( $i['type'] == 'topic' )
//                {
//                    $inTop = array_key_exists ( $data->id, $productsInTop ) ? $productsInTop[$data->id] : 0;
//                    if ( $inTop['count'] == 0 )
//                    {
//                        $disable = true;
//                    }
//                    else
//                    {
//                        $disable = false;
//                    }
//
//                    $link = $this->Link ( $data->id );
//                }
//
//                if ( $i['type'] == 'product' )
//                {
//                    $link = $this->Link ( $data->top, $data->id );
//                }
//
//                $ret[] = array ( 'name' => $data->name, 'link' => $link, 'id' => $data->id, 'disable' => $disable );
//            }
//
//            return $ret;
//        }
//        else
//            return array ();
//    }

    /**
     * <pre>Возвращает html-текст контейнера "С этим товаром покупают"</pre>
     * @param Integer $limit <p>Число выводимых позиций</p>
     * @return String <p>Html-текст контейнера</>
     */
    function alsoBoughtBlock ( $limit = 0 )
    {
        if ( !isset ( $this->product->relations ) || is_null ( $this->product->relations ) )
        {
            return "";
        }

        $ids = array_filter ( explode ( ",", $this->product->relations ) );
        $productsIds = array_map ( "trim", $ids );
        if ( count ( $productsIds ) == 0 )
        {
            return "";
        }

        if ( $limit > 0 )
        {
            $sqlLimit = ( int ) $limit;
        }
        else
        {
            $sqlLimit = null;
        }

        $findParams = array
            (
            "id" => $productsIds,
            "show" => "Y",
            "deleted" => "N",
            "limit" => $sqlLimit
        );

        $products = $this->findProducts ( $findParams ); //SqlTools::selectRows ( "SELECT * FROM `prefix_products` WHERE `id` IN (" . $this->product['relations'] . ") AND `deleted`='N' AND `show`='Y' $sql_limit", MYSQL_ASSOC );

        return TemplateEngine::view ( 'alsoBought', array ( 'products' => $products ), __CLASS__ );
    }
    /* -------- Блок поиска -------- */

    /**
     * <pre>Возвращает html-текст страницы с результатом поиска</pre>
     * @return String <p>Html-текст страницы с результатом поиска</p>
     */
    private function searchProducts ()
    {
        $pageTitle = $this->actions['name'];
        Starter::app ()->headManager->setTitle ( Sterter::app ()->title . "  — $pageTitle" );
        $productsHtml = $this->productsItemsView ( $this->getProductsItems () );
        $categoriesTree = $this->getCatalogTree ( $this->currentCategoryId );
        $categoryHtml = TemplateEngine::view ( "subcategories", array ( "categoriesTree" => $categoriesTree ), __CLASS__, true );

        return TemplateEngine::view ( 'list', array
                (
                "isFilter" => $this->isFilter,
                'name' => $pageTitle,
                'productsItems' => $productsHtml,
                'isProducts' => 1,//count ( $this->products ),
                'currentCategory' => $categoriesTree,
                "categoryHtml" => ""//$categoryHtml
                ), __CLASS__ );
    }

    /**
     * <p>Возвращает часть WHERE для поиска</p>
     * @return String <p>Часть WHERE для поиска</p>
     */
    private function search ()
    {
        $searchString = filter_input ( INPUT_POST, "searchString", FILTER_SANITIZE_STRING );
        $searchMode = filter_input ( INPUT_POST, "searchMode", FILTER_SANITIZE_NUMBER_INT );

        if (/* !$searchMode || */!$searchString)
            return null;

        $strings = explode ( ' ', $searchString );
        $highlight = array ();

        //Убираем окончания
        $stemmer = new Lingua_Stem_Ru();
        foreach ( $strings as $k => $v )
        {
            $trimv = trim ( $v );
            $strings[$k] = $stemmer->stem_word ( $trimv );
            $highlight[] = $trimv;
        }

        //Окончательно фильтруем
        $searchFor = implode ( " ", array_filter ( $strings ) );

        $like = "(t.`name` LIKE '%" . SqlTools::escapeString ( $searchFor ) . "%' OR p.`name` LIKE '%" . SqlTools::escapeString ( $searchFor ) . "%' OR b.`name` LIKE '%" . SqlTools::escapeString ( $searchFor ) . "%'" .
            " OR p.`shortName` LIKE '%" . SqlTools::escapeString ( $searchFor ) . "%')";

        return $like;
    }

    public function getModuleMap ()
    {
        $whereClause = "WHERE `deleted`='N' AND `show`='Y'";

        $categoryQuery = "
            SELECT
                t.`id` AS id,
                t.`top` AS parentId,
                t.`order` AS 'order',
                t.`nav` AS nav,
                t.`name` AS title,
                t.`show` AS view,
                t.`deleted` AS deleted
            FROM `prefix_products_topics` t
            $whereClause
            ORDER BY `order` ASC
            ";
        $categories = SqlTools::selectObjects ( $categoryQuery, "CmsSiteDoc", "id" );

        $productQuery = "
            SELECT
                p.`id` AS id,
                p.`top` AS parentId,
                p.`order` AS 'order',
                p.`nav` AS nav,
                p.`name` AS title,
                p.`show` AS view,
                p.`deleted` AS deleted
            FROM `prefix_products` AS p
            $whereClause
            ORDER BY `order`";

        $products = SqlTools::selectObjects ( $productQuery, "CmsSiteDoc", "id" );
        foreach ( $products as $product )
        {
            $product->link = $this->Link ( $product->top, $product->id );
        }

        $docsTree = array ();
        foreach ( $categories as $doc )
        {
            $doc->link = $this->Link ( $doc->id );
            $doc->children = ArrayTools::select ( $products, "parentId", $doc->id );
            if ( $doc->parentId == 0 )
            {
                $docsTree[$doc->id] = $doc;
            }
            else
            {
                if ( !array_key_exists ( $doc->parentId, $categories ) )
                    continue;
                $parentDoc = $categories[$doc->parentId];
                $parentDoc->docs[$doc->id] = $doc;
            }
        }

        return $docsTree;
    }

//    public function breadCrumbsCategory ( $arr, $level = 0 )
//    {
//        if ( count ( $arr ) <= 2 )
//        {
//            $crumbscats = array ();
//        }
//
//        if ( isset ( $arr[2] ) )
//        {
//            $crumbs = explode ( "/", substr ( $arr[2]['link'], 1 ) );
//            $crumbscats = SqlTools::selectRows ( "SELECT b.`id` , b.`name` as `name` , b.`nav` as `nav2`, a.`nav` as `nav1` FROM  `prefix_products_topics` AS a
//                JOIN  `prefix_products_topics` AS b ON a.`id`=b.`top` WHERE a.`deleted`='N' AND b.`deleted`='N' AND a.`nav`='" . $crumbs[1] . "') ORDER BY b.`id`" );
//
//            foreach ( $crumbscats as $k => $crumbscat )
//            {
//                $crumbscats[$k]['link'] = $crumbscat['nav1'] . '/' . $crumbscat['nav2'];
//                $imgSrc = SqlTools::selectValue ( "SELECT `src` FROM  `prefix_images` WHERE `module`='Topic' AND `module_id`='" . $crumbscat['id'] . "' AND `main`='Y'" );
//                $crumbscats[$k]['img'] = $imgSrc;
//            }
//        }
//
//        if ( isset ( $arr[3] ) )
//        {
//            $crumbs = explode ( "/", substr ( $arr[3]['link'], 1, strlen ( $arr[3]['link'] ) ) );
//            $crumbscats = SqlTools::selectRows ( "SELECT b.`id` , b.`name` as `name` , b.`nav` as `nav2`, a.`nav` as `nav1` FROM  `prefix_products_topics` AS a
//                JOIN  `prefix_products_topics` AS b ON a.`id`=b.`top` WHERE a.`deleted`='N' AND b.`deleted`='N' AND a.`nav`='" . $crumbs[2 - $level] . "') ORDER BY b.`id`" );
//
//            foreach ( $crumbscats as $k => $crumbscat )
//            {
//                $crumbscats[$k]['link'] = $crumbscat['nav1'] . '/' . $crumbscat['nav2'];
//                $imgSrc = SqlTools::selectValue ( "SELECT `src` FROM  `prefix_images` WHERE `module`='Topic' AND `module_id`='" . $crumbscat['id'] . "' AND `main`='Y')" );
//                $crumbscats[$k]['img'] = $imgSrc;
//            }
//        }
//
//        return ($crumbscats);
//    }

    /**
     * <pre>Возвращает список категорий текущего или нижележащего уровня.
     * В зависимости от параметра $getSub</pre>
     * @param Boolean $getSub <p><b>false</b> - текущий уровень, <b>true</b> - нижележащий уровень</p>
     */
    public function crumbsCategory ( $getSub = false )
    {
        $manager = Starter::app ()->urlManager;
        $path = array_filter ( explode ( '/', $manager->getUrlPart ( "path" ) ) );
        $nav = str_replace ( '/', '', Starter::app ()->content->getLinkByModule ( __CLASS__ ) );
        if ( !in_array ( $nav, $path ) )
        {
            return array ();
        }

        $currentCategory = $this->topic;
        $top = $getSub ? ($currentCategory->id ? : 0) : $currentCategory->top;

        $categories = SqlTools::selectObjects ( "SELECT * FROM `prefix_products_topics` WHERE `top`=$top AND `deleted`='N' AND `show`='Y'" );
        foreach ( $categories as $crumbscat )
        {
            $imgSrc = SqlTools::selectValue ( "SELECT `src` FROM  `prefix_images` WHERE `module`='Topic' AND `module_id` = '" . $crumbscat->id . "' AND `main` = 'Y'" );
            $crumbscat->img = $imgSrc;
            $crumbscat->link = $this->Link ( $crumbscat->id );
        }

        return $categories;
    }

    /**
     * <p>Возврашает текст с характеристиками товара</>
     * @return String <p>Html-текст с характеристиками товара</p>
     */
    public function tagsFeatures ()
    {
        $category = $this->currentCategoryId;

        $filterTags = array ();

        $parentCats = Tools::getParentCategories ( $category );
        $productsIds = $this->product ? array ( $this->product->id ) : ( count ( $this->products ) ? ArrayTools::pluck ( $this->products, "id" ) : null );
        if ( !$productsIds )
        {
            $productsIds = array_keys ( SqlTools::selectObjects ( "SELECT id FROM `prefix_products` WHERE `top` IN (" . ArrayTools::numberList ( $category ) . ")", null, "id" ) );
        }

        $productsIdsStr = ArrayTools::numberList ( $productsIds );
        $categoriesIdsStr = ArrayTools::numberList ( array_unique ( $parentCats ) );
        $orWhere = $productsIdsStr ? "OR (`module`!='Catalog' AND `moduleId` IN ($productsIdsStr))" : "";
        $query = "SELECT
                `id` AS id,
                `module` AS module,
                `moduleId` AS moduleId,
                `tagId` AS tagId,
                `value` AS value
            FROM `prefix_tags_values`
            WHERE ((`module`='Catalog' AND `moduleId` IN ($categoriesIdsStr))
                $orWhere)
                AND `show`='Y'";
        $tagsValues = SqlTools::selectObjects ( $query );
        $tagIds = ArrayTools::numberList ( array_unique ( ArrayTools::pluck ( $tagsValues, "tagId" ) ) );
        if ( !$tagIds )
        {
            return "";
        }

        $tags = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags` WHERE `id` IN ($tagIds)", "FilterTag", "id" );
        foreach ( $tags as $tag )
        {
            $tagValues = ArrayTools::pluck ( ArrayTools::select ( $tagsValues, "tagId", $tag->id ), "value" );
            $tag->tagValues = array_unique ( $tagValues );
            $filterTags[$tag->id] = $tag;
        }

        return count ( $filterTags ) ? TemplateEngine::view ( 'tagsFeatures', array ( 'filterTags' => $filterTags ), __CLASS__ ) : "";
    }

    public function findFilterTags ( $params = array (), $where = null )
    {
        if ( !$params )
        {
            return array ();
        }
        $conditions = array ();
        //$orderBy = array();
        foreach ( $params as $field => $value )
        {
            if ( !$value )
            {
                continue;
            }

            switch ( $field )
            {
                case "id":
                    $conditions[] = "f.`id` IN (" . ArrayTools::numberList ( $value ) . ")";
                    break;
                case "gf":
                    break;
            }
        }
        $whereClause = count ( $conditions ) || $where ? "WHERE" : "";
        $whereClause .= ( count ( $conditions ) ? implode ( " AND ", $conditions ) : "" ) . ( $where ? : "" );
        /*
          $query = "SELECT
          f.`id` AS id,
          f.`alias` AS alias,
          f.`name` AS name,
          f.`tagType` AS tagType
          FROM `prefix_catalog_tags` f
          $where
          $order
          $limit";
         */
        /*
          SELECT
          tv.`id` AS id,
          tv.`module` AS module,
          tv.`moduleId` AS moduleId,
          tv.`tagId` AS tagId,
          tv.`value` AS value,
          t.`tagType` AS tagtype,
          t.`name` AS name,
          t.`alias` AS alias
          FROM `bm_tags_values` tv
          left join `bm_catalog_tags` t On tv.`tagId`=t.`id`
          WHERE ((tv.`module`='Catalog' AND tv.`moduleId` IN (6557)) OR (tv.`module`!='Catalog' AND tv.`moduleId` IN (8743))) AND tv.`show`='Y'
         */
    }

    function CustomLittleProductsList ( $products )
    {
        if ( empty ( $products ) || !is_array ( $products ) )
            return false;

        $topicsIds = array ();
        $brandsIds = array ();
        $ids = array ();
        foreach ( $products as $k => $product )
        {
            $ids[] = $product['id'];
            $topicsIds[] = $product['top'];
            $brandsIds[] = $product['brand'];
        }
        //$topics = array_keys ( $topics );
        //$brands = array_keys ( $brands );

        if ( empty ( $topicsIds ) )
            return false;

        Starter::app ()->imager->prepareImages ( 'Catalog', $ids, true );

        $ptopics = SqlTools::selectRows ( "SELECT * FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', $topicsIds ) . ")", MYSQL_ASSOC );
        $topics = array ();
        foreach ( $ptopics as $topic )
        {
            $topic['link'] = $this->Link ( $topic['id'] );
            $topics[$topic['id']] = $topic;
        }
        if ( count ( $brandsIds ) )
            $pbrands = SqlTools::selectRows ( "SELECT * FROM `prefix_products_brands` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', $brandsIds ) . ")", MYSQL_ASSOC );
        else
        {
            $pbrands = array ();
        }
        $brands = array ();
        foreach ( $pbrands as $brand )
        {
            $brands[$brand['id']] = $brand;
        }

        foreach ( $products as $k => $product )
        {
            if ( !isset ( $topics[$product['top']] ) )
                continue;

            //Цена
            $products[$k]['priceOld'] = $this->calculatePrice ( $product['price'] );
            $products[$k]['price'] = $this->calculatePrice ( $product['price'], $product['discount'] );

            //Ссылка
            $products[$k]['link'] = $this->Link ( $product['top'], $product['id'] );

            //Топик
            $products[$k]['topic'] = $topics[$product['top']];

            //Бренд
            if ( isset ( $brands[$product['brand']] ) )
            {
                $products[$k]['brand'] = $brands[$product['brand']];
            }
            else
            {
                $products[$k]['brand'] = false;
            }
        }

        return tpl ( 'modules/' . __CLASS__ . '/littleItems', array (
            'products' => $products
            ) );
    }

    function FeaturedList ( $limit = 0 )
    {
        if ( $limit > 0 )
            $sql_limit = 'LIMIT ' . ( int ) $limit;
        else
            $sql_limit = '';

        $top = ( int ) $this->topic->id;
        $inTop = SqlTools::selectRows ( "SELECT `id` FROM `prefix_products_topics` WHERE `top`='" . $top . "' AND `deleted`='N'", MYSQL_ASSOC );
        $topicIds = array ( $top );
        foreach ( $inTop as $topicId )
        {
            if ( !in_array ( $topicId['id'], $topicIds ) )
                $topicIds[] = $topicId['id'];
        }

        if ( $top == 0 )
            $query = "SELECT * FROM `prefix_products` WHERE `is_featured`='Y' AND `deleted`='N' AND `show`='Y' AND `is_exist`='Y' ORDER BY id DESC $sql_limit";
        else
            $query = "SELECT * FROM `prefix_products` WHERE `is_featured`='Y' AND `deleted`='N' AND `show`='Y' AND `is_exist`='Y' AND `top` IN (" . implode ( ', ', $topicIds ) . ") ORDER BY id DESC $sql_limit";

        //$products = SqlTools::selectRows($query, MYSQL_ASSOC);
        $products = SqlTools::selectObjects ( $query );
        $brands = array ();

        $categoriesId = array_unique ( ArrayTools::pluck ( $products, "top" ) );

        if ( !count ( $categoriesId ) )
            return false;

        $productsCategories = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', $categoriesId ) . ")" ), "id" );

        foreach ( $productsCategories as $category )
        {
            $category->link = $this->Link ( $category->id );
        }

        $realProducts = array (); //Массив товаров, находящихся в дереве существующих категорий
        foreach ( $products as $product )
        {
            //Если для товара отсутствует категория пропускаем его
            if ( !isset ( $productsCategories[$product->top] ) )
                continue;

            //Распаковка характеристик
            if ( !is_array ( $product->types ) )
                $product->types = unserialize ( $product->types );

            if ( !is_array ( $productsCategories[$product->top]->types ) )
                $productsCategories[$product->top]->types = unserialize ( $productsCategories[$product->top]->types );

            //Ссылка
            $product->link = $this->Link ( $product->top, $product->id );

            //Топик
            $product->topic = $productsCategories[$product->top];

            $product->priceOld = $product->price;
            $product->price = !is_null ( $product->discount ) ? $this->calculatePrice ( $product->price, $product->discount ) : $product->price;

            $brands = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_products_brands` WHERE `deleted`='N' AND `show`='Y' AND `id`=" . $product->brand ), "id" );
            $product->brand = $product->brand && array_key_exists ( $product->brand, $brands ) ? $brands[$product->brand] : 0;
            $realProducts[] = $product;
        }

        return tpl ( 'modules/' . __CLASS__ . '/items', array (
            'products' => $realProducts
            ) );
    }

    function Seen ()
    {
        if ( !isset ( $_SESSION['seen'] ) || empty ( $_SESSION['seen'] ) )
            page404 ();

        $ids = array_reverse ( array_keys ( $_SESSION['seen'] ) );

        $prod = SqlTools::selectRows ( "SELECT * FROM `prefix_products` WHERE `id` IN (" . implode ( ',', $ids ) . ") AND `deleted`='N' AND `show`='Y' ", MYSQL_ASSOC );

        $productsById = array ();
        foreach ( $prod as $i )
            $productsById[$i['id']] = $i;
        unset ( $prod );

        $products = array ();
        foreach ( $ids as $id )
            $products[$id] = $productsById[$id];
        unset ( $productsById );

        $text = $this->CustomProductsList ( $products );
        $text .= $this->paging_rendered;

        return tpl ( 'page', array (
            'title' => $this->actions['seen']['name'],
            'name' => $this->actions['seen']['name'],
            'text' => $text
            ) );
    }

    function SeenBlock ( $limit = 0 )
    {
        if ( !isset ( $_SESSION['seen'] ) || empty ( $_SESSION['seen'] ) )
            return;

        $ids = array_reverse ( array_keys ( $_SESSION['seen'] ) );
        if ( $limit > 0 )
            $ids = array_slice ( $ids, 0, $limit );

        $prod = SqlTools::selectRows ( "SELECT * FROM `prefix_products` WHERE `id` IN (" . implode ( ',', $ids ) . ") AND `deleted`='N' AND `show`='Y'", MYSQL_ASSOC );

        $productsById = array ();
        foreach ( $prod as $i )
            $productsById[$i['id']] = $i;
        unset ( $prod );

        $products = array ();
        foreach ( $ids as $id )
        {
            $products[$id] = $productsById[$id];
        }
        unset ( $productsById );

        return tpl ( 'modules/' . __CLASS__ . '/promoBlocks/seen', array (
            'littleProductsList' => $this->CustomLittleProductsList ( $products ),
            'limit' => $limit,
            'count' => count ( $_SESSION['seen'] )
            ) );
    }

    function addCompare ( $id )
    {
        $product = SqlTools::selectRow ( "SELECT * FROM `prefix_products` WHERE `id`=" . abs ( ( int ) $id ) . " AND `deleted`='N' AND `show`='Y'" );
        if ( !empty ( $product ) )
            $_SESSION['compare'][$id] = true;
    }

    function delCompare ( $id )
    {
        if ( isset ( $_SESSION['compare'][$id] ) )
            unset ( $_SESSION['compare'][$id] );
    }

    function cleanCompare ()
    {
        if ( isset ( $_SESSION['compare'] ) )
            unset ( $_SESSION['compare'] );
    }

    function ajaxCompare ()
    {
        if ( isset ( $_POST['add'] ) )
            $this->addCompare ( $_POST['add'] );

        if ( isset ( $_POST['del'] ) )
            $this->delCompare ( $_POST['del'] );

        if ( isset ( $_POST['clean'] ) )
            $this->cleanCompare ();

        sendJSON ( array (
            'block' => $this->CompareBlock ()
        ) );
    }

    /**
     * Блок сравнения
     */
    function CompareBlock ()
    {
        if ( !isset ( $_SESSION['compare'] ) || empty ( $_SESSION['compare'] ) )
        {
            return tpl ( 'modules/' . __CLASS__ . '/promoBlocks/compare', array ( 'products' => array (), 'topics' => array () ) );
        }

        $ids = array_reverse ( array_keys ( $_SESSION['compare'] ) );

        $prod = SqlTools::selectRows ( "SELECT * FROM `prefix_products` WHERE `id` IN (" . implode ( ',', $ids ) . ") AND `deleted`='N' AND `show`='Y'", MYSQL_ASSOC, 'id' );

        foreach ( $prod as $k => $product )
        {
            $prod[$k]['priceOld'] = $this->calculatePrice ( $product['price'] );
            $prod[$k]['price'] = $this->calculatePrice ( $product['price'], $product['discount'] );
        }

        $brands = SqlTools::selectRows ( "SELECT * FROM `prefix_products_brands` WHERE `deleted`='N' AND `show`='Y'", MYSQL_ASSOC, 'id' );

        $products = array ();
        foreach ( $ids as $id )
        {
            $products [$prod[$id]['top']] [$id] = $prod[$id];
            $products [$prod[$id]['top']] [$id] ['link'] = $this->Link ( $prod[$id]['top'], $prod[$id]['id'] );
            if ( isset ( $brands[$prod[$id]['brand']] ) )
            {
                $products [$prod[$id]['top']] [$id] ['brand'] = $brands[$prod[$id]['brand']];
            }
            else
            {
                $products [$prod[$id]['top']] [$id] ['brand'] = false;
            }
        }

        $topics = SqlTools::selectRows ( "SELECT * FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', array_keys ( $products ) ) . ")", MYSQL_ASSOC, 'id' );
        foreach ( $topics as $id => $topic )
        {
            $topics[$id]['link'] = $this->Link ( $topic['id'] );
        }

        foreach ( $products as $k => $v )
        {
            $products[$k] = array_slice ( $v, 0, 3 );
        }

        return tpl ( 'modules/' . __CLASS__ . '/promoBlocks/compare', array (
            'products' => $products,
            'topics' => $topics,
            'count' => count ( $_SESSION['compare'] )
            ) );
    }

    /**
     * Страница сравнения, добавление/удаление товаров в сравнении
     */
    function Compare ()
    {
        // Добавление/удаление товаров сравнения
        if ( isset ( $this->path[1] ) && isset ( $this->path[2] ) && $this->path[1]['data'] == 'add' )
        {
            $this->addCompare ( $this->path[2]['data'] );
            $backMe = true;
        }
        if ( isset ( $this->path[1] ) && isset ( $this->path[2] ) && $this->path[1]['data'] == 'del' )
        {
            $this->delCompare ( $this->path[2]['data'] );
            $backMe = true;
        }
        if ( isset ( $this->path[1] ) && $this->path[1]['data'] == 'clean' )
        {
            $this->cleanCompare ();
            $backMe = true;
        }
        if ( isset ( $backMe ) && isset ( $_SERVER['HTTP_REFERER'] ) && !empty ( $_SERVER['HTTP_REFERER'] ) )
        {
            header ( 'Location: ' . $_SERVER['HTTP_REFERER'] );
            exit ();
        }

        if ( !isset ( $_SESSION['compare'] ) || empty ( $_SESSION['compare'] ) )
            page404 ();

        //Подготовка данных для сравнения
        $ids = array_reverse ( array_keys ( $_SESSION['compare'] ) );
        $prod = SqlTools::selectRows ( "SELECT * FROM `prefix_products` WHERE `id` IN (" . implode ( ',', $ids ) . ") AND `deleted`='N' AND `show`='Y'", MYSQL_ASSOC, 'id' );
        foreach ( $prod as $k => $product )
        {
            //$prod[$k]['price'] = $this->calculatePrice($product['price']);
            $prod[$k]['priceOld'] = $this->calculatePrice ( $product['price'] );
            $prod[$k]['price'] = $this->calculatePrice ( $product['price'], $product['discount'] );
        }
        $brands = SqlTools::selectRows ( "SELECT * FROM `prefix_products_brands` WHERE `deleted`='N' AND `show`='Y'", MYSQL_ASSOC, 'id' );
        $products = array ();
        foreach ( $ids as $id )
        {
            $products [$prod[$id]['top']] [$id] = $prod[$id];
            $products [$prod[$id]['top']] [$id] ['link'] = $this->Link ( $prod[$id]['top'], $prod[$id]['id'] );
            $products [$prod[$id]['top']] [$id] ['types'] = unserialize ( $prod[$id]['types'] );
            if ( isset ( $brands[$prod[$id]['brand']] ) )
                $products [$prod[$id]['top']] [$id] ['brand'] = $brands[$prod[$id]['brand']];
            else
                $products [$prod[$id]['top']] [$id] ['brand'] = false;
        }
        $topics = SqlTools::selectRows ( "SELECT * FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', array_keys ( $products ) ) . ")", MYSQL_ASSOC, 'id' );
        foreach ( $topics as $id => $topic )
        {
            $topics[$id]['link'] = $this->Link ( $topic['id'] );
            $topics[$id]['types'] = unserialize ( $topics[$id]['types'] );
            //debug($topics[$id]['types']);
        }

        //Характеристики
        foreach ( $products as $top_id => $product_group )
        {
            foreach ( $product_group as $prod_id => $product )
            {
                $types = array ();
                $pTypes = $product['types'];
                if ( is_array ( $topics[$top_id]['types'] ) )
                    foreach ( $topics[$top_id]['types'] as $groupKey => $group )
                    {
                        $types[$groupKey]['name'] = $group['name'];
                        foreach ( $group['types'] as $typeKey => $type )
                        {

                            switch ( $type['type'] )
                            {
                                case 'float':
                                    if ( $pTypes[$groupKey][$typeKey] !== '' )
                                    {
                                        $types[$groupKey]['types'][$typeKey] = array (
                                            'name' => $type['name'],
                                            'desc' => $type['desc'],
                                            'val' => $pTypes[$groupKey][$typeKey] . $type['unit']
                                        );
                                    }
                                    break;

                                case 'range':
                                    if ( isset ( $pTypes[$groupKey][$typeKey]['from'] ) && isset ( $pTypes[$groupKey][$typeKey]['to'] ) )
                                        if ( $pTypes[$groupKey][$typeKey]['from'] !== '' || $pTypes[$groupKey][$typeKey]['to'] !== '' )
                                        {
                                            if ( $pTypes[$groupKey][$typeKey]['from'] !== '' && $pTypes[$groupKey][$typeKey]['to'] !== '' )
                                            {
                                                if ( $pTypes[$groupKey][$typeKey]['from'] == $pTypes[$groupKey][$typeKey]['to'] )
                                                {
                                                    $val = $pTypes[$groupKey][$typeKey]['from'] . $type['unit'];
                                                }
                                                else
                                                {
                                                    $val = $pTypes[$groupKey][$typeKey]['from'] . '—' . $pTypes[$groupKey][$typeKey]['to'] . $type['unit'];
                                                }
                                            }
                                            elseif ( $pTypes[$groupKey][$typeKey]['from'] !== '' )
                                            {
                                                $val = 'от ' . $pTypes[$groupKey][$typeKey]['from'];
                                            }
                                            elseif ( $pTypes[$groupKey][$typeKey]['to'] !== '' )
                                            {
                                                $val = 'до ' . $pTypes[$groupKey][$typeKey]['to'];
                                            }
                                            $types[$groupKey]['types'][$typeKey] = array (
                                                'name' => $type['name'],
                                                'desc' => $type['desc'],
                                                'val' => $val
                                            );
                                        }
                                    break;

                                case 'yn':
                                    if ( $pTypes[$groupKey][$typeKey] == 'Y' )
                                        $types[$groupKey]['types'][$typeKey] = array (
                                            'name' => $type['name'],
                                            'desc' => $type['desc'],
                                            'val' => 'Есть'
                                        );
                                    if ( $pTypes[$groupKey][$typeKey] == 'N' )
                                        $types[$groupKey]['types'][$typeKey] = array (
                                            'name' => $type['name'],
                                            'desc' => $type['desc'],
                                            'val' => 'Нет'
                                        );
                                    break;

                                case 'select':
                                    if ( $pTypes[$groupKey][$typeKey] !== '' && $pTypes[$groupKey][$typeKey] !== 0 )
                                    {
                                        if ( isset ( $type['select'][$pTypes[$groupKey][$typeKey]] ) )
                                        {
                                            $types[$groupKey]['types'][$typeKey] = array (
                                                'name' => $type['name'],
                                                'desc' => $type['desc'],
                                                'val' => $type['select'][$pTypes[$groupKey][$typeKey]]
                                            );
                                        }
                                    }
                                    break;

                                case 'text':
                                    if ( !empty ( $pTypes[$groupKey][$typeKey] ) )
                                    {
                                        $types[$groupKey]['types'][$typeKey] = array (
                                            'name' => $type['name'],
                                            'desc' => $type['desc'],
                                            'val' => $pTypes[$groupKey][$typeKey]
                                        );
                                    }
                                    break;
                            }
                        }
                        if ( empty ( $types[$groupKey]['types'] ) )
                            unset ( $types[$groupKey] );
                    }

                $products [$top_id] [$prod_id] ['rtypes'] = $types;
            }

            //Нахождение различающихся характеристик
            if ( is_array ( $topics[$top_id]['types'] ) )
                foreach ( $topics[$top_id]['types'] as $groupKey => $types )
                {
                    foreach ( $types['types'] as $typeKey => $type )
                    {
                        $stored = null;
                        $topics[$top_id]['types'][$groupKey]['types'][$typeKey]['equal'] = true;
                        foreach ( $products[$topic['id']] as $product )
                        {
                            if ( !is_null ( $stored ) && $stored != $product['types'][$groupKey][$typeKey] )
                            {
                                $topics[$top_id]['types'][$groupKey]['types'][$typeKey]['equal'] = false;
                                break;
                            }
                            $stored = $product['types'][$groupKey][$typeKey];
                        }
                    }
                }
        }

        $text = tpl ( 'modules/' . __CLASS__ . '/compare', array (
            'products' => $products,
            'topics' => $topics
            ) );

        return tpl ( 'page', array (
            'title' => $this->actions['compare']['name'],
            'name' => $this->actions['compare']['name'],
            'text' => $text
            ) );
    }

    private function SelectionBlock ()
    {
        if ( empty ( $this->currentTopicTypes ) )
            $this->currentTopicTypes = unserialize ( $this->topic->types );

        if ( empty ( $this->currentTopicTypes ) )
            return;

        if ( empty ( $this->currentTopicTypes ) )
            return;

        //Сортировка групп
        usort ( $this->currentTopicTypes, function($a, $b)
        {
            if ( $a['order'] == $b['order'] )
                return 0;
            return ($a['order'] < $b['order']) ? -1 : 1;
        } );

        //Подготовка списка характеристик
        $readyTypes = array ();
        foreach ( $this->currentTopicTypes as $groupKey => $group )
        {
            foreach ( $group['types'] as $typeKey => $type )
            {
                if ( $type['main'] )
                {
                    $type['groupKey'] = $groupKey;
                    $type['typeKey'] = $typeKey;
                    if ( isset ( $_GET['select'][$groupKey][$typeKey] ) )
                    {
                        switch ( $type['type'] )
                        {
                            case 'float': case 'range':
                                if ( isset ( $_GET['select'][$groupKey][$typeKey]['from'] ) && isset ( $_GET['select'][$groupKey][$typeKey]['to'] ) )
                                {
                                    //Не задано ОТ
                                    if ( empty ( $_GET['select'][$groupKey][$typeKey]['from'] ) && !empty ( $_GET['select'][$groupKey][$typeKey]['to'] ) )
                                    {
                                        $type['val']['to'] = ( float ) $_GET['select'][$groupKey][$typeKey]['to'];
                                        $this->productsFilter['select'][$groupKey][$typeKey]['to'] = $type['val']['to'];
                                    }
                                    //Не задано ДО
                                    elseif ( !empty ( $_GET['select'][$groupKey][$typeKey]['from'] ) && empty ( $_GET['select'][$groupKey][$typeKey]['to'] ) )
                                    {
                                        $type['val']['from'] = ( float ) $_GET['select'][$groupKey][$typeKey]['from'];
                                        $this->productsFilter['select'][$groupKey][$typeKey]['from'] = $type['val']['from'];
                                    }
                                    //Все задано
                                    elseif ( !empty ( $_GET['select'][$groupKey][$typeKey]['from'] ) && !empty ( $_GET['select'][$groupKey][$typeKey]['to'] ) )
                                    {
                                        $type['val']['from'] = ( float ) $_GET['select'][$groupKey][$typeKey]['from'];
                                        $type['val']['to'] = ( float ) $_GET['select'][$groupKey][$typeKey]['to'];
                                        $this->productsFilter['select'][$groupKey][$typeKey]['from'] = $type['val']['from'];
                                        $this->productsFilter['select'][$groupKey][$typeKey]['to'] = $type['val']['to'];
                                    }
                                }
                                break;

                            case 'yn':
                                if ( $_GET['select'][$groupKey][$typeKey] == 'Y' )
                                {
                                    $type['val'] = 'Y';
                                    $this->productsFilter['select'][$groupKey][$typeKey] = $type['val'];
                                }
                                break;

                            case 'select':
                                if ( in_array ( $_GET['select'][$groupKey][$typeKey], array_keys ( $type['select'] ) ) )
                                {
                                    $type['val'] = $_GET['select'][$groupKey][$typeKey];
                                    $this->productsFilter['select'][$groupKey][$typeKey] = $type['val'];
                                }
                                break;
                        }
                    }
                    $readyTypes[] = $type;
                }
            }
        }

        if ( empty ( $readyTypes ) )
            return;

        //Добавочный URL
        $link = getget ( array ( 'page' => false, 'select' => false ), 1 );

        //Очищающий URL
        $cleanLink = getget ( array ( 'page' => false, 'select' => false ) );

        return tpl ( 'modules/' . __CLASS__ . '/selection', array (
            'types' => $readyTypes,
            'link' => $link,
            'cleanLink' => $cleanLink
            ) );
    }

    function SelectionFilter ( &$products )
    {
        foreach ( $products as $k => $product )
        {
            //Производители
            if ( isset ( $this->productsFilter['brands'] ) && !in_array ( $product->brandId, $this->productsFilter['brands'] ) )
            {
                unset ( $products[$k] );
            }
            //Цена
            if ( isset ( $this->productsFilter['price'] ) && ($product['price'] < $this->productsFilter['price']['form'] || $product['price'] > $this->productsFilter['price']['to']) )
            {
                unset ( $products[$k] );
            }
            //Наличие
            if ( $this->productsFilter['exist'] && $product['is_exist'] != 'Y' )
            {
                unset ( $products[$k] );
            }
            //Характеристики
            if ( !empty ( $this->currentTopicTypes ) )
                $tTypes = $this->currentTopicTypes;
            else
                $tTypes = $this->currentTopicTypes = unserialize ( $this->topic->types );
            if ( isset ( $this->productsFilter['select'] ) )
            {
                if ( empty ( $product['types'] ) )
                {
                    unset ( $products[$k] );
                    continue;
                }
                foreach ( $product['types'] as $groupKey => $group )
                {
                    if ( empty ( $group ) )
                    {
                        unset ( $products[$k] );
                        continue;
                    }
                    foreach ( $group as $typeKey => $type )
                    {
                        switch ( $tTypes[$groupKey]['types'][$typeKey]['type'] )
                        {
                            case 'float':
                                if ( !isset ( $this->productsFilter['select'][$groupKey][$typeKey] ) )
                                    continue;
                                //Если хоть что-то задано, а у товара эта характеристика не указана
                                if (
                                    (
                                    isset ( $this->productsFilter['select'][$groupKey][$typeKey]['from'] ) ||
                                    isset ( $this->productsFilter['select'][$groupKey][$typeKey]['to'] )
                                    ) &&
                                    $type === ''
                                )
                                {
                                    unset ( $products[$k] );
                                }
                                //Если не задано от скольки
                                if ( !isset ( $this->productsFilter['select'][$groupKey][$typeKey]['from'] ) )
                                {
                                    if ( $type > $this->productsFilter['select'][$groupKey][$typeKey]['to'] )
                                    {
                                        unset ( $products[$k] );
                                    }
                                }
                                //Если не задано до скольки
                                elseif ( !isset ( $this->productsFilter['select'][$groupKey][$typeKey]['to'] ) )
                                {
                                    if ( $type < $this->productsFilter['select'][$groupKey][$typeKey]['from'] )
                                    {
                                        unset ( $products[$k] );
                                    }
                                }
                                //Если задано все
                                elseif (
                                    $type < $this->productsFilter['select'][$groupKey][$typeKey]['from'] ||
                                    $type > $this->productsFilter['select'][$groupKey][$typeKey]['to']
                                )
                                {
                                    unset ( $products[$k] );
                                }
                                break;

                            case 'range':
                                if ( isset ( $this->productsFilter['select'][$groupKey][$typeKey]['from'] ) || isset ( $this->productsFilter['select'][$groupKey][$typeKey]['to'] ) )
                                {
                                    //Заданы обе границы (пользователем)
                                    if (
                                        isset ( $this->productsFilter['select'][$groupKey][$typeKey]['from'] ) &&
                                        isset ( $this->productsFilter['select'][$groupKey][$typeKey]['to'] )
                                    )
                                    {
                                        //У товара заданы обе границы
                                        if ( $type['from'] !== '' && $type['to'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to'] ||
                                                $this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
                                            )
                                                unset ( $products[$k] );
                                        }
                                        //У товара задана левая граница
                                        elseif ( $type['from'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
                                            )
                                                unset ( $products[$k] );
                                        }
                                        //У товара задана правая граница
                                        elseif ( $type['to'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
                                            )
                                                unset ( $products[$k] );
                                        }
                                    }
                                    //Задана левая граница (пользователем)
                                    elseif (
                                        isset ( $this->productsFilter['select'][$groupKey][$typeKey]['from'] )
                                    )
                                    {
                                        //У товара заданы обе границы
                                        if ( $type['from'] !== '' && $type['to'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
                                            )
                                                unset ( $products[$k] );
                                        }
                                        //У товара задана левая граница
                                        elseif ( $type['from'] !== '' )
                                        {
                                            // ok go
                                        }
                                        //У товара задана правая граница
                                        elseif ( $type['to'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
                                            )
                                                unset ( $products[$k] );
                                        }
                                    }
                                    //Задана правая граница (пользователем)
                                    elseif (
                                        isset ( $this->productsFilter['select'][$groupKey][$typeKey]['to'] )
                                    )
                                    {
                                        //У товара заданы обе границы
                                        if ( $type['from'] !== '' && $type['to'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
                                            )
                                                unset ( $products[$k] );
                                        }
                                        //У товара задана левая граница
                                        elseif ( $type['from'] !== '' )
                                        {
                                            if (
                                                $this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
                                            )
                                                unset ( $products[$k] );
                                        }
                                        //У товара задана правая граница
                                        elseif ( $type['to'] !== '' )
                                        {
                                            // ok go
                                        }
                                    }
                                }
                                break;

                            case 'yn':
                                if ( isset ( $this->productsFilter['select'][$groupKey][$typeKey] ) )
                                    if ( $type != $this->productsFilter['select'][$groupKey][$typeKey] )
                                    {
                                        unset ( $products[$k] );
                                    }
                                break;

                            case 'select':
                                if ( !isset ( $this->productsFilter['select'][$groupKey][$typeKey] ) || $this->productsFilter['select'][$groupKey][$typeKey] == 0 )
                                {
                                    continue;
                                }
                                if ( $type != $this->productsFilter['select'][$groupKey][$typeKey] )
                                {
                                    unset ( $products[$k] );
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

    public function ProductInBasketHint ( $product, $basketItems = array () )
    {
        if ( !is_array ( $product ) )
        {
            $id = abs ( ( int ) $product );
            $product = SqlTools::selectRow ( "
                    SELECT p.*, b.name AS `brand_name` FROM `prefix_products` AS p
                    LEFT JOIN `prefix_products_brands` AS b ON b.`id`=p.`brand`
                    WHERE p.`id`=$id
            ", MYSQL_ASSOC );
        }

        if ( empty ( $product ) )
            return '';

        if ( !count ( $basketItems ) )
        {
            $basketList = Starter::app ()->getModule ( "Basket" )->getBasketList ();
            $basketItems = $basketList ? ArrayTools::select ( $basketList->products, "productId", $product['id'] ) : array ();
        }

        if ( !count ( $basketItems ) )
            return "";

        $text = "<div>{$product['name']} {$product['brand_name']} в корзине<br>";
        $tags = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", "FilterTag", "id" );
        foreach ( $basketItems as $item )
        {
            $features = array ();
            foreach ( $tags as $tagId => $tag )
            {
                if ( is_array ( $item->features ) && array_key_exists ( $tagId, $item->features ) )
                    $features[] = "$tag->name {$item->features[$tagId]} - $item->quantity " . plural ( $item->quantity, 'штук', 'штука', 'штуки' );
            }
            $featuresText = implode ( "<br>", $features );
            $text .= "$featuresText</div>";
            //$text .= 'Вы можете:<br>добавить товар в корзину, нажав кнопку "Добавить",<br> <a href="' . linkByModule ( 'Basket' ) . '">оформить заказ</a> или <a href="' . $this->Link ( $product['top'] ) . '">продолжить покупки</a>. ';
            //$text .=!empty ( $product['relations'] ) ? 'Обратите внимание, что еще покупают с этим товаром. ' : '';
        }

        return $text;
    }
    /*
     * Блок вывода товаров
     * @param int $rate - Минимальный рейтинг товара, при котором товар попадает в вывод
     * @param int $limit - Количество товаров, которые попадают в вывод
     */

    public function ProductsScroller ( $rate = 0, $limit = 50 )
    {
        $topics = SqlTools::selectRows ( "SELECT * FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y'", MYSQL_ASSOC, "id" );
        $topicsIds = array_keys ( $topics );
        $params = array
            (
            "deleted" => "N",
            "show" => "Y",
            "exist" => "Y",
            "rateMore" => ( int ) $rate,
            "top" => $topicsIds,
            "orderBy" => "p.`is_lider`, p.`rate` DESC",
            "limit" => $limit
        );

        //$where = "p.`deleted`='N' AND p.`show`='Y' AND p.`is_exist`='Y' AND p.`rate`>=" . ( int ) $rate . " AND p.`top` IN (" . ArrayTools::numberList ( $topicsIds ) . ")";
        //$order = "p.`is_lider`, p.`rate` DESC LIMIT " . $limit;
        $products = $this->findProducts ( $params, null, "id" );
        if ( !count ( $products ) )
            return "";
        $brandsIds = ArrayTools::pluck ( $products, "brandId" );
        if ( !count ( $brandsIds ) )
            $brands = array ();
        else
        {
            $brandFind = array
                (
                "deleted" => "N",
                "show" => "Y",
                "id" => $brandsIds
            );
            $brands = $this->findBrands ( $brandFind );
        }

        foreach ( $topics as $id => $topic )
        {
            $topic['link'] = $this->Link ( $topic['id'] );
            $topics[$id] = $topic;
        }

        foreach ( $products as $product )
        {
            //Наименование
            if ( !$product->shortName || trim ( $product->shortName ) == "" )
            {
                $name = trim ( $product->name );
                $product->shortName = substr ( $name, 0, 64 ) . (strlen ( $name ) >= 64 ? " ..." : "");
            }

            //Топик
            $product->topic = $topics[$product->top];

            //Бренд
            if ( isset ( $brands[$product->brandId] ) )
            {
                $brand = $brands[$product->brandId];
                $brand->link = $this->localCacheLink . '/brands/' . $brand->nav;
                $product->brand = $brand;
            }
            else
            {
                $product->brand = false;
            }
        }

        return tpl ( 'modules/' . __CLASS__ . '/promoBlocks/slidersScroller', array ( 'products' => $products ) );
    }

    public function moreProductsScroller ( $needId )
    {
        if ( !$needId || trim ( $needId ) == "" )
        {
            return false;
        }
        // топик для этого ИД
        $needTopic = SqlTools::selectValue (
                "SELECT `top` FROM `prefix_products` WHERE `id`=$needId" );

        // вытаскиваем продукцию для отбражения
        $products = SqlTools::selectRows (
                "SELECT * FROM `prefix_products` "
                . "WHERE `deleted`='N' AND `show`='Y' AND `is_exist`='Y' AND `top`=$needTopic "
                . "ORDER BY `is_lider`, `rate` DESC LIMIT 50", MYSQL_ASSOC, 'id' );

        $brandsIds = array ();
        $ids = array ();
        foreach ( $products as $k => $product )
        {
            $ids[] = $product['id'];
            $brandsIds[] = $product['brand'];
        }

        Starter::app ()->imager->prepareImages ( 'Catalog', $ids, true );
        // вытаскиваем бренды выбранной ранее продукции
        $pbrands = SqlTools::selectRows ( "SELECT * FROM `prefix_products_brands` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', $brandsIds ) . ")", MYSQL_ASSOC );
        $brands = array ();
        foreach ( $pbrands as $brand )
        {
            $brands[$brand['id']] = $brand;
        }

        foreach ( $products as $k => $product )
        {
            //Цена
            $products[$k]['priceOld'] = $this->calculatePrice ( $product['price'] );
            $products[$k]['price'] = $this->calculatePrice ( $product['price'], $product['discount'] );

            //Наименование
            if ( !array_key_exists ( 'shortName', $products[$k] ) || trim ( $products[$k]['shortName'] ) == "" )
            {
                $name = trim ( $products[$k]['name'] );
                $products[$k]['shortName'] = substr ( $name, 0, 64 ) . (strlen ( $name ) >= 64 ? " ..." : "");
            }

            //Ссылка
            $products[$k]['link'] = $this->Link ( $product['top'], $product['id'] );

            //Топик
            $products[$k]['topic'] = $needTopic;

            //Производители
            if ( isset ( $brands[$product['brand']] ) )
            {
                $products[$k]['brand'] = $brands[$product['brand']];
            }
            else
            {
                $products[$k]['brand'] = false;
            }
        }

        return tpl ( 'modules/' . __CLASS__ . '/promoBlocks/slidersMoreProductScroller', array (
            'products' => $products
            ) );
    }

    public function smallProductImagesScroller ( $needId )
    {
        if ( !$needId || trim ( $needId ) == "" )
        {
            return false;
        }
        $images = Starter::app ()->imager->getImages ( 'Catalog', $needId );

        return tpl ( 'modules/' . __CLASS__ . '/promoBlocks/smallProductImagesScroller', array (
            'images' => $images
            ) );
    }
    /*
     * Вывод кнопки "Купить"
     *
     * @param int $id Идентификатор продукта
     * @param string $format Форматы: one, inlist
     */

    public function BuyButton ( $id, $format = 'one' )
    {
        $basket = Starter::app ()->getModule ( "Basket" )->getBasketList ();
        if ( !$basket )
            $inbasket = false;
        else
        {
            $inbasket = count ( ArrayTools::select ( $basket->products, "productId", $id ) ) > 0;
        }

        return tpl ( 'modules/' . __CLASS__ . '/buyButton', array (
            'id' => $id,
            'format' => $format,
            'inbasket' => $inbasket
            ) );
    }

    /**
     * Рассчитывает цену позиции в каталоге из цены в прайсе
     * @param Float $price цена позиции в прайсе
     * @param Float $discount скидка на на позицию
     * @return Float Цена позиции по каталогу
     * @todo удалить после замены вызовов на calculatePrice
     */
    function Price ( $price, $discount = 0 )
    {
        if ( $price > 0 )
        {
            //Расчет курса (выполняется один раз!)
            if ( empty ( $this->currency ) )
            {
                $currencies = unserialize ( getVar ( 'currency' ) );
                $selectedCurrency = Tools::getSettings ( 'Catalog', 'inner_currency' );
                if ( $currencies && isset ( $currencies[$selectedCurrency] ) && $currencies[$selectedCurrency] > 0 )
                    $this->currency = $currencies[$selectedCurrency];
                else
                    $this->currency = 1;

                //Надбавка к конвертации
                if ( $this->currency > 1 )
                    $this->currency = $this->currency * ( 1 + (Tools::getSettings ( 'Catalog', 'currency_margin' ) / 100) );
            }
            //Конвертация цены товара от валюты
            $price *= $this->currency;

            //Расчет скидки на товар (акции и подобное)
            $price *= (1 - $discount / 100);

            //Округление
            $price = round ( $price, ( float ) Tools::getSettings ( 'Catalog', 'price_round' ) );
        }

        return $price;
    }
    /**
     *
     * @var Float Курс валюты каталога с учетом процентов на конвертайию
     */
    private $currency;
    private $rate;

    /**
     * Рассчитывает цену позиции в каталоге из цены в прайсе
     * @param Float $price цена позиции в прайсе
     * @param Float $discount скидка на на позицию
     * @return Float Цена позиции по каталогу
     */
    function calculatePrice ( $price, $discount = 0 )
    {
        if ( $price > 0 )
        {
            //Расчет курса (выполняется один раз!)
            if ( empty ( $this->rate ) )
            {
                $currencies = unserialize ( getVar ( 'currency' ) );
                $selectedCurrency = Tools::getSettings ( 'Catalog', 'inner_currency' );
                if ( $currencies && isset ( $currencies[$selectedCurrency] ) && $currencies[$selectedCurrency] > 0 )
                    $this->rate = $currencies[$selectedCurrency];
                else
                    $this->rate = 1;

                //Надбавка к конвертации
                if ( $this->rate > 1 )
                    $this->rate = $this->rate * ( 1 + (Tools::getSettings ( 'Catalog', 'currency_margin' ) / 100) );
            }
            //Конвертация цены товара от валюты
            $price *= $this->rate;

            //Расчет скидки на товар (акции и подобное)
            $price *= (1 - $discount / 100);

            //Округление
            $price = round ( $price, ( float ) Tools::getSettings ( 'Catalog', 'price_round' ) );
        }

        return $price;
    }

    public function getFullCatalog ()
    {
        return $this->getCatalogTree ();
    }

    /**
     * <pre>Возвращает html-текст меню каталога</pre>
     * @return String <p>html-текст меню</p>
     */
    function MenuSlider ()
    {
        $counts = ArrayTools::index ( SqlTools::selectObjects ( "SELECT `top` , COUNT(`id`) AS `count` FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' GROUP BY `top`" ), "top" );
        $active_ids = array ();

        if ( !empty ( $this->path ) )
        {
            foreach ( $this->path as $v )
            {
                if ( $v['type'] == 'topic' )
                {
                    $active_ids[] = $v['data']['id'];
                }
            }
        }
        $topics = $this->data->GetData ( 'products_topics', "AND `show` = 'Y'" );
        //$topicsByTop = array ( );
        $newTopics = array ();
        foreach ( $topics as $topic )
        {
            $topic['link'] = $this->Link ( $topic['id'] );
            $topic['count'] = isset ( $counts[$topic['id']] ) ? $counts[$topic['id']]->count : 0;

            $desc = SqlTools::selectRow ( "SELECT `description` FROM `prefix_seo` WHERE `module_table`='products_topics' AND `module_id`=" . $topic['id'] );
            $topic['desc'] = $desc['description'];
            $topic = ( Object ) $topic;
            $newTopics[$topic->id] = $topic;
        }

        foreach ( $newTopics as $topic )
        {
            if ( $topic->count != 0 )
                $slider[] = $topic;
        }

        $html = TemplateEngine::view ( 'menuslider', array ( 'topics' => $slider ), __CLASS__ );
        return $html;
    }

    /**
     * Используется для вывода нескольких категорий на одной странице
     * Для этого задается ссылка вида: /catalog/getlist?p=id1,id2,id3...
     * @deprecated
     */
    function GetList ()
    {
        if ( $_SERVER['QUERY_STRING'] )
        {
            $preParametrs = '';
            if ( array_key_exists ( 'addGet', $_GET ) && $_GET['addGet'] )
            {
                $expressions = explode ( "&", $_GET['addGet'] );

                foreach ( $expressions as $expression )
                {
                    list($name, $value) = explode ( "=", $expression );
                    if ( $name == 'p' )
                    {
                        $preParametrs = urldecode ( $value );
                    }
                }
            }
            if ( array_key_exists ( 'p', $_GET ) && $_GET['p'] )
            {
                $preParametrs = $_GET['p'];
            }
            $ids = mysql_escape_string ( htmlspecialchars ( strip_tags ( $preParametrs ) ) );
            $link = $ids;
            $categoryArray = explode ( ',', $ids );

            $query = SqlTools::selectRows ( "SELECT `name` FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . $ids . ") " );
            $title = "";
            foreach ( $query as $key => $value )
            {
                $title = $title . $value['name'] . ", ";
            }
            $page_title = substr ( $title, 0, strlen ( $title ) - 2 );

            $sub = Array ();

            foreach ( $categoryArray as $value )
            {
                $sub = array_merge ( $sub, $this->getCatalogTree ( $value, true ) );
            }

            if ( !empty ( $sub ) )
            {
                //$parametrs = '';
                $subvalue = array ();
                foreach ( $sub as $key => $value )
                {
                    //$parametrs = $parametrs . $value->id . ",";
                    $subvalue[] = $value->id;
                }
                $params = implode ( ",", $subvalue );
                $parametrs = substr ( $params, 0, strlen ( $params ) - 2 );
            }

            $additional_brand_title = '';
            $product_ids = array ();
            $addGet = array ();

            //SEO
            $this->seo ( 'products_topics', $this->topic->id );
            //Запрос
            if ( isset ( $_GET['addGet'] ) )
            {
                parse_str ( $_GET['addGet'], $addGet );
                unset ( $_GET['addGet'] );
                $_GET = array_merge ( $_GET, $addGet );
            }
            //Хар-ки топика
            if ( empty ( $this->currentTopicTypes ) )
                $this->currentTopicTypes = unserialize ( $this->topic->types );

            //Сортировка
            $this->sortPanel ();
            if ( isset ( $this->sorting ) )
            {
                $sortField = '`' . $this->sorting['field'] . '`';
                if ( $this->sorting['field'] == 'price' )
                    $sortField = '(`price` * (1 - `discount` / 100))';

                $sqlOrder = 'ORDER BY `is_exist`, ' . $sortField . ' ' . $this->sorting['direction'];
            }
            else
                $sqlOrder = 'ORDER BY `rate` DESC';


            $products = SqlTools::selectRows ( "
			SELECT p.*, b.name AS `brand_name` FROM `prefix_products` AS p
			LEFT JOIN `prefix_products_brands` AS b ON b.`id`=p.`brand`
			WHERE
				p.`deleted`='N' AND
				p.`show`='Y' AND p.`top` IN ( " . $parametrs . " ) " . $sqlOrder, MYSQL_ASSOC );


            foreach ( $products as $k => $product )
            {
                //Цена
                $products[$k]['priceOld'] = $this->Price ( $product['price'] );
                //$product->price = $this->calculatePrice($product->price);
                $products[$k]['price'] = $this->Price ( $product['price'], $product['discount'] );
                //$product->finalPrice = $this->calculatePrice($product->price, $product->discount);
                //Распаковка характеристик
                $products[$k]['types'] = unserialize ( $product['types'] );
                //$product->types = unserialize($product->types);
                //Ссылка
                $products[$k]['link'] = $this->Link ( $product['top'], $product['id'] );
                //$product->link = $this->Link( $product->top, $product->id  );
                //Сравнение
                $products[$k]['inCompare'] = false;
                //$product->inCompare = false;
                if ( isset ( $_SESSION['compare'] ) && is_array ( $_SESSION['compare'] ) )
                {
                    //if ( isset( $_SESSION['compare'][$product->id] ) )
                    if ( isset ( $_SESSION['compare'][$product['id']] ) )
                        $products[$k]['inCompare'] = true; //$product->inCompare = true;
                }

                //Сниппет
                $snippet = array ();
                //if ( !empty( $this->currentTopicTypes ) && $product->types ) )
                if ( !empty ( $this->currentTopicTypes ) && !empty ( $products[$k]['types'] ) )
                {
                    //foreach ( $product->types as $groupKey => $group )
                    foreach ( $products[$k]['types'] as $groupKey => $group )
                    {
                        foreach ( $group as $typeKey => $type )
                        {
                            if ( !empty ( $type ) )
                            {
                                if ( isset ( $this->currentTopicTypes[$groupKey]['types'][$typeKey] ) && $this->currentTopicTypes[$groupKey]['types'][$typeKey]['main'] )
                                {
                                    switch ( $this->currentTopicTypes[$groupKey]['types'][$typeKey]['type'] )
                                    {
                                        case 'float':
                                            if ( $type !== '' )
                                                $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': ' . $type . '' . $this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
                                            break;

                                        case 'range':
                                            if ( $type['from'] !== '' && $type['to'] !== '' )
                                            {
                                                if ( $type['from'] == $type['to'] )
                                                    $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': ' . $type['from'] . '' . $this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
                                                else
                                                    $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': ' . $type['from'] . '—' . $type['to'] . '' . $this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
                                            }
                                            elseif ( $type['from'] !== '' )
                                                $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': от ' . $type['from'] . '' . $this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
                                            elseif ( $type['to'] !== '' )
                                                $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': до ' . $type['to'] . '' . $this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
                                            break;

                                        case 'yn':
                                            if ( $type == 'Y' )
                                                $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'];
                                            break;

                                        case 'select':
                                            if ( $type !== '' && $type !== 0 )
                                            {
                                                if ( isset ( $this->currentTopicTypes[$groupKey]['types'][$typeKey]['select'][$type] ) )
                                                    $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': ' . $this->currentTopicTypes[$groupKey]['types'][$typeKey]['select'][$type];
                                            }
                                            break;

                                        case 'text':
                                            if ( !empty ( $type ) )
                                                $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'] . ': ' . $type;
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
                $products[$k]['snippet'] = $snippet;
                //$product->snippet = $snippet;
            }

            //Бренды

            if ( isset ( $_GET['brands'] ) && is_array ( $_GET['brands'] ) )
                $sBrands = $_GET['brands'];
            else
                $sBrands = array ();
            /* заменить на закоментированное ниже */
            $brands_ids = array ();
            foreach ( $products as $p )
            {
                $brands_ids[] = $p['brand'];
            }
            //$brands_ids = array_unique(ArrayTools::pluck($products, "brand"));
            $brands = array ();
            if ( count ( $brands_ids ) )
            {
                $brands = SqlTools::selectRows ( "
				SELECT * FROM `prefix_products_brands`
				WHERE
					`deleted`='N' AND
					`show`='Y' AND
					`id` IN (" . implode ( ',', $brands_ids ) . ")
				ORDER BY `order`
			", MYSQL_ASSOC );

                $query = "SELECT * FROM `prefix_products_brands`
				WHERE
					`deleted`='N' AND
					`show`='Y' AND
					`id` IN (" . implode ( ',', $brands_ids ) . ")
				ORDER BY `order`";
                //$brands = SqlTools::selectObject($query);
                //foreach ( $brands as $k => $brand )
                foreach ( $brands as $k => $v )
                {
                    $brands[$k]['checked'] = false;
                    //$brand->checked = false;
                    if ( !empty ( $sBrands ) )
                    {
                        foreach ( $sBrands as $bk => $bv )
                        {
                            //if ( $bv == $brand->nav )
                            if ( $bv == $v['nav'] )
                            {
                                $brands[$k]['checked'] = true;
                                //$brand->checked = true;
                                $additional_brand_title = $brands[$k]['name'];
                                //$additional_brand_title = $brand->name;
                                $this->productsFilter['brands'][] = $brands[$k]['id'];
                                //$this->productsFilter['brands'][] = $brand->id;
                            }
                        }
                    }
                    $brands[$k]['link'] = getget ( array ( 'brands' => array ( $v['nav'] ), 'page' => false ) );
                    //$brand->link = getget( array ( 'brands' => array ( $brand->nav ), 'page' => false ) );
                }
            }
            if ( count ( $sBrands ) > 1 )
                $additional_brand_title = '';

            //Диапазон цен для слайдера
            $price_range = SqlTools::selectRow ( "SELECT MIN(`price`) AS `min`, MAX(`price`) AS `max` FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' AND `top` IN (" . $parametrs . ")" );
            $query = "SELECT
                MIN(`price`) AS `min`,
                MAX(`price`) AS `max`
             FROM `prefix_products`
             WHERE `deleted`='N'
                AND `show`='Y'
                AND `top`=" . ( int ) $this->topic->id;

            $discounted_min = SqlTools::selectRow ( "SELECT `price` * (1 - `discount` / 100) AS `dprice` FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' AND `top` IN (" . $parametrs . ") ORDER BY `dprice` ASC LIMIT 1" );
            $priceMin = $this->calculatePrice ( $discounted_min['dprice'] ); //Скидка здесь уже содержится, пересчитывать ее не нужно
            $price_min = $priceMin < 0 ? 0 : $priceMin;
            $priceMax = $this->calculatePrice ( $price_range['max'] );
            $price_max = $priceMax < 0 ? 0 : $priceMax;
            $price_from = isset ( $_GET['PriceFromValue'] ) ? ( int ) $_GET['PriceFromValue'] : $price_min;
            $price_to = isset ( $_GET['PriceToValue'] ) ? ( int ) $_GET['PriceToValue'] : $price_max;
            $slider_vals = array (
                'min' => $price_min,
                'max' => $price_max,
                'from' => $price_from,
                'to' => $price_to,
                'step' => pow ( 10, abs ( Tools::getSettings ( 'Catalog', 'price_round' ) ) )
            );
            if ( isset ( $_GET['PriceFromValue'] ) && isset ( $_GET['PriceToValue'] ) )
            {
                $this->productsFilter['price'] = array (
                    'form' => $price_from,
                    'to' => $price_to
                );
            }

            //Только в наличии
            if ( isset ( $_GET['exist'] ) )
                $this->productsFilter['exist'] = true;
            else
                $this->productsFilter['exist'] = false;

            //Блок подбора
            $selection = $this->SelectionBlock ();

            //Фильтр товаров по производителям, цене и характеристикам
            //todo переделать SelectionFilter на работу с объектом
            //$this->SelectionFilter ( $products );
            //Пэйджинг
            $products_onpage = Tools::getSettings ( __CLASS__, 'onpage', 3 );
            $products_pages = Tools::getSettings ( __CLASS__, 'show_pages', 5 );
            $products_paged = Paging ( $products, $products_onpage, $products_pages );
            $productsPaged = $products_paged['items'];
            $paging = $products_paged['rendered'];

            $this->paging_rendered = $products_paged['rendered'];

            unset ( $products_paged );

            //$brand_price_link = '';
            $brand_price_link = getget ( array ( 'page' => false, 'brands' => false, 'PriceFromValue' => false, 'PriceToValue' => false ), 1 );
            //Подготавливаем картинки
            foreach ( $productsPaged as $product )
            {
                $product_ids[] = $product['id'];
            }
            Starter::app ()->imager->prepareImages ( 'Catalog', $product_ids );
            $link = "/catalog/getlist?p=" . $link;

            return tpl ( 'modules/' . __CLASS__ . '/list', array (
                'title' => $this->actions['getlist']['name'],
                'name' => $page_title,
                'brands' => $brands,
                'brand_price_link' => $brand_price_link,
                'slider_vals' => $slider_vals,
                'exist' => $this->productsFilter['exist'],
                'link' => $link,
                'selection' => $selection,
                'products' => $productsPaged,
                'paging' => $paging,
                'currentCategory' => $this->topic
                ) );
        }
        else
            page404 ();
    }

    function tagsFilter ( $category = null )
    {
        $category = $category ? : ( $this->topic->id ? : 0 );
        $urlManager = Starter::app ()->urlManager;

        $filterTags = array ();
        $filterTags["price"] = new FilterTag ( "price", "Цена", "INTERVAL", $this->getCategoryPrices ( $category ) );
        $filterTags["brand"] = new Filtertag ( "brand", "Производитель", "SET", $this->getCategoryBrands ( $category ) );

        $parentCats = Tools::getParentCategories ( $category );
        $childCats = Tools::getChildrenCategories ( $category );

        $productIds = array_keys ( SqlTools::selectObjects ( "SELECT id FROM `prefix_products` WHERE `top` IN (" . ArrayTools::numberList ( $childCats ) . ")", null, "id" ) );
        $productIds = ArrayTools::numberList ( $productIds );
        $categoriesIds = ArrayTools::numberList ( array_unique ( array_merge ( $childCats, $parentCats ) ) );
        $orWhere = $productIds ? "OR (`module`!='Catalog' AND `moduleId` IN ($productIds))" : "";
        $tags = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", "FilterTag", "id" );

        $cats = Tools::getParentCategories ( $category );
        $query = "SELECT
                `id` AS id,
                `module` AS module,
                `moduleId` AS moduleId,
                `tagId` AS tagId,
                `value` AS value
            FROM `prefix_tags_values`
            WHERE ((`module`='Catalog' AND `moduleId` IN ($categoriesIds))
                $orWhere)
                AND `show`='Y'";
        $tagsValues = SqlTools::selectObjects ( $query );

        //$tags = Tools::getParentCategoriesTags($category);

        foreach ( $tags as $tag )
        {
            $tagValues = ArrayTools::pluck ( ArrayTools::select ( $tagsValues, "tagId", $tag->id ), "value" );
            $tag->tagValues = array_unique ( $tagValues );
            $filterTags[$tag->id] = $tag;
        }

        foreach ( $filterTags as $key => $fTag )
        {
            //Tools::dump($fTag);
            switch ( $fTag->tagType )
            {
                case "ENUM":

                    break;
                case "INTERVAL":
                    if ( count ( $fTag->tagValues ) !== 0 )
                    {
                        $fTag->min = min ( $fTag->tagValues ); //$from;
                        $fTag->max = max ( $fTag->tagValues ); //$to;
                        $fTag->pattern = "\d+(\.\d{2})?"; // число, разделитель точка
                    }
                    if ( array_key_exists ( "productsFilter", $_SESSION ) && !is_null ( $_SESSION["productsFilter"] ) )
                    {
                        $fTag->from = $_SESSION["productsFilter"][$fTag->alias]["from"];
                        $fTag->to = $_SESSION["productsFilter"][$fTag->alias]["to"];
                    }
                    else
                    {
                        $fTag->from = $urlManager->getParameter ( "tf_" . $fTag->alias . "_from" ) ? : $fTag->min;
                        $fTag->to = $urlManager->getParameter ( "tf_" . $fTag->alias . "_to" ) ? : $fTag->max;
                    }
                    break;
                case "SET":

                    break;
                default:
                    break;
            }
            // проставление значений из текущего фильтра
            $fTag->filterVal = $this->getTagFilterValues ( $key, $fTag->tagType );
        }

        return tpl ( 'modules/' . __CLASS__ . '/tagsFilter', array (
            'name' => "Подбор товара.",
            'link' => $this->Link ( $this->topic->id ),
            'currentCategory' => $category,
            'filterTags' => $filterTags,
            'isFilter' => $this->isFilter
            ) );
    }



    function ajaxGetProductsItems ()
    {
        if ( isset ( $_POST["id"] ) )
        {
            $this->topic->id = $_POST["id"];
        }

        return $this->getProductsItems ();
    }

    /** Возвращает массив уникальных значений характеристики $tagId,
     *  назначенных для товаров категории $categoryId и её дочерних
     * @param type $categoryId
     * @param type $tagId
     * @return array
     */
    /*
    function getTagValues ( $categoryId, $tagId )
    {
        $categoriesIds = Tools::getChildrenCategories ( $categoryId );

        $query = "SELECT `id` FROM `prefix_products` "
            . " WHERE `top` IN (" . implode ( ",", $categoriesIds ) . ") AND `deleted`='N'";
        $productIds = ArrayTools::numberList ( ArrayTools::pluck ( SqlTools::selectRows ( $query, MYSQL_ASSOC ), "id" ) );

        $query = "SELECT DISTINCT `value` FROM `prefix_products_tags_values` "
            . " WHERE "
            . " `tag_id`=$tagId"
            . " AND `product_id` IN ($productIds) "
            . " AND NOT ISNULL(`value`) AND `value`!='' "
            . " AND `deleted`='N'";
        $values = SqlTools::selectRows ( $query, MYSQL_ASSOC );

        $ret = ArrayTools::pluck ( $values, "value" );
        return $ret;
    }
    */
    /** Возвращает массив уникальных значений цен
     *  товаров категории $categoryId и её дочерних
     * @param Integer $categoryId
     * @return array
     */
    function getCategoryPrices ( $categoryId )
    {
        $whereClause = [];
        $categoriesIds = ArrayTools::numberList ( Tools::getChildrenCategories ( $categoryId ) );
        if ( $categoriesIds )
        {
            $whereClause[] = "p.`top` IN ($categoriesIds)";
        }
        else
        {
            if ( is_array ( $this->filters['brand'] ) && count ( $this->filters['brand'] ) )
            {
                $brandIds = ArrayTools::numberList ( $this->filters['brand'] );
                $whereClause[] = "p.`brand` IN ($brandIds)";
            }
        }


        $hasCurrencies = SqlTools::selectObjects ( "SELECT * FROM `prefix_products_currencies`" ) > 0;
        if ( !$hasCurrencies )
        {
            $whereClause[] = "`deleted`='N'";
            $query = "SELECT DISTINCT `price` FROM `prefix_products` WHERE " . implode ( " AND ", $whereClause );
        }
        else
        {
            $whereClause[] = "p.`deleted`='N'";
            $query = "SELECT DISTINCT p.`price`*c.`value` AS price FROM `prefix_products` p JOIN `prefix_products_currencies` c ON c.`id`=p.`currency`"
                . " WHERE " . implode ( " AND ", $whereClause );
        }
        $rows = SqlTools::selectRows ( $query, MYSQL_ASSOC );
        $prices = ArrayTools::pluck ( $rows, "price" );

        return $prices;
    }

    /** Возвращает массив [id => name] уникальных по id значений производителей
     *  товаров категории $categoryId и её дочерних
     * @param type $categoryId
     * @return array
     */
    function getCategoryBrands ( $categoryId )
    {
        $categoriesIds = ArrayTools::numberList ( Tools::getChildrenCategories ( $categoryId ) );

        $query = "SELECT DISTINCT p.`brand` AS id, pb.`name` AS name "
            . " FROM `prefix_products` p"
            . "   JOIN `prefix_products_brands` pb ON pb.`id`=p.`brand`"
            . " WHERE " . ( $categoriesIds ? "p.`top` IN ($categoriesIds) AND " : "" ) . "p.`deleted`='N'";
        $rows = SqlTools::selectRows ( $query, MYSQL_ASSOC );
        $brands = count ( $rows ) ? array_combine ( ArrayTools::pluck ( $rows, "id" ), ArrayTools::pluck ( $rows, "name" ) ) : array ();

        $brandName = Tools::getSettings ( "Catalog", "brandName", "" );
        if ( $brandName )
            $brands[0] = $brandName;

        return $brands;
    }

    function getWhereClausesFromParams ( $params )
    {
        $wClauses = array ();
        $namedParams = array ();
        foreach ( $params as $name => $param )
        {
            if ( !is_numeric ( $name ) )
            {
                $namedParams[$name] = $param;
            }
        }

        $hasCurrencies = SqlTools::selectObjects ( "SELECT * FROM `prefix_products_currencies`" ) > 0;
        foreach ( $namedParams as $name => $param )
        {
            switch ( $name )
            {
                case "price":
                    $expression = $hasCurrencies ? "p.`price`*c.`value`" : "p.`price`";
                    if ( isset ( $param['from'] ) && trim ( $param['from'] ) != "" )
                        $wClauses["priceNoLess"] = $param['from'];

                    if ( isset ( $param['to'] ) && trim ( $param['to'] ) != "" )
                        $wClauses["priceNoBigger"] = $param["to"];
                    break;
                case "brand": // раз set, в параметрах идёт набор id=>on, брать только id
                    if ( count ( $param ) > 0 )
                    {
                        $wClauses["brand"] = array_keys ( $param );
                    }
                    break;
                default:
                    break;
            }
        }

        return $wClauses;
    }

    public function getFeaturedList ( $column, $limit = 6 )
    {
        if ( $limit > 0 )
            $sql_limit = 'LIMIT ' . ( int ) $limit;
        else
            $sql_limit = '';

        $top = isset ( $this->topic ) ? ( int ) $this->topic->id : 0;
        $inTop = SqlTools::selectRows ( "SELECT `id` FROM `prefix_products_topics` WHERE `top`='" . $top . "' AND `deleted`='N'", MYSQL_ASSOC );
        $topicIds = array ( $top );
        foreach ( $inTop as $topicId )
        {
            if ( !in_array ( $topicId['id'], $topicIds ) )
                $topicIds[] = $topicId['id'];
        }

        if ( $top == 0 )
        {
            $query = "SELECT p.*, i.`src`
                FROM `prefix_products` p
                LEFT JOIN `prefix_images` i ON (p.`id`=i.`module_id` AND i.`module`='Catalog')
                WHERE p.`$column`='Y'
                    AND p.`is_exist`='Y'
                    AND p.`show`='Y'
                    AND p.`deleted`='N'
                    AND i.`main`='Y'
                ORDER BY RAND() $sql_limit";
        }
        else
        {
            $query = "SELECT *
                FROM `prefix_products` p LEFT JOIN `prefix_images` i ON (p.`id`=i.`module_id` AND i.`module`='Catalog')
                WHERE p.`$column`='Y'
                     AND p.`is_exist`='Y'
                     AND p.`show`='Y'
                     AND p.`deleted`='N'
                     AND p.`top` IN (" . implode ( ', ', $topicIds ) . ")
                     AND i.`main`='Y'
                ORDER BY RAND() $sql_limit";
        }


        //$products = SqlTools::selectRows($query, MYSQL_ASSOC);
        $products = SqlTools::selectObjects ( $query );
        $brands = array ();

        $categoriesId = array_unique ( ArrayTools::pluck ( $products, "top" ) );

        if ( !count ( $categoriesId ) )
            return array ();

        $productsCategories = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_products_topics` WHERE `deleted`='N' AND `show`='Y' AND `id` IN (" . implode ( ',', $categoriesId ) . ")" ), "id" );

        foreach ( $productsCategories as $category )
        {
            $category->link = $this->Link ( $category->id );
        }

        $realProducts = array (); //Массив товаров, находящихся в дереве существующих категорий
        foreach ( $products as $product )
        {
            //Если для товара отсутствует категория пропускаем его
            if ( !isset ( $productsCategories[$product->top] ) )
                continue;

            //Распаковка характеристик
            if ( !is_array ( $product->types ) )
                $product->types = unserialize ( $product->types );

            if ( !is_array ( $productsCategories[$product->top]->types ) )
                $productsCategories[$product->top]->types = unserialize ( $productsCategories[$product->top]->types );

            //Ссылка
            $product->link = $this->Link ( $product->top, $product->id );

            //Топик
            $product->topic = $productsCategories[$product->top];

            $product->priceOld = $product->price;
            $product->price = !is_null ( $product->discount ) ? $this->calculatePrice ( $product->price, $product->discount ) : $product->price;

            $brands = ArrayTools::index ( SqlTools::selectObjects ( "SELECT * FROM `prefix_products_brands` WHERE `deleted`='N' AND `show`='Y' AND `id`=" . $product->brand ), "id" );
            $product->brand = $product->brand && array_key_exists ( $product->brand, $brands ) ? $brands[$product->brand] : 0;
            $realProducts[] = $product;
        }

        return $realProducts;
    }

    public function paging ()
    {
        if ( $this->paging_rendered )
            return $this->paging_rendered;
        else
            return "";
    }

    public function getProductsList ()
    {
        if ( !$this->products )
        {
            $this->getProductsItems ();
        }

        $model = new DataModel();
        $model->dataModel = $this->products;
        $model->template = "productFeatures";

        return $model;
    }

    public function getCategoriesFeatures ( $categoryId = null )
    {
        $categoryId = $categoryId ? : $this->currentCategoryId;
        $childCategories = $this->childCategories ? : $this->getChildCategories ( $categoryId );
        $parentCategories = $this->parentCategories ? : $this->getParentCategories ( $categoryId );
        $categories = array_merge ( $parentCategories, array ( $categoryId ), $childCategories );

        $query = "SELECT
                    `id` AS id,
                    `module` AS module,
                    `moduleId` AS moduleId,
                    `tagId` AS tagId,
                    `value` AS tagValue
                FROM `prefix_tags_values`
                WHERE `module`='Catalog'
                    AND `moduleId` IN (" . ArrayTools::numberList ( $categories ) . ")
                    AND `show`='Y'";

        $categoriesFeatures = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", "FilterTag", "id" );


        $categoryId = $categoryId ? : $this->topic->id;
        $childCategories = Tools::getChildrenCategories ( $categoryId );
        $taggedCategories = array_unique ( array_merge ( $childCategories, Tools::getParentCategories ( $categoryId ) ) );
        $catIds = ArrayTools::numberList ( $taggedCategories );

        if ( !$productIds )
        {
            $query = "SELECT `id` FROM `prefix_products` WHERE `top` IN (" . ArrayTools::numberList ( $childCategories ) . ") AND `show`='Y' AND `deleted`='N'";
            $productIds = array_keys ( SqlTools::selectRows ( $query, MYSQLI_ASSOC, "id" ) );
        }

        $ids = $productIds ? ArrayTools::numberList ( $productIds ) : null;

//        $query = "SELECT * FROM `prefix_products_topics_tags` WHERE `topic_id` IN ($catIds) AND `show`='Y' AND `deleted`='N'";
//        $categoryFeatures = SqlTools::selectObjects ( $query, null, "id" );
//        if ( $productIds )
//        {
//            $ids = ArrayTools::numberList ( $productIds );
//            $query = "SELECT * FROM `prefix_products_tags_values` WHERE `product_id` IN ($ids)";
//        }
//        else
//            $query = "SELECT tags.* FROM `prefix_products_tags_values` tags
//                JOIN `prefix_products` p ON p.`id`=tags.`product_id`
//                WHERE p.`top`=" . $this->topic->id;
//
//        $productsFeatures = SqlTools::selectObjects ( $query, null );
//        $productIds = $productIds ? : array_unique ( ArrayTools::pluck ( $productsFeatures, "product_id" ) );

        if ( $ids )
        {
            $where = "((`module`='Catalog' AND `moduleId` IN ($catIds)) OR (`module`!='Catalog' AND `moduleId` IN ($ids)))";
        }
        else
            $where = "";



        $tagValues = SqlTools::selectObjects ( $query );
        $tags = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", "FilterTag", "id" );
        $productsFeatures = ArrayTools::select ( $tagValues, "module", "" );
        $categoryFeatures = ArrayTools::select ( $tagValues, "module", "Catalog" );
        foreach ( $productIds as $prodId )
        {
            $features = ArrayTools::select ( $productsFeatures, "moduleId", $prodId );
            $usedTags = array_unique ( array_merge ( ArrayTools::pluck ( $features, "tagId" ), ArrayTools::pluck ( $categoryFeatures, "tagId" ) ) );
            foreach ( $usedTags as $tagId )
            {
                $taggedFeatures = array_merge ( ArrayTools::select ( $features, "tagId", $tagId ), ArrayTools::select ( $categoryFeatures, "tagId", $tagId ) );
                $tag = $tags[$tagId];
                $values = array_unique ( ArrayTools::pluck ( $taggedFeatures, "value" ) );

                $productFeatures[$tag->id] = new ProductFeature ( $tag->id, $tag->alias, $tag->name, $tag->tagType, $values );
            }

            $this->productsFeatures[$prodId] = $productFeatures;
        }
        return $this->productsFeatures;
    }

    public function catalogTree ()
    {
        return TemplateEngine::view ( 'catalogTree', array (), __CLASS__ );
    }

    public function getFilterTags ( $param = null/* , $key = null */ )
    {
        return SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags` $param", "FilterTag", "id" );
    }

    private function _mainpage ()
    {
        if ( isset ( $_GET['brands'] ) && $_GET['brands'][0] != 'all' )
        {
            $additional_brand_title = '';
            $this->seo ( 'products_topics', $this->topic->id );
            $brandNav = $_GET['brands'][0];

            //Сортировка
            $this->processSorting ();
            //$this->sortPanel ();
            if ( isset ( $this->sorting ) )
            {
                $ordered = array ( "is_exist" => "", $this->sorting['field'] => $this->sorting['direction'] );
            }
            else
                $ordered = array ( "rate" => "" );

            $params = array
                (
                "deleted" => "N",
                "show" => "Y",
                "nav" => $brandNav,
                "orderBy" => $ordered
            );
            $products = $this->findProducts ( $params, null, "id" );

            if ( isset ( $_GET['brands'] ) && is_array ( $_GET['brands'] ) )
                $sBrands = $_GET['brands'];
            else
                $sBrands = array ();

            /* заменить на закоментированное ниже */
            $brandsIds = ArrayTools::pluck ( $products, "brandId" );
            $brands = array ();
            if ( count ( $brandsIds ) )
            {
                $brands = SqlTools::selectRows ( "
                    SELECT * FROM `prefix_products_brands`
                    WHERE `deleted`='N' AND `show`='Y'
                    ORDER BY `order`
                ", MYSQL_ASSOC, "id" );

                foreach ( $brands as $k => $brand )
                {
                    $brand['checked'] = false;
                    if ( !empty ( $sBrands ) )
                    {
                        foreach ( $sBrands as $searchedBrand )
                        {
                            if ( $searchedBrand == $brand['nav'] )
                            {
                                $brand['checked'] = true;
                                $additional_brand_title = $brand['name'];
                                $this->productsFilter['brands'][] = $brand['id'];
                            }
                        }
                    }
                    $brand['link'] = getget ( array ( 'brands' => array ( $brand['nav'] ), 'page' => false ) );
                    $brands[$k] = $brand;
                }
            }

            //Бренд
            foreach ( $products as $product )
            {
                if ( array_key_exists ( $product->brandId, $brands ) )
                {
                    $brand = $brands[$product->brandId];
                    $brand['link'] = getget ( array ( 'brands' => array ( $brand['nav'] ), 'page' => false ) );
                    $product->brand = $brand;
                }
            }

            if ( count ( $sBrands ) > 1 )
                $additional_brand_title = '';

            //Диапазон цен для слайдера
            $price_range = SqlTools::selectRow ( "SELECT
                    MIN(IF(c.`value`,c.`value`*p.`price`,p.`price`)) AS `min`,
                    MAX(IF(c.`value`,c.`value`*p.`price`,p.`price`)) AS `max`
                FROM `prefix_products` p
                JOIN `prefix_products_currencies` c ON c.`id`=p.`currency`
                WHERE p.`deleted`='N' AND p.`show`='Y'" );

            $discounted_min = SqlTools::selectRow ( "SELECT
                    IF(c.`value`,c.`value`*p.`price`,p.`price`)*(1 - p.`discount` / 100) AS `dprice`
                FROM `prefix_products` p
                JOIN `prefix_products_currencies` c ON c.`id`=p.`currency`
                WHERE p.`deleted`='N' AND p.`show`='Y'
                ORDER BY `dprice` ASC LIMIT 1" );

            $priceMin = $this->calculatePrice ( $discounted_min['dprice'] ) > 0 ? : 0;
            $priceMax = $this->calculatePrice ( $price_range['max'] ) > 0 ? : 0;
            $priceFrom = isset ( $_GET['PriceFromValue'] ) ? ( int ) $_GET['PriceFromValue'] : $priceMin;
            $priceTo = isset ( $_GET['PriceToValue'] ) ? ( int ) $_GET['PriceToValue'] : $priceMax;
            $slider_vals = array (
                'min' => $priceMin,
                'max' => $priceMax,
                'from' => $priceFrom,
                'to' => $priceTo,
                'step' => pow ( 10, abs ( Tools::getSettings ( 'Catalog', 'price_round' ) ) )
            );
            if ( isset ( $_GET['PriceFromValue'] ) && isset ( $_GET['PriceToValue'] ) )
            {
                $this->productsFilter['price'] = array (
                    'form' => $priceFrom,
                    'to' => $priceTo
                );
            }

            //Только в наличии
            if ( isset ( $_GET['exist'] ) )
                $this->productsFilter['exist'] = true;
            else
                $this->productsFilter['exist'] = false;

            //Пэйджинг
            $productsOnpage = Tools::getSettings ( __CLASS__, 'onpage', 3 );
            $productsPages = Tools::getSettings ( __CLASS__, 'show_pages', 5 );
            $productsPaged = Paging ( $products, $productsOnpage, $productsPages );
            $productList = $productsPaged['items'];
            $paging = $productsPaged['rendered'];

            $this->paging_rendered = $productsPaged['rendered'];

            unset ( $productsPaged );

            $productsItems = tpl ( 'modules/' . __CLASS__ . '/productsItems', array
                (
                'exist' => $this->productsFilter['exist'],
                'products' => $productList,
                'paging' => $paging,
                'currentCategory' => $this->topic,
                ) );

            //Финальные приготовления
            $page_title = $this->topic->name . (empty ( $additional_brand_title ) ? '' : ' ' . $additional_brand_title);
            $brand_price_link = getget ( array ( 'page' => false, 'brands' => false, 'PriceFromValue' => false, 'PriceToValue' => false ), 1 );

            if ( empty ( $products ) )
            {
                $empty = true;
            }
//$productItems = $this->getProductsItems();
            Starter::app ()->headManager->setTitle ( Sterter::app ()->title . (isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $this->currentDocument->title ) );

            return tpl ( 'modules/' . __CLASS__ . '/mainpage', array (
                'name' => (isset ( $page_title ) && !empty ( $page_title )) ? $page_title : $this->currentDocument->title,
                'text' => $this->currentDocument->html,
                'products' => $productsItems,
                'brands' => $brands,
                //'link' => $link,
                'brand_price_link' => $brand_price_link,
                'slider_vals' => $slider_vals,
                'exist' => $this->productsFilter['exist'],
                //'selection' => $selection,
                'empty' => $empty,
                'brands' => $brands,
                'paging' => $paging,
                'title' => isset ( $this->seo['title'] ) && !empty ( $this->seo['title'] ) ? $this->seo['title'] : $this->currentDocument->title . ' — ' . Sterter::app ()->title
                ) );
        }
        else
        {
            $categoriesHtml = TemplateEngine::view ( "subcategories", array ( "categories" => $categories ), __CLASS__, true );
            return TemplateEngine::view ( 'mainpage', array ( 'name' => $this->currentDocument->title, "text" => $categoriesHtml ), __CLASS__ );
        }
    }

    public function getTagFilterValues ( $tagId, $tagType = "" )
    {
        $ret = "";
        if ( !$this->isFilter || !array_key_exists ( $tagId, $_SESSION["productsFilter"] ) )
        {
            return $ret;
        }

        $filterVal = $_SESSION["productsFilter"][$tagId];
        if ( $tagType == "INTERVAL" || $tagId == "price" )
        {
            if ( empty ( $filterVal["from"] ) )
            {
                unset ( $filterVal["from"] );
            }
            if ( empty ( $filterVal["to"] ) )
            {
                unset ( $filterVal["to"] );
            }
            if ( count ( $filterVal ) > 0 )
            {
                $ret = $filterVal;
            }
        }
        elseif ( $tagType == "ENUM" )
        {
            if ( isset ( $filterVal ) && !empty ( $filterVal ) )
            {
                $ret = $filterVal;
            }
        }
        elseif ( $tagType == "SET" || $tagId == "brand" )
        {
            if ( isset ( $filterVal ) && count ( $filterVal ) > 0 )
            {
                $ret = $filterVal;
            }
        }
        else
        {
            $ret = $filterVal;
        }
        return $ret;
    }
}

class ProductItem
{
    /**
     * <p>Id товарной позиции</p>
     * @var Integer
     */
    public $id;
    /**
     * <p>Id родительской категории</p>
     * @var Integer
     */
    public $top;
    /**
     * <p>Родительская категория</p>
     * @var StdClass
     */
    public $topic;
    /**
     * <p>Порядок показа товара</p>
     * @var Integer
     */
    public $order;
    /**
     * <p>Наименование позиции</p>
     * @var String
     */
    public $name;
    /**
     * <p>Код. артикул позиции</p>
     * @var String
     */
    public $shortName;
    /**
     * <p>Ссылка на товарную позици</p>
     * @var String
     */
    public $nav;
    /**
     * <p>URI полной ссылки на позицию</p>
     * @var String
     */
    public $link;
    /**
     * <p>Полное имя файла с главным изоюражением для позиции</p>
     * @var String
     */
    public $image;
    /**
     * <p>Массив с полными именами всех изображений для позиции</p>
     * @var Array
     */
    public $images;
    /**
     * <p>Id бренда позиции</p>
     * @var Integer
     */
    public $brandId;
    /**
     * <p>Бренд позиции</p>
     * @var brandItem
     */
    public $brand;
    /**
     * <p>Цена позиции</p>
     * @var Float
     */
    public $price;
    /**
     * <p>Цена позиции со скидкой</p>
     * @var Float
     */
    public $discountPrice;
    /**
     * <p>Id валюты</p>
     * @var Integer
     */
    public $valutaId;
    /**
     * <p>Единица измерения</p>
     * @var String
     */
    public $unit;
    public $date;
    public $view;
    public $deleted;
    public $created;
    public $modified;
    public $anons;
    public $text;
    public $types;
    public $isAction;
    public $isFeatured;
    public $isLider;
    public $isExists;
    public $availability;
    public $relations;
    public $rate;
    public $discount;
    public $inCompare;
    /**
     * <p>Массив характеристик товара</p>
     * @var Array_Of_ProductFeature
     */
    public $features;
    /**
     * <p>Количество товара в корзине</p>
     * @var Integer
     */
    public $inBasket;
    public $noIndex;
    public $quantityByFeatures;
    public $isModel = false;
}

class brandItem
{
    public $id;
    public $order;
    public $name;
    public $nav;
    public $text;
    public $show;
    public $deleted;
    public $created;
    public $modified;
    public $selected;

}

class FilterTag
{
    public $id;
    public $alias;
    public $name;
    public $tagType;
    public $tagValues;
    public $from;
    public $to;
    public $min;
    public $max;
    public $pattern;
    public $filterVal;

    /**
     *
     * @param String $alias <p>Алиас</p>
     * @param String $name <p>Наименование зарактеристикм</p>
     * @param String $type <p>Тип</p>
     * @param Mixed $values <p>Значения</p>
     * @return vouid
     */
    public function __construct ( $alias = null, $name = null, $type = null, $values = null )
    {
        if ( $this->id )
            return;

        $this->alias = $alias;
        $this->name = $name;
        $this->tagType = $type;
        $this->tagValues = $values;
    }
}

class ProductFeature
{
    public $id;
    public $alias;
    public $name;
    public $type;
    public $values;

    public function __construct ( $id, $alias, $name, $type, $values )
    {
        $this->id = $id;
        $this->alias = $alias;
        $this->name = $name;
        $this->type = $type;
        $this->values = $values;
    }
}
