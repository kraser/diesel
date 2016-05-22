<form action="/privateOffice/registration" name="registrationForm" id="registrationForm" method="post">
    <div class="leftCol">
        <?php
        foreach ($data->errors as $error)
        {
            ?>
        <span class="error"><?php echo $error; ?></span><br>
            <?php
        }
        ?>
        <h2>Личные данные</h2>
        <input type="hidden" value="REGISTR" name="regBox">
        <!--<span class="required">*</span> -->Имя, Отчество:<br>
        <input type="text" class="large-field" value="<?php echo $data->firstName; ?>" name="firstname" id="Rfirstname"><br>
        <!--<span class="required">*</span> -->Фамилия:<br>
        <input type="text" class="large-field" value="<?php echo $data->lastName; ?>" name="lastname" id="Rlastname"><br>
        <span class="required">*</span> E-Mail:<br>
        <input type="text" class="large-field" value="<?php echo $data->email; ?>" name="email" id="Remail"><br>
        <span class="required">*</span> Телефон:<br>
        <input type="text" class="large-field" value="<?php echo $data->phone; ?>" name="phone" id="Rphone"><br>
        <span class="required">*</span> Логин:<br>
        <input type="text" class="large-field" value="<?php echo $data->login; ?>" name="login" id="Rlogin"><br>
        <span class="required">*</span> Пароль:<br>
        <input type="password" class="large-field" value="<?php echo $data->password; ?>" name="password" id="Rpassword"><br>
        <span class="required">*</span> Подтверждение пароля:<br>
        <input type="password" class="large-field" value="<?php echo $data->confirm; ?>" name="confirm" id="Rconfirm"><br>
        <span class="required">*</span> Поля обязательные для заполнения
    </div>
    <div class="rightCol">
        <h2>Адрес</h2>
        Компания (для юридических лиц):<br>
        <input type="text" class="large-field" value="<?php echo $data->company; ?>" name="company" id="Rcompany"><br>
        <!--<span class="required">*</span> -->Адрес:<br>
        <input type="text" class="large-field" value="<?php echo $data->address; ?>" name="address" id="Raddress"><br>
        <!--<span class="required">*</span> -->Город:<br>
        <input type="text" class="large-field" value="" name="city" id="Rcity"><br>
        <!--<span class="required" id="payment-postcode-required" style="display: none;">*</span>--> Индекс:<br>
        <input type="text" class="large-field" value="" name="postcode" id="Rpostcode"><br>
        <div id='authCaptcha'></div>
        <?php
        $publickey = SqlTools::selectValue("SELECT `value` FROM `prefix_settings` WHERE `callname`='publicKey'");
        /*
        require_once(LIBS.DS.'recaptchalib.php');
        $publickey = "6LeH--8SAAAAAHOGxATOtycy1N7OLzDjEE0gdTTI"; // you got this from the signup page
        $test = recaptcha_get_html($publickey);
        */
        ?>
        <!-- <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LdYVPASAAAAANyeNAI7MoRtMXv8Tvkt3ZS7q2Lz"></script>
        <noscript>
           <iframe src="http://www.google.com/recaptcha/api/noscript?k=your_public_key" height="300" width="500" frameborder="0"></iframe><br>
           <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
           <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
        </noscript>-->
      <br>
      <input type="button" class="button" id="button-register" value="Регистрация">
    </div>
</form>

</div>
<script type="text/javascript">
    <?php
    if($publickey)
    {
        ?>
    Recaptcha.create("<?php echo $publickey; ?>",
    "authCaptcha",
    {
      theme: "clean",
      callback: Recaptcha.focus_response_field
    }
  );
  <?php
    }
    ?>
</script>