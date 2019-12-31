<?php
$allow_change_order = false;
$colspan = 0;
?>

<!-- CSS3 Table -->
<?php if($tbody) { ?>
<table id="datatable_<?php echo $table ?>" class="datatable table table-hover table-condensed">
    <thead>
        <tr>
            <?php if(!empty($thead)) { ?>
                <?php foreach($thead as $v) { ?>
                    <?php if(!isset($v['orderd'])) $v['orderd'] = 'DESC'; ?>
                    <th style="<?php echo isset($v['style']) ? $v['style'] : '' ?>" class="<?php if(isset($v['class'])) echo $v['class'] ?>">
                        <?php if($v['field'] != @$options['nouns']['order'] || !isset($v['order'])) { ?>
                            <a href="<?php echo $plink ?>&order=<?php echo $v['field'] ?>&orderd=<?php echo $v['orderd'] == 'ASC' ? 'DESC' : 'ASC' ?>"><?php echo ($v['shortLabel']) ? ('<span class="tooltip-button" data-toggle="tooltip" data-placement="top" title="' . $v['name'] . '">' . $v['shortLabel'] . '</span>') : $v['name']; ?></a>
                        <?php }
                        else {
                            ?>
                            <?php echo ($v['shortLabel']) ? ('<span class="tooltip-button" data-toggle="tooltip" data-placement="top" title="' . $v['name'] . '">' . $v['shortLabel'] . '</span>') : $v['name']; ?>
                        <?php } ?>
                        <?php
                        if(isset($v['order'])) {
                            if($v['field'] == @$options['nouns']['order']) {
                                echo '&nbsp;<i class="glyph-icon icon-sort-amount-asc"></i>';
                                $allow_change_order = true;
                            }
                            else {
                                echo $v['orderd'] == 'ASC' ? '&nbsp;<i class="glyph-icon icon-sort-amount-asc"></i>' : '&nbsp;<i class="glyph-icon icon-sort-amount-desc"></i>';
                            }
                        }
                        ?>
                    </th>
                <?php } ?>
            <?php } ?>
            <?php if(isset($options['nouns']['text']) && !in_array('no_edit_text', $options['controls'])) { ?>
                <th></th>
            <?php } ?>
            <?php if(in_array('edit', $options['controls'])) { ?>
                <th></th>
            <?php } ?>
            <?php if(in_array('del', $options['controls'])) { ?>
                <th></th>
            <?php } ?>
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
                    <td style="text-align:center;<?php echo isset($thead[$fk]['style']) ? $thead[$fk]['style'] : '' ?>">
                        <?php if($tRowsOpts[$idx]["edit"]) { ?>
                        <div class="checkbox-inline checkbox-success">
                            <label>
                                <input type="checkbox" name="<?php echo $fk ?>" value="<?php echo $field ?>" class="custom-checkbox ajax" <?php echo($field == 'Y') ? 'checked' : 'false'; ?> data-iid="<?php echo strip_tags($row[$options['nouns']['id']]) ?>" data-itable="<?php echo $table ?>" id="uniform-inlineCheckbox<?php echo strip_tags($row[$options['nouns']['id']]) ?>">
                            </label>
                        </div>

                            <!--<input type="checkbox" name="<?php echo $fk ?>" value="<?php echo $field ?>" class="input-switch ajax" data-size="small" data-on-text="Да" data-off-text="Нет" <?php echo($field == 'Y') ? 'checked' : 'false'; ?> data-on-color="success" data-off-color="danger" data-iid="<?php echo strip_tags($row[$options['nouns']['id']]) ?>" data-itable="<?php echo $table ?>">-->
                    <?php } ?>
                    </td>
                    <?php
                    // Текст
                    } else { ?>
                    <td style="<?php echo isset($thead[$fk]['style']) ? $thead[$fk]['style'] : '' ?>" class="<?php echo $allow_change_order ? 'order' : '' ?> <?php echo $fk == @$options['nouns']['order'] ? 'sortorder' : '' ?> <?php echo (array_key_exists("name", $options['nouns']) && $fk == $options['nouns']['name'] ? 'namefordel' : ''); ?>"><?php echo $field ?></td>
                    <?php } ?>
                <?php } ?>
                <?php if(isset($options['nouns']['text']) && !in_array('no_edit_text', $options['controls'])) {
                    $colspan++;
                    ?>
                    <td class="text-center">
                        <?php if($tRowsOpts[$idx]["edit"]) { ?>
                            <a href="<?php echo $plink . '&edit_text=' . strip_tags($row[$options['nouns']['id']]) ?>" class="edit_text_link tooltip-button" data-toggle="tooltip" data-placement="top" title="Редактировать текстовое содержание записи"><i class="glyph-icon icon-file-text-o"></i></a>
                    <?php } ?>
                    </td>
                <?php } ?>
                    <?php if(in_array('edit', $options['controls'])) {
                        $colspan++;
                        ?>
                    <td class="text-center">
                    <?php if($tRowsOpts[$idx]["edit"]) { ?>
                            <a href="<?php echo $plink ?>" class="edit_link tooltip-button" data-toggle="tooltip" data-placement="top" iid="<?php echo strip_tags($row[$options['nouns']['id']]) ?>" title="Редактировать поля"><i class="glyph-icon icon-pencil"></i></a>
                    <?php } ?>
                    </td>
                    <?php } ?>
                    <?php if(in_array('del', $options['controls'])) {
                        $colspan++;
                        ?>
                    <td class="text-center">
                    <?php if($tRowsOpts[$idx]["edit"]) { ?>
                            <a href="<?php echo $plink ?>&delete=<?php echo strip_tags($row[$options['nouns']['id']]) ?>" class="del_link text-danger tooltip-button" data-toggle="tooltip" data-placement="top" iid="<?php echo strip_tags($row[$options['nouns']['id']]) ?>" title="Удалить"><i class="glyph-icon icon-remove"></i></a>
                <?php } ?>
                    </td>
    <?php } ?>
            </tr>
