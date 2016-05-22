<?php

/**
 * Класс для построения "объектов-режимов"
 * @abstract необходимо переопределить методы
 */
abstract class Mode
{

    /**
     * Выполняет запуск ядра в режиме Mode
     * @abstract запуск ядра в режиме Mode
     */
    abstract public function Run ();
}
