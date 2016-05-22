CREATE TABLE IF NOT EXISTS `prefix_shop_orders_items`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `product` INTEGER(11) NOT NULL,
    `brand` INTEGER(11) NOT NULL,
    `order` INTEGER(11) NOT NULL,
    `featureId` INTEGER(11) NOT NULL DEFAULT 0 COMMENT 'Id выбранной характеристики товара',
    `featureValue` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Значение выбранной характеристики товара',
    `name` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `top` INTEGER(11) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `count` INTEGER(11) NOT NULL,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Суммарная стоимость товара',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
