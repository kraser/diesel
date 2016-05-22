<style type="text/css">
    #<?php echo $table ?>_dialog label, input { display:block; }
/*    #<?php echo $table ?>_dialog input.text,textarea { margin-bottom:12px; width:95%; padding: .4em; outline:none; }*/
    #<?php echo $table ?>_dialog input.radio { display: inline; margin-bottom: 0px; margin-top: 0px; outline: none; }
    #<?php echo $table ?>_dialog textarea {height:100px;}
    #<?php echo $table ?>_dialog fieldset { padding:0; border:0; margin-top:10px; }
    #<?php echo $table ?>_dialog select { padding:5px 5px 4px 5px; width:100%; outline:none; margin-bottom:0px; }
    #<?php echo $table ?>_dialog .ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
    #<?php echo $table ?>_dialog .ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }
    .ui-datepicker { z-index: 10000 !important; }
    .datefield { width: 150px !important; }
    .tabs { padding:0.2em 0 0 0 !important; }

    .floating_fields { float:left; width:32.33333%; margin: 0px 0.5% 15px 0.5%; }
    .fullwidth_fields { clear:both; margin: 0px 0.5% 15px 0.5%; }

    .ui-autocomplete {
        max-height: 133px;
        overflow-y: auto;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 133px;
    }
</style>

<!-- для обеспечения вывода редактора tinyMCE по предопределённым полям-->
<script type="text/javascript" src="/admin/js/tinymce3x/jquery.tinymce.js"></script>
<script type="text/javascript">
    function tiny_mce_init_() {
        var text_changed = false;
        $('textarea.edit_text').tinymce({
            script_url: '/admin/js/tinymce3x/tiny_mce.js',
            skin: "o2k7",
            theme: "advanced",
            plugins: "jaretypograph,safari,style,table,images,advhr,advimage,advlink,inlinepopups"
                    + ",contextmenu,paste,lists,fullscreen,noneditable,visualchars,nonbreaking"
                    + ",searchreplace,spellchecker,insertdatetime,media,wordcount",
            theme_advanced_buttons1: "bold,italic,underline,strikethrough,formatselect,fontselect,fontsizeselect,justifyleft,justifycenter,justifyright,justifyfull"
                    + ",|,bullist,numlist,|,outdent,indent,|,sub,sup,|,jaretypograph,|,forecolorpicker,backcolorpicker,|,|,styleprops,|,|,nonbreaking,hr,advhr,blockquote,charmap",
            theme_advanced_buttons2: "undo,redo,|,search,replace,|,spellchecker,|,|,insertdate,inserttime,|,link,unlink,anchor,|,pastetext,pasteword,pastetext,table,|,images,image,|,media,|,cleanup,removeformat,|,|,visualchars,visualaid,code,fullscreen",
            theme_advanced_buttons3: "",
            theme_advanced_buttons4: "",
            theme_advanced_font_sizes: "9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px",
            // theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            // для вставки <obiect> и <iframe>
            extended_valid_elements: "iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width],"
                    + "object[declare|classid|codebase|data|type|codetype|archive|standby|height|width|usemap|name|tabindex|align|border|hspace|vspace]",
            // Site CSS + изменения для tiny
            content_css: "/themes/<?php echo Starter::app ()->theme; ?>/css/typo.css",
            relative_urls: false,
            remove_script_host: true,
            language: "ru",
            setup: function (ed) {
                ed.onChange.add(function (ed, l) {
                    text_changed = true;
                });
            }
        });
    }
</script>

<script type="text/javascript">
    /* jQuery UI Spinner */
    $(function () {
        "use strict";
        $(".spinner-input").spinner();
    });
</script>

