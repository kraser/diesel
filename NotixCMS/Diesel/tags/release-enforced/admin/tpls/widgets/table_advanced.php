<?php
$allow_change_order = false;
$colspan = 0;
?>
<style type="text/css">
    .dataTables_info { padding-top: 0; float: left;}
    .dataTables_paginate { padding-top: 0; }
    .css_right { float: right; }
    #datatable_<?php echo $table ?>_wrapper .fg-toolbar { margin: 10px 0px 10px 0px;font-size: 14px; color:#333; }
    #theme_links span { float: left; padding: 2px 10px; }
    .dataTables_filter label, input { display:inline !important; }
    .dataTables_paginate { text-shadow:none; }
    .ex_highlight #datatable_<?php echo $table ?> tbody tr.even:hover, #datatable_<?php echo $table ?> tbody tr.even td.highlighted {
        background-color: #ECFFB3;
    }
    .ex_highlight #datatable_<?php echo $table ?> tbody tr.odd:hover, #datatable_<?php echo $table ?> tbody tr.odd td.highlighted {
        background-color: #E6FF99;
    }
    .noborder { border: none; }
    #datatable_<?php echo $table ?> td { /*white-space: nowrap;*/ }
    #datatable_<?php echo $table ?> div[id^=uniform-] { margin: 0px; }
    .add_block { padding: 0px 0px 0px 0px; }
    #datatable_<?php echo $table ?>_length {float: right;}
    #datatable_<?php echo $table ?>_length select{display:inline;float:none;margin: 0px 5px;}
    .dataTables_filter {float: left;}
</style>

