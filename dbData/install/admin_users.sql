CREATE TABLE IF NOT EXISTS `prefix_admin_users`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID пользователя',
    `login` VARCHAR(32) NOT NULL COMMENT 'Логин администратора',
    `password` VARCHAR(128) NOT NULL COMMENT 'Пароль пользователя (хэш)',
    `type` ENUM('a','m') NOT NULL DEFAULT 'm' COMMENT 'Статус администратора',
    `access` TEXT NOT NULL COMMENT 'Список доступных модулей',
    `name` VARCHAR(255) NOT NULL COMMENT 'Имя администратора',
    `post` VARCHAR(255) NOT NULL COMMENT 'Должность администратора',
    `email` VARCHAR(128) NOT NULL COMMENT 'E-mail администратора',
    `lastenter` DATETIME NOT NULL COMMENT 'Время последнего входа администратора в систему',
    `created` DATETIME NOT NULL COMMENT 'Время создания записи',
    `modified` DATETIME NOT NULL COMMENT 'Время последней модификации записи',
    PRIMARY KEY (`id`),
    UNIQUE KEY `login` (`login`)
)
ENGINE=MyISAM DEFAULT CHARSET=UTF8 COMMENT 'Таблица администраторов';
