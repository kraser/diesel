-- update for timestamp 1471187493
ALTER TABLE `prefix_menu`
MODIFY `module` VARCHAR(32) NOT NULL DEFAULT 'Content' COMMENT 'Модуль, исполняющий пункт меню',
MODIFY `template` VARCHAR(63) NOT NULL DEFAULT 'mainpage' COMMENT 'Шаблон',
MODIFY `created` DATETIME,
MODIFY `modified` DATETIME;