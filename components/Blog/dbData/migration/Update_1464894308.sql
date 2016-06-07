-- update for timestamp 1464894308
ALTER TABLE `prefix_blog_topics`
ADD COLUMN `template` VARCHAR(255) COMMENT 'Имя шаблона' AFTER `name`;