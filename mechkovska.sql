-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: mechkovska
-- ------------------------------------------------------
-- Server version	5.5.49-0ubuntu0.14.04.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `mce_admin_users`
--

DROP TABLE IF EXISTS `mce_admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID пользователя',
  `login` varchar(32) NOT NULL COMMENT 'Логин администратора',
  `password` varchar(128) NOT NULL COMMENT 'Пароль пользователя (хэш)',
  `type` enum('a','m') NOT NULL DEFAULT 'm' COMMENT 'Статус администратора',
  `access` text NOT NULL COMMENT 'Список доступных модулей',
  `name` varchar(255) NOT NULL COMMENT 'Имя администратора',
  `post` varchar(255) NOT NULL COMMENT 'Должность администратора',
  `email` varchar(128) NOT NULL COMMENT 'E-mail администратора',
  `lastenter` datetime NOT NULL COMMENT 'Время последнего входа администратора в систему',
  `created` datetime NOT NULL COMMENT 'Время создания записи',
  `modified` datetime NOT NULL COMMENT 'Время последней модификации записи',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Таблица администраторов';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_admin_users`
--

LOCK TABLES `mce_admin_users` WRITE;
/*!40000 ALTER TABLE `mce_admin_users` DISABLE KEYS */;
INSERT INTO `mce_admin_users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','a','','Имярек','Администратор','dummy@notix.su','0000-00-00 00:00:00','2016-05-20 00:18:37','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `mce_admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_blocks`
--

DROP TABLE IF EXISTS `mce_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `callname` (`callname`),
  KEY `show` (`show`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_blocks`
--

LOCK TABLES `mce_blocks` WRITE;
/*!40000 ALTER TABLE `mce_blocks` DISABLE KEYS */;
INSERT INTO `mce_blocks` VALUES (1,0,'Скидка','discount','Y','N','2016-05-22 18:35:15','2016-05-22 18:55:17','Текст в блоке \'discount\' для объявления о скидке. Выплывающее окно \"Скидка\" появляется автоматически при установлении скидки в системных установках в значение отличное от 0. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat. '),(2,1,'Подарочный сертификат','cert','Y','N','2016-05-31 01:55:03','0000-00-00 00:00:00','<h1 class=\"text-head sertificat\">НА КУРСЫ ШКОЛЫ&nbsp;<br /><strong data-new-link=\"true\">MECHKOVSKAYA</strong></h1>\r\n<p><a class=\"w-button button-line vers2\" href=\"#\">ПОДРОБНЕЕ</a></p>\r\n<h5 class=\"text-small\">подарочный сертификат</h5>'),(3,2,'Введение','intro','Y','N','2016-06-01 00:38:53','0000-00-00 00:00:00','<p class=\"text-paragraph\"><span class=\"text-spain-paragraph\">&laquo;MECHKOVSKAYA SCHOOL OF BEAUTY Обучает успешных<br /> визажистов с 2011 года.<br /></span> <br />Мы художники, новаторы и трендсеттеры. Мы раскрываем уникальные таланты наших учеников. Делаем сложное простым и доступным.&nbsp;За время обучения Вы овладеете всеми техниками макияжа и сможете работать с любыми текстурами. Обучение проходит на профессиональной косметике знаменитых мировых брендов и постоянно пополняется новинками.&nbsp; <br /> <br />За время обучения Вы овладеете всеми техниками макияжа и сможете работать с любыми текстурами. Обучение проходит на профессиональной косметике знаменитых мировых брендов и постоянно пополняется новинками. <br /> <br /> КУРСЫ ШКОЛЫ ПОСТРОЕНЫ ТАК, ЧТО ЗА КОРОТКИЙ СРОК УЧЕНИКИ ПОЛУЧАЮТ ИМЕННО ТЕ ЗНАНИЯ И ОПЫТ, КОТОРЫЕ ПОЗВОЛЯЮТ ПРИНИМАТЬ КЛИЕНТОВ СРАЗУ ПОСЛЕ ОКОНЧАНИЯ КУРСА.&nbsp;</p>');
/*!40000 ALTER TABLE `mce_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_blog`
--

DROP TABLE IF EXISTS `mce_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_blog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  `top` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_blog`
--

LOCK TABLES `mce_blog` WRITE;
/*!40000 ALTER TABLE `mce_blog` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_blog_topics`
--

DROP TABLE IF EXISTS `mce_blog_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_blog_topics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(11) unsigned NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `types` text NOT NULL,
  `cases` text NOT NULL,
  `rate` int(11) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `top` (`top`),
  KEY `order` (`order`),
  KEY `deleted` (`deleted`),
  KEY `show` (`show`,`deleted`),
  KEY `show_2` (`show`),
  KEY `rate` (`rate`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_blog_topics`
--

LOCK TABLES `mce_blog_topics` WRITE;
/*!40000 ALTER TABLE `mce_blog_topics` DISABLE KEYS */;
INSERT INTO `mce_blog_topics` VALUES (1,0,0,'Виджеты','','','','',0,'Y','N','2016-05-22 18:18:59','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `mce_blog_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_catalog_tags`
--

DROP TABLE IF EXISTS `mce_catalog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_catalog_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `tagType` enum('INTERVAL','SET','ENUM') NOT NULL DEFAULT 'SET',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_catalog_tags`
--

LOCK TABLES `mce_catalog_tags` WRITE;
/*!40000 ALTER TABLE `mce_catalog_tags` DISABLE KEYS */;
INSERT INTO `mce_catalog_tags` VALUES (1,'clip','Тип обоймы','SET','2016-05-22 16:39:25','2016-05-22 16:46:59'),(2,'size','Размер','SET','2016-05-22 16:44:49','0000-00-00 00:00:00'),(3,'hair','Тип ворса','SET','2016-05-22 16:46:37','2016-05-22 16:47:13'),(4,'handler','Тип ручки','SET','2016-05-22 16:47:55','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `mce_catalog_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_clickjacking`
--

DROP TABLE IF EXISTS `mce_clickjacking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_clickjacking` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `reffer` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `modered` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_clickjacking`
--

LOCK TABLES `mce_clickjacking` WRITE;
/*!40000 ALTER TABLE `mce_clickjacking` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_clickjacking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_comments`
--

DROP TABLE IF EXISTS `mce_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL,
  `author` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `text` text NOT NULL,
  `module` varchar(20) NOT NULL,
  `element_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_comments`
--

LOCK TABLES `mce_comments` WRITE;
/*!40000 ALTER TABLE `mce_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_content`
--

DROP TABLE IF EXISTS `mce_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(11) unsigned NOT NULL,
  `order` int(11) NOT NULL,
  `nav` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `module` varchar(32) NOT NULL,
  `template` varchar(63) NOT NULL DEFAULT 'page.php',
  `showmenu` enum('Y','N') NOT NULL DEFAULT 'N',
  `show` enum('Y','N') NOT NULL DEFAULT 'N',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_content`
--

LOCK TABLES `mce_content` WRITE;
/*!40000 ALTER TABLE `mce_content` DISABLE KEYS */;
INSERT INTO `mce_content` VALUES (1,0,0,'/','Главная','','','','N','N','N','2016-05-20 19:16:14','0000-00-00 00:00:00'),(2,0,0,'catalog','STORE | BRUSH ','','Catalog','','N','Y','N','2016-05-20 20:52:57','2016-05-21 16:02:11'),(3,0,1,'himselfmakeup','Сам себе визажист','<div id=\"main\" class=\"w-section main\">\r\n<div class=\"w-slider slide\" data-animation=\"cross\" data-duration=\"800\" data-infinite=\"1\" data-delay=\"4000\" data-autoplay=\"1\">\r\n<div class=\"w-slider-mask mask-slide\">\r\n<div class=\"w-slide slide\">\r\n<div class=\"picture-slide makeup-artist-himself\" data-ix=\"foto-slider\">&nbsp;</div>\r\n<h1 class=\"text-head\" data-ix=\"slider-animation-text\">Сам сеБЕ <strong data-new-link=\"true\">визажист</strong></h1>\r\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\r\n<h5 class=\"text-h5\">Двухдневный курс макияжа для себя</h5>\r\n<a class=\"w-button button-line v2\" href=\"#training\">ПОДРОБНЕЕ</a></div>\r\n</div>\r\n</div>\r\n<div class=\"w-slider-arrow-left left-arrow\">&nbsp;</div>\r\n<div class=\"w-slider-arrow-right right-arrow\">&nbsp;</div>\r\n<div class=\"w-slider-nav w-round slide-nav\">&nbsp;</div>\r\n</div>\r\n</div>\r\n<div id=\"training\" class=\"w-section training\">\r\n<div class=\"w-row\">\r\n<div class=\"w-col w-col-6 w-col-stack training-column makeup\">\r\n<div class=\"div-block-content-training\">\r\n<h2 class=\"head\">О КУРСЕ</h2>\r\n<div class=\"div-line\" data-ix=\"line-5\">&nbsp;</div>\r\n<div class=\"paragraph makeup-artist-himself\">\r\n<h2 class=\"quote-teacher v1\">&laquo;На курсе вы последовательно отработаете все этапы макияжа &mdash; от основ и тонирования до декоративной части</h2>\r\n<p class=\"text-paragraph\"><span class=\"text-spain-paragraph\">В первый день подробно продемонстрирую, техники и средства правильного нанесения макияжа, а на второй &mdash; вы будете тренироваться сами, где я помогу индивидуально скорректировать ваши действия&nbsp;<br /></span></p>\r\n<p class=\"text-paragraph visage-intensive\">На курсе &laquo;Сам себе визажист&raquo; учим на практике подчёркивать уникальные достоинства вашего лица, делать дневной и вечерний макияж, экспресс-макияж, который занимает несколько минут</p>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-col w-col-6 w-col-stack training-column makeup\">\r\n<div class=\"div-training-picture makeup-artist-himself foto3\" data-ix=\"picture-up\">&nbsp;</div>\r\n<div class=\"divteacher\">\r\n<div class=\"previews-teacher makeup-artist-himself-name\"><span class=\"teacher\" data-new-link=\"true\"><strong class=\"teacher-name\">НИКА МИШИНА</strong></span> <br />Профессиональный визажист, стилист по причёскам,</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-section professional-courses\">\r\n<div class=\"rectangle vers3\">&nbsp;</div>\r\n<h2 class=\"titlecourse-content makeup2\">СОДЕРЖАНИЕ КУРСА</h2>\r\n<div class=\"w-row\">\r\n<div class=\"w-col w-col-6 w-col-stack column-courses makeup-course-content\">\r\n<div class=\"div-training-picture makeup-artist-himself _2\" data-ix=\"picture-up\">&nbsp;</div>\r\n</div>\r\n<div class=\"w-col w-col-6 w-col-stack column-courses makeup-artist-himself3\">\r\n<div class=\"content makeup3\">\r\n<p class=\"text-rap makeup-artist-himself-content\">НА КУРСЕ ВЫ НАУЧИТЕСЬ ПРАВИЛЬНО РАБОТАТЬ С КОСМЕТИКОЙ И ИНСТРУМЕНТАМИ ДЛЯ МАКИЯЖА, ПОЛУЧИТЕ БАЗОВЫЕ НАВЫКИ ПО УХОДУ ЗА КОЖЕЙ ЛИЦА, СМОЖЕТЕ ПРОАНАЛИЗИРОВАТЬ ФОРМУ ЛИЦА, РАЗБЕРЕТЕСЬ В ЭТАПАХ НАНЕСЕНИЯ МАКИЯЖА И НАУЧИТЕСЬ КОРРЕКТИРОВАТЬ ФОРМУ ЛИЦА, ГУБ И БРОВЕЙ. ДВУХДНЕВНАЯ ПРОГРАММА ПРАКТИКИ ВКЛЮЧАЕТ</p>\r\n<div class=\"list-of-courses content-makeup-artist\">&bull; Методы подбора косметических средств, основы цветоведения и текстур; <br /> &bull; Скульптурирование лица &mdash; способы коррекции форм лица и носа; <br /> &bull; Дневной и вечерний макияж; <br /> &bull; Составление индивидуальной схему вашего макияжа &mdash; правильные брови, четкие стрелки и яркие губы; <br /> &bull; Подбир средств для корректировки недостатков кожи; <br /> &bull; Smokeeyes 3D; <br /> &bull; Работу с инструментами; <br /> &bull; Техники быстрого превращения дневного макияжа в вечерний.</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-section cost-and-schedule\">\r\n<h2 class=\"head cost-and-schedule1 head2\">СТОИМОСТЬ И ПРОГРАММА</h2>\r\n<div class=\"cost-and-schedule-content\">По окончании курса выдается сертификат MECHKOVSKAYA School of beauty опрохождении курса &laquo;Сам себе визажист&raquo;.</div>\r\n<div class=\"w-row row-price\">\r\n<div class=\"w-col w-col-4 w-col-stack columncost\">\r\n<div class=\"cost-and-schedule-table\">\r\n<div class=\"cost-and-schedule-header-text\">Хронометраж курса:</div>\r\n<h2 class=\"head v2 cost-lyrics\">10</h2>\r\n<div class=\"cost-text2\">академических часов <br />(две недели).</div>\r\n</div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-stack\">\r\n<div class=\"cost-and-schedule-table\">\r\n<div class=\"cost-and-schedule-header-text\">График: пнд-пт</div>\r\n<h2 class=\"head v2 cost-lyrics head2\">11:00-16:00</h2>\r\n</div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-stack\">\r\n<div class=\"cost-and-schedule-table\">\r\n<div class=\"cost-and-schedule-header-text\">ГРУППА:</div>\r\n<h2 class=\"head v2 cost-lyrics peoples\">ДО 10 Ч.</h2>\r\n<div class=\"cost-text2\">От 3-х человек работают два преподавателя. От 7-и человек &mdash; <br />три преподавателя&raquo;</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-row row-price2\">\r\n<div class=\"w-col w-col-8 w-col-stack\">\r\n<div class=\"cost-and-schedule-content _2\">На время обучениях школа предоставляет наборы стерильных кистей и профессиональной косметики. Занятия проходят в профессионально-оборудованной студии по адресу варшавское шоссе, 33, станция метро &laquo;Нагатинская&raquo;</div>\r\n<a class=\"w-button reservations cost makeup-artist-himself\" href=\"#\" data-ix=\"button-application-form\">ЗАПИСАТЬСЯ</a></div>\r\n<div class=\"w-col w-col-4 w-col-stack\">\r\n<div class=\"divthe-price-of-the-course\">\r\n<h4 class=\"the-cost-price-of-a-title\">СТОИМОСТЬ:</h4>\r\n<h1 class=\"price-ruble\">10000 <span class=\"ruble\">₽</span></h1>\r\n<div class=\"text-price1\">Курс можно приобрести <br />в подарок</div>\r\n<div class=\"circle-price _2\">&nbsp;</div>\r\n<div class=\"circle-price\">&nbsp;</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-section schedule-selection\">\r\n<h2 class=\"head cost-and-schedule1 head2\">РАСПИСАНИЕ</h2>\r\n<div class=\"w-row row-price\">\r\n<div class=\"w-col w-col-4 w-col-stack columncost\">\r\n<div class=\"cost-and-schedule-table\">\r\n<div class=\"cost-and-schedule-header-text\">ФЕВРАЛЬ</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">15 февраля</strong> <br />17-00</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">27 февраля</strong> <br />17-00</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">30 февраля</strong> <br />17-00</div>\r\n</div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-stack\">\r\n<div class=\"cost-and-schedule-table\">\r\n<div class=\"cost-and-schedule-header-text\">МАРТ</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">10 Март</strong> <br />17-00</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">19 Март</strong> <br />17-00</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">25 Март</strong> <br />17-00</div>\r\n</div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-stack\">\r\n<div class=\"cost-and-schedule-table\">\r\n<div class=\"cost-and-schedule-header-text\">АПРЕЛЬ</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">3 апреля</strong> <br />17-00</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">15 апреля</strong> <br />17-00</div>\r\n<div class=\"schedule-text-con\"><strong data-new-link=\"true\">18 апреля</strong> <br />17-00</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-section portfolio-rate\">\r\n<div class=\"w-row\">\r\n<div class=\"w-col w-col-6\">\r\n<h2 class=\"head-portfolio\">ПОРТФОЛИО</h2>\r\n<div class=\"div-line-2 portline\" data-ix=\"line-7\">&nbsp;</div>\r\n</div>\r\n<div class=\"w-col w-col-6\">\r\n<div class=\"text-portfolio\">Курс вобрал в себя практический опыт работы с сотнями моделями и клиентами MECHKOVSKAYA school of beauty. Ознакомьтесь с работами преподавателей и учеников Школы</div>\r\n</div>\r\n</div>\r\n<div class=\"w-row portfoliosquares\" data-ix=\"89\">\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself1\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"_id\": \"example_bg\",\r\n                \"cdnUrl\": \"https://d3e54v103j8qbb.cloudfront.net/img/example-bg.png\",\r\n                \"fileName\": \"example-bg.png\",\r\n                \"origFileName\": \"example-bg.png\",\r\n                \"width\": 250,\r\n                \"height\": 250,\r\n                \"fileSize\": 3618,\r\n                \"type\": \"image\",\r\n                \"url\": \"https://d3e54v103j8qbb.cloudfront.net/img/example-bg.png\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself2\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/KLTEUSdkRSQ (1).jpg\",\r\n                \"fileName\": \"56827e5e75b86ae462584acd_KLTEUSdkRSQ (1).jpg\",\r\n                \"origFileName\": \"KLTEUSdkRSQ (1).jpg\",\r\n                \"width\": 1280,\r\n                \"height\": 914,\r\n                \"size\": 120358,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself3\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/mNnyhxjYkgQ.jpg\",\r\n                \"fileName\": \"56827eb03a4a2d217fd4c35e_mNnyhxjYkgQ.jpg\",\r\n                \"origFileName\": \"mNnyhxjYkgQ.jpg\",\r\n                \"width\": 768,\r\n                \"height\": 1024,\r\n                \"size\": 66798,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n</div>\r\n<div class=\"w-row portfoliosquares\" data-ix=\"89\">\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself4\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/MPG74uFpTA4.jpg\",\r\n                \"fileName\": \"56827da075b86ae462584a10_MPG74uFpTA4.jpg\",\r\n                \"origFileName\": \"MPG74uFpTA4.jpg\",\r\n                \"width\": 741,\r\n                \"height\": 960,\r\n                \"size\": 154685,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself5\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/uVyvNIdUB10 (1).jpg\",\r\n                \"fileName\": \"56827f3074cf1bc76bab9f89_uVyvNIdUB10 (1).jpg\",\r\n                \"origFileName\": \"uVyvNIdUB10 (1).jpg\",\r\n                \"width\": 768,\r\n                \"height\": 1024,\r\n                \"size\": 117149,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself6\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/rJ3nwIRH_Mg.jpg\",\r\n                \"fileName\": \"56827f5374cf1bc76bab9f8d_rJ3nwIRH_Mg.jpg\",\r\n                \"origFileName\": \"rJ3nwIRH_Mg.jpg\",\r\n                \"width\": 1280,\r\n                \"height\": 904,\r\n                \"size\": 116152,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n</div>\r\n<div class=\"w-row portfoliosquares\" data-ix=\"89\">\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself7\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/Uaf6QZyItW0.jpg\",\r\n                \"fileName\": \"56827f7ad19a7ec26b8d05f8_Uaf6QZyItW0.jpg\",\r\n                \"origFileName\": \"Uaf6QZyItW0.jpg\",\r\n                \"width\": 739,\r\n                \"height\": 1024,\r\n                \"size\": 141153,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself8\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"id\": \"569cfd52ff97a81e1bec05b9\",\r\n                \"url\": \"images/1IMG_6050.jpg\",\r\n                \"fileName\": \"example-bg.png\",\r\n                \"origFileName\": \"example-bg.png\",\r\n                \"width\": 250,\r\n                \"height\": 250,\r\n                \"size\": 3618,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\r\n<div class=\"div-block-portfolio makeup-artist-himself9\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\r\n<script type=\"application/json\">// <![CDATA[\r\n{ \"items\": [{\r\n                \"url\": \"images/brush&blow_2.9_6560.jpg\",\r\n                \"fileName\": \"569d03ddca4d9823078bc4f4_brush&blow_2.9_6560.jpg\",\r\n                \"origFileName\": \"brush&blow_2.9_6560.jpg\",\r\n                \"width\": 1750,\r\n                \"height\": 1167,\r\n                \"size\": 322285,\r\n                \"type\": \"image\"\r\n              }] }\r\n// ]]></script>\r\n</a></div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-section training-for-yourself visage-intensive\">\r\n<h2 class=\"titlemakeup-artist-himself\">Что даёт курс &laquo;Сам себе визажист&raquo;</h2>\r\n<div class=\"w-row row-training curs\">\r\n<div class=\"w-col w-col-6 w-col-stack training-for-yourself-column\">\r\n<div class=\"content v2 block\">\r\n<p class=\"text-rap makeup-artist-himself-content _2\">Искусство макияжа &mdash; возможность постоянно развивать свой стиль, технику, навыки. Настоящий успех приходит тогда, когда вы открыты новому и видите мир вокруг себя.</p>\r\n<div class=\"list-of-courses\"><strong data-new-link=\"true\">После курса вы на практике получите следующие навыки и преимущества:</strong> <br />&bull; Нивелировать недостатки лица с помощью макияжа <br />&bull; Приобретете уверенные отточенные движения рук с ясным пониманием техник; <br />&bull; Приближать особенности своего к идеальному; <br />&bull; Экономить время и средства <br />&bull; Новые образы себя, которые доставят Вам невероятное удовольствие) <br />Также с курсом приобретают: Профессиональный курс &laquo;Визаж-интенсив&raquo; и курс &laquo;Дизайн бровей&raquo;</div>\r\n<a class=\"w-button button-line pink makeup-artist-himself _2\" href=\"#\" data-ix=\"button-application-form\">ЗАПИСАТЬСЯ НА КУРС</a></div>\r\n</div>\r\n<div class=\"w-col w-col-6 w-col-stack training-for-yourself-column visage-intensive\">\r\n<div class=\"div-training-picture pic2\" data-ix=\"picture-up\">&nbsp;</div>\r\n</div>\r\n</div>\r\n<div class=\"rectangle makeup4\">&nbsp;</div>\r\n</div>\r\n<div class=\"w-section reviews\">\r\n<h2 class=\"head reviews-text\">ОТЗЫВЫ</h2>\r\n<div class=\"div-line v2\" data-ix=\"line-6\">&nbsp;</div>\r\n<div class=\"reviews-div\">\r\n<div class=\"w-slider slider-comments\" data-animation=\"cross\" data-duration=\"500\" data-infinite=\"1\" data-delay=\"5000\" data-autoplay=\"1\" data-nav-spacing=\"5\" data-easing=\"ease-in-out-quart\">\r\n<div class=\"w-slider-mask mask-comments\">\r\n<div class=\"w-slide slide-comments\">\r\n<div class=\"w-row row-comments\">\r\n<div class=\"w-col w-col-4 w-col-stack\"><img class=\"imagereviews foto2 _1\" src=\"/site/mechkovska/assets/images/re_w.jpg\" alt=\"\" /></div>\r\n<div class=\"w-col w-col-8 w-col-stack column-comments\">\r\n<div class=\"text-block-reviews\">&laquo;Не устану говорить про себя спасибо моей любимой @mechkovskaya_school&nbsp;за свой навык, умением быть настоящей девушкой и делать еще красивее других дам я прошла два курса, просто думаю, что каждая девушка должна уметь правильно пользоваться косметикой. Именно здесь открылись все тонкости в работе с лицом, расставление правильных акцентов и работа с разными продуктами, кистями, текстурами... Ну и не могу не сказать про абсолютно дружелюбную атмосферу и мой любимейший преподавательский состав ой люблю не могу всем сердцем! P.S. Девочки, а курс \"сам себе визажист\" в воообще уж точно маст хэв для каждой девушки&raquo;</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n<div class=\"w-slider-arrow-left reviews-arrow\">\r\n<div class=\"w-icon-slider-left\">&nbsp;</div>\r\n</div>\r\n<div class=\"w-slider-arrow-right reviews-arrow right\">\r\n<div class=\"w-icon-slider-right\">&nbsp;</div>\r\n</div>\r\n<div class=\"w-slider-nav w-slider-nav-invert w-round circlereviews\">&nbsp;</div>\r\n</div>\r\n</div>\r\n</div>','','','N','Y','N','2016-06-01 00:44:51','0000-00-00 00:00:00'),(4,0,0,'makeup','Makeup Design','<div id=\"main\" class=\"w-section main\">\n<div class=\"w-slider slide\" data-animation=\"cross\" data-duration=\"800\" data-infinite=\"1\" data-delay=\"4000\" data-autoplay=\"1\">\n<div class=\"w-slider-mask mask-slide\">\n<div class=\"w-slide slide\">\n<h1 class=\"text-head\" data-ix=\"slider-animation-text\">Makeup <br /><strong>Design</strong></h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\n<h5 class=\"text-h5\">вы научитесь работать с формой, цветом, текстурами.</h5>\n<a class=\"w-button button-line\" href=\"#training\">ПОДРОБНЕЕ</a></div>\n<div class=\"picture-slide makeupdesigner\" data-ix=\"foto-slider\">&nbsp;</div>\n</div>\n</div>\n<div class=\"w-slider-arrow-left left-arrow\">&nbsp;</div>\n<div class=\"w-slider-arrow-right right-arrow\">&nbsp;</div>\n<div class=\"w-slider-nav w-round slide-nav\">&nbsp;</div>\n</div>\n</div>\n<div id=\"training\" class=\"w-section training intensity\">\n<div class=\"w-row\">\n<div class=\"w-col w-col-6 w-col-stack training-column design\">\n<div class=\"div-block-content-training\">\n<h2 class=\"head\">О КУРСЕ</h2>\n<div class=\"div-line\" data-ix=\"line-5\">&nbsp;</div>\n<div class=\"paragraph visage-intensive\">\n<p class=\"text-paragraph\"><span class=\"text-spain-paragraph\">Курс для тех, кто хочет овладеть искусством макияжа, научиться создавать ART и FASHION образы, FACECHARTS при помощи косметических текстур.<br /></span></p>\n<p class=\"text-paragraph visage-intensive\">Программа курса насыщена практикой и актуальными знаниями для новичков &mdash; модные тренды, коммерческий макияж, макияж для фото/видео съемок, занятие по прическам. <br />Так же мы поможем раскрыть ваши &nbsp;художественные навыки, без которых невозможно создание идеального макияжа. На занятиях по FACECHARTS вы научитесь работать с формой, цветом, текстурами. <br /> <br /><strong data-new-link=\"true\">Курс ведут два преподавателя Елена Налобина и Женя Шубина</strong></p>\n</div>\n</div>\n</div>\n<div class=\"w-col w-col-6 w-col-stack training-column design\">\n<div class=\"w-row\">\n<div class=\"w-col w-col-6\">\n<div class=\"foto-teacher-1\">&nbsp;</div>\n<div class=\"divteacher col\">\n<div class=\"previews-teacher intensity\"><span class=\"teacher\" data-new-link=\"true\"><strong>ЖЕНЯ ШУБИНА</strong></span> <br />Преподаватель</div>\n<a class=\"w-button button-line v3 visage-intensive\" href=\"/admin/command/evgeny-shubin.html\">ПОДРОБНЕЕ</a></div>\n</div>\n<div class=\"w-col w-col-6 design-coll\">\n<div class=\"foto-teacher-2\">&nbsp;</div>\n<div class=\"divteacher col\">\n<div class=\"previews-teacher intensity\"><span class=\"teacher\" data-new-link=\"true\"><strong class=\"teacher-name\">ЕЛЕНА НАЛОБИНА</strong></span> <br />Визажист, преподаватель&nbsp;</div>\n<a class=\"w-button button-line v3 visage-intensive\" href=\"/admin/command/elena-nalobina.html\">ПОДРОБНЕЕ</a></div>\n</div>\n</div>\n</div>\n</div>\n</div>\n<div class=\"w-section professional-courses design5\">\n<h2 class=\"titlecourse-content intensity\">СОДЕРЖАНИЕ КУРСА</h2>\n<div class=\"w-row\">\n<div class=\"w-col w-col-6 w-col-stack column-courses visae-intensive\">\n<div class=\"div-training-picture design1\" data-ix=\"picture-up\">&nbsp;</div>\n</div>\n<div class=\"w-col w-col-6 w-col-stack column-courses design3\">\n<div class=\"content intensity1 design\">\n<p class=\"text-rap\">Во время обучения проходит три профессиональных фотосессии, благодаря чему выпускники Школы нарабатывают профессиональное портфолио.</p>\n<div class=\"list-of-courses v2\">&bull; Цветометрия - теория цвета в макияже; <br />&bull; Скульптурирование лица (коррекция формы лица и носа); <br />&bull; Дизайн и оформление бровей; <br />&bull; NUDE макияж; <br />&bull; Все виды вечернего макияжа; <br />&bull; Wedding makeup; <br />&bull; Лифтинг-макияж; <br />Работа с кремовыми текстурами и пигментами, основы карандашной техники; <br />&bull; Основы причесок. Различные техники накручивания локонов и создание причесок на базе локонов; <br />&bull; Занятие с психологом.</div>\n<a class=\"w-button button-line v3 design\" href=\"#portfolio\" data-ix=\"button-application-form\">образы, созданные в Школе</a></div>\n</div>\n</div>\n<div class=\"rectangle intensity\">&nbsp;</div>\n</div>\n<div class=\"w-section cost-and-schedule\">\n<h2 class=\"head cost-and-schedule1 head2\">СТОИМОСТЬ И ПРОГРАММА</h2>\n<div class=\"cost-and-schedule-content\">По окончании курса выдается сертификат MECHKOVSKAYA School of beauty о <br />прохождении курса &laquo;Голивудские локоны&raquo;.</div>\n<div class=\"w-row row-price\">\n<div class=\"w-col w-col-4 w-col-stack columncost\">\n<div class=\"cost-and-schedule-table\">\n<div class=\"cost-and-schedule-header-text\">Хронометраж курса:</div>\n<h2 class=\"head v2 cost-lyrics _4\">3 часа</h2>\n</div>\n</div>\n<div class=\"w-col w-col-4 w-col-stack\">\n<div class=\"cost-and-schedule-table\">\n<div class=\"cost-and-schedule-header-text\">График:&nbsp;</div>\n<div class=\"cost-text2 design2\">Занятия три раза в <br />неделю с 18:30</div>\n</div>\n</div>\n<div class=\"w-col w-col-4 w-col-stack\">\n<div class=\"cost-and-schedule-table\">\n<div class=\"cost-and-schedule-header-text\">ГРУППА:</div>\n<h2 class=\"head v2 cost-lyrics\">3-7 <br />человек</h2>\n</div>\n</div>\n</div>\n<div class=\"w-row row-price2\">\n<div class=\"w-col w-col-8 w-col-stack\">\n<div class=\"cost-and-schedule-content _2\">Все необходимые материалы &nbsp;на время обучения &nbsp;предоставляет Школа. &nbsp; <br />Мы используем лучшую косметику мировых брендов, профессиональные наборы кистей. <br />Для учеников проявивших себя, Есть возможность дальнейшего трудоустройства (как фриланс, так и на постоянной основе). По окончанию курса вы получаете диплом &nbsp;&laquo;School of Beauty \"Mechkovskaya&raquo;. Ученики, прошедшие обучение на базовом курсе по визажу в нашей школе, автоматически получают скидку 20% на все курсы повышения квалификации (&laquo;Дизайн бровей&raquo;, &laquo;Свадебный стилист&raquo;, &laquo;Дизайн волос&raquo;). Наши ученики имеют возможность принимать своих клиентов в школе. Косметика предоставляется в аренду.</div>\n<a class=\"w-button reservations cost makeup-artist-himself\" href=\"#\" data-ix=\"button-application-form\">ЗАПИСАТЬСЯ</a></div>\n<div class=\"w-col w-col-4 w-col-stack\">\n<div class=\"divthe-price-of-the-course\">\n<h4 class=\"the-cost-price-of-a-title\">СТОИМОСТЬ:</h4>\n<h1 class=\"price-ruble\">50000 <span class=\"ruble\">₽</span></h1>\n<div class=\"text-price1\">Возможна рассрочка на <br />время обучения</div>\n<div class=\"circle-price _2\">&nbsp;</div>\n<div class=\"circle-price\">&nbsp;</div>\n</div>\n</div>\n</div>\n</div>\n<div class=\"w-section schedule-selection\">\n<h2 class=\"head cost-and-schedule1 head2\">РАСПИСАНИЕ</h2>\n<div class=\"w-row row-price\">\n<div class=\"w-col w-col-4 w-col-stack columncost\">\n<div class=\"cost-and-schedule-table\">\n<div class=\"cost-and-schedule-header-text\">май</div>\n<div class=\"schedule-text-con colonna\"><strong data-new-link=\"true\">с 23 мая</strong> <br />в 18-30</div>\n</div>\n</div>\n<div class=\"w-col w-col-4 w-col-stack\">\n<div class=\"cost-and-schedule-table\">\n<div class=\"cost-and-schedule-header-text\">август</div>\n<div class=\"schedule-text-con colonna\"><strong data-new-link=\"true\">29 августа</strong> <br />в 18-30</div>\n</div>\n</div>\n<div class=\"w-col w-col-4 w-col-stack\">\n<div class=\"cost-and-schedule-table\">\n<div class=\"cost-and-schedule-header-text\">январь</div>\n<div class=\"schedule-text-con colonna\"><strong data-new-link=\"true\">9 января 2017 года</strong> <br />в 18-30</div>\n</div>\n</div>\n</div>\n</div>\n<div id=\"portfolio\" class=\"w-section portfolio-rate\">\n<div class=\"w-row\">\n<div class=\"w-col w-col-6\">\n<h2 class=\"head-portfolio\">ПОРТФОЛИО</h2>\n<div class=\"div-line-2 portline\" data-ix=\"line-7\">&nbsp;</div>\n</div>\n<div class=\"w-col w-col-6\">\n<div class=\"text-portfolio\">КУРС ВОБРАЛ В СЕБЯ ПРАКТИЧЕСКИЙ ОПЫТ РАБОТЫ С СОТНЯМИ КЛИЕНТОВ MECHKOVSKAYA SCHOOL OF BEAUTY. Ознакомьтесь с работами преподавателей и учеников школы</div>\n</div>\n</div>\n<div class=\"w-row portfoliosquares\" data-ix=\"89\">\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d117399aaa616f50885809\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d117399aaa616f50885809_1.jpg\",\n                \"fileName\": \"1_1.jpg\",\n                \"origFileName\": \"1_1.jpg\",\n                \"width\": 1679,\n                \"height\": 2350,\n                \"fileSize\": 492005,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/1_1.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design2\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d1178e898556d1598b58ed\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d1178e898556d1598b58ed_8.jpg\",\n                \"fileName\": \"8.jpg\",\n                \"origFileName\": \"8.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 178788,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/8.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design3\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d117cf6f6f9c9848a884d2\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d117cf6f6f9c9848a884d2_5.jpg\",\n                \"fileName\": \"5.jpg\",\n                \"origFileName\": \"5.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 226474,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/5.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n</div>\n<div class=\"w-row portfoliosquares\" data-ix=\"89\">\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design4\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"/admin/images/3.jpg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d1189eb5a882d659432b8b\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d1189eb5a882d659432b8b_3.jpg\",\n                \"fileName\": \"3.jpg\",\n                \"origFileName\": \"3.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 276685,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/3.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design5\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d1197c033166a1488a20c4\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d1197c033166a1488a20c4_10.jpg\",\n                \"fileName\": \"10.jpg\",\n                \"origFileName\": \"10.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 203048,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/10.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design6\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d11a1c9aaa616f50885a32\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d11a1c9aaa616f50885a32_12.jpg\",\n                \"fileName\": \"12.jpg\",\n                \"origFileName\": \"12.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 203268,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/12.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n</div>\n<div class=\"w-row portfoliosquares\" data-ix=\"89\">\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design7\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d11abd6f6f9c9848a8865b\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d11abd6f6f9c9848a8865b_13.jpg\",\n                \"fileName\": \"13.jpg\",\n                \"origFileName\": \"13.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 166766,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/13.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design8\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d11b07898556d1598b5e3e\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d11b07898556d1598b5e3e_7.jpg\",\n                \"fileName\": \"7.jpg\",\n                \"origFileName\": \"7.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 186913,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/7.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n<div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n<div class=\"div-block-portfolio makeup-design9\"><a class=\"w-lightbox w-inline-block\" href=\"#\"><img class=\"image-box\" src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" alt=\"\" />\n<script type=\"application/json\">\n{ \"items\": [{\n                \"_id\": \"56d11b5e9aaa616f50885a62\",\n                \"cdnUrl\": \"https://daks2k3a4ib2z.cloudfront.net/5657f39678fa83e26145ff8b/56d11b5e9aaa616f50885a62_4.jpg\",\n                \"fileName\": \"4.jpg\",\n                \"origFileName\": \"4.jpg\",\n                \"width\": 1200,\n                \"height\": 1680,\n                \"fileSize\": 219342,\n                \"type\": \"image\",\n                \"url\": \"/site/mechkovska/assets/images/4.jpg\"\n              }] }\n</script>\n</a></div>\n</div>\n</div>\n</div>\n<div class=\"w-section advantages-of-courses intensive6 _47\">\n<h2 class=\"head cost-and-schedule1 v3\">Преимущества курсов в <br />MECHKOVSKAYA school <br />of beauty</h2>\n<div class=\"w-row rowadvantages visage-intens\">\n<div class=\"w-col w-col-6\">\n<div class=\"div-the-advantage-of-courses _45\" data-ix=\"price\">\n<div class=\"div-icon _2\">&nbsp;</div>\n<div class=\"textadvantage-of-courses-content\"><span class=\"headadvantage-of-courses\">Кисти и косметика</span> <br /> <br />Используют косметические средства, которые предоставляет Школа &mdash; профессиональные наборы кистей и косметику таких брендов как&raquo; добавляем &laquo;Urban decay, Inglot, Make up for ever, Mac, Smash Box, Dolce&amp;Gabbana, Dior, Est&eacute;e Lauder</div>\n</div>\n</div>\n<div class=\"w-col w-col-6\">\n<div class=\"div-the-advantage-of-courses _45\" data-ix=\"price-2\">\n<div class=\"div-icon _4\">&nbsp;</div>\n<div class=\"textadvantage-of-courses-content _2 _47\"><span class=\"headadvantage-of-courses\">Участвовать в модных событиях и <br />получить новый уровень свободы</span> <br />Школа Красоты Mechkovskaya выступает бьюти-партнером известных, значимых, мероприятий в России и Европе. Работаем с глянцевыми журналами, телевидением и федеральными компаниями. Ученики получают возможность получить бесплатную практику на материалах школы &mdash; участвовать в бьюти-подготовке к модным событиям.</div>\n</div>\n</div>\n</div>\n<div class=\"w-row row2advantage-of-courses visage-intensiv\">\n<div class=\"w-col w-col-6\">\n<div class=\"div-the-advantage-of-courses v1 _45\" data-ix=\"price\">\n<div class=\"div-icon _5\">&nbsp;</div>\n<div class=\"textadvantage-of-courses-content\"><span class=\"headadvantage-of-courses\">Трудоустройство<br /></span> <br />Найти себя, используя потрясающий шанс с возможностью дальнейшего трудоустройства и продвижения в качестве фриланс-мастеров и постоянных специалистов. Лучших выпускников приглашаем работать</div>\n</div>\n</div>\n<div class=\"w-col w-col-6\">\n<div class=\"div-the-advantage-of-courses c1 _45\" data-ix=\"price-2\">\n<div class=\"div-the-advantage-of-courses map _45\" data-ix=\"price-3\">\n<div class=\"div-icon map\">&nbsp;</div>\n<div class=\"advantages-map-text\">Занятия проходят в профессионально-оборудованной студии по адресу Варшавское шоссе, 33, станция метро &laquo;Нагатинская&raquo; <br /><a class=\"_55\" href=\"#\">2</a></div>\n<a class=\"w-button button-line v3 button2 _47\" href=\"#\" data-ix=\"button-application-form\">УЗНАТЬ О ДРУГИх КУРСАх</a></div>\n</div>\n</div>\n</div>\n</div>\n<div class=\"w-section reviews\">\n<h2 class=\"head reviews-text\">ОТЗЫВЫ</h2>\n<div class=\"div-line v2\" data-ix=\"line-6\">&nbsp;</div>\n<div class=\"reviews-div\">\n<div class=\"w-slider slider-comments\" data-animation=\"cross\" data-duration=\"500\" data-infinite=\"1\" data-delay=\"5000\" data-autoplay=\"1\" data-nav-spacing=\"5\" data-easing=\"ease-in-out-quart\">\n<div class=\"w-slider-mask mask-comments\">\n<div class=\"w-slide slide-comments\">\n<div class=\"w-row row-comments\">\n<div class=\"w-col w-col-4 w-col-stack\"><img class=\"imagereviews foto2 _1\" src=\"/site/mechkovska/assets/images/re2_w.jpg\" alt=\"\" /></div>\n<div class=\"w-col w-col-8 w-col-stack column-comments\">\n<div class=\"text-block-reviews _4\">&laquo;Ни секунды не жалею, что пошла именно в @mechkovskaya_school С каждым разом нравится и получается лучше. Спасибо моему преподавателю @elena_nalobina. My beautiful mdoel today @akopyan_kris&raquo;</div>\n</div>\n</div>\n</div>\n</div>\n<div class=\"w-slider-arrow-left reviews-arrow\">\n<div class=\"w-icon-slider-left\">&nbsp;</div>\n</div>\n<div class=\"w-slider-arrow-right reviews-arrow right\">\n<div class=\"w-icon-slider-right\">&nbsp;</div>\n</div>\n<div class=\"w-slider-nav w-slider-nav-invert w-round circlereviews\">&nbsp;</div>\n</div>\n</div>\n</div>','Content','','N','Y','N','2016-06-01 21:07:46','2016-06-01 23:23:17'),(5,0,3,'eyebrows','ДИЗАЙН БРОВЕЙ','<div id=\"main\" class=\"w-section main\">\n    <div data-animation=\"cross\" data-duration=\"800\" data-infinite=\"1\" data-delay=\"4000\" data-autoplay=\"1\" class=\"w-slider slide\">\n      <div class=\"w-slider-mask mask-slide\">\n        <div class=\"w-slide slide\">\n          <div data-ix=\"foto-slider\" class=\"picture-slide eyebrows\"></div>\n          <h1 data-ix=\"slider-animation-text\" class=\"text-head\">ДИЗАЙН <strong data-new-link=\"true\">БРОВЕЙ</strong></h1>\n          <div data-ix=\"slider-animation-text\" class=\"text-block-2\">\n            <h5 class=\"text-h5\">Учим и помогаем овладевать секретами идеального взгляда</h5><a href=\"#training\" class=\"w-button button-line v2\">ПОДРОБНЕЕ</a>\n          </div>\n        </div>\n      </div>\n      <div class=\"w-slider-arrow-left left-arrow\">\n        <div class=\"w-icon-slider-left\"></div>\n      </div>\n      <div class=\"w-slider-arrow-right right-arrow\">\n        <div class=\"w-icon-slider-right\"></div>\n      </div>\n      <div class=\"w-slider-nav w-round slide-nav\"></div>\n    </div>\n    <a href=\"http://www.mechkovskayaschool.ru/\" data-ix=\"logo\" class=\"w-inline-block logo\"></a>\n  </div>\n  <div id=\"training\" class=\"w-section training\">\n    <div class=\"w-row\">\n      <div class=\"w-col w-col-6 w-col-stack training-column makeup\">\n        <div class=\"div-block-content-training\">\n          <h2 class=\"head\">О КУРСЕ</h2>\n          <div data-ix=\"line-5\" class=\"div-line\"></div>\n          <div class=\"paragraph eyebrows-block-content\">\n            <h2 class=\"quote-teacher v1\">« — С помощью курса дизайна бровей учим создавать гипнотическое обаяние взгляда — делать его наивным или хищным, акцентировать внимание на больших глазах или подчеркнут изящность разреза.»</h2>\n            <p class=\"text-paragraph\"><span class=\"text-spain-paragraph\">Учим и помогаем овладевать секретами идеального взгляда с помощью авторских техник дизайна бровей и ресниц. Авторы курса — известные стилисты и бровисты России и Европы. Все процедуры по окрашиванию и оформлению бровей в Школе проводятся с использованием легендарных средств марки Igora и Brow Henna, которым доверяют профессионалы.<br></span>\n            </p><a href=\"#\" data-ix=\"button-application-form\" class=\"w-button button-line pink eyebrows-but\">Узнать подробнее</a>\n          </div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6 w-col-stack training-column makeup\">\n        <div data-ix=\"picture-up\" class=\"div-training-picture makeup-artist-himself eyebrows-div\"></div>\n        <div class=\"divteacher\">\n          <div class=\"previews-teacher makeup-artist-himself-name\"><span class=\"teacher\" data-new-link=\"true\"><strong class=\"teacher-name\">НАДЕЖДА РАМЕНСКАЯ</strong></span>\n            <br>\n            <br>Преподаватель курса <strong data-new-link=\"true\">«Сам себе визажист». </strong>\n            <br>Преподаватель курса <strong data-new-link=\"true\">«Дизайн бровей».</strong>\n          </div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section training-for-yourself visage-intensive\">\n    <h2 class=\"titleeyebrows\">СОДЕРЖАНИЕ КУРСА</h2>\n    <div class=\"w-row row-training curs\">\n      <div class=\"w-col w-col-6 w-col-stack training-for-yourself-column eyebrows4\">\n        <div class=\"content v2 block eyebrows\">\n          <p class=\"text-rap eyebrows-block\">Учим подбирать идеальную форму бровей с учетом овала лица, ширины переносицы и посадки глаз, восстанавливать брови,делать их более густыми и широкими.</p>\n          <div class=\"list-of-courses eyebrows-list\"><strong data-new-link=\"true\">День первый:</strong>\n            <br>• Рабочее место мастера. Инструменты и их обработка перед использованием.\n            <br>• Анализ анатомии лица.\n            <br>• Правильное построение формы бровей с учетом овала лица и модных тенденций.\n            <br>• Изучение средств для окрашивания и коррекции бровей.\n            <br>• Коррекция и окрашивание бровей с использованием различных красителей (краска, хна). Техника Бровей \"ОМБРЕ\". Объемное\n            <br>окрашивание.\n            <br>\n            <br data-new-link=\"true\"><strong>День второй:</strong>\n            <br>• Работа с клиентом\n            <br>(отработка навыков и общения)\n            <br>• Работа с трафаретами\n            <br>• Практика — отработка на моделях с пошаговым повторением пройденного материала.</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6 w-col-stack training-for-yourself-column eyebrows-colon\"><a href=\"#\" data-ix=\"button-application-form\" class=\"w-button button-line v3 eyebrows\">Записаться на пробное занятие</a>\n        <div data-ix=\"picture-up\" class=\"div-training-picture pic3 eyebrows\"></div>\n      </div>\n    </div>\n    <div class=\"rectangle makeup4 eyebrows\"></div>\n  </div>\n  <div class=\"w-section cost-and-schedule\">\n    <h2 class=\"head cost-and-schedule1 head2\">СТОИМОСТЬ И ПРОГРАММА</h2>\n    <div class=\"cost-and-schedule-content\">По окончании курса выдается сертификат MECHKOVSKAYA School of beauty и присваивается звание специалиста по оформлению бровей, мастера-бровиста. Лучших трудоустраиваем</div>\n    <div class=\"w-row row-price\">\n      <div class=\"w-col w-col-4 w-col-stack columncost\">\n        <div class=\"cost-and-schedule-table\">\n          <div class=\"cost-and-schedule-header-text\">Хронометраж курса:</div>\n          <h2 class=\"head v2 cost-lyrics\">2 дня</h2>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-stack\">\n        <div class=\"cost-and-schedule-table\">\n          <div class=\"cost-and-schedule-header-text\">График: пнд-пт</div>\n          <h2 class=\"head v2 cost-lyrics head2\">11:00-16:00</h2>\n          <div class=\"cost-text2\">обед с 13:00 до 14:00\n            <br>часов</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-stack\">\n        <div class=\"cost-and-schedule-table\">\n          <div class=\"cost-and-schedule-header-text\">ГРУППА:</div>\n          <h2 class=\"head v2 cost-lyrics peoples\">ДО 10 Ч.</h2>\n        </div>\n      </div>\n    </div>\n    <div class=\"w-row row-price2\">\n      <div class=\"w-col w-col-8 w-col-stack\">\n        <div class=\"cost-and-schedule-content _2\">Занятия проходят в профессионально-оборудованной студии по адресу варшавское шоссе, 33, станция метро «Нагатинская»</div><a href=\"#\" data-ix=\"button-application-form\" class=\"w-button reservations cost makeup-artist-himself\">ЗАПИСАТЬСЯ</a>\n      </div>\n      <div class=\"w-col w-col-4 w-col-stack\">\n        <div class=\"divthe-price-of-the-course\">\n          <h4 class=\"the-cost-price-of-a-title\">СТОИМОСТЬ:</h4>\n          <h1 class=\"price-ruble\">6000 <span class=\"ruble\">₽</span></h1>\n          <div class=\"text-price1\">возможна рассрочка или скидка при единовременной оплате</div>\n          <div class=\"circle-price _2\"></div>\n          <div class=\"circle-price\"></div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section portfolio-rate\">\n    <div class=\"w-row\">\n      <div class=\"w-col w-col-6\">\n        <h2 class=\"head-portfolio\">ПОРТФОЛИО</h2>\n        <div data-ix=\"line-7\" class=\"div-line-2 portline\"></div>\n      </div>\n      <div class=\"w-col w-col-6\">\n        <div class=\"text-portfolio\">Курс вобрал в себя практический опыт работы с сотнями моделями и клиентами MECHKOVSKAYA school of beauty.Помимо увлекательной теоретической части, большую часть занимает практическая программа с отработкой на моделях. Ознакомьтесь с работами преподавателей и учеников Школы</div>\n      </div>\n    </div>\n    <div data-ix=\"89\" class=\"w-row portfoliosquares\">\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56a15874634478fc046eca73\",\n                \"url\": \"images/9V4_acAJe5Q.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows2\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"images/cifVBNHVodY.jpg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56af0a742c93fd35043756b4\",\n                \"url\": \"images/4fJn7EON_7g.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows3\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"images/g411cCXDw2I.jpg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56a158fcb21a6b7e5d376c33\",\n                \"url\": \"images/g411cCXDw2I.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n    </div>\n    <div data-ix=\"89\" class=\"w-row portfoliosquares\">\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio imageeyebrows4\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56af0a96730da2df6d3621f2\",\n                \"url\": \"images/X5wO2-sW3bY.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows5\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/dyRsPHhov3U.jpg\",\n                \"fileName\": \"56a159a162a786795d9a5605_dyRsPHhov3U.jpg\",\n                \"origFileName\": \"dyRsPHhov3U.jpg\",\n                \"width\": 533,\n                \"height\": 801,\n                \"size\": 86175,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows6\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56af0aca5b54d73e04b28789\",\n                \"url\": \"images/DxgnXsspM8g.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n    </div>\n    <div data-ix=\"89\" class=\"w-row portfoliosquares\">\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows7\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/8Daxu3aKmM8.jpg\",\n                \"fileName\": \"56a1556262a786795d9a51da_8Daxu3aKmM8.jpg\",\n                \"origFileName\": \"8Daxu3aKmM8.jpg\",\n                \"width\": 602,\n                \"height\": 845,\n                \"size\": 80928,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows8\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/RjMmpDIrsvE.jpg\",\n                \"fileName\": \"56a15a96634478fc046ecbba_RjMmpDIrsvE.jpg\",\n                \"origFileName\": \"RjMmpDIrsvE.jpg\",\n                \"width\": 640,\n                \"height\": 640,\n                \"size\": 47500,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio image-eyebrows9\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/brush&blow_2.jpg\",\n                \"fileName\": \"56a15bb5634478fc046ecbca_brush&blow_2.jpg\",\n                \"origFileName\": \"brush&blow_2.jpg\",\n                \"width\": 1000,\n                \"height\": 667,\n                \"size\": 97841,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section professional-courses\">\n    <div class=\"rectangle vers3 eyebrows-rec\"></div>\n    <h2 class=\"titlecourse-content eyebrows-title\">ЧТО ДАЕТ КУРС ДИЗАЙН БРОВЕЙ</h2>\n    <div class=\"w-row\">\n      <div class=\"w-col w-col-6 w-col-stack column-courses makeup-course-content\">\n        <div data-ix=\"picture-up\" class=\"div-training-picture eyebrows-pick2 div\"></div>\n        <div class=\"course-content\">Также с курсом приобретают: Профессиональный курс <strong data-new-link=\"true\">«Визаж-интенсив» и курс «Сам себе визажист»</strong>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6 w-col-stack column-courses eyebrows-colonna _2\">\n        <div class=\"content eyebrowscontent\">\n          <p class=\"text-rap eyebrows-text-rap\">После курса вы получите следующие навыки</p>\n          <div class=\"list-of-courses eyebrows-list2\">• Создавать архитектуру бровей\n            <br>• Работать с симметрией\n            <br>• Нивелировать недостатки лица с помощью формы бровей\n            <br>• Экономить время и средства\n            <br>• Получите собственное профессиональное портфолио\n            <br>• Психологии общения с клиентом\n            <br>• Умение работать в команде, с визажистами и организаторами, редакторами журналов, проф моделями, фотографами и видео\n            <br>режиссерам.\n            <br>• Получите возможность участвовать в модных событиях.\n            <br>• Персональный промоушн. Авторы включили в курс занятия с психологом, чтобы помочь выпускнику реализовать свой\n            <br>творческий потенциал в коммерческий проект — научиться продвигать себя как специалиста.</div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section reviews\">\n    <h2 class=\"head reviews-text\">ОТЗЫВЫ</h2>\n    <div data-ix=\"line-6\" class=\"div-line v2\"></div>\n    <div class=\"reviews-div\">\n      <div data-animation=\"cross\" data-duration=\"500\" data-infinite=\"1\" data-delay=\"5000\" data-autoplay=\"1\" data-nav-spacing=\"5\" data-easing=\"ease-in-out-quart\" class=\"w-slider slider-comments\">\n        <div class=\"w-slider-mask mask-comments\">\n          <div class=\"w-slide slide-comments\">\n            <div class=\"w-row row-comments\">\n              <div class=\"w-col w-col-4 w-col-stack\"><img src=\"images/rew2.jpg\" class=\"imagereviews foto2 _1\">\n              </div>\n              <div class=\"w-col w-col-8 w-col-stack column-comments\">\n                <div class=\"text-block-reviews\">«Проходила курс по оформлению бровей) Всё очень понравилось: в самом центре так уютно — чай, кофе, печеньки. Как дома. Курс проводила Надежда — отдельное тебе спасибо за увлекательную информацию. Теперь буду раздавать всем красивые бровки. Большой плюс — на время обучения предоставляют раздаточные материалы и стерильные инструменты. Жаль, что время на курсе пролетает быстро. Рекомендую всем желающим!» Осипенко</div>\n              </div>\n            </div>\n          </div>\n        </div>\n        <div class=\"w-slider-arrow-left reviews-arrow\">\n          <div class=\"w-icon-slider-left\"></div>\n        </div>\n        <div class=\"w-slider-arrow-right reviews-arrow right\">\n          <div class=\"w-icon-slider-right\"></div>\n        </div>\n        <div class=\"w-slider-nav w-slider-nav-invert w-round circlereviews\"></div>\n      </div>\n    </div>\n  </div>','Content','','N','Y','N','2016-06-02 20:30:13','2016-06-02 20:34:06'),(6,0,5,'intensive','Visage  Intensive','<div id=\"main\" class=\"w-section main\">\n    <div data-animation=\"cross\" data-duration=\"800\" data-infinite=\"1\" data-delay=\"4000\" data-autoplay=\"1\" class=\"w-slider slide\">\n      <div class=\"w-slider-mask mask-slide\">\n        <div class=\"w-slide slide\">\n          <h1 data-ix=\"slider-animation-text\" class=\"text-head\">Visage&nbsp;<br><strong>Intensive</strong></h1>\n          <div data-ix=\"slider-animation-text\" class=\"text-block-2\">\n            <h5 class=\"text-h5\">ТВОРЧЕСКАЯ ПРОФЕССИЯ ЗА ДВЕ НЕДЕЛИ.</h5><a href=\"#training\" class=\"w-button button-line\">ПОДРОБНЕЕ</a>\n          </div>\n          <div data-ix=\"foto-slider\" class=\"picture-slide visage\"></div>\n        </div>\n      </div>\n      <div class=\"w-slider-arrow-left left-arrow\">\n        <div class=\"w-icon-slider-left\"></div>\n      </div>\n      <div class=\"w-slider-arrow-right right-arrow\">\n        <div class=\"w-icon-slider-right\"></div>\n      </div>\n      <div class=\"w-slider-nav w-round slide-nav\"></div>\n    </div>\n    <a href=\"http://www.mechkovskayaschool.ru/\" data-ix=\"logo\" class=\"w-inline-block logo\"></a>\n  </div>\n  <div id=\"training\" class=\"w-section training intensity\">\n    <div class=\"w-row\">\n      <div class=\"w-col w-col-6 w-col-stack training-column intensity\">\n        <div class=\"div-block-content-training\">\n          <h2 class=\"head\">О КУРСЕ</h2>\n          <div data-ix=\"line-5\" class=\"div-line\"></div>\n          <div class=\"paragraph visage-intensive\">\n            <h2 class=\"quote-teacher v1\">« — Макияж обладает огромной силой, такой, что меняет не только внешность, но и внутреннее состояние человека. Именно поэтому визаж и стиль — полноценное искусство.»</h2>\n            <p class=\"text-paragraph\"><span class=\"text-spain-paragraph\" style=\"\">Практический курс для тех, кто желает овладеть искусством макияжа. Авторы курса — известные стилисты и визажисты России и Европы. Мы наполнили программу курса знаниями и поделимся с вами опытом, модными трендами, увлекательной историей моды и современной практикой коммерческого макияжа.<br><br></span>\n            </p>\n            <p class=\"text-paragraph visage-intensive\">Мы учим&nbsp;специалистов, которые хотят экспериментировать, изучать новые и творчески переосмысливать устоявшиеся образы визажа. На курсе вы научитесь грамотно и легко наносить макияж за 30 минут, воплощать художественный образ, продавать свои услуги и продвигать себя как профессионала. реализоваться как профессионалу, получая высокие гонорары и признание.</p>\n          </div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6 w-col-stack training-column intensity\">\n        <div data-ix=\"picture-up\" class=\"div-training-picture visage-intensive\">\n          <div class=\"foro-image-1 visage-intensive\"></div>\n        </div>\n        <div class=\"divteacher\">\n          <div class=\"previews-teacher intensity\"><span class=\"teacher\" data-new-link=\"true\"><strong class=\"teacher-name\">ЕЛЕНА НАЛОБИНА</strong></span>\n            <br>Визажист, преподаватель школы визажа, красоты и стиля <strong data-new-link=\"true\">“MECHKOVSKAYA”</strong>\n          </div><a href=\"command/elena-nalobina.html\" class=\"w-button button-line v3 visage-intensive\">ПОДРОБНЕЕ</a>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section professional-courses visage-intensive _23\">\n    <h2 class=\"titlecourse-content intensity\">СОДЕРЖАНИЕ КУРСА</h2>\n    <div class=\"w-row\">\n      <div class=\"w-col w-col-6 w-col-stack column-courses visae-intensive\">\n        <div data-ix=\"picture-up\" class=\"div-training-picture intensive\"></div>\n        <div class=\"course-content\">С помощью искусства макияжа всегда раскрываем какую-то историю: радостную, печальную, историю любви, или вашу собственную историю. Кожа — главный элемент макияжа. Лицо — холст. Поэтому мы обучаем играть сочетанием праймеров, тональных основ, корректоров и хайлайтеров, создавая безупречную текстуру кожи.</div>\n      </div>\n      <div class=\"w-col w-col-6 w-col-stack column-courses intensive\">\n        <div class=\"content intensity1\">\n          <p class=\"text-rap\">Миссия курса «VISAGE INTESIVE» — научить раскрывать истинную красоту женщины. Ту, что скрывается за ее скромностью. И вместе с тем, учим промоутировать себя как профессионала, находить свои уникальные качества и развиваться в прфессии. За две недели вы освоите на практике:</p>\n          <div class=\"list-of-courses v2\">• Скульптурирование лица\n            <br>• Дизайн бровей\n            <br>• Дневной NUDE макияж\n            <br>• 3D вечерний макияж\n            <br>• Свадебный макияж со спецэффектами\n            <br>• Возрастной лифтинговый макияж\n            <br>• Макияж для фото/видео съемки\n            <br>• Работу с различными текстурами и основы карандашной техники\n            <br>• Основы причёсок — техники накручивания локонов и причёски на базе локонов. Практика со второго занятия. Бонус: В курс входит занятие по основам прически.</div><a href=\"#\" data-ix=\"button-application-form\" class=\"w-button button-line v3 list-of-courses-button\">Записаться на пробное занятие</a>\n        </div>\n      </div>\n    </div>\n    <div class=\"rectangle intensity\"></div>\n  </div>\n  <div class=\"w-section cost-and-schedule\">\n    <h2 class=\"head cost-and-schedule1 head2\">СТОИМОСТЬ И ПРОГРАММА</h2>\n    <div class=\"cost-and-schedule-content\">Скажем сразу: на интенсиве ждем тех, кто готов отработать 14 дней с утра и до вечера, не жалея себя. За это время выведем наших слушателей на новый профессиональный уровень, потому что курсы преподают только практики\n      <br>с внушительным опытом работы.</div>\n    <div class=\"w-row row-price\">\n      <div class=\"w-col w-col-4 w-col-stack columncost\">\n        <div data-ix=\"price\" class=\"cost-and-schedule-table\">\n          <div class=\"cost-and-schedule-header-text\">Хронометраж курса:</div>\n          <h2 class=\"head v2 cost-lyrics head2 c1\">2</h2>\n          <div class=\"cost-text2\">недели</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-stack\">\n        <div data-ix=\"price-2\" class=\"cost-and-schedule-table\">\n          <div class=\"cost-and-schedule-header-text\">График: пн-пт</div>\n          <h2 class=\"head v2 cost-lyrics head2 c1\">10:00-17:00</h2>\n          <div class=\"cost-text2\">обед с 13:00 до 14:00\n            <br>часов</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-stack\">\n        <div data-ix=\"price-3\" class=\"cost-and-schedule-table\">\n          <div class=\"cost-and-schedule-header-text\">ГРУППА:</div>\n          <h2 class=\"head v2 cost-lyrics head2 c1\">3-7</h2>\n          <div class=\"cost-text2\">человек</div>\n        </div>\n      </div>\n    </div>\n    <div class=\"w-row row-price2\">\n      <div class=\"w-col w-col-8 w-col-stack\">\n        <div class=\"cost-and-schedule-content _2\">20% скидка ученикам, которые обучились на базовом курсе по визажу в нашей школе. Скидка действует на курсы повышения квалификации — «Дизайн бровей» и «Свадебный стилист».</div><a href=\"#\" data-ix=\"button-application-form\" class=\"w-button reservations cost\">ЗАБРОНИРОВАТЬ МЕСТО</a>\n      </div>\n      <div class=\"w-col w-col-4 w-col-stack\">\n        <div class=\"divthe-price-of-the-course\">\n          <h4 class=\"the-cost-price-of-a-title\">СТОИМОСТЬ:</h4>\n          <h1 class=\"price-ruble\">75000 <span class=\"ruble\">₽</span></h1>\n          <div class=\"text-price1\">возможна рассрочка или скидка при единовременной оплате</div>\n          <div class=\"circle-price _2\"></div>\n          <div class=\"circle-price\"></div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section portfolio-rate\">\n    <div class=\"w-row\">\n      <div class=\"w-col w-col-6\">\n        <h2 class=\"head-portfolio\">ПОРТФОЛИО</h2>\n        <div data-ix=\"line-7\" class=\"div-line-2 portline\"></div>\n      </div>\n      <div class=\"w-col w-col-6\">\n        <div class=\"text-portfolio\">Изменять внешность увлекательно — макияж можно менять, и вы опять становитесь новой персоной. Посмотрите как мы учимся работать воображением с помощью искусства макияжа:</div>\n      </div>\n    </div>\n    <div data-ix=\"89\" class=\"w-row portfoliosquares\">\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visage-intensive1\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/Hair-redo-1.jpg\",\n                \"fileName\": \"56822af774cf1bc76bab4ed9_Hair-redo-1.jpg\",\n                \"origFileName\": \"Hair-redo-1.jpg\",\n                \"width\": 1920,\n                \"height\": 1277,\n                \"size\": 237265,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visage-intensive2\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/Nastya_Tsoy0942.jpg\",\n                \"fileName\": \"56894fc451c5c84f33672f99_Nastya_Tsoy0942.jpg\",\n                \"origFileName\": \"Nastya_Tsoy0942.jpg\",\n                \"width\": 564,\n                \"height\": 845,\n                \"size\": 70174,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visage-intensive3\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56821d6175b86ae462580018\",\n                \"url\": \"images/a16YaOBwVao.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n    </div>\n    <div data-ix=\"89\" class=\"w-row portfoliosquares\">\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio makeup-artist-himself\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/rtweteyh.jpg\",\n                \"fileName\": \"rtweteyh.jpg\",\n                \"origFileName\": \"rtweteyh.jpg\",\n                \"width\": 534,\n                \"height\": 801,\n                \"size\": 223966,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visage-intensive5\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/HvXHvSli8P0.jpg\",\n                \"fileName\": \"56821c8275b86ae462580015_HvXHvSli8P0.jpg\",\n                \"origFileName\": \"HvXHvSli8P0.jpg\",\n                \"width\": 649,\n                \"height\": 845,\n                \"size\": 214287,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visageintensive6\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/IMG_9782-(1).jpg\",\n                \"fileName\": \"56821dfe3a4a2d217fd48782_IMG_9782-(1).jpg\",\n                \"origFileName\": \"IMG_9782-(1).jpg\",\n                \"width\": 1037,\n                \"height\": 1500,\n                \"size\": 82865,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n    </div>\n    <div data-ix=\"89\" class=\"w-row portfoliosquares\">\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visage-intensive7\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"56821f0ed19a7ec26b8cbfe2\",\n                \"url\": \"images/Hair-3-copy.jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio visage-intensive8\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"id\": \"5682210c75b86ae46258020a\",\n                \"url\": \"images/lips-2-(1).jpg\",\n                \"fileName\": \"example-bg.png\",\n                \"origFileName\": \"example-bg.png\",\n                \"width\": 250,\n                \"height\": 250,\n                \"size\": 3618,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n      <div class=\"w-col w-col-4 w-col-small-4 column-portfoliosquares\">\n        <div class=\"div-block-portfolio _9\">\n          <a href=\"#\" class=\"w-lightbox w-inline-block\"><img src=\"https://d3e54v103j8qbb.cloudfront.net/img/placeholder-thumb.svg\" class=\"image-box\">\n            <script type=\"application/json\" class=\"w-json\">\n              { \"items\": [{\n                \"url\": \"images/Y-RK3qXlB8I.jpg\",\n                \"fileName\": \"Y-RK3qXlB8I.jpg\",\n                \"origFileName\": \"Y-RK3qXlB8I.jpg\",\n                \"width\": 533,\n                \"height\": 801,\n                \"size\": 85668,\n                \"type\": \"image\"\n              }] }\n            </script>\n          </a>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section training-for-yourself visage-intensive\">\n    <h2 class=\"head intensive2\">Что даёт курс VISAGE INTENSIVE</h2>\n    <div class=\"w-row row-training curs intensive\">\n      <div class=\"w-col w-col-6 w-col-stack training-for-yourself-column visage-intensive\">\n        <div class=\"content intensive4\">\n          <p class=\"text-rap visage-intensive _2\">Искусство макияжа — возможность постоянно развивать свой стиль, технику, навыки. Настоящий успех приходит тогда, когда вы открыты новому и видите мир вокруг себя.</p>\n          <div class=\"list-of-courses intensive5\"><strong data-new-link=\"true\">После курса вы сможете:</strong>\n            <br>\n            <br>• Создавать новые образы (макияж и прически)\n            <br>• Экономить время и средства\n            <br>• Работать художником - чувствовать грань и гармонию света\n            <br>• Освоите все виды коммерческого макияжа (повседневный, деловой, вечерний),\n            <br>• Арт-работы\n            <br>• Сможете работать в команде с фотографами и видео режиссерами, профессиональными моделями.&nbsp;\n            <br>• Участвовать в съемках для\n            <br>глянцевых журналов и телевидения.&nbsp;\n            <br>• Создавать образы для модных показов и фотосессий.</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6 w-col-stack training-for-yourself-column visage-intensive\">\n        <div data-ix=\"picture-up\" class=\"div-training-picture intensive3\"></div>\n      </div>\n    </div>\n    <div class=\"rectangle intensuve\"></div>\n  </div>\n  <div class=\"w-section advantages-of-courses intensive6 _47\">\n    <h2 class=\"head cost-and-schedule1 v3\">Преимущества курсов в <br>MECHKOVSKAYA school <br>of beauty</h2>\n    <div class=\"w-row rowadvantages visage-intens\">\n      <div class=\"w-col w-col-6\">\n        <div data-ix=\"price\" class=\"div-the-advantage-of-courses _45\">\n          <div class=\"div-icon _2\"></div>\n          <div class=\"textadvantage-of-courses-content\"><span class=\"headadvantage-of-courses\">Кисти и косметика</span>\n            <br>\n            <br>Используют косметические средства, которые предоставляет Школа — профессиональные наборы кистей и косметику таких брендов как» добавляем «Urban decay, Inglot, Make up for ever, Mac, Smash Box, Dolce&amp;Gabbana, Dior, Estée Lauder</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6\">\n        <div data-ix=\"price-2\" class=\"div-the-advantage-of-courses _45\">\n          <div class=\"div-icon _4\"></div>\n          <div class=\"textadvantage-of-courses-content _2 _47\"><span class=\"headadvantage-of-courses\">Участвовать в модных событиях и <br>получить новый уровень свободы</span>\n            <br>Школа Красоты Mechkovskaya выступает бьюти-партнером известных, значимых, мероприятий в России и Европе. Работаем с глянцевыми журналами, телевидением и федеральными компаниями. Ученики получают возможность получить бесплатную практику на материалах школы — участвовать в бьюти-подготовке к модным событиям.</div>\n        </div>\n      </div>\n    </div>\n    <div class=\"w-row row2advantage-of-courses visage-intensiv\">\n      <div class=\"w-col w-col-6\">\n        <div data-ix=\"price\" class=\"div-the-advantage-of-courses v1 _45\">\n          <div class=\"div-icon _5\"></div>\n          <div class=\"textadvantage-of-courses-content\"><span class=\"headadvantage-of-courses\">Трудоустройство<br></span>\n            <br>Найти себя, используя потрясающий шанс с возможностью дальнейшего трудоустройства и продвижения в качестве фриланс-мастеров и постоянных специалистов. Лучших выпускников приглашаем работать</div>\n        </div>\n      </div>\n      <div class=\"w-col w-col-6\">\n        <div data-ix=\"price-2\" class=\"div-the-advantage-of-courses c1 _45\">\n          <div data-ix=\"price-3\" class=\"div-the-advantage-of-courses map _45\">\n            <div class=\"div-icon map\"></div>\n            <div class=\"advantages-map-text\">Занятия проходят в профессионально-оборудованной студии по адресу Варшавское шоссе, 33, станция метро «Нагатинская»\n              <br><a class=\"_55\" href=\"#\">2</a>\n            </div><a href=\"#\" data-ix=\"button-application-form\" class=\"w-button button-line v3 button2 _47\">УЗНАТЬ О ДРУГИх КУРСАх</a>\n          </div>\n        </div>\n      </div>\n    </div>\n  </div>\n  <div class=\"w-section reviews\">\n    <h2 class=\"head reviews-text\">ОТЗЫВЫ</h2>\n    <div data-ix=\"line-6\" class=\"div-line v2\"></div>\n    <div class=\"reviews-div\">\n      <div data-animation=\"cross\" data-duration=\"500\" data-infinite=\"1\" data-delay=\"5000\" data-autoplay=\"1\" data-nav-spacing=\"5\" data-easing=\"ease-in-out-quart\" class=\"w-slider slider-comments\">\n        <div class=\"w-slider-mask mask-comments\">\n          <div class=\"w-slide slide-comments\">\n            <div class=\"w-row row-comments\">\n              <div class=\"w-col w-col-4 w-col-stack\"><img src=\"images/re2_w.jpg\" class=\"imagereviews foto2 _1\">\n              </div>\n              <div class=\"w-col w-col-8 w-col-stack column-comments\">\n                <div class=\"text-block-reviews\">Хотите научиться делать красивый Makeup? А узнать все новые фишечки макияжа? А еще попробовать профессиональную косметику? А может побыть моделью? Если вы ответили хотя бы на один вопрос «ДА», тогда срочно в школу красоты «Mechkovskaya». Я обучалась там на курсе «Визажист-художник», за три месяца я действительно получила всё что мне обещали и не только. Преподаватели отрабатывают на все 100%. Елена Налобина – это один из тех преподавателей, которые запоминаются на всю жизнь, она вкладывает душу в свои занятия и искренне переживает за своих учеников. Не вижу смысла описывать какие конкретно знания я получила на обучении, мне просто не хватит места, да и найти программу курса легко. Для меня важно отметить, что в «Мечковской» очень комфортные условия и учебы, и работы. Попадая туда ты словно обретаешь семью. СПАСИБО БОЛЬШОЕ!</div>\n              </div>\n            </div>\n          </div>\n        </div>\n        <div class=\"w-slider-arrow-left reviews-arrow\">\n          <div class=\"w-icon-slider-left\"></div>\n        </div>\n        <div class=\"w-slider-arrow-right reviews-arrow right\">\n          <div class=\"w-icon-slider-right\"></div>\n        </div>\n        <div class=\"w-slider-nav w-slider-nav-invert w-round circlereviews\"></div>\n      </div>\n    </div>\n  </div>\n  \n  \n  <div data-ix=\"menu-board\" class=\"information-about-teacher\">\n    <div data-ix=\"close-3\" class=\"close mob v2\"></div>\n    <div class=\"w-row rowinformation-about-teacher\">\n      <div class=\"w-col w-col-5 w-col-stack columninformation-about-teacher\"></div>\n      <div class=\"w-col w-col-7 w-col-stack columninformation-about-teacher t2\">\n        <h1 class=\"head information-aboutteacher\">Елена Налобина</h1>\n        <p class=\"text-paragraph achievements-text\"><span class=\"text-spain-paragraph\" data-new-link=\"true\"><strong>Достижения<br></strong><br>500 выпускников&nbsp;<br>Призёр свыше 27 конкурсов<br>Судья конкурсов красоты<br></span>\n        </p>\n        <div data-duration-in=\"300\" data-duration-out=\"100\" class=\"w-tabs tabs1\">\n          <div class=\"w-tab-menu teacher-tab\">\n            <a data-w-tab=\"Tab 1\" class=\"w-tab-link w-inline-block tab-link-button\">\n              <div>ОБРАЗОВАНИЕ</div>\n            </a>\n            <a data-w-tab=\"Tab 2\" class=\"w-tab-link w--current w-inline-block tab-link-button\">\n              <div>ОПЫТ РАБОТЫ</div>\n            </a>\n          </div>\n          <div class=\"w-tab-content tab-content1\">\n            <div data-w-tab=\"Tab 1\" class=\"w-tab-pane\">\n              <p class=\"text-paragraph contentteacher\"><strong data-new-link=\"true\">Образование: </strong>\n                <br>\n                <br>• НГПУ (факультет филологии, массовой информации и психологии)\n                <br>• Специальное образование : Авторский курс Анны Петайкиной по специальности «Начинающий визажист»\n                <br>• Курс повышения квалификации «Работа с акварелью»\n                <br>• Курс повышения квалификации «Стилистический блок.\n                <br>• Макияж разных эпох 20-го столетия»\n                <br>• Курс «Визажист» в школе-студии «Модерн».\n                <br>• Преподаватель Наталья Павлова МК Марины Хабаровой «Hair style. Fashion look. Art style»,\n                <br>«Wedding style. Fashion style. Color make up».\n                <br>• МК по карандашной технике Таисии Васильевой\n                <br>• Авторский МК Валерии Куцан «Свадебный и праздничный макияж»\n                <br>• МК повышения квалификации Анастасии Дурасовой и Ольги Романовой\n                <br>• АВТОРСКИЙ БАЗОВЫЙ КУРС МАКИЯЖА Дениса Карташева\n                <br>• Курс Fashion Make-Up Дениса Карташева</p>\n            </div>\n            <div data-w-tab=\"Tab 2\" class=\"w-tab-pane w--tab-active\">\n              <p class=\"text-paragraph contentteacher\"><strong data-new-link=\"true\">Опыт работы:</strong>\n                <br>\n                <br>• Визажист сети магазинов «Парфюмика»\n                <br>• Преподаватель макияжа учебного центра Free Fashion\n                <br>• Преподаватель школы визажа, красоты и стиля «MECHKOVSKAYA»\n                <br>• Участие в проектах и конкурсах: IV Новосибирская Неделя Моды Конкурс\n                <br>фантазийного макияжа Kosmetik Expo Сибирь.\n                <br>• 4 место среди профессионалов\n                <br>• Участие в Siberian Fashion Week.\n                <br>• Работа на показе Дмитрия Логинова.\n                <br>• Работа с лучшим глянцем Новосибирска – журналом Fashion Collection</p>\n            </div>\n          </div>\n        </div>\n      </div>\n    </div>\n    \n  </div>','Content','','N','Y','N','2016-06-02 20:34:49','2016-06-02 20:39:42'),(7,0,0,'super','Visage  Intensive super','','Content','','N','Y','N','2016-06-02 20:40:13','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `mce_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_data`
--

DROP TABLE IF EXISTS `mce_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id variable',
  `key` varchar(64) NOT NULL COMMENT 'Variable name',
  `data` longtext NOT NULL COMMENT 'Variable value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of variables DEPRECATED';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_data`
--

LOCK TABLES `mce_data` WRITE;
/*!40000 ALTER TABLE `mce_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_files`
--

DROP TABLE IF EXISTS `mce_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Порядковый номер документа в отображении',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'Категория документа',
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_files`
--

LOCK TABLES `mce_files` WRITE;
/*!40000 ALTER TABLE `mce_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_forms`
--

DROP TABLE IF EXISTS `mce_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `template` varchar(64) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_forms`
--

LOCK TABLES `mce_forms` WRITE;
/*!40000 ALTER TABLE `mce_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_forms_fields`
--

DROP TABLE IF EXISTS `mce_forms_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_forms_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form` int(11) unsigned NOT NULL,
  `type` enum('text','textarea','checkbox') NOT NULL DEFAULT 'text',
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `regex` varchar(255) NOT NULL,
  `regex_error` varchar(255) NOT NULL,
  `default` varchar(255) NOT NULL,
  `required` enum('Y','N') NOT NULL DEFAULT 'N',
  `order` int(11) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form` (`form`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_forms_fields`
--

LOCK TABLES `mce_forms_fields` WRITE;
/*!40000 ALTER TABLE `mce_forms_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_forms_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_images`
--

DROP TABLE IF EXISTS `mce_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID изображения',
  `src` varchar(255) NOT NULL COMMENT 'Полноое имя файла',
  `md5` varchar(32) NOT NULL COMMENT 'Хэш имени файла',
  `module` varchar(64) NOT NULL COMMENT 'Алиас модуля',
  `module_id` int(11) NOT NULL COMMENT 'ID записи ',
  `alter_key` varchar(64) NOT NULL COMMENT 'Текст для аттрибута alter',
  `main` enum('Y','N') NOT NULL COMMENT 'Флаг главного изображения',
  `video` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Хранилище изображений';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_images`
--

LOCK TABLES `mce_images` WRITE;
/*!40000 ALTER TABLE `mce_images` DISABLE KEYS */;
INSERT INTO `mce_images` VALUES (3,'/data/moduleImages/Content/1/d7c245cf9a92d816fbe2ddbfcceb0060.jpg','d7c245cf9a92d816fbe2ddbfcceb0060','Content',1,'','N',''),(2,'/data/moduleImages/Content/1/3648330153c2a777ad0f7cc40e565102.jpg','3648330153c2a777ad0f7cc40e565102','Content',1,'','Y',''),(4,'/data/moduleImages/Content/2/6755185b6ea7969475c339e2e1e97f59.jpg','6755185b6ea7969475c339e2e1e97f59','Content',2,'','Y',''),(5,'/data/moduleImages/Content/2/f6dec5e8568e6401461c9b4e34250481.jpg','f6dec5e8568e6401461c9b4e34250481','Content',2,'','N',''),(6,'/data/moduleImages/Catalog/1/37b3c86d62b23fc572dc35455d96f4e5.jpg','37b3c86d62b23fc572dc35455d96f4e5','Catalog',1,'','Y',''),(7,'/data/moduleImages/Slider/1/93d053d838894e956daa8076254612d8.jpg','93d053d838894e956daa8076254612d8','Slider',1,'','Y',''),(8,'/data/moduleImages/Slider/2/e684a41ca682ddc4055df05894a5b0c6.jpg','e684a41ca682ddc4055df05894a5b0c6','Slider',2,'','N',''),(9,'/data/moduleImages/Slider/2/2297c6383d33081bb804455169ddd6b9.jpg','2297c6383d33081bb804455169ddd6b9','Slider',2,'','Y',''),(10,'/data/moduleImages/Slider/3/1f768944d76ffd7cb349ba928e9a1b86.jpg','1f768944d76ffd7cb349ba928e9a1b86','Slider',3,'','Y',''),(11,'/data/moduleImages/Slider/4/427d190d971b4fe1a08c4a33432c11b2.jpg','427d190d971b4fe1a08c4a33432c11b2','Slider',4,'','Y',''),(12,'/data/moduleImages/Slider/5/125f8d52444ad68cd21404b699106ae2.jpg','125f8d52444ad68cd21404b699106ae2','Slider',5,'','Y',''),(13,'/data/moduleImages/Slider/6/337e0d81d4f752fff69ece26f2a78465.jpg','337e0d81d4f752fff69ece26f2a78465','Slider',6,'','Y',''),(14,'/data/moduleImages/Slider/2/e6b1c67782f7a90faf489001800e9560.jpg','e6b1c67782f7a90faf489001800e9560','Slider',2,'','N',''),(15,'/data/moduleImages/Slider/2/e6b1c67782f7a90faf489001800e9560.jpg','e6b1c67782f7a90faf489001800e9560','Slider',2,'','N',''),(16,'/data/moduleImages/Blocks/2/e6b1c67782f7a90faf489001800e9560.jpg','e6b1c67782f7a90faf489001800e9560','Blocks',2,'','Y','');
/*!40000 ALTER TABLE `mce_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_images_storage`
--

DROP TABLE IF EXISTS `mce_images_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_images_storage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `del` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_images_storage`
--

LOCK TABLES `mce_images_storage` WRITE;
/*!40000 ALTER TABLE `mce_images_storage` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_images_storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_mediafiles`
--

DROP TABLE IF EXISTS `mce_mediafiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_mediafiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `filetype` varchar(4) NOT NULL,
  `fileinfo` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_mediafiles`
--

LOCK TABLES `mce_mediafiles` WRITE;
/*!40000 ALTER TABLE `mce_mediafiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_mediafiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_menu`
--

DROP TABLE IF EXISTS `mce_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(64) NOT NULL DEFAULT '',
  `link` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(128) NOT NULL DEFAULT '',
  `module` varchar(32) NOT NULL,
  `template` varchar(63) NOT NULL DEFAULT 'page.php',
  `root` varchar(64) NOT NULL DEFAULT '',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_menu`
--

LOCK TABLES `mce_menu` WRITE;
/*!40000 ALTER TABLE `mce_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_migration`
--

DROP TABLE IF EXISTS `mce_migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stamp` varchar(20) NOT NULL COMMENT 'Время создания миграции',
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT 'Флаг установки таблиц для модуля',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='Миграции';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_migration`
--

LOCK TABLES `mce_migration` WRITE;
/*!40000 ALTER TABLE `mce_migration` DISABLE KEYS */;
INSERT INTO `mce_migration` VALUES (1,'1463681918','FileLoader'),(2,'1463242728',''),(3,'1459449480',''),(4,'1459682297',''),(5,'1461080844',''),(6,'1461082199',''),(7,'1461083821',''),(8,'1462986102',''),(9,'1463682369','Content'),(10,'1463683834','Catalog'),(11,'1463919519','Blog'),(12,'1464617927','Slider'),(13,'1464623705','');
/*!40000 ALTER TABLE `mce_migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_products`
--

DROP TABLE IF EXISTS `mce_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `top` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `shortName` varchar(64) NOT NULL,
  `nav` varchar(100) NOT NULL,
  `brand` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` int(11) NOT NULL,
  `unit` varchar(10) DEFAULT '',
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  `types` text NOT NULL,
  `is_action` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_featured` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_lider` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_exist` enum('Y','N') NOT NULL DEFAULT 'Y',
  `noIndex` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Флаг запрета на индексирование Y-запрещает индексирование, N-разрешает',
  `availability` text NOT NULL,
  `relations` varchar(255) NOT NULL,
  `rate` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `show` (`show`),
  KEY `deleted` (`deleted`),
  KEY `top` (`top`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_products`
--

LOCK TABLES `mce_products` WRITE;
/*!40000 ALTER TABLE `mce_products` DISABLE KEYS */;
INSERT INTO `mce_products` VALUES (1,1,0,'Кисть для нанесения тона','01 LN','',0,1350.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 01:54:49','2016-05-22 19:03:41','<p>Этот текст представлен полем \"Анонс\" в карточке товара</p>','<p>Этот текст представлен полем \"Описание\" в карточке товара. Для доступа к карточке товра нажать на иконку \"карандаш\" в списке товаров.</p>\n<p>Для доступа к характеристикам товара \"Размер\". \"Тип обоймы\", \"Тип ворса\", \"Тип ручки\" нажать на наименование товара.</p>','','N','N','N','Y','N','','',18,0),(2,1,0,'Кисть для консилера','02 LN','conseel',0,450.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 02:55:17','0000-00-00 00:00:00','<p>Кисть для консилера - анонс</p>','<p>Кисть для консилера - описание</p>','','N','N','N','Y','N','','',10,0),(3,2,0,'Кисть для румян','04N','',0,700.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 13:55:06','2016-05-22 16:36:18','<p>Это анонс 04N кисть для румян</p>','<p>Это описание 04N кисть для румян</p>','','N','N','N','Y','N','','',20,0),(4,2,0,'Кисть для румян','05N','',0,650.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 13:55:42','0000-00-00 00:00:00','','','','N','N','N','Y','N','','',3,0),(5,1,0,'Кисть для нанесения тона','03 LN','',0,1400.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 17:51:17','0000-00-00 00:00:00','','','','N','N','N','Y','N','','',0,0),(6,3,0,'Кисть для теней','07 N','',0,400.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 17:53:42','0000-00-00 00:00:00','','','','N','N','N','Y','N','','',0,0),(7,3,0,'Кисть для теней','08 N','',0,450.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 17:54:04','2016-05-22 17:54:25','','','','N','N','N','Y','N','','',0,0),(8,3,0,'Кисть для теней','09 N','',0,400.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 17:54:45','2016-05-22 17:54:58','','','','N','N','N','Y','N','','',0,0),(9,4,0,'Кисть для хайлайтера и теней','06 N','',0,400.00,0,'шт.','0000-00-00 00:00:00','Y','N','2016-05-22 17:56:06','0000-00-00 00:00:00','','','','N','N','N','Y','N','','',0,0);
/*!40000 ALTER TABLE `mce_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_products_brands`
--

DROP TABLE IF EXISTS `mce_products_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_products_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav` (`nav`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_products_brands`
--

LOCK TABLES `mce_products_brands` WRITE;
/*!40000 ALTER TABLE `mce_products_brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_products_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_products_currencies`
--

DROP TABLE IF EXISTS `mce_products_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_products_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(5) NOT NULL,
  `symbol` varchar(10) NOT NULL DEFAULT '',
  `value` decimal(10,4) NOT NULL DEFAULT '1.0000',
  `symbol_position` enum('left','right') NOT NULL DEFAULT 'right',
  `is_main` enum('Y','N') NOT NULL DEFAULT 'N',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_products_currencies`
--

LOCK TABLES `mce_products_currencies` WRITE;
/*!40000 ALTER TABLE `mce_products_currencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_products_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_products_topics`
--

DROP TABLE IF EXISTS `mce_products_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_products_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `top` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(100) NOT NULL DEFAULT '',
  `isModel` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'флаг отнесения товаров категории к одной модели',
  `text` text NOT NULL,
  `types` text NOT NULL,
  `cases` text NOT NULL,
  `rate` int(11) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `top` (`top`),
  KEY `order` (`order`),
  KEY `deleted` (`deleted`),
  KEY `show` (`show`,`deleted`),
  KEY `show_2` (`show`),
  KEY `rate` (`rate`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_products_topics`
--

LOCK TABLES `mce_products_topics` WRITE;
/*!40000 ALTER TABLE `mce_products_topics` DISABLE KEYS */;
INSERT INTO `mce_products_topics` VALUES (1,0,0,'Коррекция','correction','N','','','',28,'Y','N','2016-05-20 20:44:04','2016-05-21 21:05:21'),(2,0,1,'Румяна','rumiana','N','','','',23,'Y','N','2016-05-21 21:03:51','2016-05-21 21:05:01'),(3,0,2,'Для теней','','N','','','',0,'Y','N','2016-05-21 21:06:01','0000-00-00 00:00:00'),(4,0,3,'Хайлайтер','highlight','N','','','',0,'Y','N','2016-05-21 22:29:41','2016-05-21 22:30:06'),(5,0,4,'Пудра','powder','N','','','',0,'Y','N','2016-05-21 22:31:19','2016-05-21 22:31:40'),(6,0,5,'Для бровей','','N','','','',0,'Y','N','2016-05-22 17:56:51','0000-00-00 00:00:00'),(7,0,6,'Для губ','','N','','','',0,'Y','N','2016-05-22 17:57:13','0000-00-00 00:00:00'),(8,0,7,'Для подводки','','N','','','',0,'Y','N','2016-05-22 17:57:28','0000-00-00 00:00:00'),(9,0,9,'Для туши','','N','','','',0,'Y','N','2016-05-22 17:57:43','0000-00-00 00:00:00'),(10,0,10,'Для помад','','N','','','',0,'Y','N','2016-05-22 17:58:20','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `mce_products_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_seo`
--

DROP TABLE IF EXISTS `mce_seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_seo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `module_table` varchar(64) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `keywords` varchar(2048) NOT NULL,
  `description` varchar(2048) NOT NULL,
  `tagH1` varchar(1024) NOT NULL COMMENT 'Значение тэга H1 для текущей страницы',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Данные для СЕО';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_seo`
--

LOCK TABLES `mce_seo` WRITE;
/*!40000 ALTER TABLE `mce_seo` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_settings`
--

DROP TABLE IF EXISTS `mce_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_settings`
--

LOCK TABLES `mce_settings` WRITE;
/*!40000 ALTER TABLE `mce_settings` DISABLE KEYS */;
INSERT INTO `mce_settings` VALUES (1,'Catalog','Скидка','discount','10','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `mce_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_shop_orders`
--

DROP TABLE IF EXISTS `mce_shop_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_shop_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `paymethod` int(11) NOT NULL,
  `orderSum` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Сумма заказа',
  `shopId` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_shop_orders`
--

LOCK TABLES `mce_shop_orders` WRITE;
/*!40000 ALTER TABLE `mce_shop_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_shop_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_shop_orders_items`
--

DROP TABLE IF EXISTS `mce_shop_orders_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_shop_orders_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` int(11) NOT NULL,
  `brand` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `featureId` int(11) NOT NULL DEFAULT '0' COMMENT 'Id выбранной характеристики товара',
  `featureValue` varchar(255) NOT NULL DEFAULT '' COMMENT 'Значение выбранной характеристики товара',
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `top` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `count` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Суммарная стоимость товара',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_shop_orders_items`
--

LOCK TABLES `mce_shop_orders_items` WRITE;
/*!40000 ALTER TABLE `mce_shop_orders_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_shop_orders_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_shop_paymethods`
--

DROP TABLE IF EXISTS `mce_shop_paymethods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_shop_paymethods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_shop_paymethods`
--

LOCK TABLES `mce_shop_paymethods` WRITE;
/*!40000 ALTER TABLE `mce_shop_paymethods` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_shop_paymethods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_shop_statuses`
--

DROP TABLE IF EXISTS `mce_shop_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_shop_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_shop_statuses`
--

LOCK TABLES `mce_shop_statuses` WRITE;
/*!40000 ALTER TABLE `mce_shop_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_shop_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_shops`
--

DROP TABLE IF EXISTS `mce_shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deletetd` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_shops`
--

LOCK TABLES `mce_shops` WRITE;
/*!40000 ALTER TABLE `mce_shops` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_slider`
--

DROP TABLE IF EXISTS `mce_slider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Порядковый номер слайда',
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `link` varchar(255) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `images_ids` varchar(255) NOT NULL DEFAULT '' COMMENT 'Ссылки (через запятую) на ИД привязанных к записи изображений в таблице xx_images_storage, первый ид - "главной" картинки',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_slider`
--

LOCK TABLES `mce_slider` WRITE;
/*!40000 ALTER TABLE `mce_slider` DISABLE KEYS */;
INSERT INTO `mce_slider` VALUES (1,0,'main','2016-05-30 00:00:00','','Y','N','2016-05-30 20:25:20','2016-05-30 22:28:02','','<h1 class=\"text-head\" data-ix=\"slider-animation-text\">VISAGE <strong>INTENSIVE</strong></h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\n<h5 class=\"text-h5\">ТВОРЧЕСКАЯ ПРОФЕССИЯ ЗА ДВЕ НЕДЕЛИ.</h5>\n<a class=\"w-button button-line\" href=\"/admin/visage-intensive.html\">ПОДРОБНЕЕ</a></div>'),(2,1,'smoky','2016-05-30 00:00:00','','Y','N','2016-05-30 23:04:47','2016-06-01 19:46:22','','<h1 class=\"text-head\" data-ix=\"slider-animation-text\">Сам сеБЕ <strong data-new-link=\"true\">визажист</strong></h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\n<h5 class=\"text-h5\">ДВУХДНЕВНЫЙ КУРС МАКИЯЖА ДЛЯ СЕБЯ</h5>\n<a class=\"w-button button-line v2\" href=\"/himselfmakeup\">ПОДРОБНЕЕ</a></div>'),(3,2,'eyebrows','2016-05-30 00:00:00','','Y','N','2016-05-30 23:36:16','2016-05-30 23:42:13','','<h1 class=\"text-head\" data-ix=\"slider-animation-text\">ДИЗАЙН <strong data-new-link=\"true\">БРОВЕЙ</strong></h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\n<h5 class=\"text-h5\">Учим и помогаем овладевать секретами идеального взгляда</h5>\n<a class=\"w-button button-line v2\" href=\"/admin/design-eyebrows-moscow.html\">ПОДРОБНЕЕ</a></div>'),(4,3,'makeup','2016-05-30 00:00:00','','Y','N','2016-05-30 23:43:53','2016-06-01 21:46:37','','<h1 class=\"text-head\" data-ix=\"slider-animation-text\">Makeup <strong>Design</strong></h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\n<h5 class=\"text-h5\">ТВОРЧЕСКАЯ ПРОФЕССИЯ ЗА ДВЕ НЕДЕЛИ.</h5>\n<a class=\"w-button button-line\" href=\"/makeup\">ПОДРОБНЕЕ</a></div>'),(6,5,'style','2016-05-31 00:00:00','','Y','N','2016-05-31 00:00:28','0000-00-00 00:00:00','','<h1 class=\"text-head\" data-ix=\"slider-animation-text\">Стилистика</h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\"><a class=\"w-button button-line stylistics-but\" href=\"/admin/stylistics.html\">ПОДРОБНЕЕ</a></div>'),(5,4,'curle','2016-05-30 00:00:00','','Y','N','2016-05-30 23:57:36','0000-00-00 00:00:00','','<h1 class=\"text-head hollywood-curls-title\" data-ix=\"slider-animation-text\">Голливудские <strong>локоны</strong></h1>\n<div class=\"text-block-2\" data-ix=\"slider-animation-text\">\n<h5 class=\"text-h5\">Вы научитесь создавать голливудские волны и стильные звездные укладки!</h5>\n<a class=\"w-button button-line\" href=\"/admin/hollywood-curls.html\">ПОДРОБНЕЕ</a></div>');
/*!40000 ALTER TABLE `mce_slider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_tags_values`
--

DROP TABLE IF EXISTS `mce_tags_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_tags_values` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `moduleId` int(10) NOT NULL,
  `order` int(10) NOT NULL,
  `tagId` int(10) NOT NULL,
  `value` varchar(1024) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_tags_values`
--

LOCK TABLES `mce_tags_values` WRITE;
/*!40000 ALTER TABLE `mce_tags_values` DISABLE KEYS */;
INSERT INTO `mce_tags_values` VALUES (1,'',3,0,1,'Уран-235','','2016-05-22 16:41:08','2016-05-22 16:58:38','Y'),(2,'',3,0,2,'Общая длина кисти 185 мм, длина ворса 45 мм, ширина (самая широкая часть кисточки) 35 мм','','2016-05-22 16:49:13','2016-05-22 16:57:16','Y'),(3,'',3,0,3,'Натуральный (драная коза) + синтетический','','2016-05-22 16:49:42','2016-05-22 17:00:55','Y'),(4,'',3,0,4,'Дерево, цвет: серобуромалиновый','','2016-05-22 16:50:09','2016-05-22 17:01:46','Y');
/*!40000 ALTER TABLE `mce_tags_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mce_users`
--

DROP TABLE IF EXISTS `mce_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mce_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) NOT NULL DEFAULT '',
  `passwd` varchar(128) NOT NULL,
  `status` enum('super','admin','manager','client','guest') NOT NULL DEFAULT 'guest',
  `firstName` varchar(255) NOT NULL DEFAULT '',
  `lastName` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `anons` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `company` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mce_users`
--

LOCK TABLES `mce_users` WRITE;
/*!40000 ALTER TABLE `mce_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `mce_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-02 20:42:53
