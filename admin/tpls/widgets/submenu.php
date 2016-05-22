<div class="list-group">
    <?php foreach($data as $i) { ?>
        <a href="<?php echo $i['link']; ?>" class="list-group-item <?php echo $i['act'] ? 'active' : ''; ?>"><?php echo $i['name']; ?></a>
    <?php } ?>
</div>