<?php } ?>
    </tbody>
</table>
<?php } else { ?>
    <p>Данных нет</p>
<?php } ?>
<br>
<?php if(in_array('add', $options['controls'])) { ?>
    <a href="<?php echo $plink; ?>" class="add_link btn btn-primary"><i class="glyph-icon icon-plus"></i>&nbsp;Добавить запись</a>
<?php } ?>


<div id="del_dialog" title="Удаление записи">
    <p><i class="glyph-icon icon-warning text-warning"></i>&nbsp;Вы уверены что хотите удалить запись «<span class="itWillName"></span>»?</p>
</div>

<script type="text/javascript">
$(function () {
    "use strict";
//    $('.input-switch').bootstrapSwitch();
//
//    $('.input-switch').on('switchChange.bootstrapSwitch', function (event, state) {
//        this.value = (state) ? 'Y' : 'N';
//        this.checked = 'checked';
//    });
//
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
$(function () {
    "use strict";

    <?php if($allow_change_order) { ?>
    $("#datatable_<?php echo $table ?> tbody")
        .sortable({
            axis: 'y',
            opacity: 0.6,
            items: '> tr',
            distance: 5,
            scroll: false,
            update: function () {
                $.post('<?php echo $plink ?>', $(this).sortable('serialize'));
                $('.sortorder').each(function (i, e) {
                    $(e).html(i);
                });
            },
            helper: function (e, tr)
            {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index)
                {
                    // Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
        })
        .disableSelection();
    <?php } ?>

    var to_del_id = '';
    $('.del_link').click(function () {
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
            'Да': function () {
                document.location = '<?php echo $plink ?>&delete=' + to_del_id;
                $(this).dialog('close');
            },
            'Нет': function () {
                $(this).dialog('close');
            }
        }
    });
});
</script>
