<?php
/**
 * <pre>Расположение сервиса</pre>
 * @todo Перенести в Настройки/конфигурации
 */
$service = Starter::app ()->services;
define ('SERVICE_HOST', $service['host'] );
include "http://" . SERVICE_HOST . "/services/SmsService/SoapSmsGateWay.inc";
/**
 * <pre>Класс для работы с сервисом SmsService</pre>
 *
 * @author kraser
 */
class SmsSender extends MessageSender
{
    /**
     * <p>Сообщение для отправки сервису</p>
     * @var Message
     */
    private $msg;
    /**
     * <p>Клиент сервиса</p>
     * @var SoapClient
     */
    private $soapClient;
    /**
     * <p>Логин пользователя сервиса</p>
     * @var String
     */
    private $login;
    /**
     * <p>Пароль пользователя сервиса</p>
     * @var String
     */
    private $password;

    /**
     * <pre>Конструктор объекта реализующего отправку смс</pre>
     * @param type $to <p>Номер получателя в формате 7[\d]{10}</p>
     * @param type $from <p>Логин или номер отправителя</p>
     * @param type $subject <p>Сообщение 160 латинских символов или 70 русских символов</p>
     */
    public function __construct ($to, $from, $subject)
    {
        $service = Starter::app ()->services;
        $this->login = $service['login'];
        $this->password = $service['password'];


        $sendTo = $this->validateAddress($to);
        if ( class_exists ( "Message" ) )
        {
            $msg = new Message();
            $msg->phone = $sendTo;
            $msg->text = $subject;
            $msg->date = date( "c" );
            $msg->type = 1;
            $msg->sender = $this->login;
            $msg->password = $this->password;
            $this->msg = $msg;
        }

        if ( class_exists ( "UserData") )
        {
            $userData = new UserData();
            $userData->login = $service['login'];
            $userData->authId = '1';
            $userData->password = $service['password'];
            $this->user = $userData;
        }
    }

    /**
     * <pre>Отправка смс-сообщения</pre>
     */
    public function send()
    {
        if ( !$this->login || !$this->password || empty ( $this->user ) || empty ( $this->msg ) )
            return;

        $uData = new SoapVar($this->user, SOAP_ENC_OBJECT, 'UserData');
        $requ = new SoapVar($this->msg, SOAP_ENC_OBJECT, "Message");

        $this->soapClient = new SoapClient( "http://" . SERVICE_HOST . "/services/SmsService/smsservice.wsdl.php", array( 'soap_version' => SOAP_1_2));
        $authorization = $this->soapClient->login ( $uData );
        $sendReply = $this->soapClient->sendSms($requ);
        $balance = $this->soapClient->readUpdate();
    }

    /**
     * <pre>Проверяет на валидность и возвращает валидный номер или <b>null</b> если номер не валидируется</pre>
     * @param String $number <p>Проверяемый номер</p>
     * @return String <p>Валидный номер</p>
     */
    protected function validateAddress ( $number )
    {
        if(  preg_match ( '/^7[\d]{10}$/', $number ))
            return $number;

        $num = preg_replace('/[^\d]/', '', $number);
        if(  preg_match ( '/^7[\d]{10}$/', $num ))
            return $num;

        if(preg_match('/^8[\d]{10}$/', $num))
        {
            $num = preg_replace('/^8/', '7', $num);
            return $num;
        }

        return null;
    }
}
