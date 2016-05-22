<style type="text/css">
#regionsForm textarea { height:50px; }
.message { padding:4px; margin:0 30px 6px -4px; font-size:16px; }
</style>

<div class="message"><?php echo $message; ?></div>

<form action="<?php echo $link; ?>" method="post" id="regionsForm">
    <select name="regions[]" id="regions" multiple size="20">
        <option value="all" <?php echo ($hasActive === false) ? 'selected="selected"' : ''; ?>>- Все регионы -</option>
        <?php foreach($regions as $region) { ?>
        <option value="<?php echo $region['id']; ?>" <?php echo ($region['active'] === true) ? 'selected="selected"' : ''; ?>><?php echo $region['name']; ?></option>
        <?php } ?>
    </select>
</form>

<script type="text/javascript">
var msgBar = $('.message');
if(msgBar.html() != '')
    msgBar.effect("highlight", {}, 3000);
</script>