<?php if(!empty($thead)) { ?>
<table id="datatable_<?php echo $table; ?>" class="table table-hover table-condensed">
    <thead>
        <tr>
            <?php if(!empty($thead)) foreach($thead as $k => $v) { ?>
                <th class="<?php echo ($v['class']) ? $v['class'] : ''; ?>"><?php echo ($v['shortLabel']) ? ('<span class="tooltip-button" data-toggle="tooltip" data-placement="top" title="' . $v['name'] . '">' . $v['shortLabel'] . '</span>') : $v['name']; ?></th>
            <?php } ?>
            <?php if(isset($options['nouns']['text']) && !in_array('no_edit_text', $options['controls'])) { ?><th></th><?php } ?>
            <?php if(in_array('edit', $options['controls'])) { ?><th></th><?php } ?>
            <?php if(in_array('del', $options['controls'])) { ?><th></th><?php } ?>
        </tr>
    </thead>

    <tbody>
        <?php $idx = -1; ?>
        <?php foreach($tbody as $row) { ?>
            <?php ++$idx; ?>
            <tr id="tr<?php echo $table ?>_<?php echo strip_tags($row[$options['nouns']['id']]); ?>">
                <?php foreach($row as $fk => $field) { ?>
                    <?php
                    // ДА / НЕТ
                    if($syscolumns[$fk]['Type'] == "enum('Y','N')" && ($field == 'Y' || $field == 'N')) { ?>
                    <td align="center">
                        <?php if($tRowsOpts[$idx]["edit"]) {
                            ?>
                        <div class="checkbox-inline checkbox-success">
                            <label>
                                <input type="checkbox" name="<?php echo $fk ?>" value="<?php echo $field ?>" class="custom-checkbox ajax" <?php echo($field == 'Y') ? 'checked' : 'false'; ?> data-iid="<?php echo strip_tags($row[$options['nouns']['id']]) ?>" data-itable="<?php echo $table ?>" id="uniform-inlineCheckbox<?php echo strip_tags($row[$options['nouns']['id']]) ?>">
                            </label>
                        </div>

                        <!-- <input type="checkbox" name="<?php echo $fk ?>" value="<?php echo $field ?>" class="input-switch ajax" data-size="small" data-on-text="Да" data-off-text="Нет" <?php echo($field == 'Y') ? 'checked' : 'false'; ?> data-on-color="success" data-off-color="danger" data-iid="<?php echo strip_tags($row[$options['nouns']['id']]) ?>" data-itable="<?php echo $table ?>"> -->

                        <?php } ?>
                    </td>
                    <?php
                    // Текст
                    } else {
                    ?>
                    <td class="<?php echo ($thead[$fk]['class']) ? $thead[$fk]['class'] : ''; ?> <?php echo array_key_exists("name", $options['nouns']) && $fk == $options['nouns']['name'] ? 'namefordel' : '' ?>"><?php echo $field; ?></td>
                    <?php } ?>
                <?php } ?>
                <?php if(isset($options['nouns']['text']) && !in_array('no_edit_text', $options['controls'])) { ?>
                    <td class="text-center <?php echo ($v['class']) ? $v['class'] : ''; ?>">
                        <?php if($tRowsOpts[$idx]["edit"]) { ?>
                            <a href="<?php echo $plink . '&edit_text=' . $row[$options['nouns']['id']]; ?>" class="edit_text_link tooltip-button" data-toggle="tooltip" data-placement="top" title="Редактировать текстовое содержание записи"><i class="glyph-icon icon-file-text-o"></i></a>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?php if(in_array('edit', $options['controls'])) { ?>
                    <td class="text-center">
                        <?php if($tRowsOpts[$idx]["edit"]) { ?>
                            <a href="<?php echo $plink; ?>" class="edit_link tooltip-button" data-toggle="tooltip" data-placement="top" iid="<?php echo strip_tags($row[$options['nouns']['id']]); ?>" title="Редактировать поля"><i class="glyph-icon icon-pencil"></i></a>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?php if(in_array('del', $options['controls'])) { ?>
                    <td class="text-center">
                        <?php if($tRowsOpts[$idx]["edit"]) {
                            ?>
                            <a href="<?php echo $plink; ?>&delete=<?php echo strip_tags($row[$options['nouns']['id']]); ?>" class="del_link text-danger tooltip-button" data-toggle="tooltip" data-placement="top" iid="<?php echo strip_tags($row[$options['nouns']['id']]); ?>" title="Удалить"><i class="glyph-icon icon-remove"></i></a>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php } else { ?>
<p>Нет записей<?php if(in_array('add', $options['controls'])) { ?>, но можно <a href="<?php echo $plink; ?>" class="add_link">добавить</a><?php } ?></p>
<?php } ?>
<br>


<?php if(in_array('add', $options['controls'])) { ?>
<div class="add_block">
    <a href="<?php echo $plink; ?>" class="add_link btn btn-primary"><i class="glyph-icon icon-plus"></i>&nbsp;Добавить запись</a>
</div>
<?php } ?>

<div id="del_dialog" title="Удаление записи">
    <p><i class="glyph-icon icon-warning text-warning"></i>&nbsp;Вы уверены что хотите удалить запись «<span class="itWillName"></span>»?</p>
</div>

<script type="text/javascript">
$(function () {
    "use strict";
//    $('.input-switch').bootstrapSwitch();

//    $('.input-switch').on('switchChange.bootstrapSwitch', function (event, state) {
//        this.value = (state) ? 'Y' : 'N';
//        this.checked = 'checked';
//    });

//    $('.ajax.input-switch').on('switchChange.bootstrapSwitch', function (event, state) {
//        $.ajax({
//            "url": "<?php echo $plink ?>&update",
//            "type": "POST",
//            "data": {
//                'table': this.getAttribute('data-itable'),
//                'id': this.getAttribute('data-iid'),
//                'field': this.name,
//                'val': (state) ? 'Y' : 'N'
//            },
//            "success": function (result) {
//            }
//        });
//    });

    $('.ajax.custom-checkbox').change(function () {
        $.ajax({
            "url": "<?php echo $plink; ?>&update",
            "type": "POST",
            "data": {
                'table': this.getAttribute('data-itable'),
                'id': this.getAttribute('data-iid'),
                'field': this.name,
                'val': (this.checked) ? 'Y' : 'N'
            },
            "success": function (result) {
            }
        });
    });
});
</script>
<script type="text/javascript">
$(function(){
    $('#datatable_<?php echo $table ?>').dataTable({
    "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "bAutoWidth": false,
            "bStateSave": true,
            "oLanguage": {
            "sLengthMenu": "Показывать по _MENU_ записей",
                    "sSearch" : "Найти",
                    "sZeroRecords": "Ничего не найдено",
                    "sInfo": "Показаны с _START_ по _END_ из _TOTAL_ записей",
                    "sInfoEmtpy": "Нет записей",
                    "sInfoFiltered": "(отфильтровано из _MAX_ возможных записей)",
                    "oPaginate": {
                    "sFirst":    "Первая",
                            "sPrevious": "←",
                            "sNext":     "→",
                            "sLast":     "Последняя"
                    }
            },
            "aoColumns": [
                <?php if(!empty($thead)) foreach($thead as $k => $v) { ?>
                null,
                <?php } ?>
                <?php if(isset($options['nouns']['text']) && !in_array('no_edit_text', $options['controls'])) { ?>{"bSearchable": false, "bSortable": false},<?php } ?>
                <?php if(in_array('edit', $options['controls'])) { ?>{"bSearchable": false, "bSortable": false},<?php } ?>
                <?php if(in_array('del', $options['controls'])) { ?>{"bSearchable": false, "bSortable": false}<?php } ?>
            ],
            "fnDrawCallback": function() {

            //ДА / НЕТ
            $('.yesnoInSlider').draggable({
            containment: 'parent',
                    drag: function(event, ui) {
                    var left = parseInt($(event.target).css('left'));
                            if (left > 15) $(event.target).html('Нет');
                            else $(event.target).html('Да');
                    },
                    stop: function(event, ui) {
                    var left = parseInt($(event.target).css('left'));
                            if (left > 15) $(event.target).parent().parent().find('.yesnoLabelNoSlider').trigger('click');
                            else $(event.target).parent().parent().find('.yesnoLabelYesSlider').trigger('click');
                    }
            });
            }
    });
    var to_del_id = '';
    $('.del_link').live('click', function(){
        to_del_id = $(this).attr('iid');
        $("#del_dialog .itWillName").html($('#tr<?php echo $table ?>_' + to_del_id + ' td.namefordel').html());
        $("#del_dialog").dialog('open');
        return false;
    });
    $("#del_dialog").dialog({
        autoOpen: false,
        resizable: false,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Да': function() {
                document.location = '<?php echo $plink ?>&delete=' + to_del_id;
                $(this).dialog('close');
            },
            'Нет': function() {
                $(this).dialog('close');
            }
        }
    });
    //ДА / НЕТ
    $('.yesnoLabelYesSlider').live('click', function(){
        var sliderWd = $(this).parent().parent();
        var slider = sliderWd.find('.yesnoInSlider');
        slider.css('left', '0px').html('Да');
        yesno(sliderWd.attr('itable'), sliderWd.attr('iid'), sliderWd.attr('iname'), 'Y');
    });
    $('.yesnoLabelNoSlider').live('click', function(){
        var sliderWd = $(this).parent().parent();
        var slider = sliderWd.find('.yesnoInSlider');
        slider.css('left', '30px').html('Нет');
        yesno(sliderWd.attr('itable'), sliderWd.attr('iid'), sliderWd.attr('iname'), 'N');
    });
    function yesno(table, id, field, val) {
        $.ajax({
        "url":		"<?php echo $plink ?>&update",
        "type":		"POST",
        "data":		{
            'table':	table,
            'id':		id,
            'field':	field,
            'val':		val
        },
        "success":	function(result) {}
        });
    }
});
</script>
