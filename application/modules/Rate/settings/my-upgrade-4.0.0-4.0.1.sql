-- --------------------------------------------------------

--
-- Update module Rate
--

UPDATE `engine4_core_modules` SET `version` = '4.0.1' WHERE `name` = 'rate';

UPDATE `engine4_core_menuitems` SET `order` = 4 WHERE `name` = 'rate_admin_main_faq';

CREATE TABLE IF NOT EXISTS `engine4_rate_types` (
  `type_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `label` varchar(50) collate utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type_id`),
  UNIQUE KEY `category_id` (`category_id`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_rate_votes` (
  `vote_id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '0',
  `review_id` int(11) NOT NULL default '0',
  `page_id` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `creation_date` date NOT NULL,
  PRIMARY KEY  (`vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_rate_pagereviews` (
  `pagereview_id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `user_id` int(11) default '0',
  `title` varchar(90) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  `creation_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`pagereview_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('rate_admin_main_review', 'rate', 'RATE_REVIEW_MENUITEM', 'Rate_Plugin_Menus::canReviewManage', '{"route":"admin_default","module":"rate","controller":"types"}', 'rate_admin_main', '', 1, 0, 3);

INSERT IGNORE INTO `engine4_activity_actiontypes` VALUES
('pagereview_new', 'rate', '{item:$subject} posted review {var:$link}: {body:$body}', 1, 3, 2, 1, 1, 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('post_pagereview', 'activity', '{item:$subject} posted review {item:$object:$label}.', 0, ''),
('rated', 'activity', '{item:$subject} rated {item:$object:$label}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_post_pagereview', 'activity', '[host],[email],[recipient_title],[object_title],[object_link],[object_parent_title],[object_parent_link]');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'rate' as `type`,
    'reviewcreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('rate.reviewteamremove', '1');