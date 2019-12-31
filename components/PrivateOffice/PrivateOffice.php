<?php

class PrivateOffice extends CmsModule
{
    protected $user;
    protected $orders;
    protected $payments;

    /**
     * <p>Флаг упрощенного входа в кабинет</>
     * @var String
     */
    protected $serviceEntry;
    protected $mainTab;
    protected $tabs;
    protected $services;

    const EASY = "EASY";
    const LOGIN = "LOGIN";

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
    	$this->serviceEntry = Tools::getSettings("Office", "easyEntry", 'Y') == 'Y' ? PrivateOffice::EASY : PrivateOffice::LOGIN;
        $this->mainTab = array ( "alias" => "user", "tabName" => "Пользователь" );
        $this->template = "mainpage";
        $this->actions = array
        (
            'default' => [ 'method' => 'indexAction' ],
            'login' => array ( 'method' => 'authorize', 'name' => 'Авторизация' ),
            "registration" => array ( "method" => "registration", 'name' => 'Регистрация' ),
            'checkout' => array ( 'method' => 'createOrder'),
            'paymentResult' => array ( 'method' => 'paymentResult', 'name' => 'Оповещение об оплате' ),
            'paymentSuccess' => array ( 'method' => 'paymentSuccess', 'name' => 'Успешная оплата' ),
            'paymentFail' => array ( 'method' => 'paymentFail', 'name' => 'Отказ от оплаты' ),
            'failure' => array ( 'method' => 'failure', 'name' => 'Сбой в оплате' ),
            'logout' => array ( 'method' => 'logout'),
            'remind' => array ( 'method' => 'remind'),
            'restore' => array ( 'method' => 'restore')
        );
//        $this->tabs = array
//        (
//            "order" => array ( "alias" => "order", "tabName" => "Заказы" )/*,
//            "cold" => array ( "alias" => "cold", "tabName" => "Холодная вода" ),
//            "electro" => array ( "alias" => "electro", "tabName" => "Электричество" )*/
//        );
//        $this->services = array
//        (
//            "user" => new Users (),
//            "order" => new Order ()/*,
//            "cold" => new ConsumptionManager("cold"),
//            "electro" => new ConsumptionManager("electro")*/
//        );
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

    /**
     * <pre>Возвращает страницу личного кабинета</pre>
     * @return String
     */
    public function _Run ()
    {

        $uri = Starter::app ()->urlManager->getUrlPart ( "path" );
        $uriParts = array_filter ( explode ( "/", $uri ) );
        array_shift ( $uriParts );
        if ( count ( $uriParts ) )
        {
            $alias = array_shift ( $uriParts );
            $method = array_key_exists ( $alias, $this->actions ) ? $this->actions[$alias]['method'] : "indexAction";
        }
        else
            $method = "indexAction";

        if ( $this->serviceEntry == PrivateOffice::EASY || UserIdentity::getInstance ()->isAuthorized () )
        {
            $model = $this->$method ();
        }
        else
        {
            $model = $this->authorize ();
        }

        return TemplateEngine::view( "mainpage", $model, __CLASS__);
    }

    private function indexAction ()
    {
        $authorized = UserIdentity::getInstance ()->isAuthorized ();
        if ( !$authorized )
            return $this->authorize ();
        else
        {
            $user = UserIdentity::getUser ();
            if ( $user->status == "manager" )
            {
                $title = "Менеджер";
                $html .= TemplateEngine::view ( "modules/" . __CLASS__ . "/managerTab", array ( "mainTab" => $this->mainTab, "tabs" => $this->tabs, "user" => $user, "clientId" => 0 ), true );
            }
            elseif ( $user->status == "client" )
            {
                $title = "Клиент";
                $this->mainTab['html'] = $this->services['user']->renderTab ( $user, $user->id );
                $this->tabs['order']['html'] = $this->services['order']->renderTab ( "WHERE userId=" . $user->id );
                $html = TemplateEngine::view ( "managerTab", array ( "mainTab" => $this->mainTab, "tabs" => $this->tabs, "user" => $user, "clientId" => $user->id ), __CLASS__, true );
            }
            elseif ( $user->status == "admin" )
            {
                $title = "Администратор";
                $html = TemplateEngine::view ( "managerTab", array ( "mainTab" => $this->mainTab, "tabs" => array (), "user" => $user, "clientId" => 0 ), __CLASS__, true );
            }
            return array ( "title" => $title, "content" => $html );
        }
    }

