-- upadte for timestamp 1419189405
CREATE TABLE IF NOT EXISTS `prefix_golos_detail`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `golos_id` INT NOT NULL COMMENT 'Ссылка на голосование в таблицу bm_golos',
    `order` INT NOT NULL COMMENT 'Пункт голосования по порядку',
    `quest` TEXT NOT NULL COMMENT 'Вопрос',
    `answers` INT NOT NULL COMMENT 'Количество ответов',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
