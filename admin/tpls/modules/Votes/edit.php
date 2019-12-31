<form action="<?php echo $action; ?>" class="form-horizontal form-votes form-edit" method="post">
    <input type="hidden" name="validate_url" value="<?php echo $validate; ?>" />
    <input type="hidden" name="valid" value="false" />
    <input type="hidden" name="votes[id]" value="<?php echo $vote['id']; ?>" />
    <div class="form-group">
        <label for="inputName" class="col-sm-4 control-label">Ваш вопрос</label>
        <div class="col-sm-4">
            <input type="text" name="votes[name]" value="<?php echo $vote['name']; ?>" class="form-control" id="inputName">
        </div>
    </div>
    <div class="form-group">
        <label for="inputShow" class="col-sm-4 control-label">Опубликовать на сайте</label>
        <div class="col-sm-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="votes[show]" <?php echo ($vote['show'] == 'Y' ? 'checked' : ''); ?> id="inputShow">
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputNoResult" class="col-sm-4 control-label">Запретить показывать результаты голосования на сайте</label>
        <div class="col-sm-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="votes[no_result]" <?php echo ($vote['no_result'] == 'Y' ? 'checked' : ''); ?> id="inputNoResult">
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputUserversion" class="col-sm-4 control-label">Пользователи могут дать свой вариант ответа</label>
        <div class="col-sm-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="votes[userversion]" <?php echo ($vote['userversion'] == 'Y' ? 'checked' : ''); ?> id="inputUserversion">
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Варианты ответа</label>
        <div class="col-sm-4">
            <?php if(empty($vote['answers_text'])) { ?>
            <div class="row answer_text_row">
                <div class="col-sm-11">
                    <input type="hidden" name="answer_id[]" value="" />
                    <input type="text" name="answer_text[]" class="form-control">
                </div>
                <div class="col-sm-1"><a href="#" class="hide remove_ansver_text text-danger"><i class="glyph-icon icon-times"></i></a></div>
            </div>
            <?php } ?>
            <?php foreach($vote['answers_text'] as $answer) { ?>
            <div class="row answer_text_row">
                <div class="col-sm-10">
                    <input type="hidden" name="answer_id[]" value="<?php echo $answer['id']; ?>" />
                    <input type="text" value="<?php echo $answer['text']; ?>" name="answer_text[]" class="form-control">
                </div>
                <div class="col-sm-1"><span class="count"><?php echo '(' . $answer['count'] . ')'; ?></span></div>
                <div class="col-sm-1"><a href="#" class="hide remove_ansver_text text-danger"><i class="glyph-icon icon-times"></i></a></div>
            </div>
            <?php } ?>
            <a href="#" id="add_answer_text" class="text-success"><i class="glyph-icon icon-plus-circle"></i>&nbsp;Добавить</a>
        </div>
    </div>

    <hr />
    <div class="col-md-4 col-md-offset-4">
        <div class="action_next">
            <label>
                <input type="checkbox" name="continue"> Продолжить редактирование
            </label>
            <br>
            <label>
                <input type="checkbox" name="new"> Добавить еще
            </label>
        </div>
        <div class="action_buttons">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="<?php echo $link; ?>" class="btn btn-danger">Отменить</a>
        </div>
    </div>
</form>

<script type="text/javascript">
if($('.answer_text_row').length > 1) {
    $('.remove_ansver_text').removeClass('hide');
}

$('#add_answer_text').click(function(){
    $('input[name="answer_text[]"]:last').parents('.row').after('<div class="row answer_text_row"><div class="col-md-10"><input type="hidden" name="answer_id[]" value="" /><input type="text" name="answer_text[]" class="form-control"></div><div class="col-md-1 col-md-offset-1"><a href="#" class="remove_ansver_text text-danger hide"><i class="glyph-icon icon-times"></i></a></div></div>');

    if($('.answer_text_row').length > 1) {
        $('.remove_ansver_text').removeClass('hide');
    }
});
$(document).on('click', '.remove_ansver_text', function(){
    if(confirm('Вы действительно хотите удалить запись?')) {
        $(this).parents('.row').remove();
    }

    if($('.answer_text_row').length == 1) {
        $('.remove_ansver_text').addClass('hide');
    }
});

$('.form-votes').submit(function(){
    $('.input-error').remove();
    var form = $(this);
    if ($('input[name="valid"]').val() == 'false') {
        $.ajax({
            url: $('input[name="validate_url"]').val(),
            method: 'post',
            dataType: 'json',
            data: form.serialize(),
            success: function(data){
                console.log(data);
                if(data.error) {
                    for(e in data.error) {
                        $('input[name*="'+e+'"]').after('<p class="input-error text-danger">'+data.error[e]+'</p>');
                    }
                } else {
                    $('input[name="valid"]').val('true');
                    form.submit();
                }
            }
        });
        return false;
    }
});
</script>