    /**
     * <pre>Возвращает Html-текст формы авторизации</pre>
     * @return String
     */
    private function authorize ()
    {
        if ( UserIdentity::getInstance ()->isAuthorized () )
            return $this->indexAction ();

        $this->title = "Оформить заказ";
        return $this->render ( "loginForm",
        [
            'paymethods' => Starter::app ()->getModule ( "Order")->getPayMethods(),
            'action' => Starter::app ()->urlManager->getCurrentUrl ()
        ] );
    }

    private function logout ()
    {
        UserIdentity::logout ();
        header ( "Location: /" );
    }

    public function registration ()
    {
        if ( UserIdentity::getInstance ()->isAuthorized () )
            return $this->indexAction ();

        $users = Starter::app ()->getModule ( "Users" );
        $model['title'] = "Регистрация в личном кабинете";
        $model['content'] = $users->createRecord ();
        return $model;

        $regBox = filter_input ( INPUT_POST, 'regBox', FILTER_SANITIZE_STRING );
        if ( $regBox )
            $result = $users->createRecord ();

        if ( !$regBox || array_key_exists ( "error", $result ) )
        {
            $model['title'] = "Регистрация в личном кабинете";
            $model['content'] = $users->getRegistrationForm ();
        }
        else
        {
            $model['title'] = "Регистрация в личном кабинете";
            $model['content'] = TemplateEngine::view ( "registrSuccess", array (), __CLASS__, true );
        }

        return $model;
    }

    public function remind()
    {
        return Starter::app ()->getModule ( "Users" )->remind ();
    }

    public function restore()
    {
        return array( "title" => "Восстановление пароля", "content" => Starter::app ()->getModule ( "Users" )->restore () );
    }

    public function createOrder ()
    {
//        if ( !UserIdentity::getInstance ()->isAuthorized () )
//            return $this->authorize ();
//        else
//        {
            $this->title = 'Оформление заказа';
            $orderInfo = Starter::app ()->getModule ( "Order" )->createOrder ();
            if ( array_key_exists ( 'errors', $orderInfo )  )
            {
                return $this->render ( "loginForm",
                [
                    'paymethods' => $orderInfo['paymethods'],
                    'action' => Starter::app ()->urlManager->getCurrentUrl (),
                    'orderData' => $orderInfo['orderData'],
                    'errors' => array_key_exists ( 'errors', $orderInfo ) ? $orderInfo['errors'] : []
                ] );
            }
            else
                return $this->render ( 'orderPayment', $orderInfo );

//        }
    }

    public function paymentResult()
    {
        Starter::app ()->getModule ( "Order" )->paymentResult ();
    }

    public function paymentSuccess ()
    {
        return array ( 'title' => 'Оформление заказа', 'content' => Starter::app ()->getModule ( "Order" )->paymentSuccess () );
    }

    public function paymentFail ()
    {
        return array ( 'title' => 'Оформление заказа', 'content' => Starter::app ()->getModule ( "Order" )->paymentFail () );
    }

    public function failure ()
    {
        $uri = Starter::app ()->urlManager->getUrlPart ( "path" );
        $uriParts = array_filter ( explode ( "/", $uri ) );
        $alias = array_pop ( $uriParts );
        return array ( 'title' => 'Оформление заказа', 'content' => Starter::app ()->getModule ( "Order" )->failure ( $alias ) );
    }

    public function officePage ()
    {
        return array('title' => 'Личный кабинет пользователя', 'content' => "jlsjflsfjlsfj");
        return tpl ( "modules/" . __CLASS__ . "/officePage", array
        (
            'title' => 'Личный кабинет пользователя',
            'user' => null//$this->user->getUserInfo ()
        ) );
    }

