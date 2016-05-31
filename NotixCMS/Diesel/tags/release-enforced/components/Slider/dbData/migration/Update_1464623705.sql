-- update for timestamp 1464623705
ALTER TABLE `prefix_slider`
ADD COLUMN `order` INTEGER (11) NOT NULL DEFAULT 0 COMMENT 'Порядковый номер слайда' AFTER `id`;