<?php
/**
 * Description of BlockWidget
 *
 * @author kraser
 */
class FormWidget extends CmsWidget
{
    private $table;
    public function __construct ( $parent )
    {
        parent::__construct ( "Form", $parent );
        $this->table = "forms";
    }

    private $formId;
    public function getFormId ()
    {
        return $this->formId;
    }

    public function setFormId ( $blockId )
    {
        $this->formId = $blockId;
    }

    public function run ()
    {
        $form = $this->getForm ();
        $fields = $this->getFormFields ( $form->id );
        $validationScript = $this->getScript ( $fields );
        $template = $form->template ? : "form";
        $params =
        [
            'form' => $form,
            'fields' => $fields,
            'js' => $validationScript
        ];
        return $this->renderPart ( "widgets/$template", $params );
    }

    /**
     * <pre>Выбирает по alias/id форму и возвращает её</pre>
     */
    private function getForm ()
    {
        $where =
        [
            "`f`.`deleted`= 'N'",
            "`f`.`show`='Y'"
        ];
        if ( !$this->formId )
            return null;
        else if ( is_numeric ( $this->formId ) )
            $where[] = "`f`.`id`=$this->formId";
        else
            $where[] = "`f`.`callname`='$this->formId'";

        $query = "SELECT
            `f`.`id` AS id,
            `f`.`order` AS 'order',
            `f`.`name` AS title,
            `f`.`callname` AS alias,
            `f`.`template` AS template,
            `f`.`email` AS email,
            `f`.`show` AS view
        FROM `prefix_forms` `f`
        WHERE " . implode ( " AND ", $where );

        $form = SqlTools::selectObject ( $query );

        return $form;
    }

    /**
     * <pre>Возвращает поля для формы с заданным Id<pre>
     * @param Integer $formId <p>Id формы</p>
     */
    private function getFormFields ( $formId )
    {
        $query = "SELECT
            `id` AS id,
            `form` AS formId,
            `type` AS fieldType,
            `label` AS label,
            `name` AS name,
            `regex` AS 'regExp',
            `regex_error` AS regExpError,
            `default` AS value,
            `required` AS required,
            `order` AS 'order',
            `show` AS view
        FROM `prefix_forms_fields`
        WHERE `form`=$formId AND `show`='Y' ORDER BY `order`";
        $fields = SqlTools::selectObjects ( $query );
        return $fields;
    }

    private function getScript ( $fields )
    {
        $js = "";
        $emptyValueAlert = 'Пожалуйста, заполните поле «{label}»';
        foreach ( $fields as $field )
        {
            if ( $field->required == "Y" )
            {
                $js .= '
                    if($("#autoform_' . $field->name . '").val() == "" || $("#autoform_' . $field->name . '").val() == "' . $field->value . '") {
                            alert("' . str_replace ( '{label}', $field->label, $emptyValueAlert ) . '");
                            $("#autoform_' . $field->name . '").focus();
                            return false;
                    }';
            }
        }
        return $js;
    }
}