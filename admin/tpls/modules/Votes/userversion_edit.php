<form action="<?php echo $action; ?>" class="form-horizontal form-votes form-userversion-edit" method="post">
    <input type="hidden" name="version[id]" value="<?php echo $version['id']; ?>" />

    <div class="form-group">
        <label for="inputText" class="col-sm-4 control-label">Ответ</label>
        <div class="col-sm-4">
            <input type="text" name="version[text]" value="<?php echo $version['text']; ?>" class="form-control" id="inputText">
        </div>
    </div>

    <hr />
    <div class="col-md-4 col-md-offset-4">
        <div class="action_next">
            <label>
                <input type="checkbox" name="continue"> Продолжить редактирование
            </label>
        </div>
        <div class="action_buttons">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="<?php echo $link; ?>" class="btn btn-danger">Отменить</a>
        </div>
    </div>
</form>