<?php
    $p_module = "Portfolio";
    $p_module_id = $module_id;
    require_once JS.'/elFinder2rc1/elf.inc.php';
?>
<div id="ImagesSet">
<?php if(!empty($images)) { ?>
    <table class="portfolioPages" width="100%" border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <td width="285px">Изображение</td>
                <td>Описание</td>
                <td width="100px">Сортировка</td>
                <td width="125px">&nbsp;</td>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($images as $i) { ?>
            <tr>
                <td align="center"><img src="<?php echo $i['src']; ?>" alt="<?php echo $i['image_id']; ?>"/></td>
                <td align="center" valign="top"><textarea name="text" cols="30" rows="10"><?php echo $i['description']; ?></textarea></td>
                <td align="center"><input type="text" size="3" name="sort" value="<?php echo $i['order']; ?>"/></td>
                <td align="center">
                    <input type="hidden" name="item_id" value="<?php echo $i['image_id']; ?>" />
                    <input type="button" name="save" value="Сохранить"/><br/>
                    <input type="button" name="delete" value="Удалить"/>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
<?php } ?>
</div>
<br/><br/>
<h2>Добавить новую запись</h2>
<form action="<?php echo $link?>" enctype="multipart/form-data" method="post" id="ImagesCatalogForm">
	<input type="hidden" name="id" value="<?php echo $module_id?>">
    <table class="portfolioPages" width="100%" border="0" cellspacing="0" cellpadding="0">
        <thead>
            <thead>
                <tr>
                    <td width="285px">Изображение</td>
                    <td>Описание</td>
                    <td width="100px">Сортировка</td>
                    <td width="125px">&nbsp;</td>
                </tr>
            </thead>
        </thead>
        <tbody>
            <tr>
                <td valign="top" align="center"><input type="file" name="image" id="UploadCatalogImage"></td>
                <td valign="top" align="center"><textarea name="text" cols="30" rows="10" id="PageDescription"></textarea></td>
                <td align="center"><input type="text" size="3" name="sort" id="PageSort" value="0" /></td>
                <td align="center"><input type="submit" name="add" value="Добавить" class="ui-button-text" /></td>
            </tr>
        </tbody>
    </table>
</form>

<script type="text/javascript">
    // $('input[name=id]').val($('#se_portfolio_id').val());
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

    // Сохранить изменения
    $('input[name="save"]').live('click', function() {

        var row = $(this).parents('tr');
        item_id = $('input[name="item_id"]', row).val();
        sort = $('input[name="sort"]', row).val();
        text = $('[name="text"]', row).val();

        $.ajax({
            url: $('#ImagesCatalogForm').attr('action'),
            type: 'POST',
            data: {'id': <?php echo $module_id?>, 'update': 1, 'item_id': item_id, 'sort': sort, 'text': text},
            cache: false,
            dataType: 'json',
            success: function(html){
                alert('Запись успешно обновлена!');
                $('#ImagesSet').load($('#ImagesCatalogForm').attr('action') + " #ImagesSet", {"id": <?php echo $module_id?>});
            },
            error: function() {
                alert('Ошибка при обновлении!');
            }
        });
        return false;
    });

	//Удаление
	$('input[name="delete"]').live('click', function(){
		if(confirm('Удалить запись?')) {
			$.ajax({
				url: $('#ImagesCatalogForm').attr('action'),
                type: 'POST',
                data: {'id': <?php echo $module_id?>, 'del': 1, 'item_id': $(this).siblings('input[name="item_id"]').val()},
                cache: false,
                dataType: 'json',
				success: function(html){
					alert('Запись удалена!');
                    $('#ImagesSet').load($('#ImagesCatalogForm').attr('action') + " #ImagesSet", {"id": <?php echo $module_id?>});
				},
                error: function() {
                    alert('Ошибка при удалении!');
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

    // Variable to store your files
	var files;

	// Add events
	$('input[type=file]').on('change', prepareUpload);
	$('#ImagesCatalogForm').on('submit', uploadFiles);

	// Grab the files and set them to our variable
	function prepareUpload(event)
	{
		files = event.target.files;
	}

	// Catch the form submit and upload the files
	function uploadFiles(event)
	{
		event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE
        if(!files) {
            alert('Файл не загружен!');
            return false;
        }
        // Create a formdata object and add the files
		var formData = new FormData();
		$.each(files, function(key, value)
		{
			formData.append(key, value);
		});

        formData.append('id', $('input[name=id]').val());
        formData.append('sort', $('input[name=sort]').val());
        formData.append('text', $('#PageDescription').val());
        formData.append('add', $('input[name=add]').val());

        $.ajax({
            url: $('#ImagesCatalogForm').attr('action'),
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR)
            {
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
                    alert('Запись успешно добавлена');
                    $('#ImagesCatalogForm').trigger('reset');
            		// submitForm(event, data);

                    $('#ImagesSet').load($('#ImagesCatalogForm').attr('action') + " #ImagesSet", {"id": <?php echo $module_id?>});

            	}
            	else
            	{
                    alert('Ошибка при добавлении записи!');
            		// Handle errors here
            		console.log('ERRORS: ' + data.error);
            	}
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            	// Handle errors here
            	console.log('ERRORS: ' + textStatus);
            	// STOP LOADING SPINNER
            }

        });
    }

    function submitForm(event, data)
	{
		// Create a jQuery object from the form
		$form = $(event.target);

		// Serialize the form data
		var formData = $form.serialize();

		// You should sterilise the file names
		$.each(data.files, function(key, value)
		{
			formData = formData + '&filenames[]=' + value;
		});

		$.ajax({
			url: $('#ImagesCatalogForm').attr('action'),
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR)
            {
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
            		console.log('SUCCESS: ' + data.success);
            	}
            	else
            	{
            		// Handle errors here
            		console.log('ERRORS: ' + data.error);
            	}
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            	// Handle errors here
            	console.log('ERRORS: ' + textStatus);
            },
            complete: function()
            {
            	// STOP LOADING SPINNER
            }
		});
	}


</script>
