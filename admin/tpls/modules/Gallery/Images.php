<?php
    $p_module = "Gallery";
    $p_module_id = $module_id;
    require_once JS.'/elfinder-2.x/elf.inc.php';
?>

<div id="ImagesSet" style="overflow:hidden; margin:5px 0;">

<?php if(isset($info)) { ?>
    <p><?php echo $info; ?></p>
<?php } ?>   
<?php foreach ($images as $i) { ?>
	<div style="width:100px; height:100px; float:left; margin:15px 10px 0 15px; position:relative;" class="imagesImage">
	<?php if($i['main'] == 'Y') {?>
		<img src="/admin/images/icons/star.png" style="position:absolute; top:-12px; left:-8px; z-index:2" alt="Картинка по-умолчанию">
	<?php }?>
		<a style="position:absolute; top:0; left:0; z-index:1" href="<?php echo $i['src']?>" target="_blank"><img src="<?php echo image($i['src'], 100, 100)?>"></a>
		<div style="width:100px; height:20px; position:absolute; left:0; bottom:0; z-index:4; overflow: hidden; display:none;" class="imagesIconsPanel">
			<a title="Сделать изображением по-умолчанию" class="imageMakeStar" href="<?php echo $link?>&id=<?php echo $module_id?>&star=<?php echo $i['id']?>" target="_blank">
				<img src="/admin/images/icons/star-mini.png" style="float: left; margin:2px 0 0 5px;">
			</a>
			<a title="Увеличить" class="imageMagnify" href="<?php echo $i['src']?>" target="_blank">
				<img src="/admin/images/icons/magnifier-left.png" style="float: left; margin:2px 0 0 8px;">
			</a>
			<a title="Удалить" class="imageDelImage" href="<?php echo $link?>&id=<?php echo $module_id?>&del=<?php echo $i['id']?>" target="_blank">
				<img src="/admin/images/icons/cross.png" style="float:right; margin:2px 5px 0 0;">
			</a>
                        <a title="Ссылка на видео" class="imageYoutube" href="<?php echo $link?>&id=<?php echo $module_id?>&add=<?php echo $i['id']?>" target="_blank">
                            <img src="/admin/images/icons/video.jpg" height="24" width="24" style="float:right; margin:-3px 5px 0 0;">
			</a>
		</div>
		<div style="width:100px; height:20px; position:absolute; left:0; bottom:0; background-color:white; opacity: 0.5; z-index: 3; display:none;" class="imagesIconsPanelBack"></div>
	</div>
<?php }?>
</div>

<form style="visibility: hidden;" action="<?php echo $link?>" method="post" enctype="multipart/form-data" id="ImagesCatalogForm">
	<input type="hidden" name="id" value="<?php echo $module_id?>">
	<input type="file" name="image" id="UploadCatalogImage">
</form>
<button id="elfbutton" class="btn btn-default btn-lg" type="button" role="button" aria-disabled="false">
    Обзор
</button>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function () {
        initImagesTab();
    });
    function initImagesTab() {
	//Сделать по-умолчанию
	$('.imageMakeStar').click(function(){
		var url = $(this).attr('href');
		$.ajax({
			"url": url,
			success: function(html){
				$('#ImagesCatalogForm').parent().html(html);
			}
		});
		return false;
	});
	//Увеличение
	$('.imageMagnify').click(function(){
		window.open( $(this).attr('href') );
		return false;
	});
        
        //Ссылка на Youtube
	$('.imageYoutube').on('click', function(){
                var url = $(this).attr('href');
                var id;
                
                url.split('&').forEach(function(item) 
                {
                    item = item.split('=');
                    if (item[0] == 'add' ) id = item[1];
                });
                
                $('#linkYoutube-'+id).modal('toggle');
                              
                $('#addVideo').on('click', function(){
                    //$('#linkYoutube').modal('hide.bs.modal');
                    var video = $('#videoYouTube').val();
                    var title = $('#titleYouTube').val();
                    url = url + '&url='+video+'&title='+title;

                    $.ajax({
                        "url": url,
                        success: function(html){
                            $('.modal-title').html('Видео добавлено');
                            $('#videoYouTube').val('');
                            $('#titleYouTube').val('');
                            url = '';
                        }
                    });
                    return false;
                });
                
                // Фистим форму для нового заполнения
                $('#linkYoutube').on('hidden.bs.modal', function(){
                    $('#videoYouTube').val('');
                    $('#titleYouTube').val('');
                    $('.modal-title').html('&nbsp;');
                })
                
		return false;
	});
       
         // Фистим форму для нового заполнения
                $('#linkYoutube').on('hidden.bs.modal', function(){
                    $('#videoYouTube').val('');
                    $('.modal-title').html('&nbsp;');
                })
       
	//Удаление
	$('.imageDelImage').click(function(){
		if(confirm('Удалить изображение?')) {
			var url = $(this).attr('href');
			$.ajax({
				"url": url,
				success: function(html){
					$('#ImagesCatalogForm').parent().html(html);
				}
			});
		}
		return false;
	});
	//Показ панели в картинке
	$('.imagesImage').hover(function(){
			$(this).find('.imagesIconsPanel,.imagesIconsPanelBack').show();
		}, function(){
			$(this).find('.imagesIconsPanel,.imagesIconsPanelBack').hide();
		}
	);
	//АвтоЗагрузка
	$('#UploadCatalogImage').change(function(){
		$(this).parent().ajaxSubmit({
			target:        '#ui-tabs-'+currentTabId
		});
	});
    }

</script>
<?php foreach($images as $i): ?>
    <div class="modal fade" id="linkYoutube-<?php echo $i['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"></h4>
          </div>
          <div class="modal-body">
              <div class="form-group">
                  <label for="exampleInputEmail1">Ссылка на видео из YouTube</label>
                  <input type="text" name="videoYouTube" id="videoYouTube"  class="form-control" value="<?php echo $i['video'] ?>"/>
              </div>
              <div class="form-group">
                  <label for="exampleInputEmail1">Подпись к видео</label>
                  <input type="text" name="titleYouTube" id="titleYouTube"  class="form-control" value="<?php echo $i['title'] ?>"/>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            <button id="addVideo" type="button" class="btn btn-primary">Добавить</button>
          </div>
        </div>
      </div>
    </div>
<?php endforeach; ?>