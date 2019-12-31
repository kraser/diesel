<div style="overflow:hidden; margin:5px 0;">
    <?php if(isset($info)) { ?>
        <p><?php echo $info; ?></p>
    <?php }
//    $mime = Tools::getMimeIconForExt(pathinfo($file['link'])['extension']);
    if(!empty($file['link']))
    { ?>
        <div style="width:100px; height:100px; float:left; margin:5px 5px 5px 5px; position:relative;" class="filesImage">
            <div style="position:absolute; top:0; left:0; z-index:1">
                <img src="<?php echo image($mime, 50, 50) ?>">
                <p style='vertical-align:bottom;'><?php echo  basename($file['link']); ?></p>
            </div>
            <div style="width:100px; height:20px; position:absolute; left:0; bottom:0; z-index:4; overflow: hidden; display:none;" class="filesIconsPanel">
                <a title="Удалить" class="fileDelImage" href="<?php echo $link ?>&del=<?php echo $file['link']; ?>&id=<?php echo $module_id?>" target="_blank">
                    <img src="/admin/images/icons/cross.png" style="float:right; margin:2px 5px 0 0;">
                </a>
            </div>
            <div style="width:100px; height:20px; position:absolute; left:0; bottom:0; background-color:white; opacity: 0.5; z-index: 3; display:none;" class="filesIconsPanelBack"></div>
            <div style=" display:<?php echo $file['found'] ? 'none' : 'block'?>;color:red;text-align:center;width:100px; height:100px; position:absolute; left:0; background-color:white; opacity: 0.4; z-index: 2;" class="filesNoFoundPanelBack">Файл недоступен</div>
        </div>
    <?php } ?>
</div>

<form action="<?php echo $link?>" method="post" enctype="multipart/form-data" id="FilesCatalogForm">
    <input type="hidden" name="id" value="<?php echo $module_id?>">
    <input type="file" name="file" id="UploadCatalogFile">
</form>

<script type="text/javascript">

	//Удаление
	$('.fileDelImage').click(function(){
		if(confirm('Удалить файл?')) {
			var url = $(this).attr('href');
			$.ajax({
				"url": url,
				success: function(html){
					$('#FilesCatalogForm').parent().html(html);
				}
			});
		}
		return false;
	});
	//Показ панели в картинке
	$('.filesImage').hover(function(){
			$(this).find('.filesIconsPanel,.filesIconsPanelBack').show();
		}, function(){
			$(this).find('.filesIconsPanel,.filesIconsPanelBack').hide();
		}
	);
	//АвтоЗагрузка
	$('#UploadCatalogFile').change(function(){
		$(this).parent().ajaxSubmit({
			target:        '#ui-tabs-'+currentTabId
		});
	});

</script>
