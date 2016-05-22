<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <!--[if gte IE 9]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
    <!--[if IE 8]><meta http-equiv="X-UA-Compatible" content="IE=8"><![endif]-->
    <title><?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Favicons -->
    <link rel="shortcut icon" href="/admin/images/icons/favicon.ico">

    <link rel="stylesheet" type="text/css" href="/admin/theme/all-demo.css">
    <style>
        #loading {position: fixed;width: 100%;height: 100%;left: 0;top: 0;right: 0;bottom: 0;display: block;background: #fff;z-index: 10000;}
        #loading img {position: absolute;top: 50%;left: 50%;margin: -23px 0 0 -23px;}
    </style>

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script type="text/javascript">
        $(window).load(function () {
            setTimeout(function () {
                $('#loading').fadeOut(400, "linear");
            }, 300);
        });
    </script>
</head>
<body>
    <div id="loading"><img src="/admin/theme/images/spinner/loader-dark.gif" alt="Загрузка..."></div>

    <div class="center-vertical">
        <div class="center-content">

            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="login-validation" class="col-md-4 center-margin" method="">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <img src="/admin/images/logo-big.jpg" alt="NOTIX CMS" class="img-responsive" />
                            <br>
                        </div>
                    </div>
                </div>
                <h3 class="text-center pad25B font-gray text-transform-upr font-size-23"><span class="opacity-80"><?php echo $title; ?></span><br>NOTIX CMS</h3>

                <div id="login-form" class="content-box modal-content">
                    <div class="content-box-wrapper pad20A">
                        <div class="text-center text-danger"><?php echo ( isset ( $error ) ? $error : "" ); ?></div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Логин:</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon addon-inside bg-white font-primary">
                                    <i class="glyph-icon icon-user"></i>
                                </span>
                                <input type="text" name="login" class="form-control" id="exampleInputEmail1" value="<?php echo (isset($post['login'])) ? $post['login'] : ''; ?>" placeholder="Введите логин" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Пароль:</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon addon-inside bg-white font-primary">
                                    <i class="glyph-icon icon-unlock-alt"></i>
                                </span>
                                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Введите пароль">
                            </div>
                        </div>
                    </div>
                    <div class="button-pane">
                        <button type="submit" class="btn btn-block btn-primary" name="send" value="true">Войти</button>
                    </div>
                </div>

            </form>

            <div class="text-center">&copy; 2009—<?php echo date('Y'); ?>  —  <a href="http://notix-cms.ru/" target="blank">CMS Нотикс (Diesel)</a><br><a href="mailto:develop@notix-cms.ru">Письмо разработчикам</a>&nbsp;|&nbsp;<a href="http://notix-cms.ru/partners" target="_blank">Создание и продажа интернет-сайтов</a></div>

        </div>
    </div>

    <script type="text/javascript" src="/admin/theme/all-demo.js"></script>
    <!--[if lt IE 9]>
      <script src="/admin/theme/js-core/html5shiv.js"></script>
    <![endif]-->
</body>
</html>