    /**
     *
     * @return String
     */
    public function orders ()
    {
        $basket = Starter::app ()->getModule ( "Basket" )->getBasketList ();
        $userInfo = $this->user->getUserInfo ();
        $orderManager = Starter::app ()->getModule ( "Order" );
        $orders = $orderManager->getOrderList ( "WHERE status=1 AND deleted='N' AND userId=" . $userInfo->id );

        //Методы оплаты
        $paymethods = $orderManager->getPayMethods();

        $select = '';
        $join = '';
        $where = '';
//        if(_REGION !== null)
//        {
//            $select .= ', r.`id` AS `region`';
//            $join .= " LEFT JOIN `prefix_module_to_region` AS m2r ON (s.`id` = m2r.`module_id` AND m2r.`module` = '" . __CLASS__ . "')"
//            . " LEFT JOIN `prefix_regions` AS r ON (m2r.`region_id` = r.`id`)";
//            $where .= " AND (r.`id` IS NULL OR (r.`id` = '" . _REGION . "' AND r.`show` = 'Y' AND r.`deleted` = 'N'))";
//        }

        $sql = "SELECT s.*" . $select
        . " FROM `prefix_shops` AS s"
        . $join
        . " WHERE s.`show` = 'Y'" . $where
        . " ORDER BY s.`id`";
        $result = ArrayTools::index ( SqlTools::selectObjects ( $sql ), "id" );
        $defaultPointId = reset ( array_keys ( $shops ) );
        $tags = Starter::app ()->getModule ( "Catalog" )->getFilterTags ( null, "id" ); //$tags = SqlTools::selectObjects("SELECT * FROM `prefix_catalog_tags`", "FilterTag", "id");
        $shops = array ();
        foreach ( $result as $point )
        {
            if ( _REGION == null || ($point->region == null || $point->region == _REGION) )
            {
                $point->text = preg_replace ( '/\s+/', ' ', $point->text );
                $shops[] = $point;
            }
        }

        $model = array
        (
            "basket" => $basket,
            "shops" => $shops,
            "features" => $tags,
            'paymethods' => $paymethods,
            "orders" => $orders,
            "userInfo" => $userInfo
        );

        return TemplateEngine::view ( "orderTab", $model, __CLASS__, true );
    }

    /**
     *
     * <pre>Метод для работы с закладкой Заказы</pre>
     * @param Array $args <p>Параметры для работы з заказами</p>
     * @return String
     */
    public function order ( $args )
    {
        $mode = array_shift ( $args );
        $authorized = $this->user->isAuthorized ();
        if ( !$authorized && !$mode )
        {
            $data = array ();
            $data["headAuthOption"] = "<span>Шаг 1: Способ оформления заказа</span><a>Изменить »</a>";
            $data["headPayInfo"] = "Шаг 2: Регистрационные данные<a>Изменить »</a>";
            $data['authOption'] = tpl ( "parts/authOption" );
            $data["logged"] = false;
            $data['payInfo'] = "";
            $data["remind"] = false;

            $retVal["windowTitle"] = "Вход в личный кабинет";
            $retVal["windowContent"] = tpl ( "modules/" . __CLASS__ . "/testOrder", $data );
        }
        else if ( !$authorized && $mode == "remind" )
        {
            $data = array ();
            $data["headAuthOption"] = "<span>Шаг 1: Способ оформления заказа</span><a>Изменить »</a>";
            $data["headPayInfo"] = "Шаг 2: Регистрационные данные<a>Изменить »</a>";
            $data['authOption'] = tpl ( "parts/authOption" );
            $data["logged"] = false;
            $data['payInfo'] = "";
            $data["remind"] = true;

            $retVal["windowTitle"] = "Вход в личный кабинет";
            $retVal["windowContent"] = tpl ( "modules/" . __CLASS__ . "/testOrder", $data );
        }
        else if ( !$authorized || (!$authorized && $mode == "checkout") )
        {
            $data = array ();
            $data["headAuthOption"] = "<span>Шаг 1: Способ оформления заказа</span><a>Изменить »</a>";
            $data["headPayInfo"] = "Шаг 2: Регистрационные данные<a>Изменить »</a>";
            $data['authOption'] = tpl ( "parts/authOption" );
            $data["logged"] = false;
            $data['payInfo'] = "";
            $data["remind"] = false;

            $retVal["windowTitle"] = "Оформление заказа";
            $retVal["windowContent"] = TemplateEngine::view ( "testOrder", $data, __CLASS__ );
        }
        else if ( $mode == "create" || $mode == "modify" )
        {
            $order = Starter::app ()->getModule ( "Order" );
            sendJSON ( $retVal = $order->Run () );
            exit ();
        }
        else
        {
            $retVal["windowTitle"] = "Личный кабинет";
            $retVal["windowContent"] = tpl ( "parts/office", array () );
        }

        return $retVal;
    }

