<div class="message text-center text-success"><?php echo $message; ?></div>
<form action="<?php echo $link; ?>" method="post" id="seoForm">
    <div class="form-group">
        <label for="seoTitle">Title</label>
        <textarea name="title" id="seoTitle" class="form-control"><?php echo $title; ?></textarea>
    </div>
    <div class="form-group">
        <label for="seoKeywords">Keywords</label>
        <textarea name="keywords" id="seoKeywords" class="form-control"><?php echo $keywords; ?></textarea>
    </div>
    <div class="form-group">
        <label for="seoDescription">Description</label>
        <textarea name="description" id="seoDescription" class="form-control"><?php echo $description; ?></textarea>
    </div>
    <div class="form-group">
        <label for="seoTagH1">H1</label>
        <textarea name="tagH1" id="seoTagH1" class="form-control"><?php echo $tagH1; ?></textarea>
    </div>
</form>

<script type="text/javascript">
    var msgBar = $('.message');

    if (msgBar.html() != '')
    {
        msgBar.delay(2500).slideUp();
    }
</script>
