    -- update for timestamp 1464896861
ALTER TABLE `prefix_blog`
ADD COLUMN `link` VARCHAR(255) COMMENT 'Ссылка' AFTER `name`;