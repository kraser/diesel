-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: work
-- ------------------------------------------------------
-- Server version	5.5.41-0ubuntu0.14.04.1-log

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
-- Table structure for table `bm_admin_users`
--

DROP TABLE IF EXISTS `bm_admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  `type` enum('a','m') NOT NULL DEFAULT 'm',
  `access` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `post` varchar(255) NOT NULL,
  `email` varchar(128) NOT NULL,
  `lastenter` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_admin_users`
--

LOCK TABLES `bm_admin_users` WRITE;
/*!40000 ALTER TABLE `bm_admin_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_articles`
--

DROP TABLE IF EXISTS `bm_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_articles`
--

LOCK TABLES `bm_articles` WRITE;
/*!40000 ALTER TABLE `bm_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_autocomplete`
--

DROP TABLE IF EXISTS `bm_autocomplete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_autocomplete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tableName` varchar(100) NOT NULL,
  `fieldName` varchar(100) NOT NULL,
  `whereFields` varchar(500) NOT NULL DEFAULT '' COMMENT 'поля,через запятую без пробелов, для однозначной идентификации записи-записей, для которых хранятся значения списка в values',
  `whereValues` varchar(500) NOT NULL DEFAULT '' COMMENT 'поля,через запятую без пробелов, для однозначной идентификации записи-записей, для которых хранятся значения списка в values',
  `autocompleteValues` varchar(1000) NOT NULL DEFAULT '' COMMENT 'значения для списка, перечень, через запятую без пробелов\n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_autocomplete`
--

LOCK TABLES `bm_autocomplete` WRITE;
/*!40000 ALTER TABLE `bm_autocomplete` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_autocomplete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_blocks`
--

DROP TABLE IF EXISTS `bm_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_blocks`
--

LOCK TABLES `bm_blocks` WRITE;
/*!40000 ALTER TABLE `bm_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_blog`
--

DROP TABLE IF EXISTS `bm_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_blog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
-- Dumping data for table `bm_blog`
--

LOCK TABLES `bm_blog` WRITE;
/*!40000 ALTER TABLE `bm_blog` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_blog_topics`
--

DROP TABLE IF EXISTS `bm_blog_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_blog_topics` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(8) unsigned NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(100) NOT NULL DEFAULT '',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  `types` longtext NOT NULL,
  `cases` text NOT NULL,
  `rate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `top` (`top`),
  KEY `order` (`order`),
  KEY `deleted` (`deleted`),
  KEY `show` (`show`,`deleted`),
  KEY `show_2` (`show`),
  KEY `rate` (`rate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_blog_topics`
--

LOCK TABLES `bm_blog_topics` WRITE;
/*!40000 ALTER TABLE `bm_blog_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_blog_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_catalog_tags`
--

DROP TABLE IF EXISTS `bm_catalog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_catalog_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `tagType` enum('INTERVAL','SET','ENUM') NOT NULL DEFAULT 'SET',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_catalog_tags`
--

LOCK TABLES `bm_catalog_tags` WRITE;
/*!40000 ALTER TABLE `bm_catalog_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_catalog_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_comments`
--

DROP TABLE IF EXISTS `bm_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` char(32) NOT NULL,
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
-- Dumping data for table `bm_comments`
--

LOCK TABLES `bm_comments` WRITE;
/*!40000 ALTER TABLE `bm_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_content`
--

DROP TABLE IF EXISTS `bm_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(10) unsigned NOT NULL,
  `order` int(10) NOT NULL,
  `nav` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `anons2` text NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  `module` varchar(32) NOT NULL,
  `template` varchar(63) NOT NULL DEFAULT 'page.php',
  `showmenu` enum('Y','N') NOT NULL DEFAULT 'N',
  `show` enum('Y','N') NOT NULL DEFAULT 'N',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_content`
--

LOCK TABLES `bm_content` WRITE;
/*!40000 ALTER TABLE `bm_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_content_extra_fields`
--

DROP TABLE IF EXISTS `bm_content_extra_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_content_extra_fields` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `content` int(4) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_content_extra_fields`
--

LOCK TABLES `bm_content_extra_fields` WRITE;
/*!40000 ALTER TABLE `bm_content_extra_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_content_extra_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_data`
--

DROP TABLE IF EXISTS `bm_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_data`
--

LOCK TABLES `bm_data` WRITE;
/*!40000 ALTER TABLE `bm_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_forms`
--

DROP TABLE IF EXISTS `bm_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
-- Dumping data for table `bm_forms`
--

LOCK TABLES `bm_forms` WRITE;
/*!40000 ALTER TABLE `bm_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_forms_fields`
--

DROP TABLE IF EXISTS `bm_forms_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_forms_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` int(10) unsigned NOT NULL,
  `type` enum('text','textarea','checkbox','select','date') NOT NULL DEFAULT 'text',
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
-- Dumping data for table `bm_forms_fields`
--

LOCK TABLES `bm_forms_fields` WRITE;
/*!40000 ALTER TABLE `bm_forms_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_forms_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_gallery`
--

DROP TABLE IF EXISTS `bm_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `images_ids` varchar(255) NOT NULL DEFAULT '' COMMENT 'Ссылки (через запятую) на ИД привязанных к записи изображений в таблице xx_images_storage, первый ид - "главной" картинки',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_gallery`
--

LOCK TABLES `bm_gallery` WRITE;
/*!40000 ALTER TABLE `bm_gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_golos`
--

DROP TABLE IF EXISTS `bm_golos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_golos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `golos_quest` text NOT NULL,
  `golos_answer` text NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_golos`
--

LOCK TABLES `bm_golos` WRITE;
/*!40000 ALTER TABLE `bm_golos` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_golos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_golos_detail`
--

DROP TABLE IF EXISTS `bm_golos_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_golos_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `golos_id` int(11) NOT NULL COMMENT 'Ссылка на голосование в таблицу bm_golos',
  `order` int(11) NOT NULL COMMENT 'Пункт голосования по порядку',
  `quest` text NOT NULL COMMENT 'Вопрос',
  `answers` int(11) NOT NULL COMMENT 'Количество ответов',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_golos_detail`
--

LOCK TABLES `bm_golos_detail` WRITE;
/*!40000 ALTER TABLE `bm_golos_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_golos_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_images`
--

DROP TABLE IF EXISTS `bm_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `alter_key` varchar(64) NOT NULL,
  `main` enum('Y','N') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_images`
--

LOCK TABLES `bm_images` WRITE;
/*!40000 ALTER TABLE `bm_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_images_storage`
--

DROP TABLE IF EXISTS `bm_images_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_images_storage` (
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
-- Dumping data for table `bm_images_storage`
--

LOCK TABLES `bm_images_storage` WRITE;
/*!40000 ALTER TABLE `bm_images_storage` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_images_storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_issuepoints`
--

DROP TABLE IF EXISTS `bm_issuepoints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_issuepoints` (
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
-- Dumping data for table `bm_issuepoints`
--

LOCK TABLES `bm_issuepoints` WRITE;
/*!40000 ALTER TABLE `bm_issuepoints` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_issuepoints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_mediafiles`
--

DROP TABLE IF EXISTS `bm_mediafiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_mediafiles` (
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
-- Dumping data for table `bm_mediafiles`
--

LOCK TABLES `bm_mediafiles` WRITE;
/*!40000 ALTER TABLE `bm_mediafiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_mediafiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_migration`
--

DROP TABLE IF EXISTS `bm_migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stamp` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='Миграции';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_migration`
--

LOCK TABLES `bm_migration` WRITE;
/*!40000 ALTER TABLE `bm_migration` DISABLE KEYS */;
INSERT INTO `bm_migration` VALUES (1,'1419143590'),(2,'1419185961'),(3,'1419186182'),(4,'1419186313'),(5,'1419188132'),(6,'1419188208'),(7,'1419188335'),(8,'1419188630'),(9,'1419189405'),(10,'1419189644'),(11,'1419189786'),(12,'1419189886'),(13,'1419190016'),(14,'1419190678'),(15,'1419190766'),(16,'1419191505'),(17,'1419191591'),(18,'1419191737'),(19,'1419191777'),(20,'1419191927'),(21,'1419191970'),(22,'1419192204'),(23,'1419192982'),(24,'1419193077'),(25,'1419193415'),(26,'1422081926');
/*!40000 ALTER TABLE `bm_migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_news`
--

DROP TABLE IF EXISTS `bm_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_news`
--

LOCK TABLES `bm_news` WRITE;
/*!40000 ALTER TABLE `bm_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_portfolio`
--

DROP TABLE IF EXISTS `bm_portfolio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_portfolio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nav` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  `worked` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_portfolio`
--

LOCK TABLES `bm_portfolio` WRITE;
/*!40000 ALTER TABLE `bm_portfolio` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_portfolio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_portfolio_images`
--

DROP TABLE IF EXISTS `bm_portfolio_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_portfolio_images` (
  `image_id` int(10) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_portfolio_images`
--

LOCK TABLES `bm_portfolio_images` WRITE;
/*!40000 ALTER TABLE `bm_portfolio_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_portfolio_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_portfolio_topics`
--

DROP TABLE IF EXISTS `bm_portfolio_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_portfolio_topics` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(8) unsigned NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(100) NOT NULL DEFAULT '',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  `types` longtext NOT NULL,
  `cases` text NOT NULL,
  `rate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `top` (`top`),
  KEY `order` (`order`),
  KEY `deleted` (`deleted`),
  KEY `show` (`show`,`deleted`),
  KEY `show_2` (`show`),
  KEY `rate` (`rate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_portfolio_topics`
--

LOCK TABLES `bm_portfolio_topics` WRITE;
/*!40000 ALTER TABLE `bm_portfolio_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_portfolio_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_prices`
--

DROP TABLE IF EXISTS `bm_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `link` varchar(500) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_prices`
--

LOCK TABLES `bm_prices` WRITE;
/*!40000 ALTER TABLE `bm_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products`
--

DROP TABLE IF EXISTS `bm_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nav` varchar(100) NOT NULL,
  `brand` int(11) NOT NULL,
  `price` float(11,2) NOT NULL,
  `currency` int(11) NOT NULL,
  `unit` varchar(10) DEFAULT '',
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `text` text NOT NULL,
  `types` longtext NOT NULL,
  `is_action` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_featured` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_lider` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_exist` enum('Y','N') NOT NULL DEFAULT 'Y',
  `availability` text NOT NULL,
  `relations` varchar(255) NOT NULL,
  `rate` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `show` (`show`),
  KEY `deleted` (`deleted`),
  KEY `top` (`top`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products`
--

LOCK TABLES `bm_products` WRITE;
/*!40000 ALTER TABLE `bm_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_brands`
--

DROP TABLE IF EXISTS `bm_products_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
-- Dumping data for table `bm_products_brands`
--

LOCK TABLES `bm_products_brands` WRITE;
/*!40000 ALTER TABLE `bm_products_brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_products_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_currencies`
--

DROP TABLE IF EXISTS `bm_products_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(5) NOT NULL,
  `symbol` varchar(10) NOT NULL DEFAULT '',
  `value` float NOT NULL DEFAULT '1',
  `symbol_position` enum('left','right') NOT NULL DEFAULT 'right',
  `is_main` enum('Y','N') NOT NULL DEFAULT 'N',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products_currencies`
--

LOCK TABLES `bm_products_currencies` WRITE;
/*!40000 ALTER TABLE `bm_products_currencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_products_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_tags_values`
--

DROP TABLE IF EXISTS `bm_products_tags_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_tags_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `tagType` enum('ENUM','INTERVAL','SET') DEFAULT 'SET' COMMENT 'Тип характеристики при подборе по характеристикам: ''ENUM'' - выбор одного значения, ''INTERVAL'' - от/до, ''SET'' - выбор нескольких значений',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products_tags_values`
--

LOCK TABLES `bm_products_tags_values` WRITE;
/*!40000 ALTER TABLE `bm_products_tags_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_products_tags_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_topics`
--

DROP TABLE IF EXISTS `bm_products_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_topics` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `top` int(8) unsigned NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nav` varchar(100) NOT NULL DEFAULT '',
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  `types` longtext NOT NULL,
  `cases` text NOT NULL,
  `rate` int(11) NOT NULL,
  `isModel` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'флаг отнесения товаров категории к одной модели',
  PRIMARY KEY (`id`),
  KEY `top` (`top`),
  KEY `order` (`order`),
  KEY `deleted` (`deleted`),
  KEY `show` (`show`,`deleted`),
  KEY `show_2` (`show`),
  KEY `rate` (`rate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products_topics`
--

LOCK TABLES `bm_products_topics` WRITE;
/*!40000 ALTER TABLE `bm_products_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_products_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_products_topics_tags`
--

DROP TABLE IF EXISTS `bm_products_topics_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_products_topics_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `unit` varchar(10) DEFAULT '',
  `default` varchar(255) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_products_topics_tags`
--

LOCK TABLES `bm_products_topics_tags` WRITE;
/*!40000 ALTER TABLE `bm_products_topics_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_products_topics_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_question`
--

DROP TABLE IF EXISTS `bm_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `person` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `anons` text NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_question`
--

LOCK TABLES `bm_question` WRITE;
/*!40000 ALTER TABLE `bm_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_seo`
--

DROP TABLE IF EXISTS `bm_seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_seo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `module_id` int(11) NOT NULL,
  `module_table` varchar(64) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `keywords` varchar(2048) NOT NULL,
  `description` varchar(2048) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_seo`
--

LOCK TABLES `bm_seo` WRITE;
/*!40000 ALTER TABLE `bm_seo` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_settings`
--

DROP TABLE IF EXISTS `bm_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `callname` varchar(128) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_settings`
--

LOCK TABLES `bm_settings` WRITE;
/*!40000 ALTER TABLE `bm_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_orders`
--

DROP TABLE IF EXISTS `bm_shop_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `paymethod` int(11) NOT NULL,
  `shopId` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_orders`
--

LOCK TABLES `bm_shop_orders` WRITE;
/*!40000 ALTER TABLE `bm_shop_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shop_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_orders_items`
--

DROP TABLE IF EXISTS `bm_shop_orders_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_orders_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product` int(11) NOT NULL,
  `brand` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `top` int(11) NOT NULL,
  `price` float(11,2) NOT NULL,
  `count` int(11) NOT NULL,
  `total_fake` float(11,2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_orders_items`
--

LOCK TABLES `bm_shop_orders_items` WRITE;
/*!40000 ALTER TABLE `bm_shop_orders_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shop_orders_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_paymethods`
--

DROP TABLE IF EXISTS `bm_shop_paymethods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_paymethods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_paymethods`
--

LOCK TABLES `bm_shop_paymethods` WRITE;
/*!40000 ALTER TABLE `bm_shop_paymethods` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shop_paymethods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shop_statuses`
--

DROP TABLE IF EXISTS `bm_shop_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shop_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_shop_statuses`
--

LOCK TABLES `bm_shop_statuses` WRITE;
/*!40000 ALTER TABLE `bm_shop_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shop_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_shops`
--

DROP TABLE IF EXISTS `bm_shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_shops` (
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
-- Dumping data for table `bm_shops`
--

LOCK TABLES `bm_shops` WRITE;
/*!40000 ALTER TABLE `bm_shops` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_site_golos`
--

DROP TABLE IF EXISTS `bm_site_golos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_site_golos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_site_golos`
--

LOCK TABLES `bm_site_golos` WRITE;
/*!40000 ALTER TABLE `bm_site_golos` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_site_golos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_slider`
--

DROP TABLE IF EXISTS `bm_slider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_slider` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `link` varchar(255) NOT NULL,
  `show` enum('Y','N') NOT NULL DEFAULT 'Y',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_slider`
--

LOCK TABLES `bm_slider` WRITE;
/*!40000 ALTER TABLE `bm_slider` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_slider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_sorting`
--

DROP TABLE IF EXISTS `bm_sorting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_sorting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `direction` enum('Y','N') NOT NULL DEFAULT 'Y',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_sorting`
--

LOCK TABLES `bm_sorting` WRITE;
/*!40000 ALTER TABLE `bm_sorting` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_sorting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_tags_values`
--

DROP TABLE IF EXISTS `bm_tags_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_tags_values` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_tags_values`
--

LOCK TABLES `bm_tags_values` WRITE;
/*!40000 ALTER TABLE `bm_tags_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_tags_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bm_users`
--

DROP TABLE IF EXISTS `bm_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `passwd` varchar(45) NOT NULL,
  `status` enum('super','admin','manager','client') NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `deleted` enum('Y','N') NOT NULL,
  `anons` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bm_users`
--

LOCK TABLES `bm_users` WRITE;
/*!40000 ALTER TABLE `bm_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `bm_users` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-02-01 22:42:30
