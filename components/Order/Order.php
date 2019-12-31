<?php

class Order extends CmsModule implements OfficeService
{
    private $orderCreation;
    private $paymethods;
    private $payment;

    const EASY = "EASY";
    const AUTHORIZE = "AUTHORIZE";

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->orderCreation = Order::EASY;
        $this->payment = new Payment ();
        $this->paymethods = $this->payment->getMethods();
    }

    public function Run ()
    {
        $action = array_shift ( Starter::app ()->urlManager->urlParts );
        switch ( $action )
        {
            case "create":
                $result = $this->createOrder ();
                break;
            case "modify":
                $result = $this->modifyOrder ();
                break;
            default :
        }

        return $result;
    }

    public function createOrder ()
    {
        $basket = Starter::app ()->getModule ( "Basket" )->getBasketList ();
        $features = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", null, "id" );

        if ( !$basket )
            return TemplateEngine::view ( 'empty', null, __CLASS__, true );

        $user = UserIdentity::getUser();
        $order = $this->validateOrderData ();
        if ( empty ( $order->error ) )
        {
            $order->userId = $user ? $user->id : null;
            $order->orderSum = $basket->total;
            //Статус для нового заказа
            $newstatus = ArrayTools::head ( SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_statuses` WHERE `type`='New'" ) );

            $lastId = SqlTools::insert ( "
                INSERT INTO `prefix_shop_orders`
                    (`userId`, `date`,`paymethod`,`orderSum`, `status`, `name`, `mail`, `phone`, `address`)
                VALUE (
                    " . intval ( $order->userId ) . ",
                    NOW(),
                    $order->payment,
                    $order->orderSum,
                    $newstatus->id,
                    '$order->name',
                    '$order->mail',
                    '$order->phone',
                    '$order->address'
                )" );

            $order->id = $lastId;
            SqlTools::execute ( "DELETE FROM `prefix_shop_orders_items` WHERE `order`=$order->id" );

            $serverName = filter_input ( INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING );
            $bodyProduct = "";
            foreach ( $basket->products as $product )
            {
                $name = $product->name . " " . $features[$product->featureId]->name . ": $product->featureValue $product->info";
                $insertOrderItems = "INSERT
                    INTO `prefix_shop_orders_items` (
                        `product`,
                        `order`,
                        `featureId`,
                        `featureValue`,
                        `name`,
                        `link`,
                        `top`,
                        `brand`,
                        `price`,
                        `count`,
                        `total`
                ) VALUE (
                        $product->productId,
                        $order->id,
                        " . ( isset ( $product->featureId ) ? $product->featureId : 0 ) . ",
                        '" . ( isset ( $product->featureValue ) ? $product->featureValue : "" ) . "',
                        '" . SqlTools::escapeString ( $name . ($product->brand ? ' ' . $product->brand['name'] : '') ) . "',
                        '" . SqlTools::escapeString ( $product->link ) . "',
                        $product->top,
                        " . ( $product->brandId ? : 0 ) . ",
                        $product->price,
                        $product->quantity,
                        $product->total
                )";
                SqlTools::insert ( $insertOrderItems );

                $bodyProduct .=
                    $name .
                    ($product->brand ? ' ' . $product->brand['name'] : '') . ' ' .
                    '(http://' . $serverName . $product->link . ') ' .
                    priceFormat ( $product->price ) . ' руб. × ' . $product->quantity . 'шт. = ' .
                    priceFormat ( $product->total ) . " руб.\r\n";
            }

            //Отправляем на почту уведомление о заказе
            $messenger = ComponentFactory::getComponent ( "Messanger" );
            $toMail = Tools::getSettings ( 'Shop', 'notify_mail', Starter::app ()->adminMail );
            if ( !empty ( $toMail ) )
            {
                $body = "№ заказа: " . $order->id . "\r\n";
                $body .= "Имя: " . $order->name . "\r\n";
                $body .= "Почта: " . $order->mail . "\r\n";
                $body .= "Телефон: " . $order->phone . "\r\n";
                $body .= "Адрес: " . $order->address . "\r\n";
                $body .= "IP: $serverName\r\n";
                $body .= "Карточка заказа: http://$serverName/admin/?module=Shop&method=Info#open" . $order->id . " \r\n";
                $body .= "Список товаров заказа: http://$serverName/admin/?module=Shop&method=Info&top=" . $order->id . " \r\n";
                $body .= "\r\nЗаказ:\r\n";
                $body .= $bodyProduct;
                $body .= "\r\n";
                $body .= 'К заказу ' . $basket->count . ' ' . plural ( $basket->count, 'товаров', 'товар', 'товара' ) . ' на сумму ' . priceFormat ( $basket->total ) . ' ' . plural ( $basket->total, 'рублей', 'рубль', 'рубля' ) . "\n";
                $body .= "Способ оплаты: " . $this->paymethods[$order->payment]->name;
                $mail = array ( 'mail', $toMail, 'noreply@' . $serverName, 'Заказ с сайта ' . $serverName, $body );
                $messenger->send ( $mail );
            }

            $toSms = Tools::getSettings ( 'Shop', 'notifySms', Starter::app ()->adminPhone );
            if ( !empty ( $toSms ) )
            {
                $smsPhones = explode ( ",", $toSms );
                $smsMessage = "Заказ $order->id\r\n " . $basket->count . ' ' . plural ( $basket->count, 'товаров', 'товар', 'товара' ) . ' на сумму ' . priceFormat ( $basket->total ) . ' ' . plural ( $basket->total, 'рублей', 'рубль', 'рубля' );
                foreach ( $smsPhones as $smsPhone )
                {
                    $sms = array ( 'sms', $smsPhone, "Заказ $order->id с сайта " . $serverName, $smsMessage );
                    $messenger->send ( $sms );
                }
            }

            Starter::app ()->getModule ( "Basket" )->clearBasket ();
            $payNote = $this->payment->initPayment ( $order );

            return array( 'order' => $order, 'payNote' => $payNote );
        }
        else
        {
            return array
            (
                'basket' => $basket,
                'paymethods' => $this->paymethods,
                'errors' => $order->error,
                'orderData' => array
                (
                    'name' => $order->name,
                    'email' => $order->email,
                    'phone' => $order->phone,
                    'adress' => $order->adress,
                )
            );
        }
    }

    public function getPayMethods()
    {
        return $this->paymethods;
    }

    public function validateOrderData ()
    {
        $order = new stdClass();
        $error = array ();
        $order->address = filter_input ( INPUT_POST, 'address', FILTER_SANITIZE_STRING );
        $order->delivery = filter_input ( INPUT_POST, 'delivery', FILTER_SANITIZE_NUMBER_INT );
        $order->shopId = $order->delivery ? filter_input ( INPUT_POST, 'shopId', FILTER_SANITIZE_NUMBER_INT ) : 0;
        $order->phone = filter_input ( INPUT_POST, 'phone', FILTER_SANITIZE_STRING );
        $order->mail = filter_input ( INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL );
        if ( !$order->address && !$order->delivery )
            $error["address"] = "Адрес должен быть заполнен";

        if ( !$order->phone )
            $error["phone"] = "Введите ваш контактный телефон";

        $order->userId = filter_input ( INPUT_POST, "userId", FILTER_SANITIZE_NUMBER_INT );
        $order->name = filter_input ( INPUT_POST, "name", FILTER_SANITIZE_STRING );
        $paymentId = filter_input ( INPUT_POST, "payment", FILTER_SANITIZE_NUMBER_INT );
        if ( isset ( $paymentId ) && in_array ( $paymentId, array_keys ( $this->paymethods ) ) )
            $order->payment = ( int ) $paymentId;
        else
            $order->payment = ArrayTools::head ( array_keys ( $this->paymethods ) );

        if ( count ( $error ) )
            $order->error = $error;

        return $order;
    }

    public function paymentResult()
    {
        $result = $this->payment->checkPayment();
        $serverName = filter_input ( INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING );
        $messenger = ComponentFactory::getComponent ( "Messanger" );
        $order = $this->getOrder ( $result['orderId'] );
        $toMail = Tools::getSettings ( 'Shop', 'notify_mail', Starter::app ()->adminMail );
        if ( !$result['success'] )
        {
            $body = "Оплата заказа " . $order->id . " на сумму " . str_replace ( "&nbsp;", " ", priceFormat ( $order->orderSum ) ) . ' ' . plural ( $order->orderSum, 'рублей', 'рубль', 'рубля' ) . "\n";
            $body .= "Неверная контрольная сумма\n";
            $body .= "sum: " . $result['orderSum'];
            $body .= "\ncrc: " . $result['crc'];
            $mail = array ( 'mail', $toMail, 'noreply@' . $serverName, 'Оплата заказа ' . $order->id, $body );
            $messenger->send ( $mail );
            header ( "Location: failure/$order->id" );
        }
        else
        {
            $paidStatus = SqlTools::selectValue ( "SELECT `id` FROM `prefix_shop_statuses` WHERE `type`='Paid'" );
            $query = "UPDATE `prefix_shop_orders` SET `status`=$paidStatus WHERE `id`=$order->id";
            SqlTools::execute ( $query );
            $body = "Оплата заказа " . $order->id . " на сумму " . str_replace ( "&nbsp;", " ", priceFormat ( $order->orderSum ) ) . ' ' . plural ( $order->orderSum, 'рублей', 'рубль', 'рубля' ) . "\n";
            $body .= "Оплачен: " . $result['orderSum'];
            $mail = array ( 'mail', $toMail, 'noreply@' . $serverName, 'Оплата заказа ' . $order->id, $body );
            $messenger->send ( $mail );
            echo "OK$order->id\n";
            exit();
        }
    }

    public function paymentSuccess()
    {
        $result = $this->payment->successPayment ();
        $order = $this->getOrder ( $result['orderId'] );
        if ( $result['success'] )
        {
            $payNote = "Заказ " . $order->id . " на сумму " . priceFormat ( $order->orderSum ) . ' ' . plural ( $order->orderSum, 'рублей', 'рубль', 'рубля' ) . " успешно оплачен.\n";
            return TemplateEngine::view ( 'thanks', array( 'order' => $order, 'payNote' => $payNote ), __CLASS__ );
        }
        else
        {
            header ( "Location: failure/$order->id" );
            $payNote = "Что-то странное с оплатой заказа " . $order->id . " на сумму " . priceFormat ( $order->orderSum ) . ' ' . plural ( $order->orderSum, 'рублей', 'рубль', 'рубля' ) . ".\n";
            return TemplateEngine::view ( 'orderPayment', array( 'order' => $order, 'payNote' => $payNote ), __CLASS__ );
        }
    }

    public function paymentFail()
    {
        $orderSum = filter_input ( INPUT_GET, "OutSum", FILTER_SANITIZE_NUMBER_FLOAT );
        $orderId = filter_input ( INPUT_GET, "InvId", FILTER_SANITIZE_NUMBER_INT );
        $order = $this->getOrder ( $orderId );
        $payNote = "Оплата неудачна. Нажав кнопку «Back» в браузере вы можете вернуться на страницу оплаты и повторить попытку.";
        return TemplateEngine::view ( 'orderPayment', array( 'order' => $order, 'payNote' => $payNote ), __CLASS__ );

        /*
        OutSum Сумма, оплаченная покупателем (та самая, которую Вы прислали в ROBOKASSA, на страницу оплаты).
        InvId Номер счета в магазине.
        Culture Язык, использовавшийся при совершении оплаты. В соответствии с ISO 3166-1.

        Переход пользователя по данному адресу, строго говоря, не означает окончательного отказа Покупателя от оплаты,
        нажав кнопку «Back» в браузере он может вернуться на страницы ROBOKASSA.
        Поэтому в случае блокировки товара на складе под заказ, для его разблокирования желательно проверять
        факт отказа от платежа запросом запроса XML-интерфейса получения состояния оплаты счета,
        используя в запросе номер счета InvId имеющийся в БД магазина (Продавца).
        */
    }

    public function failure ( $orderId )
    {
        if ( $orderId !== "failure" && is_numeric ( $orderId))
            $order = $this->getOrder ( $orderId );
        else
        {
            $orderId = "";
            $order = null;
        }

        $payNote = "При оплате заказа $orderId произошёл сбой. Администрация сайта уже уведомлена о проблеме.";
        return TemplateEngine::view ( 'orderPayment', array( 'order' => $order, 'payNote' => $payNote ), __CLASS__ );
    }

    private function modifyOrder ()
    {
        $basketList = Starter::app ()->getModule ( 'Basket' )->getBasketList ();
        $user = User::getInstance ()->getUserInfo ();

        if ( !$basketList )
            return;

        //Данные заказа
        $error = array ();
        $address = filter_input ( INPUT_POST, 'address', FILTER_SANITIZE_STRING );
        $delivery = filter_input ( INPUT_POST, 'delivery', FILTER_SANITIZE_NUMBER_INT );
        $shopId = $delivery ? filter_input ( INPUT_POST, 'shopId', FILTER_SANITIZE_NUMBER_INT ) : 0;
        $phone = filter_input ( INPUT_POST, 'phone', FILTER_SANITIZE_STRING );

        if ( !$address && !$delivery )
            $error["address"] = "Адрес должен быть заполнен";

        if ( !$phone )
            $error["phone"] = "Телефон должен быть заполнен";

        if ( count ( $error ) )
            return array ( "error" => $error );

        $paymethod = filter_input ( INPUT_POST, 'payments', FILTER_SANITIZE_NUMBER_INT );
        $shop = ArrayTools::head ( SqlTools::selectObjects ( "SELECT * FROM `prefix_shops` WHERE `id`=$shopId" ) );
        $orderId = filter_input ( INPUT_POST, 'orderId', FILTER_SANITIZE_NUMBER_INT );

        $order = $this->getOrder ( $orderId );
        $update = array ();
        $detail = array ();

        if ( !$delivery && $order->address != $address )
        {
            $update[] = "`address`='$address'";
            $detail["address"] = "Доставка по адресу: " . $address;
        }

        if ( $order->phone != $phone )
        {
            $update[] = "`phone`='$phone'";
            $detail["phone"] = "Телефон: " . $phone;
        }

        if ( $order->paymethod != $paymethod )
            $update[] = "`paymethod`=$paymethod";

        if ( $delivery && $shopId != $order->shopId )
        {
            $update[] = "`shopId`=$shopId";
            $detail["delivery"] = "Самовывоз с " . $shop->address;
        }

        if ( count ( $update ) )
        {
            $query = "UPDATE `prefix_shop_orders` SET " . implode ( ",", $update ) . ", `date`=NOW() WHERE `id`=$orderId";
            SqlTools::execute ( $query );
        }

        $modifiedProducts = array ();
        foreach ( $basketList->products as $product )
        {
            $orderProduct = ArrayTools::head ( ArrayTools::select ( $order->products, "product", $product->id ) );
            if ( count ( $orderProduct ) )
            {
                $count = $orderProduct->count + $product->quantity;
                $orderProduct->count += $product->quantity;
                $orderProduct->diff = "+$product->quantity";
                $total = $orderProduct->total + $product->total;
                $orderProduct->total += $product->total;
                $query = "UPDATE `prefix_shop_orders_items` SET `count`=$count, `total`=$total, `modified`=NOW() WHERE `order`=$orderId AND `product`=$product->id";
                SqlTools::execute ( $query );
                $modifiedProducts[] = $orderProduct;
            }
            else
            {
                $total = $product->price * $product->quantity;
                $insertOrderItems = "INSERT
                        INTO `prefix_shop_orders_items` (
                            `product`,
                            `order`,
                            `name`,
                            `link`,
                            `top`,
                            `brand`,
                            `price`,
                            `count`,
                            `created`,
                            `modified`,
                            `total`
                    ) VALUE (
                            $product->id,
                            $orderId,
                            '" . SqlTools::escapeString ( $product->name ) . "',
                            '" . SqlTools::escapeString ( $product->link ) . "',
                            $product->top,
                            " . ($product->brand ? $product->brand : 0) . ",
                            $product->price,
                            $product->quantity,
                            '$product->created',
                            '$product->modified',
                            $total
                    );";
                $id = SqlTools::insert ( $insertOrderItems );
                $product->count = $product->quantity;
                $product->total = $total;
                $product->diff = "Новый";
                $product->id = $id;
                $modifiedProducts[] = $product;
            }
        }

        //Отправляем на почту уведомление о заказе
        $toMail = Tools::getSettings ( 'Shop', 'notify_mail', Starter::app ()->adminMail );
        if ( !empty ( $toMail ) )
        {
            $body = "Изменения в заказе №" . $orderId . "\r\n";
            $body .= $user->name ? "Имя: " . $user->name . "\r\n" : "";
            $body .= $user->email ? "Почта: " . $user->email . "\r\n" : "";
            $body .= ( array_key_exists ( "phone", $detail ) ? "Изменено: Контактный телефон: " . $phone : "Контактный телефон: " . $phone ) . "\r\n";
            $body .= ( $delivery ? (array_key_exists ( "delivery", $detail ) ? "Изменено: Самовывоз с " . $shop->address : "Самовывоз с " . $shop->address ) : (array_key_exists ( "address", $detail ) ? "Изменено: Доставка по адресу: " . $address : "Доставка по адресу: " . $address )
                ) . "\r\n";
            $body .= "IP: " . filter_input ( INPUT_SERVER, 'REMOTE_ADDR' ) . "\r\n";
            $body .= "Карточка заказа: http://" . filter_input ( INPUT_SERVER, 'SERVER_NAME' ) . "/admin/?module=Shop&method=Info#open" . $orderId . " \r\n";
            $body .= "Список товаров заказа: http://" . filter_input ( INPUT_SERVER, 'SERVER_NAME' ) . "/admin/?module=Shop&method=Info&top=" . $orderId . " \r\n";
            $body .= "\r\nЗаказ:\r\n";
            $count = 0;
            $total = 0;
            foreach ( $modifiedProducts as $product )
            {
                $body .=
                    $product->name .
                    '(http://' . filter_input ( INPUT_SERVER, 'SERVER_NAME' ) . $product->link . ') ' .
                    priceFormat ( $product->price ) . ' руб. × ' . $product->count . 'шт. = ' .
                    priceFormat ( $product->total ) . " руб. " . (!empty ( $product->diff ) ? "($product->diff)" : "" ) . "\r\n";
                $count += $product->count;
                $total += $product->total;
            }
            $body .= "\r\n";
            $body .= 'К заказу ' . $count . ' ' . plural ( $count, 'товаров', 'товар', 'товара' ) . ' на сумму ' . priceFormat ( $total ) . ' ' . plural ( $total, 'рублей', 'рубль', 'рубля' );
            $mail = new ZFmail ( $toMail, 'noreply@' . filter_input ( INPUT_SERVER, 'SERVER_NAME' ), 'Заказ с сайта ' . filter_input ( INPUT_SERVER, 'SERVER_NAME' ), $body );
            $mail->send ();
        }

        unset ( $_SESSION['basket'] );

        return array ( 'redirect' => '/privateOffice/order' );
    }

    public function getOrder ( $orderId )
    {
        $order = ArrayTools::head ( SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_orders` WHERE `id`=$orderId" ) );
        $products = SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_orders_items` WHERE `order`=$orderId" );
        $order->products = $products;
        return $order;
    }

    public function getOrderList ( $params = null )
    {
        $orders = SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_orders` $params", null, "id" );

        if ( !count ( $orders ) )
            return array ();

        if ( count ( $orderIds ) )
        {
            $products = SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_orders_items` WHERE `order` IN (" . implode ( ',', $orderIds ) . ")" );
            foreach ( $orders as $order )
            {
                $order->products = ArrayTools::select ( $products, "order", $order->id );
            }
        }

        return $orders;
    }



    private function saveOrder ()
    {
        //Записываем заказ
        if ( $lightness != "light" )
        {
            $userId = intval ( $user->id );
            $phone = $user->phone;
        }
        else
            $userId = 0;

        $query = "INSERT INTO `prefix_shop_orders` (`userId`, `name`, `mail`, `phone`, `address`, `paymethod`, `shopId`, `status`, `date`)
            VALUE ( $userId, '$user->name', '$user->email', '$phone', '$address', $paymethod, $shopId, $newstatus, NOW())";

        $orderId = SqlTools::insert ( $query );

        $featuresText = array ();
        foreach ( $basket->products as $product )
        {

            $tagIds = array_keys ( $product->features );
            $features = array ();
            foreach ( $tagIds as $tagId )
            {
                $features[] = $tags[$tagId]->name . " - " . $product->features[$tagId];
            }
            $featuresText[$product->id] = implode ( "/", $features );
            $total = $product->price * $product->quantity;
            $insertOrderItems = "INSERT
                INTO `prefix_shop_orders_items` (
                    `product`,
                    `order`,
                    `name`,
                    `link`,
                    `top`,
                    `brand`,
                    `price`,
                    `count`,
                    `total`
            ) VALUE (
                    $product->productId,
                    $orderId,
                    '" . SqlTools::escapeString ( $product->name . "/" . $featuresText[$product->id] ) . "',
                    '" . SqlTools::escapeString ( $product->link ) . "',"
                . $referenceProds[$product->productId]->top . ","
                . ($referenceProds[$product->productId]->brand ? : 0) . ",
                    $product->price,
                    $product->quantity,
                    $total
            );";

            SqlTools::insert ( $insertOrderItems );
        }
    }

    public function checkUserRegistration ()
    {
        $user = User::getInstance ();
        if ( !$user->isAuthorized () )
            return $user->authorizationBox ();
        else
            return $user->getUserInfo ();
    }



    public function getData ( $params )
    {

    }

    public function renderList ( $params )
    {

    }

    public function renderTab ( $params )
    {
        $orders = $this->getOrderList ( $params );
        $statuses = SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_statuses`", null, "id" );
        $shops = SqlTools::selectObjects ( "SELECT * FROM `prefix_shops`", null, "id" );
        $features = SqlTools::selectObjects ( "SELECT * FROM `prefix_catalog_tags`", null, "id" );
        $basket = Starter::app ()->getModule("Basket")->getBasketList();
        $data = array
        (
            "orders" => $orders,
            'statuses' => $statuses,
            'paymethods' => $this->paymethods,
            'shops' => $shops,
            'basket' => $basket,
            'features' => $features,
            'user' => UserIdentity::getUser ()
        );
        return TemplateEngine::view ( "list", $data, __CLASS__, true );
    }

    public function setData ( $params )
    {

    }

    public function createRecord ( $params )
    {

    }
}
