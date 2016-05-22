<?php

/**
 * <pre>Класс Basket для работы с корзиной товаров</pre>
 * @todo Убрать deprecated поля в BasketItem
 */
class Basket extends CmsModule
{
    private $alterPages =
    [
        'add' => array ( 'method' => 'StaticAdd', 'name' => 'Добавить в корзину' ),
        'del' => array ( 'method' => 'StaticDel', 'name' => 'Убрать из корзины' ),
        'edit' => array ( 'method' => 'StaticEdit', 'name' => 'Редактировать количество' ),
        'thanks' => array ( 'method' => 'ThanksPage', 'name' => 'Спасибо за заказ!' )
    ];
    private $basket;
    private $payment;
    private $currentBasketId;

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        if ( !session_id () )
            session_start ();

        $this->payment = new Payment();
    }

    public function Run ()
    {
        $alter = $this->checkAlterPage ();
        if ( !$alter )
        {
            if ( !array_key_exists ( "basket", $_SESSION ) )
                $box = $this->EmptyBasketPage ();
            elseif ( $this->currentDocument->nav == 'order' )
                $box = $this->getOrderPage ();
            else
                $box = $this->getBasketPage ();
        }
        else
            $box = $alter; //Альтернативные

        if ( is_array ( $box ) )
        {
            $name = $box['name'];
            $text = $box['text'];
        }
        else
        {
            $name = $this->currentDocument->title;
            $text = $box;
        }

        $print = filter_input ( INPUT_GET, "print" );
        if ( $print )
            $template = 'basket-light';
        else
            $template = 'basket';

        Starter::app ()->headManager->setTitle ( Starter::app ()->title . ' — ' . $this->currentDocument->title );
        return tpl ( $template,
        [
            'name' => $name,
            'text' => $text
        ] );
    }

    private function checkAlterPage ()
    {
        $request = Starter::app ()->urlManager->getUrlPart ( 'path' );

        $mlink = trim ( str_replace ( Starter::app ()->content->getLinkById ( $this->currentDocument->id ), '', $request ) );
        $mpath = array_filter ( explode ( '/', $mlink ) );

        //@todo посмотреть аналоги кода в других модулях и перенести в родительский объект
        $checkAlter = current ( $mpath );
        if ( isset ( $this->alterPages[$checkAlter] ) )
        {
            // todo заменить вызов 404 на выброс исключения
            if ( !method_exists ( $this, $this->alterPages[$checkAlter]['method'] ) )
                page404 ();

            $params_path = [];
            $params_path[] =
            [
            'type' => 'alter_page',
            'method' => $this->alterPages[$checkAlter]['method'],
            'data' => $checkAlter
            ];

            $skipFirst = false;
            foreach ( $mpath as $ppath )
            {
                if ( !$skipFirst )
                {
                    $skipFirst = true;
                    continue;
                }
                $params_path[] =
                [
                    'type' => 'param',
                    'data' => $ppath
                ];
            }
            $this->path = $params_path;
        }

        if ( !empty ( $mlink ) && empty ( $this->path ) )
            page404 ();

        if ( empty ( $this->path ) )
            return false;
        $alterPage = current ( $this->path );

        if ( $alterPage['type'] == 'alter_page' )
            return $this->$alterPage['method'] (); //Вызов альтернативного метода
        else
            return false;
    }

    /**
     * <pre>Основной метод добавления в корзину</pre>
     * @param Integer $id <p>Id добавляемой позиции в каталоге</p>
     * @param Integer $count <p>Кол-во мест добавляемой позиции</p>
     * @param String $basketId <p>Id добавляемой позиции в корзине/null - если впервые вносится</p>
     * @param type $featureId
     * @param type $featureValue
     * @return boolean <p>true -успешное добавление / false - товар не добавлен</p>
     */
    private function AddToBasket ( $id, $count = 1, $basketId = null, $featureId = null, $featureValue = null )
    {
        $success = true;
        $id = abs ( ( int ) $id );
        $basket = $this->getBasketList ();

        if ( !$basket )
            $basket = new BasketList();

        if ( $basketId && array_key_exists ( $basketId, $basket->products ) )
            $basketItem = $basket->products[$basketId];
        else
        {
            $basketItems = ArrayTools::select ( $basket->products, "productId", $id );
            if ( !$featureId || !$featureValue )
                $basketItem = ArrayTools::head ( $basketItems );
            else if ( $featureId && $featureValue )
                $basketItem = ArrayTools::head ( ArrayTools::select ( ArrayTools::select ( $basketItems, "featureId", $featureId ), "featureValue", $featureValue ) );
            else
                $basketItem = null;
        }

        if ( $basketItem )
        {
            $basketId = $basketItem->id;
            $basketItem->quantity += $count;
            $basketItem->total = $basketItem->price * $basketItem->quantity;
        }
        else
        {
            $catalog = Starter::app ()->getModule ( "Catalog" );
            $where = [ "deleted" => 'N', "show" => 'Y', "id" => $id ];
            $product = ArrayTools::head ( $catalog->findProducts ( $where, null, "id" ) );
            if ( !$product )
                return !$success;

            $basketId = uniqid ();
            $basketItem = new BasketItem();
            $basketItem->id = $basketId;
            $basketItem->name = $product->name;
            $basketItem->top = $product->top;
            $basketItem->productId = $id;
            $basketItem->quantity = $count;
            $basketItem->price = $product->discountPrice;
            $basketItem->total = $basketItem->price * $basketItem->quantity;
            $basketItem->link = $catalog->Link ( $product->top, $product->nav ? : $product->id  );

            if ( $featureId && $featureValue )
            {
                $basketItem->featureId = $featureId;
                $basketItem->featureValue = $featureValue;
            }

            $basket->products[$basketId] = $basketItem;
        }


        $basket->count += $count;
        $basket->total += $count * $basketItem->price;

        $this->basket = $basket;
        $this->currentBasketId = $basketId;

        $this->setBasketList ( $basket );

        return $success;
    }

    /**
     * <pre>Добавление товара в корзину через AJAX запросы</pre>
     */
    public function Add ()
    {
        $id = filter_input ( INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT );
        $toAdd = filter_input ( INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT );
        $featureId = filter_input ( INPUT_POST, "featureId", FILTER_SANITIZE_NUMBER_INT );
        $featureValue = filter_input ( INPUT_POST, "featureValues" );
        $basketId = filter_input ( INPUT_POST, "basketId", FILTER_SANITIZE_STRING );

        $quantity = intval ( $toAdd ) ? : 1;
        $error = true;
        if ( $id )
            $result = $this->AddToBasket ( $id, $quantity, $basketId, $featureId, $featureValue );

        $format = filter_input ( INPUT_POST, "format", FILTER_SANITIZE_STRING );
        $basket = $this->getBasketList ();
        $basket->html = $this->Block ();

        $items = ArrayTools::select ( $basket->products, "productId", $id );
        $catalog = Starter::app ()->getModule ( 'Catalog' );
        return
        [
            'basket' => $basket,
            'totalFormated' => priceFormat ( $basket->total ),
            'countFormatted' => $basket->count . ' ' . plural ( $basket->count, 'товаров', 'товар', 'товара' ),
            'basketId' => $this->currentBasketId,
            'buybutton' => $catalog->BuyButton ( $id, $format ),
            'error' => $error XOR $result,
        ];
    }

    /**
     * Основной метод удаления из корзины
     * @param Integer $id <p>Id позиции в корзине</p>
     * @return Boolean <p>true - удаление успешно / false - удаление неуспешно</p>
     */
    private function DelFromBasket ( $id )
    {
        $basket = $this->getBasketList ();
        if ( $basket and array_key_exists ( $id, $basket->products ) )
        {
            unset ( $basket->products[$id] );
            $this->setBasketList ( $basket );
            return true;
        }
        else
            return false;
    }

    /**
     * Удаление из корзины по ссылке (не аякс)
     */
    private function StaticDel ()
    {
        if ( isset ( $this->path[1]['data'] ) )
            $result = $this->DelFromBasket ( $this->path[1]['data'] );

        if ( isset ( $_SERVER['HTTP_REFERER'] ) && !empty ( $_SERVER['HTTP_REFERER'] ) )
        {
            header ( 'Location: ' . $_SERVER['HTTP_REFERER'] );
            exit ();
        }
        else
            return $result;
    }

    /**
     * <p>Удаление товара из корзины через AJAX запрос</>
     */
    public function Del ()
    {
        $error = true;
        $basketId = filter_input ( INPUT_POST, "basketId", FILTER_SANITIZE_STRING );
        if ( $basketId )
            $result = $this->DelFromBasket ( $basketId );

        $basket = $this->getBasketList ();
        $basket->html = $this->Block ();
        $empty = (!$basket->total && !$basket->count);
        //$catalog = Starter::app ()->getModule ( "Catalog" );

        return
        [
            'basket' => $basket,
            'totalFormated' => priceFormat ( $basket->total ),
            'countFormatted' => $basket->count . ' ' . plural ( $basket->count, 'товаров', 'товар', 'товара' ),
            'basketId' => $this->currentBasketId,
            'empty' => $empty,
            'error' => $error XOR $result,
            'html' => $empty ? $this->EmptyBasketPage () : ""
        ];
    }

    private function EditCountBasket ( $id, $count )
    {
        $success = true;
        $basket = $this->getBasketList ();
        $count = abs ( ( int ) $count );

        if ( $basket && array_key_exists ( $id, $basket->products ) )
        {
            $basket->products[$id]->quantity = $count;
            $this->basket = $basket;
            $this->currentBasketId = $id;
            $this->setBasketList ( $basket );
            return $success;
        }
        else
            return !$success;
    }

    public function Edit ()
    {
        $id = filter_input ( INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT );
        $count = filter_input ( INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT );
        $basketId = filter_input ( INPUT_POST, "basketId", FILTER_SANITIZE_STRING );
        $format = filter_input ( INPUT_POST, "format", FILTER_SANITIZE_STRING );

        $error = true;
        if ( isset ( $basketId ) && $count > 0 )
            $result = $this->EditCountBasket ( $basketId, $count );
        else if ( isset ( $basketId ) && $count <= 0 )
            $result = $this->DelFromBasket ( $basketId );
        else
            return [ "error" => $error ];

        $catalog = Starter::app ()->getModule ( 'Catalog' );
        $basket = $this->getBasketList ();
        $basket->html = $this->Block ();

        $empty = (!$basket->total && !$basket->count);
        if ( $empty )
            $html = $this->EmptyBasketPage ();

        $item = new stdClass();
        $item->total = priceFormat ( $basket->products[$basketId]->total );
        $item->quantity = $count;

        return
        [
            'basket' => $basket,
            'totalFormated' => priceFormat ( $basket->total ),
            'countFormated' => $basket->count . ' ' . plural ( $basket->count, 'товаров', 'товар', 'товара' ),
            'currentItem' => $item,
            'basketId' => $this->currentBasketId,
            'buybutton' => $catalog->BuyButton ( $id, $format ),
            'error' => $error XOR $result,
            'empty' => $empty,
            'html' => $empty ? $html : "",
        ];
    }

    public function editComment ()
    {
        $info = filter_input ( INPUT_POST, "info", FILTER_SANITIZE_STRING );
        $basketId = filter_input ( INPUT_POST, "basketId", FILTER_SANITIZE_STRING );
        $basket = $this->getBasketList ();
        $basketItem = $basket->products[$basketId];
        $basketItem->info = $info;
        $this->setBasketList ( $basket );

        return [ "success" => 1 ];
    }

    /**
     * Страница корзины
     */
    private function getBasketPage ()
    {
        $basket = $this->getBasketList ();
        if ( !$basket || count ( $basket->products ) == 0 )
            return $this->EmptyBasketPage ();

        $clean = filter_input ( INPUT_GET, "clean" );
        if ( $clean )
        {
            $this->setBasketList ( null );
            return $this->EmptyBasketPage ();
        }

        $catalog = Starter::app ()->getModule ( 'Catalog' );
        $ids = ArrayTools::arraypluck ( $basket->products, "productId" );
        $where = [ "deleted" => "N", "show" => "Y", "id" => $ids ];
        $products = $catalog->findProducts ( $where, null, "id" );
        $topicsIds = ArrayTools::numberList ( ArrayTools::pluck ( $products, "top" ) );
        $brandsIds = ArrayTools::numberList ( ArrayTools::pluck ( $products, "brandId" ) );
        $topics = $catalog->findCategories ( [ "deleted" => 'N', "show" => 'Y', "id" => $topicsIds ] );
        $brands = $catalog->findBrands ( [ "deleted" => 'N', "show" => 'Y', "id" => $brandsIds ] );
        foreach ( $basket->products as $basketItem )
        {
            /*
            if ( count ( $basketItem->features ) > 0 )
            {
                $featureIds = array_keys ( $basketItem->features );
                //@todo Заменить на вызов метода из Catalog
                $basketItem->info['features'] = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags` WHERE `id` IN (" . ArrayTools::numberList ( $featureIds ) . ")", null, "id" );
            }
            */
        }

        $paymethods = $this->payment->getMethods();

        //Запись заказа в базу
        $errors = false;

        $data =
        [
            'basket' => $basket,
            'products' => $products,
            'topics' => $topics,
            'brands' => $brands,
            'paymethods' => $paymethods,
            'errors' => $errors
        ];

        return TemplateEngine::view ( 'list', $data, __CLASS__ );
    }

    private function EmptyBasketPage ()
    {
        return tpl ( 'modules/' . __CLASS__ . '/empty' );
    }

    public function ThanksPage ()
    {
        return tpl ( 'modules/' . __CLASS__ . '/thanks' );
    }

    public function Block ()
    {
        return tpl ( 'modules/' . __CLASS__ . '/block', [ 'basket' => $this->getBasketList () ] );
    }

    /**
     * <pre>Возвращает объект с информацией о текущес состоянии корзины:
     * список позиций, находящихся в корзине
     * суммарная стоимость корзины
     * кол-во мест в корзине</pre>
     * @return BasketList|null
     */
    public function getBasketList ()
    {
        if ( empty ( $_SESSION['basket'] ) )
            return null;

        $basket = unserialize ( $_SESSION['basket'] );
        $totals = 0;
        $count = 0;
        $productsId = ArrayTools::numberList ( ArrayTools::pluck ( $basket->products, "productId" ) );
        if ( $productsId )
            $names = SqlTools::selectObjects ( "SELECT `id`, `name`, `top` FROM `prefix_products` WHERE `id` IN ($productsId)", null, "id" );
        foreach ( $basket->products as $basketItem )
        {
            $basketItem->name = $names[$basketItem->productId]->name;
            $basketItem->top = $names[$basketItem->productId]->top;
            $count += $basketItem->quantity;
            $basketItem->total = $basketItem->price * $basketItem->quantity;
            $totals += $basketItem->total;
        }

        $basket->total = $totals;
        $basket->count = $count;

        return $basket;
    }

    /**
     * <pre>Записывает объект с информацией о текущес состоянии корзины:
     * список позиций, находящихся в корзине
     * суммарная стоимость корзины
     * кол-во мест в корзине</pre>
     * @return void
     */
    public function setBasketList ( BasketList $basket = null )
    {
        if ( (!$basket ) )
        {
            unset ( $_SESSION['basket'] );
            return;
        }

        $totals = 0;
        $count = 0;
        foreach ( $basket->products as $basketItem )
        {
            $count += $basketItem->quantity;
            $basketItem->total = $basketItem->price * $basketItem->quantity;
            $totals += $basketItem->total;
        }

        $basket->total = $totals;
        $basket->count = $count;

        $_SESSION['basket'] = serialize ( $basket );
    }

    /**
     * Очищает корзину
     */
    public function clearBasket ()
    {
        $this->setBasketList ( null );

        return [ 'block' => $this->Block (), 'error' => false, 'html' => $this->EmptyBasketPage () ];
    }

    /**
     *
     */
    public function getOrderPage ()
    {
        return PrivateOffice::getInstance ()->createOrder ();
    }

    public function changeFeature ()
    {
        $id = filter_input ( INPUT_POST, "id", FILTER_SANITIZE_STRING );
        $featureId = filter_input ( INPUT_POST, "featureId", FILTER_SANITIZE_NUMBER_INT );
        $value = filter_input ( INPUT_POST, "featureValues", FILTER_SANITIZE_STRING );
        $basket = $this->getBasketList ();
        if ( !$basket )
            return [ 'error' => "Корзина пустая" ];

        $basketItem = $basket->products[$id];
        $basketItem->featureId = $featureId;
        $basketItem->featureValue = $value;
        $this->basket = $basket;
        $this->currentBasketId = $id;
        $items = ArrayTools::select ( $basket->products, "productId", $basketItem->productId );
        $_SESSION['basket'] = serialize ( $basket );

        return [ 'redirect' => "1" ];
    }

    /**
     * @todo Реализовать
     * @param BasketItem $item
     * @return string
     */
    public function inBasketHint ( BasketItem $item )
    {
        return "";
    }
}