    /**
     * Подключает пользователя к системе
     */
    public function login ()
    {
        $userInfo = array (
            "login" => filter_input ( INPUT_POST, "login", FILTER_SANITIZE_STRING ),
            "password" => filter_input ( INPUT_POST, "password", FILTER_SANITIZE_STRING )
        );
        if ( $this->user->isAuthorized ( $userInfo ) )
        {
            $response['error'] = 0;
            $response['html'] = base64_encode ( tpl ( "/parts/shortProfile", array ( "userData" => $this->user->getUserInfo (), "checkout" => true ) ) );
        }
        else
            $response['error'] = 1;

        sendJSON ( $response );
    }

    /**
     * <pre>
     * Проверяет на валидность данные пользователя
     * и регистрирует или модифицирует учетную запись
     * </pre>
     * @param String $mode <p>режим работы работы </p>
     */
    public function _registration ( $mode )
    {
        $mode = array_shift ( $mode );
        if ( $mode == 'validate' )
        {
            $regData = array ();
            $regData['address1'] = filter_input ( INPUT_POST, "address_1", FILTER_SANITIZE_STRING );
            $regData['address2'] = filter_input ( INPUT_POST, "address_2", FILTER_SANITIZE_STRING );
            $regData['city'] = filter_input ( INPUT_POST, "city", FILTER_SANITIZE_STRING );
            $regData['company'] = filter_input ( INPUT_POST, "company", FILTER_SANITIZE_STRING );
            $regData['confirm'] = filter_input ( INPUT_POST, "confirm", FILTER_SANITIZE_STRING );
            $regData['email'] = filter_input ( INPUT_POST, "email", FILTER_SANITIZE_EMAIL );
            $regData['fax'] = filter_input ( INPUT_POST, "fax", FILTER_SANITIZE_STRING );
            $regData['firstname'] = filter_input ( INPUT_POST, "firstname", FILTER_SANITIZE_STRING );
            $regData['lastname'] = filter_input ( INPUT_POST, "lastname", FILTER_SANITIZE_STRING );
            $regData['login'] = filter_input ( INPUT_POST, "login", FILTER_SANITIZE_STRING );
            $regData['password'] = filter_input ( INPUT_POST, "password", FILTER_SANITIZE_STRING );
            $regData['postcode'] = filter_input ( INPUT_POST, "postcode", FILTER_SANITIZE_STRING );
            $regData['telephone'] = filter_input ( INPUT_POST, "telephone", FILTER_SANITIZE_STRING );
            $regData['zoneId'] = filter_input ( INPUT_POST, "zone_id", FILTER_SANITIZE_NUMBER_INT );

            $result = $this->user->registration ( $regData );
            sendJSON ( $result );
        }
        else if ( $mode == "update" )
        {
            $result = $this->user->updateAccount ();
            sendJSON ( $result );
        }
        else
            echo tpl ( "parts/authBox" );
    }



    public static function getInstaller ()
    {
        return new OfficeInstaller();
    }
}
