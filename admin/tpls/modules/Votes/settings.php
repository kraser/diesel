<form action="<?php echo $link; ?>" class="form-horizontal form-votes form-settings" method="post">
    <div class="form-group">
        <label for="inputSecurityUser" class="col-sm-4 control-label">Только для зарегистрированных пользователей</label>
        <div class="col-sm-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="settings[security_user]" <?php echo ($settings['security_user'] ? 'checked' : ''); ?> id="inputSecurityUser">
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Защита от накруток</label>
        <div class="col-sm-4">
            <select name="settings[security]" class="form-control">
                <option value="0" <?php echo ($settings['security'] == 0 ? 'selected' : ''); ?>>нет</option>
                <option value="1" <?php echo ($settings['security'] == 1 ? 'selected' : ''); ?>>запрещать голосовать повторно</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputSortCountVotes" class="col-sm-4 control-label">Сортировать ответы по количеству голосов</label>
        <div class="col-sm-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="settings[sort_count_votes]" <?php echo ($settings['sort_count_votes'] ? 'checked' : ''); ?> id="inputSortCountVotes">
                </label>
            </div>
        </div>
    </div>

    <hr />
    <div class="col-md-4 col-md-offset-4">
        <div class="action_buttons">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</form>

<script type="text/javascript">
$('#add_answer_text').click(function(){
    $('input[name="answer_text[]"]:last').parents('.row').after('<div class="row answer_text_row"><div class="col-md-11"><input type="text" name="answer_text[]" class="form-control"></div><div class="col-md-1"><a href="#" class="remove_ansver_text text-danger hide"><i class="glyph-icon icon-times"></i></a></div></div>');

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