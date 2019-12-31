<a href="<?php echo $addnew; ?>" class="text-success"><i class="glyph-icon icon-plus-circle"></i>&nbsp;Добавить опрос</a>
<br><br>

<?php if(! empty($votes)) { ?>
<table class="table">
    <?php foreach($votes as $vote) { ?>
    <tr>
        <td class="min-col"><?php echo $vote['id']; ?></td>
        <td><a href="<?php echo $edit; ?>&id=<?php echo $vote['id']; ?>"><?php echo $vote['name']; ?></a></td>
        <td class="min-col"><a href="<?php echo $delete; ?>&id=<?php echo $vote['id']; ?>" class="remove_votes text-danger"><i class="glyph-icon icon-times"></i></a></td>
    </tr>
    <?php } ?>
</table>
<?php } ?>

<script type="text/javascript">
$('.remove_votes').click(function(){
    if(!confirm("Вы дейстивтельно хотите удалить запись?")) {
        return false;
    }
});
</script>