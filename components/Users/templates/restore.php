<div id="authError" class="error"></div>
<div id="restore">
    Новый пароль<br>
    <input type="password" name="passwd"><br>
    Потверждение<br>
    <input type="password" name="confirm">
    <span class="error"></span><br>
    <div style="margin-top:10px">
    <input type="button" id="button-restore" class="button" value="Сменить" name="update">
    <input type="button" id="restoreCancel" value="Отменить" class="button" name="escape">
    <input type="hidden" name="check" value="<?php echo $hidden ?>">
    </div>
</div>
