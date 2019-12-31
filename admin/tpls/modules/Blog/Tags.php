<form action="<?php echo $link?>" method="post" id="tagsform">
<input type="hidden" name="id" value="<?php echo $module_id; ?>">
<input type="text" name="tags" id="tagsInput" value="<?php echo $tags; ?>">
<ul id="myTags"></ul>
<input type="button" name="save" value="Сохранить"/>
</form>

<script type="text/javascript">
    $(document).ready(function(){

        var sampleTags = [<?php echo $allTags; ?>];

        $('#myTags').tagit({
            singleField: true,
            singleFieldNode: $('#tagsInput'),
            availableTags: sampleTags,
        });
        // Сохранить изменения
        $(document).on('click', 'input[name="save"]', function() {

            $.ajax({
                url: $('#tagsform').attr('action'),
                type: 'POST',
                data: $('#tagsform').serialize(),
                cache: false,
                dataType: 'json',
                success: function(html){
                    alert('Теги добавлены!');
                },
                error: function() {
                    alert('Ошибка при обновлении!');
                }
            });
            return false;
        });
    });
</script>