<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
  <title><?php echo Sterter::app ()->title?></title>

    <!-- Fonts and controls -->
  <link href="<?php echo $sdir?>/css/typo1.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/css/order.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/themes/<?php echo Starter::app ()->getTheme (); ?>/css/ui/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css" media="screen" />
  <link rel="stylesheet" type="text/css" media="screen" href="/css/ui.jqgrid.css" />
  <script src="/js/jquery-1.10.2.min.js"></script>
<script src="/js/jqGrid/js/i18n/grid.locale-ru.js" type="text/javascript"></script>
        <script src="/js/jqGrid/js/jquery.jqGrid.src.js" type="text/javascript"></script>
  <script type="text/javascript" src="/js/main.js"></script>
  <script type="text/javascript" src="/themes/<?php echo Starter::app ()->getTheme (); ?>/js/interface.js"></script>
</head>
<body>
  <div id="center">
  <div id="header">
    <div id="maket1">
      <div class="logo"><a href="/"><img src="/themes/<?php echo Starter::app ()->getTheme (); ?>/images/logo.png"></a></div>
      <div class="menu">
        <div class="item active"><a href="/">Главная</a></div>
        <?php echo Starter::app ()->getModule('Content')->MainMenu()?>
        <div class="item"><a href="/basket">Корзина</a></div>

      </div>
      <div class="text">
      <?php block('main_text')?>
      </div>
      <div id="button"><button onclick="location.href='/catalog'">Каталог товаров</button></div>
      <div id="button1">
        <button onclick="location.href='/login'">Войти</button>
      </div>
    </div>
  </div>
  <div class="clear"></div>
  <div id="main">
    <div id="maket3">
      <?php echo Starter::app ()->getModule('Catalog')->MainMenu();?>
        <div id="under"></div>
    </div>

  </div>
  <div class="clear"></div>

  <div id="footer">
    <div id="maket2">
      <div class="contacts">
          <?php block("address"); ?>
      </div>
      <div class="jako">
        © 2013 «Жако» мебельная фабрика
      </div>
      <div class="notix">
        Сайт разработан <a href="http://notix.su/">Notix</a>
      </div>
    </div>
  </div>
  </div>
 </body>
</html>
