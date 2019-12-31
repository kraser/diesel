<?php
class Form
{
    /**
     * <p>ID формы</p>
     * @var Integer
     */
    public $id;
    /**
     * <p>Системное имя формы</p>
     * @var String
     */
    public $alias;
    /**
     * <p>Название формы</p>
     * @var String
     */
    public $title;
    /**
     * <p>имя шаблона для вывода формы</p>
     * @var String
     */
    public $template;
    /**
     * <p>E-mail обратной связи</p>
     * @var String
     */
    public $email;
    /**
     * <p>Флаг отображения формы</p>
     * @var Boolean
     */
    public $view;
    public $order;

}

class FormField
{
    public $id;
    public $formId;
    public $fieldType;
    public $label;
    public $name;
    public $regExp;
    public $regEexpError;
    public $value;
    public $required;
    public $order;
    public $view;

}