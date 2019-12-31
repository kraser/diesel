<?php if($trash) { ?>
<a href="<?php echo $clear; ?>" class="btn btn-primary">Очистить корзину</a>
<br><br>

<table class="table">
    <?php foreach($trash as $item) { ?>
    <tr>
        <td class="min-col"><span class="text-muted"><?php echo $item['type']; ?></span></td>
        <td><?php echo $item['name']; ?></td>
        <td class="min-col"><a href="<?php echo $restore; ?>&table=<?php echo $item['table']; ?>&id=<?php echo $item['id']; ?>">Востановить</a></td>
        <td class="min-col"><a href="<?php echo $delete; ?>&table=<?php echo $item['table']; ?>&id=<?php echo $item['id']; ?>" class="remove_item text-danger"><i class="glyph-icon icon-times"></i></a></td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<p><strong>Корзина пуста.</strong><br>Удаленных элементов сайта нет.</p>
<?php } ?>

<script type="text/javascript">
$('.remove_item').click(function(){
    if(!confirm("Вы дейстивтельно хотите удалить запись?")) {
        return false;
    }
});
</script>