INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'like_user' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'like_group' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'like_event' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'like_page' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'interest' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels`;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'auth_interest' as `name`,
    5 as `value`,
    '["everyone", "registered", "owner_network", "owner_member_member", "owner_member", "owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`)
  SELECT
    'user' AS `resource_type`,
    `user_id` AS `resource_id`,
    'interest' AS `action`,
    'everyone' AS `role`,
    0 AS `role_id`,
    1 AS `value`,
    null AS `params`
  FROM `engine4_users`;

INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`)
  SELECT
    'user' AS `resource_type`,
    `user_id` AS `resource_id`,
    'interest' AS `action`,
    'registered' AS `role`,
    0 AS `role_id`,
    1 AS `value`,
    null AS `params`
  FROM `engine4_users`;

INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`)
  SELECT
    'user' AS `resource_type`,
    `user_id` AS `resource_id`,
    'interest' AS `action`,
    'owner_member' AS `role`,
    0 AS `role_id`,
    1 AS `value`,
    null AS `params`
  FROM `engine4_users`;

INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`)
  SELECT
    'user' AS `resource_type`,
    `user_id` AS `resource_id`,
    'interest' AS `action`,
    'owner_network' AS `role`,
    0 AS `role_id`,
    1 AS `value`,
    null AS `params`
  FROM `engine4_users`;

INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`)
  SELECT
    'user' AS `resource_type`,
    `user_id` AS `resource_id`,
    'interest' AS `action`,
    'owner_member_member' AS `role`,
    0 AS `role_id`,
    1 AS `value`,
    null AS `params`
  FROM `engine4_users`;

INSERT IGNORE INTO `engine4_core_likes` (`resource_type`, `resource_id`, `poster_type`, `poster_id`)
  SELECT
    `engine4_like_clubs`.`object` as `resource_type`,
    `engine4_like_clubs`.`object_id` as `resource_id`,
    'user' as `poster_type`,
    `engine4_likes`.`user_id` as `poster_id`
  FROM `engine4_likes`
  LEFT JOIN `engine4_like_clubs`
  ON (`engine4_likes`.`like_club_id` = `engine4_like_clubs`.`club_id`)
  LEFT JOIN `engine4_core_likes`
  ON (`engine4_core_likes`.`resource_type` = `engine4_like_clubs`.`object`
    AND `engine4_core_likes`.`resource_id` = `engine4_like_clubs`.`object_id`)
  WHERE `engine4_core_likes`.`resource_type` IS NULL
    AND `engine4_core_likes`.`resource_id` IS NULL
    AND `engine4_core_likes`.`poster_type` IS NULL
    AND `engine4_core_likes`.`poster_id` IS NULL;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_item', 'like', '{var:$content}', 1, 6, 0, 1, 1, 0);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_item_private', 'like', '{var:$content}', 1, 1, 0, 1, 1, 0);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('user_edit_interests', 'like', 'like_Profile Interests', 'Like_Plugin_Menus', '{"route":"like_interests","action":"index"}', 'user_edit', '', 1, 0, 4);

UPDATE `engine4_core_modules` SET `version` = '4.0.2'  WHERE `name` = 'like';