<script type="text/javascript">
$('.input-switch').bootstrapSwitch();
var currentEditId = 0;
var currentTabId = 0;
$(function ()
{
    <?php echo implode("\r\n", $addjs); ?>

    allFields = $('#<?php echo $table ?>_dialog input,textarea');
    tips = $("#validateTips");

    function updateTips(t)
    {
        tips.text(t);
    }

    function checkLength(o, n, min, max)
    {
        if (o.val().length > max || o.val().length < min)
        {
            o.addClass('ui-state-error');
            updateTips("Размер поля «" + n + "» должен быть от " + min + " до " + max + " знаков.");
            $('.buttonset button').removeClass('disabled');
            $('i.icon-spinner').remove();
            return false;
        }
        else
        {
            return true;
        }
    }

    function checkRegexp(o, regexp, n)
    {
        if (!(regexp.test(o.val())))
        {
            o.addClass('ui-state-error');
            updateTips(n);
            $('.buttonset button').removeClass('disabled');
            $('i.icon-spinner').remove();
            return false;
        }
        else
        {
            return true;
        }
    }

    var currentTab = 'tabMain';
var winHeight = $( window ).height() * 0.8;
    $("#<?php echo $table ?>_dialog").dialog({
        bgiframe: false,
        autoOpen: false,
        closeOnEscape: false,
        width: '70%',
        height: winHeight,
        //maxHeight: 800,
        modal: true,
        buttons:
        [
            {
                text: "Сохранить",
                click: function () {
                    $('#validateTips').html('');
                    $('.message').remove();
                    $('.buttonset button').addClass('disabled');
                    $('.buttonset button:focus').prepend('<i class="glyph-icon icon-spinner icon-spin"></i>');
                    if (currentTab === 'tabMain')
                    {
                        var bValid = true;
                        allFields.removeClass('ui-state-error');
                        var form = $('#<?php echo $table; ?>_form').ajaxForm();//submit();
                        if ($("input[name='mode']").size() > 0)
                            $("input[name='mode']").attr("value", "save");
                        else
                            form.prepend('<input type="hidden" name="mode" value="save">');

                        <?php echo $js; ?>

                        if (bValid)
                            form.ajaxSubmit({
                                beforeSubmit: function () {
                                },
                                success: function (data)
                                {
                                    currentEditId = data.id;
                                    $('#se_<?php echo $table ?>_id').val(data.id);
                                    //Прописываем хэш к пути
                                    location.hash = '#open' + currentEditId;
                                    $('#tabMain').prepend('<div class="message text-center text-success">Изменения сохранены</div>');
                                    $('.message').delay(2500).slideUp();
                                    $('.buttonset button').removeClass('disabled');
                                    $('i.icon-spinner').remove();
                                }
                            });
                    }
                    else
                    {
                        currentTabId = $('.ui-tabs-active a').attr('id');

                        var cform = $('.ui-tabs-panel[aria-labelledby="' + currentTabId + '"] form');
                        if (cform.length === 0)
                            $(this).dialog('close');

                        //Находим текущий id
                        //Если мы в таблице
                        if ($('#datatable_<?php echo $table ?> .edit_link').length > 0)
                        {
                            var cid = currentEditId;
                        }
                        //Если мы в дереве
                        else
                        {
                            var cid = $.tree.focused().selected.find("a").prop("id").substr(1);
                        }

                        cform.prepend('<input type="hidden" name="id" value="' + cid + '">');
                        cform.prepend('<input type="hidden" name="table" value="<?php echo $table ?>">');

                        cform.ajaxSubmit({
                            target: '.ui-tabs-panel[aria-labelledby="' + currentTabId + '"]',
                            beforeSubmit: function () {
                            },
                            success: function ()
                            {
                                $('.buttonset button').removeClass('disabled');
                                $('i.icon-spinner').remove();
                            }
                        });
                    }
                }
            },
            {
                text: 'Сохранить и выйти',
                click: function () {
                    $('#validateTips').html('');
                    $('.message').remove();
                    $('.buttonset button').addClass('disabled');
                    $('.buttonset button:focus').prepend('<i class="glyph-icon icon-spinner icon-spin"></i>');
                    if (currentTab === 'tabMain')
                    {
                        var bValid = true;
                        allFields.removeClass('ui-state-error');
                        var form = $('#<?php echo $table; ?>_form').ajaxForm();
                        if ($("input[name='mode']").size() > 0)
                            $("input[name='mode']").attr("value", "saveAndExit");
                        else
                            form.prepend('<input type="hidden" name="mode" value="saveAndExit">');

                        <?php echo $js; ?>

                        if (bValid)
                            $('#<?php echo $table; ?>_form').ajaxSubmit({
                                //mode: "save",
                                beforeSubmit: function () {
                                },
                                success: function (data)
                                {
                                    //Прописываем хэш к пути
                                    location = data.redirect;//'#open' + currentEditId;
                                    $('i.icon-spinner').remove();
                                }
                            });
                    }
                    else
                    {
                        currentTabId = $('.ui-tabs-active a').attr('id');

                        var cform = $('.ui-tabs-panel[aria-labelledby="' + currentTabId + '"] form');
                        if (cform.length === 0)
                            $(this).dialog('close');

                        //Находим текущий id
                        //Если мы в таблице
                        if ($('#datatable_<?php echo $table ?> .edit_link').length > 0)
                        {
                            var cid = currentEditId;
                        }
                        //Если мы в дереве
                        else
                        {
                            var cid = $.tree.focused().selected.find("a").prop("id").substr(1);
                        }

                        cform.prepend('<input type="hidden" name="id" value="' + cid + '">');
                        cform.prepend('<input type="hidden" name="table" value="<?php echo $table ?>">');

                        cform.ajaxSubmit({
                            target: '.ui-tabs-panel[aria-labelledby="' + currentTabId + '"]',
                            beforeSubmit: function () {
                            },
                            success: function ()
                            {
                                location.hash = "";
                                window.location.reload();
                                $('i.icon-spinner').remove();
                            }
                        });
                    }
                }
            },
            {
                text: 'Копия',
                click: function () {
                    $('#validateTips').html('');
                    $('.message').remove();
                    $('.buttonset button').addClass('disabled');
                    $('.buttonset button:focus').prepend('<i class="glyph-icon icon-spinner icon-spin"></i>');
                    var el = $('#se_<?php echo $table ?>_id');
                    var val = el.val();
                    console.log(val);
                    el.val(0);
                    console.log(el.val());
                    if (currentTab === 'tabMain')
                    {
                        var bValid = true;
                        allFields.removeClass('ui-state-error');
                        var form = $('#<?php echo $table; ?>_form').ajaxForm();//submit();
                        if ($("input[name='mode']").size() > 0)
                            $("input[name='mode']").attr("value", "save");
                        else
                            form.prepend('<input type="hidden" name="mode" value="save">');

                        <?php echo $js; ?>

                        if (bValid)
                            form.ajaxSubmit({
                                beforeSubmit: function () {
                                },
                                success: function (data)
                                {
                                    currentEditId = data.id;
                                    $('#se_<?php echo $table ?>_id').val(data.id);
                                    //Прописываем хэш к пути
                                    location.hash = '#open' + currentEditId;
                                    $('.buttonset button').removeClass('disabled');
                                    $('i.icon-spinner').remove();
                                    $('#tabMain').prepend('<div class="message text-center text-success">Копия создана</div>');
                                    $('.message').delay(2500).slideUp();
                                }
                            });
                    }
                }
            },
            {
                text: 'Отмена',
                click: function () {
                    $(this).dialog('close');
                    window.location.reload();
                }
            }
        ],
        create: function () {
            $(this).closest(".ui-dialog")
                    .find(".ui-dialog-buttonset")
                    .addClass("buttonset")
                    .removeClass("ui-dialog-buttonset");

            $(this).closest(".ui-dialog")
                    .find(".buttonset button:not(:last)")
                    .addClass("btn btn-primary");

            $(this).closest(".ui-dialog")
                    .find(".buttonset button:last")
                    .addClass("btn btn-default");
        },
        open: function()
        {
            $('html, body').css('overflow', 'hidden');
            var dialogHeight = $('.ui-dialog:visible').outerHeight();
            var windowHeight = $(window).height();

            if(dialogHeight > windowHeight)
            {
                $('.ui-dialog:visible').addClass('long');
            }
        },
        close: function ()
        {
            //allFields.val('').removeClass('ui-state-error');
            //Чистим хэш у пути
            location.hash = '';
            tips.html('');
            $('.allTips').html('');
            $('.tabs').tabs("option", "active", 0);
            $('html, body').css('overflow', 'auto');
            $('.ui-dialog:visible').removeClass('long');
        }
    });

    //Редактировать запись
    $('#datatable_<?php echo $table ?> .edit_link').on('click', function(){
        currentEditId = $(this).attr('iid');
        jEditWindow(currentEditId);
        return false;
    });

    //Добавить запись
    $('#datatable_<?php echo $table ?> .add_link, .add_link').click(function(){
        jEditWindow(0);
        return false;
    });

    $(".datefield").datepicker({
        dateFormat: "yy-mm-dd"
    });

    //Табы
    <?php if(!empty($tabs)) { ?>
    $(function () {
        $(".tabs").tabs({
            beforeActivate: function (event, ui) {
                $(ui.newPanel).append('<div id="loading" class="tab"><img src="/admin/images/spinner/loader-dark.gif" alt="Loading..."></div>');

                //Находим текущий id
                //Если мы в таблице
                if ($('#datatable_<?php echo $table ?> .edit_link').length > 0)
                {
                    var cid = currentEditId;
                }
                //Если мы в дереве
                else if (typeof ($.tree) !== 'undefined')
                {
                    var cid = $.tree.focused().selected.find("a").prop("id").substr(1);
                }
                else
                {
                    var cid = 0;
                }
                var url = $('a', ui.newTab).attr('href') + '&id=' + cid;
                $('a', ui.newTab).attr('href', url);
            },
            activate: function (event, ui)
            {
                //Находим текущий id
                //Если мы в таблице
                if ($('#datatable_<?php echo $table ?> .edit_link').length > 0)
                {
                    var cid = currentEditId;
                }
                //Если мы в дереве
                else if (typeof ($.tree) !== 'undefined')
                {
                    var cid = $.tree.focused().selected.find("a").prop("id").substr(1);
                }
                else
                {
                    var cid = 0;
                }
                currentTab = $(ui.newPanel).prop("id");
                currentTabId = ui.newTab.attr('id');

                if (currentTabId === 0)
                    return;

                if (currentTab == 'tabMain') {
                    $('#loading.tab').delay(1000).remove();
                }

                // $(ui.newPanel).load($(".tabs ul li.ui-tabs-active a").attr('data-href') + '&id=' + cid);
                // $(ui.newPanel).tabs("load", $(".tabs ul a[href='#ui-tabs-"+currentTabId+"']").data('href.tabs') + '&id=' + cid);
            },
            load: function () {
                $('#loading.tab').remove();
            }
        });
    });
    <?php } ?>
});

