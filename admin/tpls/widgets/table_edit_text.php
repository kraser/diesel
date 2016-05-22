<script type="text/javascript" src="/admin/js/tinymce3x/jquery.tinymce.js"></script>
<script type="text/javascript">
$(function () {
    var text_changed = false;
    $('#edit_text').tinymce({
        script_url: '/admin/js/tinymce3x/tiny_mce.js',
        skin: "o2k7",
        theme: "advanced",
        plugins: "jaretypograph,safari,style,table,images,advhr,advimage,advlink,inlinepopups"
                + ",contextmenu,paste,lists,fullscreen,noneditable,visualchars,nonbreaking"
                + ",searchreplace,spellchecker,insertdatetime,media,wordcount",
        theme_advanced_buttons1: "bold,italic,underline,strikethrough,formatselect,fontselect,fontsizeselect,justifyleft,justifycenter,justifyright,justifyfull"
                + ",|,bullist,numlist,|,outdent,indent,|,sub,sup,|,jaretypograph,|,forecolorpicker,backcolorpicker,|,|,styleprops",
        theme_advanced_buttons2: "undo,redo,|,search,replace,|,spellchecker,|,|,insertdate,inserttime,|,link,unlink,anchor,|,pastetext,pasteword,pastetext,table,|,images,image,|,media,|,cleanup,removeformat,|,|,visualchars,visualaid,code,fullscreen,|,nonbreaking,hr,advhr,blockquote,charmap",
        theme_advanced_buttons3: "",
        theme_advanced_buttons4: "",
        theme_advanced_font_sizes: "9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        // для вставки <obiect> и <iframe>
        extended_valid_elements: "iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width],"
                + "object[declare|classid|codebase|data|type|codetype|archive|standby|height|width|usemap|name|tabindex|align|border|hspace|vspace]",
        // Site CSS + изменения для tiny
        content_css: "/themes/<?php echo Starter::app ()->theme; ?>/css/tiny.css",
        relative_urls: false,
        remove_script_host: true,
        language: "ru",
        setup: function (ed) {
            ed.onChange.add(function (ed, l) {
                text_changed = true;
            });
        },
    });

    $('.buttons button[type="submit"]').click(function () {
        $(this).prepend('<i class="glyph-icon icon-spinner icon-spin"></i>');
        $('.buttons button').addClass('disabled');
    });
    $('#goOut').click(function () {
        if (text_changed)
            window.onbeforeunload = function () {
                return 'Изменения в содержании не сохранены.';
            }
        document.location = '<?php echo $plink ?>';
    });
});
</script>

<form action="<?php echo $plink; ?>&edit_text=<?php echo $id; ?>" method="post" class="editTextForm">
    <textarea id="edit_text" name="text"><?php echo $text; ?></textarea>

    <div class="buttons pull-right">
        <button type="button" class="btn btn-default" id="goOut">Вернуться к списку</button>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</form>