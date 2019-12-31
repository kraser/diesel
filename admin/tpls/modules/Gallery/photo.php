<?php foreach($notification as $line) { ?>
<p><?php echo $line; ?></p>
<?php } ?>
<a href="<?php echo $link; ?>&action=resize">Обработать размеры изображений</a>
&nbsp;|&nbsp;
<a href="<?php echo $link; ?>&action=delete">Удалить неиспользуемые изображения</a>