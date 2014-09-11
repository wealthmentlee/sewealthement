--
-- Update module Welcome
--

UPDATE `engine4_core_modules` SET `version` = '4.1.1p1'  WHERE `name` = 'welcome';

UPDATE `engine4_core_menuitems` SET `label` = 'HE - Welcome', `order` = 888 WHERE `name` = 'core_admin_main_plugins_welcome';