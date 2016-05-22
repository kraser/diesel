<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
    <head>
        <?php
        echo Starter::app ()->headManager->Run();
        echo tpl('parts/head');
        ?>
        <!--<link href="/css/order.css" rel="stylesheet" type="text/css" media="screen" />-->
        <link href="/themes/<?php echo Starter::app ()->getTheme (); ?>/css/ui/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css" media="screen" />
        <script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
        <script type="text/javascript" src="/js/base64.js"></script>
    </head>
    <body>
        <div id="wrap">
            <?php echo tpl('parts/header')?>

            <div id="main">
                <div id="officeContainer">
                    <?php echo Starter::app ()->getModule('Content')->breadCrumbs(); ?>
                    <div id="center" style="padding-bottom: 20px;">
                        <h1><?php echo $title; ?></h1>
                        <?php echo $content; ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            <div class="hfooter"></div>
            <?php echo tpl('parts/footer')?>
            </div>

        </div>
        </body>
</html>
