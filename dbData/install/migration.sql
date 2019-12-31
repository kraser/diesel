CREATE TABLE IF NOT EXISTS `prefix_migration`
(
    `id` INTEGER(11)  NOT NULL AUTO_INCREMENT,
    `stamp` VARCHAR(20)  NOT NULL COMMENT 'Время создания миграции',
    `module` VARCHAR(100)  NOT NULL DEFAULT '' COMMENT 'Флаг установки таблиц для модуля',
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM DEFAULT CHARSET=UTF8 COMMENT = 'Миграции';