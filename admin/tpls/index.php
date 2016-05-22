<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <!--[if gte IE 9]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
    <!--[if IE 8]><meta http-equiv="X-UA-Compatible" content="IE=8"><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $title; ?></title>

    <!-- Theme -->
    <link rel="stylesheet" type="text/css" href="/admin/theme/icons/spinnericon/spinnericon.css" />
    <link rel="stylesheet" href="/admin/theme/all-demo.css" type="text/css" />
    <link rel="stylesheet" href="/admin/css/layout.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/admin/css/jquery.tagit.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/admin/css/tagit.ui-zendesk.css" type="text/css" media="screen" />
    <link href="/admin/css/sport-icon.css" type="text/css" rel="stylesheet">

    <link rel="icon" href="/admin/images/icons/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="/admin/images/icons/favicon.ico" type="image/x-icon"/>

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.min.js"></script>


    <script type="text/javascript" src="/admin/theme/all-demo.js"></script>
    <script type="text/javascript" src="/admin/theme/widgets/input-switch/inputswitch.js"></script>
    <script type="text/javascript" src="/admin/theme/widgets/spinner/spinner.js"></script>
    <script type="text/javascript" src="/admin/js/jquery.form.js"></script>
    <script type="text/javascript" src="/admin/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="/admin/js/DataTableAdvanced/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/admin/js/tag-it.js"></script>

    <script src="/js/elFinderFunctions.js"></script>

    <style>
        #loading {position: fixed;width: 100%;height: 100%;left: 0;top: 0;right: 0;bottom: 0;display: block;background: #fff;z-index: 10000;}
        #loading img {position: absolute;top: 50%;left: 50%;margin: -23px 0 0 -23px;}
    </style>

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

    <div id="sb-site">
        <div id="page-wrapper">
            <div id="page-header" class="clearfix">
                <div id="header-logo" class="rm-transition">
                    <a href="#" class="tooltip-button hidden-desktop" title="Navigation Menu" id="responsive-open-menu">
                        <i class="glyph-icon icon-align-justify"></i>
                    </a>
                    <div class="logo-small pull-left"><img src="/admin/images/logo-small.jpg" alt="NOTIX CMS" /></div>

                    <a id="collapse-sidebar" href="#" title="">
                        <i class="glyph-icon icon-chevron-left"></i>
                    </a>

                </div>
                <!-- #header-logo -->

                <?php echo $userbar; ?>
            </div>
            <!-- #page-header -->

            <div id="page-sidebar" class="rm-transition">
                <div id="page-sidebar-wrapper">
                    <?php echo $menu; ?>
                    <div class="divider"></div>

                    <div class="hidden-mobile mrg15A copyrate">
                        <p>&copy; 2009—<?php echo date('Y'); ?>  —  <a href="http://notix-cms.ru/" target="blank">CMS Нотикс (Diesel)</a></p>
                        <p><a href="mailto:develop@notix-cms.ru">Письмо разработчикам</a></p>
                        <p><a href="http://notix-cms.ru/partners" target="_blank">Создание и продажа интернет-сайтов</a></p>
                    </div>

                    <div class="divider"></div>
                </div><!-- #page-sidebar-wrapper -->
            </div><!-- #page-sidebar -->

            <div id="page-content-wrapper" class="rm-transition">
                <div id="page-content">
                    <div class="page-box">
                        <h3 class="page-title"><?php echo $h1; ?></h3>
                        <div class="content <?php echo (!empty($hint) || !empty($sidemenu)) ? 'col-lg-9 col-sm-12' : 'full'; ?>">
                            <?php echo $content; ?>
                        </div>
                        <?php
                        if(!empty($hint) || !empty($sidemenu)) {
                            ?>
                            <div id="sidebar" class="col-lg-3 col-sm-12">
                                <?php echo $sidemenu; ?>
                                <?php echo $hint; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div><!-- #page-content -->
            </div><!-- #page-content-wrapper -->
        </div><!-- #page-wrapper -->

    </div><!-- #sb-site -->
</body>
</html>
