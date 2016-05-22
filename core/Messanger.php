<?php
/**
 * <pre>Классс для отправки сообщений разного типа</pre>
 * @todo Добавить класс унифицированного сообщения. Имя класса не должно совпадать с именем класса сообщений SOAP
 */
class Messanger
{
    /**
     * <p>Объект, реализующий конкретный способ сообщений</p>
     * @var MessageSender
     */
    private $sender;
    /**
     * <p>Ассоциативный массив <b>тип сообщения</b> => <b>класс, реализующий отправку</b></p>
     * @var Array
     */
    private $senderTypes;

    /**
     * <pre>Конструктор объекта отправки разнотипных сообщений</pre>
     */
    public function __construct ( )
    {
        Starter::import ( "core.MessageSender.*" );
        $this->senderTypes = array
        (
            'sms' => 'SmsSender',
            'mail' => 'ZFmail'
        );
    }

    /**
     * <pre></pre>
     */
    public function prepareMessage ()
    {

    }

    /**
     * <pre>Метод отправки сообщения</pre>
     * @param Array $options <p>Параметры сообщения и тип</p>
     * @todo Заменить $options на объект
     */
    public function send ( $options )
    {
        $this->createSender($options);
        $this->sender->send ();
    }

    /**
     * <pre>Фабрика объектов отправителей.
     * Создает объект для отправки конкретного типа сообщения</pre>
     * @param Array $options <p>Параметры сообщения и тип</p>
     */
    private function createSender($options)
    {
        $type = $options[0];
        switch ($type)
        {
            case "mail":
                $to = $options[1];
                $from = $options[2];
                $subject = $options[3];
                $body = $options[4];
                $senderClass = $this->senderTypes[$type];
                $this->sender = new $senderClass ( $to, $from, $subject, $body );
                break;

            case "sms":
                $to = $options[1];
                $from = $options[2];
                $subject = $options[3];
                $senderClass = $this->senderTypes[$type];
                $this->sender = new $senderClass ( $to, $from, $subject );
                break;
            default :
        }
    }
}
