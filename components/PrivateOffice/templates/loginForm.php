<div id="authBox">
    <div id="easyOrder">
        <input type="button" class="button" id="orderCancel" value="Назад">
        <form action="/privateOffice/createOrder" id="orderForm" class="mainform" method="post">
            <?php
            if(!empty($errors))
            {
                ?>
            <ul class="jep jepErrors">
                <?php
                foreach ($errors as $error)
                {
                    ?>
                <li><?php echo $error; ?></li>
                <?php
                }
                ?>
            </ul>
            <?php
            }
            ?>
            <div class="steps">
                <ol>
                    <li>
                        <h2 class="block-title">Шаг 1. Контактные данные</h2>
                        <div>
                            <input type="text" name="name" id="Oname" placeholder="Имя, фамилия" value="<?php echo isset($orderData['name'])?htmlspecialchars($orderData['name']):''?>" />
                            <input type="text" name="mail" id="Omail" placeholder="E-mail" value="<?php echo isset($orderData['email'])?htmlspecialchars($orderData['email']):''?>" />
                            <input type="text" name="phone" id="Ophone" placeholder="Телефон" value="<?php echo isset($orderData['phone'])?htmlspecialchars($orderData['phone']):''?>" />
                            <!--<a href="javascript:void();" class="btn nextStep" id="step1">Далее</a>-->
                            <input type='button' class="button nextStep" id="step1" value="Далее"/>
                        </div>
                    </li>
                    <li>
                        <h2 class="block-title disabled">Шаг 2. Доставка</h2>
                        <div>
                            <textarea name="address" id="Oaddress" placeholder="Адрес доставки"><?php echo isset($orderData['address'])?htmlspecialchars($orderData['address']):''?></textarea>
                            <?php if(!empty($paymethods)) { ?>
                            <!--<a href="#" class="btn nextStep" id="step2">Далее</a>-->
                            <input type="button" class="button nextStep" id="step2" value="Далее"/>
                            <?php } else { ?>
                            <!--<a href="#" class="btn finalStep" id="step2">Оформить заказ</a>-->
                            <input type="button" class="button finalStep" id="step2" value="Оформить заказ"/>
                            <?php } ?>
                        </div>
                    </li>
                    <?php if(!empty($paymethods)) { ?>
                    <li>
                        <h2 class="block-title disabled">Шаг 3. Оплата</h2>
                        <div>
                            <section class="ac-container">
                            <?php foreach ($paymethods as $pm) { $showFirst = !isset($showFirst)?'display:block;':'';?>
                                <div>
                                    <input type="radio" name="payment" id="PaymentMethod<?php echo $pm->id; ?>" value="<?php echo $pm->id; ?>" <?php echo empty($showFirst)?'':'checked="checked"'?> />
                                    <label for="PaymentMethod<?php echo $pm->id; ?>"><?php echo $pm->name; ?></label>
                                    <article class="ac-small">
                                       <?php echo $pm->text; ?>
                                    </article>
                                </div>
                            <?php } ?>
                            </section>
                            <!--<a href="#" class="btn finalStep" id="step3">Оформить заказ</a>-->
                            <input type="button" class="button finalStep" id="step3" value="Оформить заказ"/>
                        </div>
                    </li>
                    <?php } ?>
                </ol>
            </div>
        </form>
    </div>
    <div id="authOption">
    <div class="leftCol">
        <h2>Новый покупатель</h2>
        <p>Опции Оформления заказа:</p>
        <label for="register">
            <input type="radio" checked="checked" id="register" value="register" name="account">
            <b>Регистрация</b>
        </label><br>
        <label for="guest">
            <input type="radio" id="guest" value="guest" name="account">
            <b>Оформить заказ без регистрации</b>
        </label><br><br>
        <p>Создав учетную запись Вы сможете быстрее оформлять заказы, отслеживать их статус и просматривать историю покупок.</p>
        <input type="button" class="button" id="button-account" value="Продолжить"><br><br>
    </div>
    <div class="rightCol">
        <form action="<?php echo $action; ?>" id="loginForm" method="post">
            <div class="loginError"></div>
            <h2>Зарегистрированный пользователь</h2>
            <p>Я совершал здесь покупки ранее и/или регистрировался</p>
            <b>Логин:</b><br>
            <input type="text" value="" name="login"><br><br>
            <b>Пароль:</b><br>
            <input type="password" value="" name="password"><br>
            <input type='hidden' name="authBox" value='LOGIN'>
            <a href="javascript:void(0);" class='remind'>Забыли пароль?</a><br><br>
            <input type="submit" class="button" id="button-login" value="Войти"><br><br>
        </form>
    </div>
    </div>
    <div id='remind'>
        <h2>Восстановление пароля</h2>
        <span class="required">*</span> Введите E-mail указанный при регистрации:<br>
        <input type="text" id='mail-remind' class="large-field" style='width: 500px;' value="" name="mail">
        <input id='button-remind' class="button" type='button' value='Отправить'>
        <input id='remind-cancel' class="button" type='button' value='Отмена'>
    </div>
    <div id="authError" class="error"></div>
</div>