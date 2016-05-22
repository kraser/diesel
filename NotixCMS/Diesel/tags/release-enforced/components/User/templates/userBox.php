<?php
if($user && $type == "view")
{
?>
<div class="infoBox">
    <button name="logoutAction" class="logout userAction" title="Выход из системы">Выход</button>
    <div class='userInfo' title="Вход в личный кабинет" style='cursor:pointer;' onclick="window.location='/privateOffice'"><?php echo $user; ?></div>
</div>
<?php
}
else if($type == "logoutAction" || (!$user && $type == 'view'))
{
?>
<span><?php echo $error; ?></span>
<!--<a class="register userAction" name="registerAction" href="javascript:void(0)" title="Быстрая регистрация">Регистрация</a>
<div class="line"></div>-->
<a class="input userAction" name="loginAction" href="javascript:void(0)" title="Вход для зарегистрированных пользователей">Войти</a>
<?php
}
elseif($type == "loginAction" || $type == "registerAction")
{
?>

<ul>
    <li>
        <input id="log" name="name" type="text" placeholder="Логин" title="Допустимы латинские буквы и цифры"/>
    </li>
    <li>
        <input id="pwd" name="passwd" placeholder="Пароль" type="password" title="Допустимы латинские буквы и цифры"/>
    </li>
    <li>
        <button name="<?php echo $type; ?>" class="userAction"><?php echo ($type == "registerAction"? "Регистрация": "Вход");?></button>
        <!--<span>Вход</span>-->
    </li>
</ul>
<span style="color: #FF0000;font-size: 13px;text-shadow: none;left: 156px;position: relative;top: -47px;display: inline-table;"><?php echo $error; ?></span>
<?php
}
?>
