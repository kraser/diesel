<div class="content-box">
    <div class="content-box-wrapper">
        <h4 class="text-transform-upr font-gray font-size-16"><?php echo $title; ?></h4>
        <div class="pad10B">
            <div class="bs-label label-primary" title=".bg-primary"></div>
            <div class="bs-label label-default" title=".bg-default"></div>
        </div>
        <p class="font-gray-dark">
            <?php echo $text; ?>
            <?php if(isset($link)) { ?><a href="<?php echo $link; ?>">Раздел справки</a><?php } ?>
        </p>
    </div>
</div>