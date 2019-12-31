-- upadte for timestamp 1419189786
CREATE TABLE IF NOT EXISTS `prefix_portfolio_images`
(
    `image_id` INT(10) NOT NULL AUTO_INCREMENT,
    `order` INT(11) NOT NULL,
    `description` TEXT NOT NULL,
    PRIMARY KEY (`image_id`)
)
ENGINE=MyISAM  DEFAULT CHARSET=utf8;