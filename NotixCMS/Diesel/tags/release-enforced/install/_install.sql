
CREATE TABLE IF NOT EXISTS `bm_question` (
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

CREATE TABLE IF NOT EXISTS `bm_golos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `golos_quest` text NOT NULL,
  `golos_answer` text NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