class BasketList
{
    /**
     * <p>Список позиций в корщине</p>
     * @var Array Of BasketItem
     */
    public $products;
    /**
     * <p>Кол-во мест в корзине</p>
     * @var Integer
     */
    public $count;
    /**
     * <p>Суммарная стоимость корзины</p>
     * @var Float
     */
    public $total;
    /**
     * <p>HTML-текст корзины</p>
     * @var String
     */
    public $html;

    /**
     * <p>Конструктор объекта BasketList</p>
     */
    public function __construct ()
    {
        $this->products = array ();
        $this->count = 0;
        $this->total = 0;
        $this->html = "";
    }
}

class BasketItem
{
    /**
     * <p>Id позиции в корзине</p>
     * @var String
     */
    public $id;
    /**
     * <p>Id позиции в каталоге</p>
     * @var Integer
     */
    public $productId;
    /**
     * <p>Ссылка на карточку товара</p>
     * @var String
     */
    public $link;
    /**
     * <p>Кол-во в корзине</p>
     * @var Integer
     */
    public $quantity;
    /**
     * <p>Стоимость одного экземпляра товара</p>
     * @var Float
     */
    public $price;
    /**
     * <p>Суммарная стоимость товара</p>
     * @var Integer
     */
    public $total;
    /**
     * <p>Id характеристики для данного товра</p>
     * @var Integer
     */
    public $featureId;
    /**
     * <p>Значение характеристики для данного товара</p>
     * @var String
     */
    public $featureValue;
    /**
     * <p>Наименование товара</p>
     * @var String
     */
    public $name;
    /**
     * <p>ID категории товара</p>
     * @var Integer
     */
    public $top;
    /**
     * <p>Ассоциативный массив дополнительных параметров</p>
     * @var Array
     */
    public $info;
}
