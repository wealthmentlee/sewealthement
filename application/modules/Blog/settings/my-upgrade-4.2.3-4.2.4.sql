
ALTER TABLE `engine4_blog_blogs` ADD INDEX `owner_id` (`owner_id`,`draft`);

ALTER TABLE `engine4_blog_blogs` DROP INDEX `search`;

ALTER TABLE `engine4_blog_blogs` ADD INDEX `search` (`search`, `creation_date`);

ALTER TABLE `engine4_blog_blogs` ADD INDEX `draft` (`draft`, `search`);

ALTER TABLE `engine4_blog_categories` ADD INDEX `category_id` (`category_id`,`category_name`);

ALTER TABLE `engine4_blog_categories` ADD INDEX `category_name` (`category_name`);