function jEditWindow(id)
{
    $('.buttonset button').removeClass('disabled');
    $('i.icon-spinner').remove();
    $('.message').remove();
    $('#tabMain').append('<div id="loading" class="tab"><img src="/admin/images/spinner/loader-dark.gif" alt="Loading..."></div>');
    $.getJSON('<?php echo $plink; ?>&getFieldsById=' + id, function (data)
    {
        //Задаем id записи для формы
        $('#se_<?php echo $table ?>_id').val(id);
        //Прописываем хэш к пути
        location.hash = '#open' + currentEditId;
        //Заполняем поля
        $.each(data, function (i, field)
        {
            if (field.type === "enum('Y','N')")
            {
                if (field.value === 'Y')
                {
                    $('#se_<?php echo $table ?>_' + field.name + '_y').prop('checked', true);
                    $('#se_<?php echo $table ?>_' + field.name + '_n').prop('checked', false);
                }
                else
                {
                    $('#se_<?php echo $table ?>_' + field.name + '_y').prop('checked', false);
                    $('#se_<?php echo $table ?>_' + field.name + '_n').prop('checked', true);
                }

                $.uniform.update();
            }
            else if (field.type.substr(0, 4) === 'enum')
            {
                $('#se_<?php echo $table ?>_' + field.name + ' input').prop('checked', false);
                $('#se_<?php echo $table ?>_' + field.name + '_' + field.value).prop('checked', true);
            }
            else if (field.type === 'Image')
            {
                $('#se_<?php echo $table ?>_image_prev').html('<img src="' + field.value + '" height="80" />');
            }
            else
            {
                if (field.values)
                {
                    $('#se_<?php echo $table ?>_' + field.name).autocomplete({
                        minLength: 0,
                        source: field.values.split(","), // д.б. массив значений, через запятую, или объектов
                        def: field.value
                    }).click(function (){
                        $('#se_<?php echo $table ?>_' + field.name).autocomplete("search", "");
                    });
                }
                $('#se_<?php echo $table ?>_' + field.name).val(field.value);
            }
        });

        $('#<?php echo $table ?>_dialog').trigger('data_loaded');
        // для обеспечения вывода редактора tinyMCE по предопределённым полям
        tiny_mce_init_();

        $('#loading.tab').remove();
    });

    var winTitle = id === 0 ? 'Новая запись' : 'Редактирование';
    $('#<?php echo $table ?>_dialog').dialog('option', 'title', winTitle);

    $('#<?php echo $table ?>_dialog').dialog('open');
    return false;
}

//Автооткрытие по id записи
$(function ()
{
    if (location.hash.match(/open[\d]+/i))
    {
        currentEditId = location.hash.substr(5);
        jEditWindow(currentEditId);
    }
});
</script>

<div id="<?php echo $table; ?>_dialog" <?php if(!empty($tabs)) { ?>class="tabs"<?php } ?> title="Добавление/Редактирование">
    <?php if(!empty($tabs)) { ?>
    <ul>
        <li><a href="#tabMain">Общие</a></li>
        <?php foreach($tabs as $tab) { ?>
        <li><a href="<?php echo $plink; ?>&method=<?php echo $tab['method']; ?>"><?php echo $tab['name']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>

    <div id="tabMain">
        <p id="validateTips" class="allTips text-danger"></p>
        <form action="<?php echo $plink; ?>" id="<?php echo $table; ?>_form" method="post" <?php echo $we_have_files_fields ? 'enctype="multipart/form-data"' : ''; ?>>
            <fieldset>
                <input type="hidden" name="id" value="0" id="se_<?php echo $table; ?>_id" />
                <?php foreach($html as $v) { ?>
                    <?php echo $v; ?>
                <?php } ?>
            </fieldset>
        </form>
    </div>
</div>
