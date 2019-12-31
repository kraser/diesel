<?php if(! empty($userversions)) { ?>
<table class="table">
    <?php foreach($userversions as $variant) { ?>
    <tr>
        <td class="min-col"><span class="text-muted"><?php echo $variant['vote']; ?></span></td>
        <td><a href="<?php echo $edit; ?>&id=<?php echo $variant['id']; ?>"><?php echo $variant['text']; ?></a></td>
        <td class="min-col"><a href="<?php echo $delete; ?>&id=<?php echo $variant['id']; ?>" class="remove_votes text-danger"><i class="glyph-icon icon-times"></i></a></td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<p><strong>Ответов нет.</strong><br>Свои варианты ответов посетители оставляют в пользовательской части сайта, если у опроса отмечена опция «Свой вариант ответа».</p>
<?php } ?>

<script type="text/javascript">
$('.remove_votes').click(function(){
    if(!confirm("Вы дейстивтельно хотите удалить запись?")) {
        return false;
    }
});
</script>