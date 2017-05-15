-- update for timestamp 1470562629
ALTER TABLE `prefix_users`
MODIFY `login` VARCHAR(64) NOT NULL COMMENT 'Логин пользователя',
MODIFY `anons` VARCHAR(255) COMMENT 'Краткая характеристика - анонс пользователя',
MODIFY `description` TEXT COMMENT 'Сопроводительный текст пользователя',
MODIFY `company` VARCHAR(255) COMMENT 'Компания пользователя для юр.лиц',
MODIFY `name` VARCHAR(255) COMMENT 'Название компании',
MODIFY `created` DATETIME,
MODIFY `modified` DATETIME;