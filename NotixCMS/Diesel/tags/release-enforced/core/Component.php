<?php

/**
 * Интерфейс Компонент
 */
abstract class Component extends CmsModule
{
    
}

class DataModel
{
    public $template;
    public $dataModel;

}

class CmsComponentAction
{
    public $type;
    public $method;
    public $data;

}
