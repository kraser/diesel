<?php

/**
 * Description of Payment
 *
 * @author kraser
 */
class Payment
{
    private $payMethods;

    private $shopLogin;
    private $pass1;
    private $pass2;
    private $tPass1;
    private $tPass2;

    private $account;

    const TEST_MODE = 0;

    public function __construct ()
    {
        $payMethods = SqlTools::selectObjects ( "SELECT * FROM `prefix_shop_paymethods` WHERE `show`='Y' ORDER BY `order`", null, 'id' );;
        $this->shopLogin = Tools::getSettings ( __CLASS__, "shopLogin", "" );
        $this->pass1 = Tools::getSettings ( __CLASS__, "password1", "" );
        $this->pass2 = Tools::getSettings ( __CLASS__, "password2", "" );
        $this->tPass1 = Tools::getSettings ( __CLASS__, "testPassword1", "" );
        $this->tPass2 = Tools::getSettings ( __CLASS__, "testPassword2", "" );
        $this->account = Tools::getSettings ( __CLASS__, "paymentAccount", "" );

        foreach ( $payMethods as $id => $method )
        {
            switch ( $method->type )
            {
                case "Cash":
                    $disabled = false;
                    break;
                case "Robokassa":
                    $disabled = empty ( $this->shopLogin ) || empty ( $this->pass1 ) || empty ( $this->pass2 ) || empty ( $this->tPass1 ) || empty ( $this->tPass2 );
                    break;
                case "Transfer":
                    $disabled =  empty ( $this->account );
                    break;
                case "WM":
                default:
                    $disabled = true;
            }

            if ( $disabled )
                unset ( $payMethods[$id] );
        }
        $this->payMethods = $payMethods;
    }

    public function initPayment ( $order )
    {
        $payMethod = $this->payMethods[$order->payment];
        $payType = $payMethod->type;
        switch ( $payType )
        {
            case "Robokassa":
                $html = $this->initRobocassaPayment ( $order );
                break;
            case "Cash":
                $html = "Заказ $order->id на сумму " . priceFormat ( $order->orderSum ) . " " . plural ( $order->orderSum, 'рублей', 'рубль', 'рубля' );
                break;
            case "Transfer":
                $html = "Заказ $order->id на сумму " . priceFormat ( $order->orderSum ) . " " . plural ( $order->orderSum, 'рублей', 'рубль', 'рубля' );
                $html .= "<br>Банковские реквизиты для оплаты:<br>$this->account";
                break;
            case "WM":
            default:
                $html = "";
        }

        return $html;
    }

    private function initRobocassaPayment ( $order )
    {
        $pass1 = Payment::TEST_MODE ? $this->tPass1 : $this->pass1;
        $orderDesc = "Заказ $order->id";
        $crc = md5("$this->shopLogin:$order->orderSum:$order->id:$pass1");
        $html = "<script language=JavaScript src='https://auth.robokassa.ru/Merchant/PaymentForm/FormMS.js?" .
            "MerchantLogin=$this->shopLogin&" .
            "OutSum=$order->orderSum&" .
            "InvoiceID=$order->id&" .
            "Description=$orderDesc&" .
            "SignatureValue=$crc" . ( Payment::TEST_MODE ? "&IsTest=" . Payment::TEST_MODE : "" ) . "'></script>";
        return $html;
    }

    public function checkPayment ()
    {
        $orderSum = filter_input ( INPUT_GET, "OutSum", FILTER_SANITIZE_NUMBER_FLOAT );
        $orderId = filter_input ( INPUT_GET, "InvId", FILTER_SANITIZE_NUMBER_INT );
        $crc = filter_input ( INPUT_GET, "SignatureValue", FILTER_SANITIZE_STRING );
        $pass2 = Payment::TEST_MODE ? $this->tPass2 : $this->pass2;

        $checkCrc = strtoupper ( md5 ( "$orderSum:$orderId:$pass2" ) );

        return array ( 'success' => $checkCrc === strtoupper ( $crc ), 'orderId' => $orderId, 'orderSum' => $orderSum, 'crc' => $crc );
    }

    public function successPayment()
    {
        $pass1 = Payment::TEST_MODE ? $this->tPass1 : $this->pass1;
        $orderSum = filter_input ( INPUT_GET, "OutSum", FILTER_SANITIZE_NUMBER_FLOAT );
        $orderId = filter_input ( INPUT_GET, "InvId", FILTER_SANITIZE_NUMBER_INT );
        $crc = filter_input ( INPUT_GET, "SignatureValue", FILTER_SANITIZE_STRING );
        $checkCrc = strtoupper ( md5 ( "$orderSum:$orderId:$pass1" ) );

        return array ( 'success' => $checkCrc === strtoupper ( $crc ), 'orderId' => $orderId, 'orderSum' => $orderSum, 'crc' => $crc );
    }

    public function getMethods()
    {
        return $this->payMethods;
    }
}
