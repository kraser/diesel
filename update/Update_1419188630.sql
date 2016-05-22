-- upadte for timestamp 1419188630
CREATE TABLE IF NOT EXISTS `prefix_autocomplete`
(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tableName` VARCHAR(100) NOT NULL,
    `fieldName` VARCHAR(100) NOT NULL,
    `whereFields` VARCHAR(500) NOT NULL DEFAULT '' COMMENT 'поля,через запятую без пробелов, для однозначной идентификации записи-записей, для которых хранятся значения списка в values',
    `whereValues` VARCHAR(500) NOT NULL DEFAULT '' COMMENT 'поля,через запятую без пробелов, для однозначной идентификации записи-записей, для которых хранятся значения списка в values',
    `autocompleteValues` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT 'значения для списка, перечень, через запятую без пробелов\n',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;