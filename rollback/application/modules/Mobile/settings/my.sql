
DELETE FROM `engine4_core_modules` WHERE `name`='mobile';

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('mobile', 'Mobile', 'Mobile', '4.1.8p7', 1, 'extra');