<?php
/**
 * <pre>MessageSender - абстрактный класс на основе которого
 * строятся отправители сообщений различного типа</pre>
 * @abstract
 * @author kraser
 */
abstract class MessageSender
{
    /**
     * <pre>Абстрактный метод отправления сообщений</pre>
     * @abstract
     */
    abstract public function send ();
    /**
     * <pre>Абстрактный метод валидации адреса получателя</pre>
     * @param String $address <p>Проверяемый адрес</p>
     */
    abstract protected function validateAddress($address);
}
