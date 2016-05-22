<div id="imagesDialog">
    <iframe id="imagesIframe" height="600px" width="100%" src=""></iframe>
</div>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $("#imagesDialog").dialog({
        title: "Работа с изображениями",
        bgiframe: false,
        autoOpen: false,
        closeOnEscape: false,
        width: '70%',
        height: 'auto',
        modal: true,
        open: function(ev, ui){
            $('#imagesIframe').attr('src', '/js/elfinder-2.x/elfinder.html');
            $('#imagesIframe').attr('data-module', $('#p_module').html());
            $('#imagesIframe').attr('data-module_id', $('#p_module_id').html());
        },
        close: function(ev, ui){
            initImagesTab();
        }
    });

    $('#elfbutton').click(function(){
        $('#imagesDialog').dialog('open');
    });

    window.closeImagesDialog = function () {
        $('#imagesDialog').dialog('close');
    }
});
</script>

<span id="p_module" hidden=""><?php echo $p_module?></span>
<span id="p_module_id" hidden=""><?php echo $p_module_id?></span>