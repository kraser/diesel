{$form=$model.form}
{$fields=$model.fields}
{$js=$model.js}

<div class="section-title">
    <h3>{$form->title}</h3>
    <div class="divider"></div>
</div><!-- end section title -->

<div class="general-form autoform" id="autoform{$form->id}">
    <form action="Content/Form/{$form->id}" id="contactform" name="contactform" method="post">
        <input type="hidden" name="ajax_form" value="true" />
        <input type="hidden" name="form_id" value="{$form->id}" />
        {$requiredCount=0}
        {foreach from=$fields item=field}
            {if $field->required == "Y"}
                {$required="required"}
                {$requiredCount=$requiredCount + 1}
            {else}
                {$required=""}
            {/if}
            {if $field->fieldType == "text"}
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <label for="autoform_{$field->name}" class="{$required}">{$field->label}{if $required}<span> *</span>{/if}</label>
                    <input type="text" name="{$field->name}" id="autoform_{$field->name}" placeholder="{$field->value}" class="form-control" />
                </div>
            {elseif $field->fieldType == "textarea"}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label for="autoform_{$field->name}" class="{$required}">{$field->label}{if $required}<span>*</span>{/if}</label>
                    <textarea name="{$field->name}" id="autoform_{$field->name}" rows="6" class="form-control" placeholder="{$field->value}"></textarea>
                    <button type="submit" name="formSubmitted" value="SEND" id="submit" class="btn btn-lg btn-primary pull-right border-radius">Отправить</button>
                </div>
            {/if}
        {/foreach}
    </form>
    <div class="clearfix"></div>
</div>
<script type="text/javascript">

$("#autoform{$form->id} form").submit(function(event)
{
    event.preventDefault();
    {$js}

    $('#autoform{$form->id}').append('<div class="form_load"><i class="fa fa-spinner fa-spin"></i></div>');
    $.ajax({
        url: $(this).attr('action'),
        method: 'post',
        dataType: 'json',
        data: $(this).serialize(),
        success: function(data){
            $('.form_load').remove();
            if(data.success == 1) {
                $('#autoform{$form->id}').replaceWith(data.message);
            }
    }
    });
    return false;
});
</script>