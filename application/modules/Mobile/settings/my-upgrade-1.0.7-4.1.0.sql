DELETE FROM `engine4_core_modules` WHERE `name`='mobile';
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('mobile', 'Mobile', 'Mobile', '4.1.0', 1, 'extra');

INSERT IGNORE INTO `engine4_core_content` ( `page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(2, 'widget', 'mobile.mode-switcher', 200, 999, '{"standard":"Standard Site","mobile":"Mobile Site"}', NULL);

DELETE FROM `engine4_core_menuitems` WHERE `module`= 'mobile';
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_mobile', 'mobile', 'Mobile', NULL, '{"route":"admin_default","module":"mobile","controller":"content","action":"index"}', 'core_admin_main_plugins', '', 1, 0, 999),
('mobile_admin_main_content', 'mobile', 'Layout Editor', '', '{"route":"admin_default","module":"mobile","controller":"content","action":"index"}', 'mobile_admin_main', '', 1, 0, 1),
('mobile_admin_main_themes', 'mobile', 'Theme Editor', NULL, '{"route":"admin_default","module":"mobile","controller":"themes","action":"index"}', 'mobile_admin_main', NULL, 1, 0, 2);