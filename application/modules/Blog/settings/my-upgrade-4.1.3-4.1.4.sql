
CREATE TABLE IF NOT EXISTS `engine4_blog_subscriptions` (
  `subscription_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `subscriber_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`subscription_id`),
  UNIQUE KEY `user_id` (`user_id`,`subscriber_user_id`),
  KEY `subscriber_user_id` (`subscriber_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('blog_subscribed_new', 'blog', '{item:$subject} has posted a new blog entry: {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_blog_subscribed_new', 'blog', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('blog_gutter_subscribe', 'blog', 'Subscribe', 'Blog_Plugin_Menus', '{"route":"default","module":"blog","controller":"subscription","action":"add","class":"buttonlink smoothbox icon_blog_subscribe"}', 'blog_gutter', '', 8),
('mobi_browse_blog', 'blog', 'Blogs', '', '{"route":"blog_general"}', 'mobi_browse', '', 3);
