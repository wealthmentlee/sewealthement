-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: prod
-- ------------------------------------------------------
-- Server version	5.5.34-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `engine4_activity_actions`
--

DROP TABLE IF EXISTS `engine4_activity_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_actions` (
  `action_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `subject_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `object_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `params` text COLLATE utf8_unicode_ci,
  `date` datetime NOT NULL,
  `attachment_count` smallint(3) unsigned NOT NULL DEFAULT '0',
  `comment_count` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `like_count` mediumint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`),
  KEY `SUBJECT` (`subject_type`,`subject_id`),
  KEY `OBJECT` (`object_type`,`object_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_actions`
--

LOCK TABLES `engine4_activity_actions` WRITE;
/*!40000 ALTER TABLE `engine4_activity_actions` DISABLE KEYS */;
INSERT INTO `engine4_activity_actions` VALUES (1,'status','user',1,'user',1,'Test','[]','2014-02-17 18:43:43',0,0,0),(3,'status','user',1,'user',1,'test ','[]','2014-03-04 00:15:14',0,0,0),(5,'signup','user',2,'user',2,'','[]','2014-03-04 16:30:54',0,0,0),(8,'group_join','user',2,'group',1,'','[]','2014-03-04 16:33:11',0,0,0),(9,'signup','user',3,'user',3,'','[]','2014-03-05 02:01:27',0,1,1),(10,'friends','user',1,'user',3,'{item:$object} is now friends with {item:$subject}.','[]','2014-03-05 02:01:45',0,0,0),(11,'friends','user',3,'user',1,'{item:$object} is now friends with {item:$subject}.','[]','2014-03-05 02:01:45',0,0,0),(12,'friends','user',3,'user',2,'{item:$object} is now friends with {item:$subject}.','[]','2014-03-05 18:04:36',0,0,0),(13,'friends','user',2,'user',3,'{item:$object} is now friends with {item:$subject}.','[]','2014-03-05 18:04:36',0,0,0),(14,'post_self','user',1,'user',1,'Test on 3/5. Â Yelp is awesome. ','[]','2014-03-06 04:19:36',1,0,1),(15,'post_self','user',1,'user',1,'Test on 3/5 ','[]','2014-03-06 04:20:08',1,0,0),(17,'status','user',1,'user',1,'Testing ','[]','2014-03-06 04:20:54',0,0,0),(19,'status','user',3,'user',3,'Test from mobile device!','{\"is_mobile\":true}','2014-03-06 17:06:00',0,1,1),(20,'share','user',3,'user',3,'Test of share','{\"type\":\"<a href=\\\"\\/wealthment\\/index.php\\/posts\\/19\\/ershad-jamil\\\">post<\\/a>\"}','2014-03-07 03:39:17',1,0,1),(21,'post_self','user',3,'user',3,'figured out how to create static pages.\n\ntest of uploading an image. ','[]','2014-03-08 16:03:51',1,0,1),(23,'status','user',3,'user',3,'test #what is this. Â ok ','[]','2014-03-09 01:45:22',0,0,1),(24,'status','user',2,'user',2,'test2 #what is this ','[]','2014-03-09 01:53:18',0,0,0),(29,'hequestion_answer','user',3,'hequestion',83,'\r\n\r\n<script type=\"text/javascript\">\r\n  en4.core.runonce.add(function() {\r\n    if( !$type(Hequestion)) {\r\n      Asset.javascript(\'/wealthment/application/modules/Wall/externals/scripts/core.js\');\r\n    }\r\n    Wall.globalBind();\r\n  });\r\n</script>\r\n\r\n\r\n<a  href=\"/wealthment/index.php/question-view/83/cisco\">cisco</a> and <a href=\"/wealthment/index.php/question-view/83/cisco\" class=\"wall_grouped_other\">2 others</a>\r\n<div style=\"display:none;\" class=\"wall_grouped_other_html\">\r\n  <a href=\"/wealthment/index.php/question-view/83/tesla\">tesla</a>, <a href=\"/wealthment/index.php/question-view/83/bank-of-america\">bank of america</a></div>','[]','2014-03-09 03:53:31',0,0,0),(30,'hequestion_answer','user',2,'hequestion',83,'\r\n\r\n<script type=\"text/javascript\">\r\n  en4.core.runonce.add(function() {\r\n    if( !$type(Hequestion)) {\r\n      Asset.javascript(\'/wealthment/application/modules/Wall/externals/scripts/core.js\');\r\n    }\r\n    Wall.globalBind();\r\n  });\r\n</script>\r\n\r\n\r\n<a  href=\"/wealthment/index.php/question-view/83/tesla\">tesla</a> and <a href=\"/wealthment/index.php/question-view/83/bank-of-america\">bank of america</a>\r\n<div style=\"display:none;\" class=\"wall_grouped_other_html\">\r\n  </div>','[]','2014-03-09 22:02:42',0,0,0),(32,'status','user',3,'user',3,'Posting from iPadÂ  ','[]','2014-03-12 02:40:43',0,0,0),(33,'hequestion_ask_self','user',3,'user',3,'','{\"question\":\"<a href=\\\"\\/wealthment_dev\\/index.php\\/question-view\\/84\\/what-is-better-401k-or-ira-or-i\\\">What is better, 401k or IRA or IRA Roth?<\\/a>\"}','2014-03-12 02:44:45',1,0,0),(35,'hequestion_answer','user',1,'hequestion',84,'\r\n\r\n<script type=\"text/javascript\">\r\n  en4.core.runonce.add(function() {\r\n    if( !$type(Hequestion)) {\r\n      Asset.javascript(\'/wealthment_dev/application/modules/Wall/externals/scripts/core.js\');\r\n    }\r\n    Wall.globalBind();\r\n  });\r\n</script>\r\n\r\n\r\n<a  href=\"/wealthment_dev/index.php/question-view/84/401k\">401k</a>\r\n<div style=\"display:none;\" class=\"wall_grouped_other_html\">\r\n  </div>','[]','2014-03-13 04:18:03',0,0,0),(36,'hequestion_answer','user',3,'hequestion',84,'\r\n\r\n<script type=\"text/javascript\">\r\n  en4.core.runonce.add(function() {\r\n    if( !$type(Hequestion)) {\r\n      Asset.javascript(\'/wealthment_dev/application/modules/Wall/externals/scripts/core.js\');\r\n    }\r\n    Wall.globalBind();\r\n  });\r\n</script>\r\n\r\n\r\n<a  href=\"/wealthment_dev/index.php/question-view/84/401k\">401k</a>\r\n<div style=\"display:none;\" class=\"wall_grouped_other_html\">\r\n  </div>','[]','2014-03-13 21:41:39',0,0,0),(37,'share','user',3,'user',2,'Re-posting','{\"type\":\"<a href=\\\"\\/wealthment_dev\\/index.php\\/posts\\/24\\/jeffrey-lee\\\">post<\\/a>\"}','2014-03-14 00:38:41',1,0,0),(38,'status','user',3,'user',3,'test of status','[]','2014-03-14 02:16:31',0,0,0),(40,'status','user',3,'user',3,'Test of Pin Feed ','[]','2014-03-14 16:53:28',0,0,0),(43,'signup','user',4,'user',4,'','[]','2014-03-15 23:32:27',0,0,0),(44,'signup','user',5,'user',5,'','[]','2014-03-16 01:08:37',0,0,0),(45,'status','user',5,'user',5,'test ','[]','2014-03-16 01:22:25',0,0,0),(47,'status','user',3,'user',3,'testing ','[]','2014-03-18 21:55:34',0,0,0),(48,'status','user',3,'user',3,'#test ','[]','2014-03-24 20:15:22',0,0,0),(49,'status','user',3,'user',3,'#test2 ','[]','2014-03-25 02:37:32',0,0,0),(50,'status','user',3,'user',3,'#test2 test ','[]','2014-03-25 02:37:38',0,1,2),(51,'status','user',5,'user',5,':) test ','[]','2014-03-25 20:11:58',0,0,0),(52,'status','user',1,'user',1,'test again ','[]','2014-03-26 14:24:30',0,0,0),(53,'status','user',1,'user',1,'#test 2 test again ','[]','2014-03-26 14:27:51',0,0,0),(54,'status','user',1,'user',1,'testing the #test2 feature again ','[]','2014-03-26 14:46:45',0,0,0),(55,'status','user',1,'user',1,'creating a new #helloÂ  ','[]','2014-03-26 14:46:55',0,0,1),(56,'share','user',3,'user',1,'What does this do','{\"type\":\"<a href=\\\"\\/wealthment\\/index.php\\/posts\\/54\\/wealthment-administrator\\\">post<\\/a>\"}','2014-03-27 13:29:13',1,0,0);
/*!40000 ALTER TABLE `engine4_activity_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_actionsettings`
--

DROP TABLE IF EXISTS `engine4_activity_actionsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_actionsettings` (
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_actionsettings`
--

LOCK TABLES `engine4_activity_actionsettings` WRITE;
/*!40000 ALTER TABLE `engine4_activity_actionsettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_activity_actionsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_actiontypes`
--

DROP TABLE IF EXISTS `engine4_activity_actiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_actiontypes` (
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `displayable` tinyint(1) NOT NULL DEFAULT '3',
  `attachable` tinyint(1) NOT NULL DEFAULT '1',
  `commentable` tinyint(1) NOT NULL DEFAULT '1',
  `shareable` tinyint(1) NOT NULL DEFAULT '1',
  `is_generated` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_actiontypes`
--

LOCK TABLES `engine4_activity_actiontypes` WRITE;
/*!40000 ALTER TABLE `engine4_activity_actiontypes` DISABLE KEYS */;
INSERT INTO `engine4_activity_actiontypes` VALUES ('album_photo_new','album','{item:$subject} added {var:$count} photo(s) to the album {item:$object}:',1,5,1,3,1,1),('comment_album','album','{item:$subject} commented on {item:$owner}\'s {item:$object:album}: {body:$body}',1,1,1,1,1,0),('comment_album_photo','album','{item:$subject} commented on {item:$owner}\'s {item:$object:photo}: {body:$body}',1,1,1,1,1,0),('comment_hequestion','hequestion','{item:$subject} commented on {item:$owner}\'s {item:$object:question}: {body:$body}.',1,1,1,1,1,1),('friends','user','{item:$subject} is now friends with {item:$object}.',1,3,0,1,1,1),('friends_follow','user','{item:$subject} is now following {item:$object}.',1,3,0,1,1,1),('group_create','group','{item:$subject} created a new group:',1,5,1,1,1,1),('group_join','group','{item:$subject} joined the group {item:$object}',1,3,1,1,1,1),('group_photo_upload','group','{item:$subject} added {var:$count} photo(s).',1,3,2,1,1,1),('group_promote','group','{item:$subject} has been made an officer for the group {item:$object}',1,3,1,1,1,1),('group_topic_create','group','{item:$subject} posted a {itemChild:$object:topic:$child_id} in the group {item:$object}: {body:$body}',1,3,1,1,1,1),('group_topic_reply','group','{item:$subject} replied to a {itemChild:$object:topic:$child_id} in the group {item:$object}: {body:$body}',1,3,1,1,1,1),('hequestion_answer','hequestion','{item:$subject} answered {item:$object} with {var:$body}',1,7,1,0,0,1),('hequestion_ask','hequestion','{actors:$subject:$object} asked {var:$question}',1,7,1,0,1,1),('hequestion_ask_self','hequestion','{item:$subject} asked {var:$question}',1,7,1,0,1,1),('like_item','like','{var:$content}',1,6,0,1,1,0),('like_item_private','like','{var:$content}',1,1,0,1,1,0),('login','user','{item:$subject} has signed in.',0,1,0,1,1,1),('logout','user','{item:$subject} has signed out.',0,1,0,1,1,1),('network_join','network','{item:$subject} joined the network {item:$object}',1,3,1,1,1,1),('post','user','{actors:$subject:$object}: {body:$body}',1,7,1,1,1,0),('post_self','user','{item:$subject} {body:$body}',1,5,1,1,1,0),('profile_photo_update','user','{item:$subject} has added a new profile photo.',1,5,1,1,1,1),('share','activity','{item:$subject} shared {item:$object}\'s {var:$type}. {body:$body}',1,5,1,1,0,1),('signup','user','{item:$subject} has just signed up. Say hello!',1,5,0,1,1,1),('status','user','{item:$subject} {body:$body}',1,5,0,1,4,0),('tagged','user','{item:$subject} tagged {item:$object} in a {var:$label}:',1,7,1,1,0,1);
/*!40000 ALTER TABLE `engine4_activity_actiontypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_attachments`
--

DROP TABLE IF EXISTS `engine4_activity_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_attachments` (
  `attachment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int(11) unsigned NOT NULL,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `id` int(11) unsigned NOT NULL,
  `mode` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`attachment_id`),
  KEY `action_id` (`action_id`),
  KEY `type_id` (`type`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_attachments`
--

LOCK TABLES `engine4_activity_attachments` WRITE;
/*!40000 ALTER TABLE `engine4_activity_attachments` DISABLE KEYS */;
INSERT INTO `engine4_activity_attachments` VALUES (4,14,'album_photo',1,1),(5,15,'core_link',2,1),(6,20,'activity_action',19,1),(7,21,'album_photo',2,1),(10,33,'hequestion',84,1),(11,37,'activity_action',24,1),(12,56,'activity_action',54,1);
/*!40000 ALTER TABLE `engine4_activity_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_comments`
--

DROP TABLE IF EXISTS `engine4_activity_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_comments` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `resource_type` (`resource_id`),
  KEY `poster_type` (`poster_type`,`poster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_comments`
--

LOCK TABLES `engine4_activity_comments` WRITE;
/*!40000 ALTER TABLE `engine4_activity_comments` DISABLE KEYS */;
INSERT INTO `engine4_activity_comments` VALUES (2,9,'user',2,'Welcome Ershad','2014-03-05 19:39:01',0),(3,19,'user',3,'Comment over the box.. need to look at this.. CSS or whatever to fix','2014-03-07 03:39:00',0),(4,50,'user',1,'test','2014-03-27 13:26:27',0);
/*!40000 ALTER TABLE `engine4_activity_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_likes`
--

DROP TABLE IF EXISTS `engine4_activity_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_likes` (
  `like_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`like_id`),
  KEY `resource_id` (`resource_id`),
  KEY `poster_type` (`poster_type`,`poster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_likes`
--

LOCK TABLES `engine4_activity_likes` WRITE;
/*!40000 ALTER TABLE `engine4_activity_likes` DISABLE KEYS */;
INSERT INTO `engine4_activity_likes` VALUES (2,9,'user',2),(4,19,'user',3),(6,20,'user',1),(7,14,'user',1),(8,23,'user',3),(9,21,'user',3),(11,50,'user',3),(12,55,'user',1),(13,50,'user',1);
/*!40000 ALTER TABLE `engine4_activity_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_notifications`
--

DROP TABLE IF EXISTS `engine4_activity_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_notifications` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `subject_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `object_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `params` text COLLATE utf8_unicode_ci,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `mitigated` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `LOOKUP` (`user_id`,`date`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `object` (`object_type`,`object_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_notifications`
--

LOCK TABLES `engine4_activity_notifications` WRITE;
/*!40000 ALTER TABLE `engine4_activity_notifications` DISABLE KEYS */;
INSERT INTO `engine4_activity_notifications` VALUES (3,3,'user',1,'user',3,'friend_request',NULL,1,1,'2014-03-05 02:01:27'),(4,1,'user',3,'user',1,'friend_accepted',NULL,1,0,'2014-03-05 02:01:45'),(5,2,'user',3,'user',2,'friend_request',NULL,1,1,'2014-03-05 02:02:21'),(6,3,'user',2,'user',3,'friend_accepted',NULL,1,0,'2014-03-05 18:04:36'),(7,3,'user',2,'activity_action',9,'liked','{\"label\":\"post\"}',1,0,'2014-03-05 19:38:51'),(8,3,'user',2,'activity_action',9,'commented','{\"label\":\"post\"}',1,0,'2014-03-05 19:39:01'),(9,3,'user',1,'activity_action',17,'wall_tag','[\"\"]',1,0,'2014-03-06 04:20:54'),(12,3,'user',1,'activity_action',20,'liked','{\"label\":\"post\"}',1,0,'2014-03-07 20:50:53'),(15,3,'user',2,'hequestion',83,'hequestion_answer',NULL,1,0,'2014-03-09 22:02:42'),(16,2,'user',3,'messages_conversation',1,'message_new',NULL,0,0,'2014-03-09 22:56:57'),(20,1,'user',3,'messages_conversation',2,'message_new',NULL,1,0,'2014-03-12 00:35:36'),(22,3,'user',1,'hequestion',84,'hequestion_answer',NULL,1,0,'2014-03-13 04:18:03'),(23,2,'user',3,'activity_action',37,'shared','{\"label\":\"post\"}',0,0,'2014-03-14 00:38:41'),(24,3,'user',1,'messages_conversation',2,'message_new',NULL,1,0,'2014-03-24 16:25:08'),(25,3,'user',1,'activity_action',50,'liked','{\"label\":\"post\"}',1,0,'2014-03-27 13:26:16'),(26,3,'user',1,'activity_action',50,'commented','{\"label\":\"post\"}',1,0,'2014-03-27 13:26:27'),(27,1,'user',3,'activity_action',56,'shared','{\"label\":\"post\"}',0,0,'2014-03-27 13:29:14');
/*!40000 ALTER TABLE `engine4_activity_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_notificationsettings`
--

DROP TABLE IF EXISTS `engine4_activity_notificationsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_notificationsettings` (
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_notificationsettings`
--

LOCK TABLES `engine4_activity_notificationsettings` WRITE;
/*!40000 ALTER TABLE `engine4_activity_notificationsettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_activity_notificationsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_notificationtypes`
--

DROP TABLE IF EXISTS `engine4_activity_notificationtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_notificationtypes` (
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `is_request` tinyint(1) NOT NULL DEFAULT '0',
  `handler` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `default` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

LOCK TABLES `engine4_activity_notificationtypes` WRITE;
/*!40000 ALTER TABLE `engine4_activity_notificationtypes` DISABLE KEYS */;
INSERT INTO `engine4_activity_notificationtypes` VALUES ('commented','activity','{item:$subject} has commented on your {item:$object:$label}.',0,'',1),('commented_commented','activity','{item:$subject} has commented on a {item:$object:$label} you commented on.',0,'',1),('friend_accepted','user','You and {item:$subject} are now friends.',0,'',1),('friend_follow','user','{item:$subject} is now following you.',0,'',1),('friend_follow_accepted','user','You are now following {item:$subject}.',0,'',1),('friend_follow_request','user','{item:$subject} has requested to follow you.',1,'user.friends.request-follow',1),('friend_request','user','{item:$subject} has requested to be your friend.',1,'user.friends.request-friend',1),('group_accepted','group','Your request to join the group {item:$object} has been approved.',0,'',1),('group_approve','group','{item:$subject} has requested to join the group {item:$object}.',0,'',1),('group_discussion_reply','group','{item:$subject} has {item:$object:posted} on a {itemParent:$object::group topic} you posted on.',0,'',1),('group_discussion_response','group','{item:$subject} has {item:$object:posted} on a {itemParent:$object::group topic} you created.',0,'',1),('group_invite','group','{item:$subject} has invited you to the group {item:$object}.',1,'group.widget.request-group',1),('group_promote','group','You were promoted to officer in the group {item:$object}.',0,'',1),('hequestion_answer','hequestion','{item:$subject} has answered {item:$object}.',0,'',1),('hequestion_ask','hequestion','{item:$subject} has asked you {item:$object}.',0,'',1),('hequestion_follow','hequestion','{item:$subject} has answered {item:$object}.',0,'',1),('liked','activity','{item:$subject} likes your {item:$object:$label}.',0,'',1),('liked_commented','activity','{item:$subject} has commented on a {item:$object:$label} you liked.',0,'',1),('like_send_update','like','{item:$subject} send you to an update about {item:$object}.',0,'',1),('like_suggest','like','{item:$subject} suggest you to check this out {item:$object}.',0,'',1),('message_new','messages','{item:$subject} has sent you a {item:$object:message}.',0,'',1),('post_user','user','{item:$subject} has posted on your {item:$object:profile}.',0,'',1),('shared','activity','{item:$subject} has shared your {item:$object:$label}.',0,'',1),('tagged','user','{item:$subject} tagged you in a {item:$object:$label}.',0,'',1),('wall_tag','wall','WALL_NOTIFICATION_TAG',0,'',1);
/*!40000 ALTER TABLE `engine4_activity_notificationtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_activity_stream`
--

DROP TABLE IF EXISTS `engine4_activity_stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_activity_stream` (
  `target_type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `target_id` int(11) unsigned NOT NULL,
  `subject_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `object_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `action_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`target_type`,`target_id`,`action_id`),
  KEY `SUBJECT` (`subject_type`,`subject_id`,`action_id`),
  KEY `OBJECT` (`object_type`,`object_id`,`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_activity_stream`
--

LOCK TABLES `engine4_activity_stream` WRITE;
/*!40000 ALTER TABLE `engine4_activity_stream` DISABLE KEYS */;
INSERT INTO `engine4_activity_stream` VALUES ('everyone',0,'user',1,'user',1,'status',1),('everyone',0,'user',1,'user',1,'status',3),('everyone',0,'user',2,'user',2,'signup',5),('everyone',0,'user',2,'group',1,'group_join',8),('everyone',0,'user',3,'user',3,'signup',9),('everyone',0,'user',1,'user',3,'friends',10),('everyone',0,'user',3,'user',1,'friends',11),('everyone',0,'user',3,'user',2,'friends',12),('everyone',0,'user',2,'user',3,'friends',13),('everyone',0,'user',1,'user',1,'post_self',14),('everyone',0,'user',1,'user',1,'post_self',15),('everyone',0,'user',1,'user',1,'status',17),('everyone',0,'user',3,'user',3,'status',19),('everyone',0,'user',3,'user',3,'share',20),('everyone',0,'user',3,'user',3,'post_self',21),('everyone',0,'user',3,'user',3,'status',23),('everyone',0,'user',2,'user',2,'status',24),('everyone',0,'user',3,'hequestion',83,'hequestion_answer',29),('everyone',0,'user',2,'hequestion',83,'hequestion_answer',30),('everyone',0,'user',3,'user',3,'status',32),('everyone',0,'user',3,'user',3,'hequestion_ask_self',33),('everyone',0,'user',1,'hequestion',84,'hequestion_answer',35),('everyone',0,'user',3,'hequestion',84,'hequestion_answer',36),('everyone',0,'user',3,'user',2,'share',37),('everyone',0,'user',3,'user',3,'status',38),('everyone',0,'user',3,'user',3,'status',40),('everyone',0,'user',4,'user',4,'signup',43),('everyone',0,'user',5,'user',5,'signup',44),('everyone',0,'user',5,'user',5,'status',45),('everyone',0,'user',3,'user',3,'status',47),('everyone',0,'user',3,'user',3,'status',48),('everyone',0,'user',3,'user',3,'status',49),('everyone',0,'user',3,'user',3,'status',50),('everyone',0,'user',5,'user',5,'status',51),('everyone',0,'user',1,'user',1,'status',52),('everyone',0,'user',1,'user',1,'status',53),('everyone',0,'user',1,'user',1,'status',54),('everyone',0,'user',1,'user',1,'status',55),('everyone',0,'user',3,'user',1,'share',56),('group',1,'user',2,'group',1,'group_join',8),('members',1,'user',1,'user',1,'status',1),('members',1,'user',1,'user',1,'status',3),('members',1,'user',3,'user',1,'friends',11),('members',1,'user',1,'user',1,'post_self',14),('members',1,'user',1,'user',1,'post_self',15),('members',1,'user',1,'user',1,'status',17),('members',1,'user',1,'user',1,'status',52),('members',1,'user',1,'user',1,'status',53),('members',1,'user',1,'user',1,'status',54),('members',1,'user',1,'user',1,'status',55),('members',1,'user',3,'user',1,'share',56),('members',2,'user',2,'user',2,'signup',5),('members',2,'user',3,'user',2,'friends',12),('members',2,'user',2,'user',2,'status',24),('members',2,'user',3,'user',2,'share',37),('members',3,'user',3,'user',3,'signup',9),('members',3,'user',1,'user',3,'friends',10),('members',3,'user',2,'user',3,'friends',13),('members',3,'user',3,'user',3,'status',19),('members',3,'user',3,'user',3,'share',20),('members',3,'user',3,'user',3,'post_self',21),('members',3,'user',3,'user',3,'status',23),('members',3,'user',3,'hequestion',83,'hequestion_answer',29),('members',3,'user',2,'hequestion',83,'hequestion_answer',30),('members',3,'user',3,'user',3,'status',32),('members',3,'user',3,'user',3,'hequestion_ask_self',33),('members',3,'user',1,'hequestion',84,'hequestion_answer',35),('members',3,'user',3,'hequestion',84,'hequestion_answer',36),('members',3,'user',3,'user',3,'status',38),('members',3,'user',3,'user',3,'status',40),('members',3,'user',3,'user',3,'status',47),('members',3,'user',3,'user',3,'status',48),('members',3,'user',3,'user',3,'status',49),('members',3,'user',3,'user',3,'status',50),('members',4,'user',4,'user',4,'signup',43),('members',5,'user',5,'user',5,'signup',44),('members',5,'user',5,'user',5,'status',45),('members',5,'user',5,'user',5,'status',51),('owner',1,'user',1,'user',1,'status',1),('owner',1,'user',1,'user',1,'status',3),('owner',1,'user',1,'user',3,'friends',10),('owner',1,'user',1,'user',1,'post_self',14),('owner',1,'user',1,'user',1,'post_self',15),('owner',1,'user',1,'user',1,'status',17),('owner',1,'user',1,'hequestion',84,'hequestion_answer',35),('owner',1,'user',1,'user',1,'status',52),('owner',1,'user',1,'user',1,'status',53),('owner',1,'user',1,'user',1,'status',54),('owner',1,'user',1,'user',1,'status',55),('owner',2,'user',2,'user',2,'signup',5),('owner',2,'user',2,'group',1,'group_join',8),('owner',2,'user',2,'user',3,'friends',13),('owner',2,'user',2,'user',2,'status',24),('owner',2,'user',2,'hequestion',83,'hequestion_answer',30),('owner',3,'user',3,'user',3,'signup',9),('owner',3,'user',3,'user',1,'friends',11),('owner',3,'user',3,'user',2,'friends',12),('owner',3,'user',3,'user',3,'status',19),('owner',3,'user',3,'user',3,'share',20),('owner',3,'user',3,'user',3,'post_self',21),('owner',3,'user',3,'user',3,'status',23),('owner',3,'user',3,'hequestion',83,'hequestion_answer',29),('owner',3,'user',3,'user',3,'status',32),('owner',3,'user',3,'user',3,'hequestion_ask_self',33),('owner',3,'user',3,'hequestion',84,'hequestion_answer',36),('owner',3,'user',3,'user',2,'share',37),('owner',3,'user',3,'user',3,'status',38),('owner',3,'user',3,'user',3,'status',40),('owner',3,'user',3,'user',3,'status',47),('owner',3,'user',3,'user',3,'status',48),('owner',3,'user',3,'user',3,'status',49),('owner',3,'user',3,'user',3,'status',50),('owner',3,'user',3,'user',1,'share',56),('owner',4,'user',4,'user',4,'signup',43),('owner',5,'user',5,'user',5,'signup',44),('owner',5,'user',5,'user',5,'status',45),('owner',5,'user',5,'user',5,'status',51),('parent',1,'user',1,'user',1,'status',1),('parent',1,'user',1,'user',1,'status',3),('parent',1,'user',2,'group',1,'group_join',8),('parent',1,'user',3,'user',1,'friends',11),('parent',1,'user',1,'user',1,'post_self',14),('parent',1,'user',1,'user',1,'post_self',15),('parent',1,'user',1,'user',1,'status',17),('parent',1,'user',1,'user',1,'status',52),('parent',1,'user',1,'user',1,'status',53),('parent',1,'user',1,'user',1,'status',54),('parent',1,'user',1,'user',1,'status',55),('parent',1,'user',3,'user',1,'share',56),('parent',2,'user',2,'user',2,'signup',5),('parent',2,'user',3,'user',2,'friends',12),('parent',2,'user',2,'user',2,'status',24),('parent',2,'user',3,'user',2,'share',37),('parent',3,'user',3,'user',3,'signup',9),('parent',3,'user',1,'user',3,'friends',10),('parent',3,'user',2,'user',3,'friends',13),('parent',3,'user',3,'user',3,'status',19),('parent',3,'user',3,'user',3,'share',20),('parent',3,'user',3,'user',3,'post_self',21),('parent',3,'user',3,'user',3,'status',23),('parent',3,'user',3,'hequestion',83,'hequestion_answer',29),('parent',3,'user',2,'hequestion',83,'hequestion_answer',30),('parent',3,'user',3,'user',3,'status',32),('parent',3,'user',3,'user',3,'hequestion_ask_self',33),('parent',3,'user',1,'hequestion',84,'hequestion_answer',35),('parent',3,'user',3,'hequestion',84,'hequestion_answer',36),('parent',3,'user',3,'user',3,'status',38),('parent',3,'user',3,'user',3,'status',40),('parent',3,'user',3,'user',3,'status',47),('parent',3,'user',3,'user',3,'status',48),('parent',3,'user',3,'user',3,'status',49),('parent',3,'user',3,'user',3,'status',50),('parent',4,'user',4,'user',4,'signup',43),('parent',5,'user',5,'user',5,'signup',44),('parent',5,'user',5,'user',5,'status',45),('parent',5,'user',5,'user',5,'status',51),('registered',0,'user',1,'user',1,'status',1),('registered',0,'user',1,'user',1,'status',3),('registered',0,'user',2,'user',2,'signup',5),('registered',0,'user',2,'group',1,'group_join',8),('registered',0,'user',3,'user',3,'signup',9),('registered',0,'user',1,'user',3,'friends',10),('registered',0,'user',3,'user',1,'friends',11),('registered',0,'user',3,'user',2,'friends',12),('registered',0,'user',2,'user',3,'friends',13),('registered',0,'user',1,'user',1,'post_self',14),('registered',0,'user',1,'user',1,'post_self',15),('registered',0,'user',1,'user',1,'status',17),('registered',0,'user',3,'user',3,'status',19),('registered',0,'user',3,'user',3,'share',20),('registered',0,'user',3,'user',3,'post_self',21),('registered',0,'user',3,'user',3,'status',23),('registered',0,'user',2,'user',2,'status',24),('registered',0,'user',3,'user',3,'status',32),('registered',0,'user',3,'user',3,'hequestion_ask_self',33),('registered',0,'user',3,'user',2,'share',37),('registered',0,'user',3,'user',3,'status',38),('registered',0,'user',3,'user',3,'status',40),('registered',0,'user',4,'user',4,'signup',43),('registered',0,'user',5,'user',5,'signup',44),('registered',0,'user',5,'user',5,'status',45),('registered',0,'user',3,'user',3,'status',47),('registered',0,'user',3,'user',3,'status',48),('registered',0,'user',3,'user',3,'status',49),('registered',0,'user',3,'user',3,'status',50),('registered',0,'user',5,'user',5,'status',51),('registered',0,'user',1,'user',1,'status',52),('registered',0,'user',1,'user',1,'status',53),('registered',0,'user',1,'user',1,'status',54),('registered',0,'user',1,'user',1,'status',55),('registered',0,'user',3,'user',1,'share',56);
/*!40000 ALTER TABLE `engine4_activity_stream` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_advancedsearch_icons`
--

DROP TABLE IF EXISTS `engine4_advancedsearch_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_advancedsearch_icons` (
  `item` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `icon_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`icon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_advancedsearch_icons`
--

LOCK TABLES `engine4_advancedsearch_icons` WRITE;
/*!40000 ALTER TABLE `engine4_advancedsearch_icons` DISABLE KEYS */;
INSERT INTO `engine4_advancedsearch_icons` VALUES ('album','icon-camera',1),('blog','icon-book',2),('core_link','icon-share',3),('donation','icon-heart',4),('event','icon-calendar',5),('group','icon-group',6),('offer','icon-star-empty',7),('page','icon-map-marker',8),('hebadge_creditbadge','icon-trophy',9),('store_product','icon-shopping-cart',10),('user','icon-user',11),('video','icon-film',12),('hequestion','icon-question-sign',13),('music','icon-music',14);
/*!40000 ALTER TABLE `engine4_advancedsearch_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_album_albums`
--

DROP TABLE IF EXISTS `engine4_album_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_album_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `type` enum('wall','profile','message','blog') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`album_id`),
  KEY `owner_type` (`owner_type`,`owner_id`),
  KEY `category_id` (`category_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_album_albums`
--

LOCK TABLES `engine4_album_albums` WRITE;
/*!40000 ALTER TABLE `engine4_album_albums` DISABLE KEYS */;
INSERT INTO `engine4_album_albums` VALUES (1,'Wall Photos','','user',1,0,'2014-03-06 04:19:27','2014-03-06 04:19:27',1,0,0,1,'wall'),(2,'Wall Photos','','user',3,0,'2014-03-08 16:03:40','2014-03-08 16:03:40',2,0,0,1,'wall'),(3,'Message Photos','','user',3,0,'2014-03-09 22:56:52','2014-03-09 22:56:52',3,0,0,0,'message');
/*!40000 ALTER TABLE `engine4_album_albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_album_categories`
--

DROP TABLE IF EXISTS `engine4_album_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_album_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_album_categories`
--

LOCK TABLES `engine4_album_categories` WRITE;
/*!40000 ALTER TABLE `engine4_album_categories` DISABLE KEYS */;
INSERT INTO `engine4_album_categories` VALUES (0,1,'All Categories'),(1,1,'Arts & Culture'),(2,1,'Business'),(3,1,'Entertainment'),(5,1,'Family & Home'),(6,1,'Health'),(7,1,'Recreation'),(8,1,'Personal'),(9,1,'Shopping'),(10,1,'Society'),(11,1,'Sports'),(12,1,'Technology'),(13,1,'Other');
/*!40000 ALTER TABLE `engine4_album_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_album_photos`
--

DROP TABLE IF EXISTS `engine4_album_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_album_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `owner_type` (`owner_type`,`owner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_album_photos`
--

LOCK TABLES `engine4_album_photos` WRITE;
/*!40000 ALTER TABLE `engine4_album_photos` DISABLE KEYS */;
INSERT INTO `engine4_album_photos` VALUES (1,1,'','','2014-03-06 04:19:27','2014-03-06 04:19:27',1,'user',1,17,0,0),(2,2,'','','2014-03-08 16:03:40','2014-03-08 16:03:40',2,'user',3,20,0,0),(3,3,'Attached Image','','2014-03-09 22:56:52','2014-03-09 22:56:52',3,'user',3,28,0,0),(4,2,'','','2014-03-14 02:19:53','2014-03-14 02:19:53',0,'user',3,37,0,0),(5,1,'','','2014-03-15 20:48:57','2014-03-15 20:48:57',0,'user',1,40,0,0);
/*!40000 ALTER TABLE `engine4_album_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_announcement_announcements`
--

DROP TABLE IF EXISTS `engine4_announcement_announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_announcement_announcements` (
  `announcement_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `networks` text COLLATE utf8_unicode_ci,
  `member_levels` text COLLATE utf8_unicode_ci,
  `profile_types` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`announcement_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_announcement_announcements`
--

LOCK TABLES `engine4_announcement_announcements` WRITE;
/*!40000 ALTER TABLE `engine4_announcement_announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_announcement_announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_authorization_allow`
--

DROP TABLE IF EXISTS `engine4_authorization_allow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_authorization_allow` (
  `resource_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `action` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `role` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `role_id` int(11) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(1) NOT NULL DEFAULT '0',
  `params` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`resource_type`,`resource_id`,`action`,`role`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_authorization_allow`
--

LOCK TABLES `engine4_authorization_allow` WRITE;
/*!40000 ALTER TABLE `engine4_authorization_allow` DISABLE KEYS */;
INSERT INTO `engine4_authorization_allow` VALUES ('album',1,'comment','everyone',0,1,NULL),('album',1,'view','everyone',0,1,NULL),('album',2,'comment','everyone',0,1,NULL),('album',2,'view','everyone',0,1,NULL),('album_photo',1,'comment','everyone',0,1,NULL),('album_photo',1,'view','everyone',0,1,NULL),('album_photo',2,'comment','everyone',0,1,NULL),('album_photo',2,'view','everyone',0,1,NULL),('album_photo',4,'comment','everyone',0,1,NULL),('album_photo',4,'view','everyone',0,1,NULL),('album_photo',5,'comment','everyone',0,1,NULL),('album_photo',5,'view','everyone',0,1,NULL),('group',1,'comment','group_list',1,1,NULL),('group',1,'comment','member',0,1,NULL),('group',1,'comment','registered',0,1,NULL),('group',1,'event','group_list',1,1,NULL),('group',1,'event','member',0,1,NULL),('group',1,'event','registered',0,1,NULL),('group',1,'invite','group_list',1,1,NULL),('group',1,'invite','member',0,1,NULL),('group',1,'photo','group_list',1,1,NULL),('group',1,'photo','member',0,1,NULL),('group',1,'photo','registered',0,1,NULL),('group',1,'photo.edit','group_list',1,1,NULL),('group',1,'topic.edit','group_list',1,1,NULL),('group',1,'view','everyone',0,1,NULL),('group',1,'view','group_list',1,1,NULL),('group',1,'view','member',0,1,NULL),('group',1,'view','member_requested',0,1,NULL),('group',1,'view','registered',0,1,NULL),('group',2,'comment','group_list',2,1,NULL),('group',2,'comment','member',0,1,NULL),('group',2,'comment','registered',0,1,NULL),('group',2,'event','group_list',2,1,NULL),('group',2,'event','member',0,1,NULL),('group',2,'event','registered',0,1,NULL),('group',2,'invite','group_list',2,1,NULL),('group',2,'invite','member',0,1,NULL),('group',2,'photo','group_list',2,1,NULL),('group',2,'photo','member',0,1,NULL),('group',2,'photo','registered',0,1,NULL),('group',2,'photo.edit','group_list',2,1,NULL),('group',2,'topic.edit','group_list',2,1,NULL),('group',2,'view','everyone',0,1,NULL),('group',2,'view','group_list',2,1,NULL),('group',2,'view','member',0,1,NULL),('group',2,'view','member_requested',0,1,NULL),('group',2,'view','registered',0,1,NULL),('hequestion',83,'view','everyone',0,1,NULL),('hequestion',83,'view','owner_member',0,1,NULL),('hequestion',83,'view','owner_network',0,1,NULL),('hequestion',83,'vote','registered',0,1,NULL),('hequestion',84,'view','everyone',0,1,NULL),('hequestion',84,'view','owner_member',0,1,NULL),('hequestion',84,'view','owner_network',0,1,NULL),('hequestion',84,'vote','registered',0,1,NULL),('user',1,'comment','everyone',0,1,NULL),('user',1,'comment','member',0,1,NULL),('user',1,'comment','network',0,1,NULL),('user',1,'comment','registered',0,1,NULL),('user',1,'interest','everyone',0,1,NULL),('user',1,'interest','owner_member',0,1,NULL),('user',1,'interest','owner_member_member',0,1,NULL),('user',1,'interest','owner_network',0,1,NULL),('user',1,'interest','registered',0,1,NULL),('user',1,'view','everyone',0,1,NULL),('user',1,'view','member',0,1,NULL),('user',1,'view','network',0,1,NULL),('user',1,'view','registered',0,1,NULL),('user',2,'comment','everyone',0,1,NULL),('user',2,'comment','member',0,1,NULL),('user',2,'comment','network',0,1,NULL),('user',2,'comment','registered',0,1,NULL),('user',2,'interest','everyone',0,1,NULL),('user',2,'interest','owner_member',0,1,NULL),('user',2,'interest','owner_member_member',0,1,NULL),('user',2,'interest','owner_network',0,1,NULL),('user',2,'interest','registered',0,1,NULL),('user',2,'view','everyone',0,1,NULL),('user',2,'view','member',0,1,NULL),('user',2,'view','network',0,1,NULL),('user',2,'view','registered',0,1,NULL),('user',3,'comment','everyone',0,1,NULL),('user',3,'comment','member',0,1,NULL),('user',3,'comment','network',0,1,NULL),('user',3,'comment','registered',0,1,NULL),('user',3,'interest','everyone',0,1,NULL),('user',3,'interest','owner_member',0,1,NULL),('user',3,'interest','owner_member_member',0,1,NULL),('user',3,'interest','owner_network',0,1,NULL),('user',3,'interest','registered',0,1,NULL),('user',3,'view','everyone',0,1,NULL),('user',3,'view','member',0,1,NULL),('user',3,'view','network',0,1,NULL),('user',3,'view','registered',0,1,NULL),('user',4,'comment','everyone',0,1,NULL),('user',4,'comment','member',0,1,NULL),('user',4,'comment','network',0,1,NULL),('user',4,'comment','registered',0,1,NULL),('user',4,'view','everyone',0,1,NULL),('user',4,'view','member',0,1,NULL),('user',4,'view','network',0,1,NULL),('user',4,'view','registered',0,1,NULL),('user',5,'comment','everyone',0,1,NULL),('user',5,'comment','member',0,1,NULL),('user',5,'comment','network',0,1,NULL),('user',5,'comment','registered',0,1,NULL),('user',5,'view','everyone',0,1,NULL),('user',5,'view','member',0,1,NULL),('user',5,'view','network',0,1,NULL),('user',5,'view','registered',0,1,NULL);
/*!40000 ALTER TABLE `engine4_authorization_allow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_authorization_levels`
--

DROP TABLE IF EXISTS `engine4_authorization_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_authorization_levels` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('public','user','moderator','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `flag` enum('default','superadmin','public') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_authorization_levels`
--

LOCK TABLES `engine4_authorization_levels` WRITE;
/*!40000 ALTER TABLE `engine4_authorization_levels` DISABLE KEYS */;
INSERT INTO `engine4_authorization_levels` VALUES (1,'Superadmins','Users of this level can modify all of your settings and data.  This level cannot be modified or deleted.','admin','superadmin'),(2,'Admins','Users of this level have full access to all of your network settings and data.','admin',''),(3,'Moderators','Users of this level may edit user-side content.','moderator',''),(4,'Default Level','This is the default user level.  New users are assigned to it automatically.','user','default'),(5,'Public','Settings for this level apply to users who have not logged in.','public','public');
/*!40000 ALTER TABLE `engine4_authorization_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_authorization_permissions`
--

DROP TABLE IF EXISTS `engine4_authorization_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_authorization_permissions` (
  `level_id` int(11) unsigned NOT NULL,
  `type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` tinyint(3) NOT NULL DEFAULT '0',
  `params` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`level_id`,`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_authorization_permissions`
--

LOCK TABLES `engine4_authorization_permissions` WRITE;
/*!40000 ALTER TABLE `engine4_authorization_permissions` DISABLE KEYS */;
INSERT INTO `engine4_authorization_permissions` VALUES (1,'admin','view',1,NULL),(1,'album','auth_comment',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(1,'album','auth_tag',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(1,'album','auth_view',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(1,'album','comment',2,NULL),(1,'album','create',1,NULL),(1,'album','delete',2,NULL),(1,'album','edit',2,NULL),(1,'album','tag',2,NULL),(1,'album','view',2,NULL),(1,'announcement','create',1,NULL),(1,'announcement','delete',2,NULL),(1,'announcement','edit',2,NULL),(1,'announcement','view',2,NULL),(1,'core_link','create',1,NULL),(1,'core_link','delete',2,NULL),(1,'core_link','view',2,NULL),(1,'general','activity',2,NULL),(1,'general','style',2,NULL),(1,'group','auth_comment',5,'[\"registered\", \"member\", \"officer\"]'),(1,'group','auth_event',5,'[\"registered\", \"member\", \"officer\"]'),(1,'group','auth_photo',5,'[\"registered\", \"member\", \"officer\"]'),(1,'group','auth_view',5,'[\"everyone\", \"registered\", \"member\"]'),(1,'group','comment',2,NULL),(1,'group','commentHtml',3,'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),(1,'group','create',1,NULL),(1,'group','delete',2,NULL),(1,'group','edit',2,NULL),(1,'group','event',1,NULL),(1,'group','invite',1,NULL),(1,'group','photo',1,NULL),(1,'group','photo.edit',2,NULL),(1,'group','style',1,NULL),(1,'group','topic.edit',2,NULL),(1,'group','view',2,NULL),(1,'hequestion','auth_comment',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(1,'hequestion','auth_view',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(1,'hequestion','comment',2,NULL),(1,'hequestion','create',1,NULL),(1,'hequestion','delete',2,NULL),(1,'hequestion','edit',2,NULL),(1,'hequestion','view',2,NULL),(1,'hequestion','vote',2,NULL),(1,'messages','auth',3,'friends'),(1,'messages','create',1,NULL),(1,'messages','editor',3,'plaintext'),(1,'user','activity',1,NULL),(1,'user','auth_comment',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(1,'user','auth_interest',5,'[\"everyone\", \"registered\", \"owner_network\", \"owner_member_member\", \"owner_member\", \"owner\"]'),(1,'user','auth_view',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(1,'user','block',1,NULL),(1,'user','comment',2,NULL),(1,'user','create',1,NULL),(1,'user','delete',2,NULL),(1,'user','edit',2,NULL),(1,'user','interest',1,NULL),(1,'user','like_donation',1,NULL),(1,'user','like_event',1,NULL),(1,'user','like_group',1,NULL),(1,'user','like_offer',1,NULL),(1,'user','like_page',1,NULL),(1,'user','like_product',1,NULL),(1,'user','like_user',1,NULL),(1,'user','search',1,NULL),(1,'user','status',1,NULL),(1,'user','style',2,NULL),(1,'user','username',2,NULL),(1,'user','view',2,NULL),(2,'admin','view',1,NULL),(2,'album','auth_comment',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(2,'album','auth_tag',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(2,'album','auth_view',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(2,'album','comment',2,NULL),(2,'album','create',1,NULL),(2,'album','delete',2,NULL),(2,'album','edit',2,NULL),(2,'album','tag',2,NULL),(2,'album','view',2,NULL),(2,'announcement','create',1,NULL),(2,'announcement','delete',2,NULL),(2,'announcement','edit',2,NULL),(2,'announcement','view',2,NULL),(2,'core_link','create',1,NULL),(2,'core_link','delete',2,NULL),(2,'core_link','view',2,NULL),(2,'general','activity',2,NULL),(2,'general','style',2,NULL),(2,'group','auth_comment',5,'[\"registered\", \"member\", \"officer\"]'),(2,'group','auth_event',5,'[\"registered\", \"member\", \"officer\"]'),(2,'group','auth_photo',5,'[\"registered\", \"member\", \"officer\"]'),(2,'group','auth_view',5,'[\"everyone\", \"registered\", \"member\"]'),(2,'group','comment',2,NULL),(2,'group','commentHtml',3,'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),(2,'group','create',1,NULL),(2,'group','delete',2,NULL),(2,'group','edit',2,NULL),(2,'group','event',1,NULL),(2,'group','invite',1,NULL),(2,'group','photo',1,NULL),(2,'group','photo.edit',2,NULL),(2,'group','style',1,NULL),(2,'group','topic.edit',2,NULL),(2,'group','view',2,NULL),(2,'hequestion','auth_comment',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(2,'hequestion','auth_view',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(2,'hequestion','comment',2,NULL),(2,'hequestion','create',1,NULL),(2,'hequestion','delete',2,NULL),(2,'hequestion','edit',2,NULL),(2,'hequestion','view',2,NULL),(2,'hequestion','vote',2,NULL),(2,'messages','auth',3,'friends'),(2,'messages','create',1,NULL),(2,'messages','editor',3,'plaintext'),(2,'user','activity',1,NULL),(2,'user','auth_comment',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(2,'user','auth_interest',5,'[\"everyone\", \"registered\", \"owner_network\", \"owner_member_member\", \"owner_member\", \"owner\"]'),(2,'user','auth_view',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(2,'user','block',1,NULL),(2,'user','comment',2,NULL),(2,'user','create',1,NULL),(2,'user','delete',2,NULL),(2,'user','edit',2,NULL),(2,'user','interest',1,NULL),(2,'user','like_donation',1,NULL),(2,'user','like_event',1,NULL),(2,'user','like_group',1,NULL),(2,'user','like_offer',1,NULL),(2,'user','like_page',1,NULL),(2,'user','like_product',1,NULL),(2,'user','like_user',1,NULL),(2,'user','search',1,NULL),(2,'user','status',1,NULL),(2,'user','style',2,NULL),(2,'user','username',2,NULL),(2,'user','view',2,NULL),(3,'album','auth_comment',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(3,'album','auth_tag',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(3,'album','auth_view',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(3,'album','comment',2,NULL),(3,'album','create',1,NULL),(3,'album','delete',2,NULL),(3,'album','edit',2,NULL),(3,'album','tag',2,NULL),(3,'album','view',2,NULL),(3,'announcement','view',1,NULL),(3,'core_link','create',1,NULL),(3,'core_link','delete',2,NULL),(3,'core_link','view',2,NULL),(3,'general','activity',2,NULL),(3,'general','style',2,NULL),(3,'group','auth_comment',5,'[\"registered\", \"member\", \"officer\"]'),(3,'group','auth_event',5,'[\"registered\", \"member\", \"officer\"]'),(3,'group','auth_photo',5,'[\"registered\", \"member\", \"officer\"]'),(3,'group','auth_view',5,'[\"everyone\", \"registered\", \"member\"]'),(3,'group','comment',2,NULL),(3,'group','commentHtml',3,'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),(3,'group','create',1,NULL),(3,'group','delete',2,NULL),(3,'group','edit',2,NULL),(3,'group','event',1,NULL),(3,'group','invite',1,NULL),(3,'group','photo',1,NULL),(3,'group','photo.edit',2,NULL),(3,'group','style',1,NULL),(3,'group','topic.edit',2,NULL),(3,'group','view',2,NULL),(3,'hequestion','auth_comment',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(3,'hequestion','auth_view',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(3,'hequestion','comment',2,NULL),(3,'hequestion','create',1,NULL),(3,'hequestion','delete',2,NULL),(3,'hequestion','edit',2,NULL),(3,'hequestion','view',2,NULL),(3,'hequestion','vote',2,NULL),(3,'messages','auth',3,'friends'),(3,'messages','create',1,NULL),(3,'messages','editor',3,'plaintext'),(3,'user','activity',1,NULL),(3,'user','auth_comment',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(3,'user','auth_interest',5,'[\"everyone\", \"registered\", \"owner_network\", \"owner_member_member\", \"owner_member\", \"owner\"]'),(3,'user','auth_view',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(3,'user','block',1,NULL),(3,'user','comment',2,NULL),(3,'user','create',1,NULL),(3,'user','delete',2,NULL),(3,'user','edit',2,NULL),(3,'user','interest',1,NULL),(3,'user','like_donation',1,NULL),(3,'user','like_event',1,NULL),(3,'user','like_group',1,NULL),(3,'user','like_offer',1,NULL),(3,'user','like_page',1,NULL),(3,'user','like_product',1,NULL),(3,'user','like_user',1,NULL),(3,'user','search',1,NULL),(3,'user','status',1,NULL),(3,'user','style',2,NULL),(3,'user','username',2,NULL),(3,'user','view',2,NULL),(4,'album','auth_comment',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(4,'album','auth_tag',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(4,'album','auth_view',5,'[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(4,'album','comment',1,NULL),(4,'album','create',1,NULL),(4,'album','delete',1,NULL),(4,'album','edit',1,NULL),(4,'album','tag',1,NULL),(4,'album','view',1,NULL),(4,'announcement','view',1,NULL),(4,'core_link','create',1,NULL),(4,'core_link','delete',1,NULL),(4,'core_link','view',1,NULL),(4,'general','style',1,NULL),(4,'group','auth_comment',5,'[\"registered\", \"member\", \"officer\"]'),(4,'group','auth_event',5,'[\"registered\", \"member\", \"officer\"]'),(4,'group','auth_photo',5,'[\"registered\", \"member\", \"officer\"]'),(4,'group','auth_view',5,'[\"everyone\", \"registered\", \"member\"]'),(4,'group','comment',1,NULL),(4,'group','commentHtml',3,'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),(4,'group','create',1,NULL),(4,'group','delete',1,NULL),(4,'group','edit',1,NULL),(4,'group','event',1,NULL),(4,'group','invite',1,NULL),(4,'group','photo',1,NULL),(4,'group','photo.edit',1,NULL),(4,'group','style',1,NULL),(4,'group','topic.edit',1,NULL),(4,'group','view',1,NULL),(4,'hequestion','auth_comment',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(4,'hequestion','auth_view',5,'[\"owner\", \"owner_member\", \"owner_network\", \"everyone\"]'),(4,'hequestion','comment',1,NULL),(4,'hequestion','create',1,NULL),(4,'hequestion','delete',1,NULL),(4,'hequestion','edit',1,NULL),(4,'hequestion','view',1,NULL),(4,'hequestion','vote',1,NULL),(4,'messages','auth',3,'friends'),(4,'messages','create',1,NULL),(4,'messages','editor',3,'plaintext'),(4,'user','auth_comment',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(4,'user','auth_interest',5,'[\"everyone\",\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),(4,'user','auth_view',5,'[\"everyone\",\"registered\",\"network\",\"member\",\"owner\"]'),(4,'user','block',1,NULL),(4,'user','comment',1,NULL),(4,'user','create',1,NULL),(4,'user','delete',1,NULL),(4,'user','edit',1,NULL),(4,'user','interest',1,NULL),(4,'user','like_donation',1,NULL),(4,'user','like_event',1,NULL),(4,'user','like_group',1,NULL),(4,'user','like_offer',1,NULL),(4,'user','like_page',1,NULL),(4,'user','like_product',1,NULL),(4,'user','like_user',1,NULL),(4,'user','search',1,NULL),(4,'user','status',1,NULL),(4,'user','style',1,NULL),(4,'user','username',1,NULL),(4,'user','view',1,NULL),(5,'album','tag',0,NULL),(5,'album','view',1,NULL),(5,'announcement','view',1,NULL),(5,'core_link','view',1,NULL),(5,'group','view',1,NULL),(5,'hequestion','view',1,NULL),(5,'user','interest',1,NULL),(5,'user','view',1,NULL);
/*!40000 ALTER TABLE `engine4_authorization_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_adcampaigns`
--

DROP TABLE IF EXISTS `engine4_core_adcampaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_adcampaigns` (
  `adcampaign_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `end_settings` tinyint(4) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `limit_view` int(11) unsigned NOT NULL DEFAULT '0',
  `limit_click` int(11) unsigned NOT NULL DEFAULT '0',
  `limit_ctr` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `network` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `views` int(11) unsigned NOT NULL DEFAULT '0',
  `clicks` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`adcampaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_adcampaigns`
--

LOCK TABLES `engine4_core_adcampaigns` WRITE;
/*!40000 ALTER TABLE `engine4_core_adcampaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_adcampaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_adphotos`
--

DROP TABLE IF EXISTS `engine4_core_adphotos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_adphotos` (
  `adphoto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`adphoto_id`),
  KEY `ad_id` (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_adphotos`
--

LOCK TABLES `engine4_core_adphotos` WRITE;
/*!40000 ALTER TABLE `engine4_core_adphotos` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_adphotos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_ads`
--

DROP TABLE IF EXISTS `engine4_core_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_ads` (
  `ad_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `ad_campaign` int(11) unsigned NOT NULL,
  `views` int(11) unsigned NOT NULL DEFAULT '0',
  `clicks` int(11) unsigned NOT NULL DEFAULT '0',
  `media_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `html_code` text COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`),
  KEY `ad_campaign` (`ad_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_ads`
--

LOCK TABLES `engine4_core_ads` WRITE;
/*!40000 ALTER TABLE `engine4_core_ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_auth`
--

DROP TABLE IF EXISTS `engine4_core_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_auth` (
  `id` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`user_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_auth`
--

LOCK TABLES `engine4_core_auth` WRITE;
/*!40000 ALTER TABLE `engine4_core_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_bannedemails`
--

DROP TABLE IF EXISTS `engine4_core_bannedemails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_bannedemails` (
  `bannedemail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`bannedemail_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_bannedemails`
--

LOCK TABLES `engine4_core_bannedemails` WRITE;
/*!40000 ALTER TABLE `engine4_core_bannedemails` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_bannedemails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_bannedips`
--

DROP TABLE IF EXISTS `engine4_core_bannedips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_bannedips` (
  `bannedip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start` varbinary(16) NOT NULL,
  `stop` varbinary(16) NOT NULL,
  PRIMARY KEY (`bannedip_id`),
  UNIQUE KEY `start` (`start`,`stop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_bannedips`
--

LOCK TABLES `engine4_core_bannedips` WRITE;
/*!40000 ALTER TABLE `engine4_core_bannedips` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_bannedips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_bannedusernames`
--

DROP TABLE IF EXISTS `engine4_core_bannedusernames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_bannedusernames` (
  `bannedusername_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`bannedusername_id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_bannedusernames`
--

LOCK TABLES `engine4_core_bannedusernames` WRITE;
/*!40000 ALTER TABLE `engine4_core_bannedusernames` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_bannedusernames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_bannedwords`
--

DROP TABLE IF EXISTS `engine4_core_bannedwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_bannedwords` (
  `bannedword_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`bannedword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_bannedwords`
--

LOCK TABLES `engine4_core_bannedwords` WRITE;
/*!40000 ALTER TABLE `engine4_core_bannedwords` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_bannedwords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_comments`
--

DROP TABLE IF EXISTS `engine4_core_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_comments` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `resource_type` (`resource_type`,`resource_id`),
  KEY `poster_type` (`poster_type`,`poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_comments`
--

LOCK TABLES `engine4_core_comments` WRITE;
/*!40000 ALTER TABLE `engine4_core_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_content`
--

DROP TABLE IF EXISTS `engine4_core_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_content` (
  `content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_content_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `params` text COLLATE utf8_unicode_ci,
  `attribs` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`content_id`),
  KEY `page_id` (`page_id`,`order`)
) ENGINE=InnoDB AUTO_INCREMENT=917 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_content`
--

LOCK TABLES `engine4_core_content` WRITE;
/*!40000 ALTER TABLE `engine4_core_content` DISABLE KEYS */;
INSERT INTO `engine4_core_content` VALUES (100,1,'container','main',NULL,2,'[\"\"]',NULL),(200,2,'container','main',NULL,2,'[\"\"]',NULL),(210,2,'widget','core.menu-footer',200,2,'[\"\"]',NULL),(300,3,'container','main',NULL,2,'[\"\"]',NULL),(311,3,'container','right',300,5,'[\"\"]',NULL),(312,3,'container','middle',300,6,'[\"\"]',NULL),(400,4,'container','main',NULL,2,'[\"\"]',NULL),(410,4,'container','left',400,4,'[\"\"]',NULL),(411,4,'container','right',400,5,'[\"\"]',NULL),(412,4,'container','middle',400,6,'[\"\"]',NULL),(420,4,'widget','user.home-photo',410,3,'[\"\"]',NULL),(421,4,'widget','user.home-links',410,4,'[\"\"]',NULL),(422,4,'widget','user.list-online',410,5,'{\"title\":\"%s Members Online\"}',NULL),(423,4,'widget','core.statistics',410,6,'{\"title\":\"Network Stats\"}',NULL),(430,4,'widget','activity.list-requests',411,11,'{\"title\":\"Requests\"}',NULL),(431,4,'widget','user.list-signups',411,12,'{\"title\":\"Newest Members\"}',NULL),(432,4,'widget','user.list-popular',411,13,'{\"title\":\"Popular Members\"}',NULL),(440,4,'widget','announcement.list-announcements',412,8,'{\"0\":\"\",\"title\":\"Announcements\"}',NULL),(441,4,'widget','activity.feed',412,9,'{\"title\":\"What\'s New\"}',NULL),(500,5,'container','main',NULL,2,'[\"\"]',NULL),(510,5,'container','left',500,4,'[\"\"]',NULL),(511,5,'container','middle',500,6,'[\"\"]',NULL),(520,5,'widget','user.profile-photo',510,3,'[\"\"]',NULL),(521,5,'widget','user.profile-options',510,4,'[\"\"]',NULL),(530,5,'widget','user.profile-status',511,8,'[\"\"]',NULL),(531,5,'widget','core.container-tabs',511,10,'{\"max\":\"6\"}',NULL),(540,5,'widget','activity.feed',531,11,'{\"title\":\"Updates\"}',NULL),(542,5,'widget','user.profile-friends',531,13,'{\"title\":\"Friends\",\"titleCount\":true}',NULL),(547,6,'container','main',NULL,2,'[\"[]\"]',NULL),(548,6,'container','middle',547,6,'[\"[]\"]',NULL),(549,6,'widget','core.content',548,3,'[\"[]\"]',NULL),(550,7,'container','main',NULL,2,'[\"[]\"]',NULL),(551,7,'container','middle',550,6,'[\"[]\"]',NULL),(553,8,'container','main',NULL,1,NULL,NULL),(554,8,'container','middle',553,2,NULL,NULL),(555,8,'widget','core.content',554,1,NULL,NULL),(556,9,'container','main',NULL,1,NULL,NULL),(557,9,'container','middle',556,1,NULL,NULL),(558,9,'widget','core.content',557,1,NULL,NULL),(559,10,'container','main',NULL,1,NULL,NULL),(560,10,'container','middle',559,1,NULL,NULL),(561,10,'widget','core.content',560,1,NULL,NULL),(562,11,'container','main',NULL,2,'[\"[]\"]',NULL),(563,11,'container','middle',562,6,'[\"[]\"]',NULL),(564,11,'widget','core.content',563,3,'[\"[]\"]',NULL),(565,12,'container','main',NULL,1,NULL,NULL),(566,12,'container','middle',565,1,NULL,NULL),(567,12,'widget','core.content',566,1,NULL,NULL),(568,13,'container','main',NULL,1,NULL,NULL),(569,13,'container','middle',568,1,NULL,NULL),(570,13,'widget','core.content',569,1,NULL,NULL),(571,14,'container','top',NULL,1,NULL,NULL),(572,14,'container','main',NULL,2,NULL,NULL),(573,14,'container','middle',571,1,NULL,NULL),(574,14,'container','middle',572,2,NULL,NULL),(575,14,'widget','user.settings-menu',573,1,NULL,NULL),(576,14,'widget','core.content',574,1,NULL,NULL),(577,15,'container','top',NULL,1,NULL,NULL),(578,15,'container','main',NULL,2,NULL,NULL),(579,15,'container','middle',577,1,NULL,NULL),(580,15,'container','middle',578,2,NULL,NULL),(581,15,'widget','user.settings-menu',579,1,NULL,NULL),(582,15,'widget','core.content',580,1,NULL,NULL),(583,16,'container','top',NULL,1,NULL,NULL),(584,16,'container','main',NULL,2,NULL,NULL),(585,16,'container','middle',583,1,NULL,NULL),(586,16,'container','middle',584,2,NULL,NULL),(587,16,'widget','user.settings-menu',585,1,NULL,NULL),(588,16,'widget','core.content',586,1,NULL,NULL),(589,17,'container','top',NULL,1,NULL,NULL),(590,17,'container','main',NULL,2,NULL,NULL),(591,17,'container','middle',589,1,NULL,NULL),(592,17,'container','middle',590,2,NULL,NULL),(593,17,'widget','user.settings-menu',591,1,NULL,NULL),(594,17,'widget','core.content',592,1,NULL,NULL),(595,18,'container','top',NULL,1,NULL,NULL),(596,18,'container','main',NULL,2,NULL,NULL),(597,18,'container','middle',595,1,NULL,NULL),(598,18,'container','middle',596,2,NULL,NULL),(599,18,'widget','user.settings-menu',597,1,NULL,NULL),(600,18,'widget','core.content',598,1,NULL,NULL),(601,19,'container','top',NULL,1,NULL,NULL),(602,19,'container','main',NULL,2,NULL,NULL),(603,19,'container','middle',601,1,NULL,NULL),(604,19,'container','middle',602,2,NULL,NULL),(605,19,'widget','user.settings-menu',603,1,NULL,NULL),(606,19,'widget','core.content',604,1,NULL,NULL),(607,20,'container','main',NULL,1,NULL,NULL),(608,20,'container','middle',607,1,NULL,NULL),(609,20,'widget','core.content',608,1,NULL,NULL),(610,21,'container','main',NULL,1,NULL,NULL),(611,21,'container','middle',610,1,NULL,NULL),(612,21,'widget','core.content',611,2,NULL,NULL),(613,21,'widget','messages.menu',611,1,NULL,NULL),(614,22,'container','main',NULL,1,NULL,NULL),(615,22,'container','middle',614,1,NULL,NULL),(616,22,'widget','core.content',615,2,NULL,NULL),(617,22,'widget','messages.menu',615,1,NULL,NULL),(618,23,'container','main',NULL,1,NULL,NULL),(619,23,'container','middle',618,1,NULL,NULL),(620,23,'widget','core.content',619,2,NULL,NULL),(621,23,'widget','messages.menu',619,1,NULL,NULL),(622,24,'container','main',NULL,1,NULL,NULL),(623,24,'container','middle',622,1,NULL,NULL),(624,24,'widget','core.content',623,2,NULL,NULL),(625,24,'widget','messages.menu',623,1,NULL,NULL),(626,25,'container','main',NULL,1,NULL,NULL),(627,25,'container','middle',626,1,NULL,NULL),(628,25,'widget','core.content',627,2,NULL,NULL),(629,25,'widget','messages.menu',627,1,NULL,NULL),(630,26,'container','main',NULL,1,NULL,NULL),(631,26,'container','middle',630,2,NULL,NULL),(632,26,'widget','core.content',631,1,NULL,NULL),(633,26,'widget','core.comments',631,2,NULL,NULL),(634,27,'container','main',NULL,1,NULL,NULL),(635,27,'container','middle',634,2,NULL,NULL),(636,27,'widget','core.content',635,1,NULL,NULL),(637,27,'widget','core.comments',635,2,NULL,NULL),(638,28,'container','top',NULL,1,NULL,NULL),(639,28,'container','main',NULL,2,NULL,NULL),(640,28,'container','middle',638,1,NULL,NULL),(641,28,'container','middle',639,2,NULL,NULL),(642,28,'container','right',639,1,NULL,NULL),(643,28,'widget','album.browse-menu',640,1,NULL,NULL),(644,28,'widget','core.content',641,1,NULL,NULL),(645,28,'widget','album.browse-search',642,1,NULL,NULL),(646,28,'widget','album.browse-menu-quick',642,2,NULL,NULL),(648,29,'container','top',NULL,1,NULL,NULL),(649,29,'container','main',NULL,2,NULL,NULL),(650,29,'container','middle',648,1,NULL,NULL),(651,29,'container','middle',649,2,NULL,NULL),(652,29,'widget','album.browse-menu',650,1,NULL,NULL),(653,29,'widget','core.content',651,1,NULL,NULL),(654,30,'container','top',NULL,1,NULL,NULL),(655,30,'container','main',NULL,2,NULL,NULL),(656,30,'container','middle',654,1,NULL,NULL),(657,30,'container','middle',655,2,NULL,NULL),(658,30,'container','right',655,1,NULL,NULL),(659,30,'widget','album.browse-menu',656,1,NULL,NULL),(660,30,'widget','core.content',657,1,NULL,NULL),(661,30,'widget','album.browse-search',658,1,NULL,NULL),(662,30,'widget','album.browse-menu-quick',658,2,NULL,NULL),(664,31,'container','main',NULL,1,'',NULL),(665,31,'container','middle',664,3,'',NULL),(666,31,'container','left',664,1,'',NULL),(667,31,'widget','core.container-tabs',665,2,'{\"max\":\"6\"}',NULL),(668,31,'widget','group.profile-status',665,1,'',NULL),(669,31,'widget','group.profile-photo',666,1,'',NULL),(670,31,'widget','group.profile-options',666,2,'',NULL),(671,31,'widget','group.profile-info',666,3,'',NULL),(672,31,'widget','activity.feed',667,1,'{\"title\":\"Updates\"}',NULL),(673,31,'widget','group.profile-members',667,2,'{\"title\":\"Members\",\"titleCount\":true}',NULL),(674,31,'widget','group.profile-photos',667,3,'{\"title\":\"Photos\",\"titleCount\":true}',NULL),(675,31,'widget','group.profile-discussions',667,4,'{\"title\":\"Discussions\",\"titleCount\":true}',NULL),(676,31,'widget','core.profile-links',667,5,'{\"title\":\"Links\",\"titleCount\":true}',NULL),(677,31,'widget','group.profile-events',667,6,'{\"title\":\"Events\",\"titleCount\":true}',NULL),(678,32,'container','main',NULL,1,'',NULL),(679,32,'container','middle',678,2,'',NULL),(680,32,'widget','group.profile-status',679,3,'',NULL),(681,32,'widget','group.profile-photo',679,4,'',NULL),(682,32,'widget','group.profile-info',679,5,'',NULL),(683,32,'widget','core.container-tabs',679,6,'{\"max\":6}',NULL),(684,32,'widget','activity.feed',683,7,'{\"title\":\"What\'s New\"}',NULL),(685,32,'widget','group.profile-members',683,8,'{\"title\":\"Members\",\"titleCount\":true}',NULL),(686,33,'container','top',NULL,1,NULL,NULL),(687,33,'container','main',NULL,2,NULL,NULL),(688,33,'container','middle',686,1,NULL,NULL),(689,33,'container','middle',687,2,NULL,NULL),(690,33,'container','right',687,1,NULL,NULL),(691,33,'widget','group.browse-menu',688,1,NULL,NULL),(692,33,'widget','core.content',689,1,NULL,NULL),(693,33,'widget','group.browse-search',690,1,NULL,NULL),(694,33,'widget','group.browse-menu-quick',690,2,NULL,NULL),(695,34,'container','top',NULL,1,NULL,NULL),(696,34,'container','main',NULL,2,NULL,NULL),(697,34,'container','middle',695,1,NULL,NULL),(698,34,'container','middle',696,2,NULL,NULL),(699,34,'widget','group.browse-menu',697,1,NULL,NULL),(700,34,'widget','core.content',698,1,NULL,NULL),(701,35,'container','top',NULL,1,NULL,NULL),(702,35,'container','main',NULL,2,NULL,NULL),(703,35,'container','middle',701,1,NULL,NULL),(704,35,'container','middle',702,2,NULL,NULL),(705,35,'container','right',702,1,NULL,NULL),(706,35,'widget','group.browse-menu',703,1,NULL,NULL),(707,35,'widget','core.content',704,1,NULL,NULL),(708,35,'widget','group.browse-search',705,1,NULL,NULL),(709,35,'widget','group.browse-menu-quick',705,2,NULL,NULL),(710,36,'container','main',0,2,'[\"[]\"]',NULL),(711,36,'container','middle',710,6,'[\"[]\"]',NULL),(712,36,'widget','core.content',711,3,'[\"[]\"]',NULL),(713,36,'container','right',710,5,'[\"[]\"]',NULL),(714,37,'container','main',0,2,'[\"[]\"]',NULL),(715,37,'container','middle',714,6,'[\"[]\"]',NULL),(716,37,'widget','inviter.home-inviter',715,5,'{\"title\":\"WALL_WELCOME_INVITER\",\"name\":\"inviter.home-inviter\"}',NULL),(717,37,'widget','wall.upload-photo',715,6,'{\"title\":\"WALL_WELCOME_UPLOAD_PHOTO\",\"name\":\"wall.upload-photo\"}',NULL),(718,37,'widget','wall.welcome',715,3,'{\"title\":\"WALL_WELCOME_WELCOME\",\"name\":\"wall.welcome\"}',NULL),(719,37,'widget','wall.people-know',715,12,'{\"title\":\"WALL_WELCOME_PEOPLE_KNOW\",\"name\":\"wall.people-know\"}',NULL),(720,37,'widget','credit.faq',715,8,'{\"title\":\"WALL_WELCOME_CREDIT_FAQ\",\"name\":\"credit.faq\"}',NULL),(721,37,'widget','suggest.autorecommendations',715,7,'{\"title\":\"WALL_WELCOME_SUGGESTION\",\"titleCount\":true,\"name\":\"suggest.autorecommendations\"}',NULL),(722,37,'widget','wall.most-liked',715,9,'{\"title\":\"WALL_WELCOME_LIKES\"}',NULL),(723,37,'widget','hegift.birthdays',715,10,'{\"title\":\"WALL_WELCOME_BIRTHDAYS\",\"name\":\"hegift.birthdays\"}',NULL),(724,37,'widget','wall.gift-actual',715,11,'{\"title\":\"WALL_WELCOME_GIFTACTUAL\"}',NULL),(725,37,'widget','wall.new-wall',715,4,'{\"title\":\"WALL_WELCOME_NEWWALL\"}',NULL),(726,38,'container','main',NULL,2,'[\"[\'Member Home Page\']\"]',NULL),(727,38,'container','left',726,4,'[\"[]\"]',NULL),(728,38,'widget','user.home-photo',727,3,'[\"[]\"]',NULL),(729,38,'widget','user.home-links',727,5,'[\"[]\"]',NULL),(731,38,'container','middle',726,6,'[\"[]\"]',NULL),(733,38,'widget','pinfeed.pint-feed',731,7,'[\"[]\"]',NULL),(735,2,'widget','mobile.mode-switcher',200,3,'{\"standard\":\"Standard Site\",\"mobile\":\"Mobile Site\"}',NULL),(736,39,'container','main',NULL,2,'[\"[]\"]',NULL),(737,39,'container','middle',736,6,'[\"[]\"]',NULL),(739,40,'container','main',NULL,2,'[\"[]\"]',NULL),(740,40,'container','middle',739,6,'[\"[]\"]',NULL),(748,40,'widget','core.html-block',740,3,'{\"title\":\"\",\"data\":\"<center><img src=\\\"\\/wealthment\\/public\\/admin\\/ourmission.gif\\\" alt=\\\"Our Mission\\\">\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(749,39,'widget','core.html-block',737,3,'{\"title\":\"\",\"data\":\"<center><img src=\\\"\\/wealthment\\/public\\/admin\\/howthisworks.gif\\\" alt=\\\"How This Works\\\">\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(758,43,'container','main',NULL,2,'[\"[]\"]',NULL),(759,43,'container','middle',758,6,'[\"[]\"]',NULL),(764,44,'container','main',NULL,2,'[\"[]\"]',NULL),(765,44,'container','middle',764,6,'[\"[]\"]',NULL),(770,45,'container','main',NULL,2,'[\"[]\"]',NULL),(771,45,'container','middle',770,6,'[\"[]\"]',NULL),(772,45,'widget','core.html-block',771,3,'{\"title\":\"FAQ\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(774,46,'container','main',0,2,'[\"[]\"]',NULL),(775,46,'container','middle',774,6,'[\"[]\"]',NULL),(776,46,'widget','hequestion.recent-answers',775,5,'{\"title\":\"HEQUESTION_RECENT_ANSWERS\"}',NULL),(777,46,'widget','core.content',775,4,'[\"[]\"]',NULL),(778,46,'widget','hequestion.browse-menu',775,3,'[]',NULL),(779,46,'container','right',774,5,'[\"[]\"]',NULL),(780,46,'widget','hequestion.friend-questions',779,7,'{\"title\":\"HEQUESTION_FRIEND_QUESTIONS\"}',NULL),(781,46,'widget','hequestion.popular-questions',779,8,'{\"title\":\"HEQUESTION_POPULAR_QUESTIONS\"}',NULL),(782,47,'container','main',0,2,'[\"[]\"]',NULL),(783,47,'container','middle',782,6,'[\"[]\"]',NULL),(784,47,'widget','core.content',783,3,'[\"[]\"]',NULL),(785,47,'widget','hequestion.asked',783,4,'[\"[]\"]',NULL),(786,47,'widget','core.comments',783,5,'{\"title\":\"Comments\"}',NULL),(787,47,'container','right',782,5,'[]',NULL),(788,47,'widget','hequestion.popular-questions',787,7,'{\"title\":\"HEQUESTION_POPULAR_QUESTIONS\"}',NULL),(789,48,'container','main',0,2,'[\"[]\"]',NULL),(790,48,'container','middle',789,6,'[\"[]\"]',NULL),(791,48,'widget','core.content',790,3,'[\"[]\"]',NULL),(792,48,'widget','hequestion.asked',790,4,'[\"[]\"]',NULL),(793,48,'widget','core.comments',790,5,'{\"title\":\"Comments\"}',NULL),(794,49,'container','main',0,2,'[\"[]\"]',NULL),(795,49,'container','middle',794,6,'[\"[]\"]',NULL),(796,49,'widget','core.content',795,4,'[\"[]\"]',NULL),(797,49,'widget','hequestion.browse-menu',795,3,'[]',NULL),(798,49,'container','right',794,5,'[\"[]\"]',NULL),(799,49,'widget','hequestion.friend-questions',798,6,'{\"title\":\"HEQUESTION_FRIEND_QUESTIONS\"}',NULL),(800,5,'widget','hequestion.profile-questions',531,14,'{\"title\":\"HEQUESTION_PROFILE_QUESTIONS\",\"titleCount\":true}',NULL),(801,31,'widget','hequestion.profile-questions',667,7,'{\"title\":\"HEQUESTION_PROFILE_QUESTIONS\",\"titleCount\":true}',NULL),(802,50,'container','main',NULL,2,'[\"[]\"]',NULL),(803,50,'container','middle',802,6,'[\"[]\"]',NULL),(804,50,'container','top',NULL,1,'[\"[]\"]',NULL),(805,50,'container','middle',804,6,'[\"[]\"]',NULL),(806,50,'widget','core.html-block',805,3,'{\"title\":\"Welcome to Wealthment\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(807,50,'widget','core.html-block',803,6,'{\"title\":\"\",\"data\":\"Please provide your Name & E-mail address to be part of the Wealthment launch in the Spring of 2014!\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(808,50,'widget','core.contact',803,7,'{\"title\":\"\",\"titleCount\":true,\"name\":\"core.contact\"}',NULL),(812,3,'container','top',NULL,1,'[\"[]\"]',NULL),(813,3,'container','middle',812,6,'[\"[]\"]',NULL),(814,3,'widget','core.html-block',813,4,'{\"title\":\"\",\"data\":\"<center><B><h3>We want to help non-financial professionals CONNECT to their peers to gain insights on managing wealth<\\/h3><B><center>\\r\\n\\r\\n<BR>\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(815,3,'widget','core.html-block',312,7,'{\"title\":\"\",\"data\":\"<center><img src=\\\"\\/wealthment\\/public\\/admin\\/landingfinal.gif\\\" alt=\\\"Welcome to Wealthment\\\">\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(816,3,'widget','user.login-or-signup',311,9,'[\"[]\"]',NULL),(821,38,'widget','hashtag.trands',727,4,'{\"title\":\"Trends\",\"titleCount\":true}',NULL),(822,51,'container','main',NULL,2,'[\"[]\"]',NULL),(823,51,'container','middle',822,6,'[\"[]\"]',NULL),(829,4,'widget','hashtag.trands',411,14,'{\"title\":\"Trends\",\"titleCount\":true}',NULL),(838,51,'widget','core.container-tabs',823,4,'{\"max\":\"6\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL),(839,51,'widget','core.html-block',838,5,'{\"title\":\"Stage 1 - Starter\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(840,51,'widget','core.html-block',838,6,'{\"title\":\"Stage 2 - Dabbler\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(841,51,'widget','core.html-block',838,7,'{\"title\":\"Stage 3 - Player\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(843,1,'widget','core.html-block',100,4,'{\"title\":\"\",\"data\":\" <style> body { width:100%; margin: 0px auto; } \\r\\n\\/* Main menu *\\/ #menu { width: 100%; margin: 0; padding: 0px; list-style: none; background-color: #00B0F0; border: 1px solid transparent; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; } #menu li { float: left; padding: 0; position: relative; line-height: 0; } #menu a { float: left; height: 25px; padding: 0 10px; color: #fff;  font: bold 14px\\/25px Arial, Helvetica; text-decoration: none; } #menu li:hover > a { color: #fff; } *html #menu li a:hover \\/* IE6 *\\/ { color: #fff; } #menu li:hover > ul { display: block; } \\r\\n\\/* Sub-menu *\\/ #menu ul { list-style: none; margin: 0; width:220%; padding: 0; top:21px; display: none; position: absolute; left: 0; z-index: 99999; background: #8cc63f; color:#fff; -moz-border-radius: 6px; border-radius: 6px; } #menu ul ul { top: 0; left: 150px; } #menu ul li { float: none; margin: 0; padding: 0; display: block; } #menu ul li:last-child { -moz-box-shadow: none; -webkit-box-shadow: none; box-shadow: none; } #menu ul a { color:#fff; padding: 10px; height: 10px; line-height: 1; display: block; white-space: nowrap; float: none; text-transform: none; } *html #menu ul a \\/* IE6 *\\/ { height: 10px; } *:first-child+html #menu ul a \\/* IE7 *\\/ { height: 10px; } #menu ul a:hover { background: white;color:gray; } #menu ul li:first-child > a { -moz-border-radius: 5px 5px 0 0; border-radius: 5px 5px 0 0; } #menu ul li:first-child > a:after { content: \'\'; position: absolute; left: 30px; width: 0; height: 0; border-bottom: 8px solid #444; } #menu ul ul li:first-child a:after { left: -8px; width: 0; height: 0; border-left: 0; border-bottom: 5px solid transparent; border-top: 5px solid transparent; } #menu ul li:first-child a:hover:after { border-bottom-color: #04acec; } #menu ul ul li:first-child a:hover:after { border-right-color: #04acec; border-bottom-color: transparent; } #menu ul li:last-child > a { -moz-border-radius: 0 0 5px 5px; border-radius: 0 0 5px 5px; } \\/* Clear floated elements *\\/ #menu:after { visibility: hidden; display: block; font-size: 0; content: \\\" \\\"; clear: both; height: 0; } * html #menu { zoom: 1; } \\/* IE6 *\\/ *:first-child+html #menu { zoom: 1; } \\/* IE7 *\\/ <\\/style> <\\/head> \\r\\n<body> <ul id=\\\"menu\\\">\\r\\n <li> <a href=\\\"\\\">Stocks <\\/a>\\r\\n <ul> \\r\\n<li><a href=\\\" index.php\\/pages\\/stockwealthposts \\\"> Stock Wealthposts<\\/a> <\\/li> \\r\\n<li><a href=\\\"index.php\\/pages\\/stockinsights\\\"> Stock Insights<\\/a> <\\/li> \\r\\n<\\/ul> <\\/li> \\r\\n<li> <a href=\\\"#\\\">Real Estate <\\/a>\\r\\n <ul>\\r\\n<li><a href=\\\" index.php\\/pages\\/realestatewealthposts \\\"> Real Estate Wealthposts<\\/a> <\\/li>\\r\\n<li><a href=\\\"index.php\\/pages\\/realestateinsights\\\"> Real Estate Insights<\\/a> <\\/li> \\r\\n<\\/ul> <\\/li> \\r\\n<li> <a href=\\\"#\\\">Retirement<\\/a>\\r\\n <ul>\\r\\n<li><a href=\\\" index.php\\/pages\\/retirementwealthposts \\\"> Retirement Wealthposts<\\/a> <\\/li> \\r\\n<li><a href=\\\"index.php\\/pages\\/retirementinsights\\\"> Retirement Insights<\\/a> <\\/li> \\r\\n<\\/ul> <\\/li> \\r\\n <\\/li> <li> <a href=\\\"#\\\"> Other Savings<\\/a>\\r\\n <ul> \\r\\n<li><a href=\\\" index.php\\/pages\\/othersavingswealthposts \\\"> Other Savings Wealthposts<\\/a> <\\/li> \\r\\n<li><a href=\\\"index.php\\/pages\\/othersavingsinsights\\\"> Other Savings Insights<\\/a> <\\/li> \\r\\n<\\/ul> <\\/li> \\r\\n<\\/ul> \",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(845,3,'widget','core.html-block',311,10,'{\"title\":\"\",\"data\":\"<center><H5>By clicking \\\"Sign In\\\", you agree to Wealthment\\u2019s Privacy & Terms.<\\/H5><\\/center>\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(846,7,'widget','core.html-block',551,3,'{\"title\":\"Privacy & Terms\",\"data\":\"Welcome to WWW.WEALTHMENT.COM.  By using this website (\\\"Site\\\"), you are agreeing to comply with and be bound by the following terms and conditions of use.  \\r\\n\\r\\n<BR>\\r\\nThe use of the Site is subject to the following terms:\\r\\n\\r\\n<BR>\\r\\n<BR>\\r\\n\\r\\n1.The content of the pages of the Site is for your general information and use only.  It is subject to change without notice.<BR><BR>\\r\\n2.Your use of any information on the Site is entirely at your own risk, for which we shall not be liable.  It shall be your own responsibility to ensure that any products, services or information available through this Site meet your specific requirements. <BR><BR>\\r\\n3.The information you provide or receive from your peers is for general knowledge only.\\r\\n\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(847,53,'container','main',NULL,2,'[\"[\'Member Home Page\']\"]',NULL),(852,53,'container','middle',847,6,'[\"[]\"]',NULL),(855,54,'container','main',NULL,2,'[\"[\'Member Home Page\']\"]',NULL),(860,54,'container','middle',855,6,'[\"[]\"]',NULL),(863,55,'container','main',NULL,2,'[\"[]\"]',NULL),(864,55,'container','middle',863,6,'[\"[]\"]',NULL),(865,55,'widget','core.html-block',864,3,'{\"title\":\"\",\"data\":\"<center><img src=\\\"\\/wealthment\\/public\\/admin\\/wealthmaturity.gif\\\" alt=\\\"Wealth Maturity\\\">\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(866,56,'container','main',NULL,2,'[\"[]\"]',NULL),(867,56,'container','middle',866,6,'[\"[]\"]',NULL),(868,56,'widget','core.container-tabs',867,3,'{\"max\":\"6\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL),(869,56,'widget','core.html-block',868,4,'{\"title\":\"Stage 1 - First-Time Homebuyer\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(870,56,'widget','core.html-block',868,5,'{\"title\":\"Stage 2 - Investment Property Buyer\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(871,56,'widget','core.html-block',868,6,'{\"title\":\"Stage 3 - Remodeler\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(872,57,'container','main',NULL,2,'[\"[]\"]',NULL),(873,57,'container','middle',872,6,'[\"[]\"]',NULL),(874,57,'widget','core.container-tabs',873,3,'{\"max\":\"6\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL),(875,57,'widget','core.html-block',874,4,'{\"title\":\"Stage 1 - Newcomer\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(876,57,'widget','core.html-block',874,5,'{\"title\":\"Stage 2 - Cultivator\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(877,57,'widget','core.html-block',874,6,'{\"title\":\"Stage 3 - Controller\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(878,1,'widget','core.html-block',100,3,'{\"title\":\"\",\"data\":\"<center>\\r\\n<a href=\\\"index.php\\\"><img src=\\\"\\/wealthment\\/public\\/admin\\/finalbanner.gif\\\" alt=\\\"Wealthment Banner\\\">\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(879,58,'container','main',NULL,2,'[\"[]\"]',NULL),(880,58,'container','middle',879,6,'[\"[]\"]',NULL),(881,58,'widget','core.container-tabs',880,3,'{\"max\":\"6\",\"title\":\"\",\"nomobile\":\"0\",\"name\":\"core.container-tabs\"}',NULL),(882,58,'widget','core.html-block',881,4,'{\"title\":\"Stage 1 - Beginning Saver\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(883,58,'widget','core.html-block',881,5,'{\"title\":\"Stage 2 - Improving Saver\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(884,58,'widget','core.html-block',881,6,'{\"title\":\"Stage 3 - Excelling Saver\",\"data\":\"\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(885,5,'widget','core.html-block',511,9,'{\"title\":\"\",\"data\":\"<BR>\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(887,5,'widget','user.profile-fields',531,12,'{\"title\":\"Wealthuser Info\",\"name\":\"user.profile-fields\"}',NULL),(891,3,'widget','core.html-block',813,3,'{\"title\":\"\",\"data\":\"<center>\\r\\n<a href=\\\"index.php\\\"><img src=\\\"\\/wealthment\\/public\\/admin\\/finalbanner.gif\\\" alt=\\\"Wealthment Banner\\\"><\\/center>\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(892,51,'widget','core.html-block',823,3,'{\"title\":\"\",\"data\":\"<center>\\u201cI WOULD BET A LOT OF MONEY THAT INCOME FROM A DIVERSIFIED GROUP OF STOCKS WILL INCREASE SIGNIFICANTLY OVER THE NEXT 20 YEARS. SO THE HEADLINES WILL NOT MAKE ANY DIFFERENCE IN THAT. STOCKS CAN GO UP AND DOWN. THEY ALWAYS WILL GO UP AND DOWN. BUT, AMERICAN BUSINESS IS GOING TO MOVE FORWARD OVER TIME.\\u201d <BR>\\r\\n \\u2013 WARREN BUFFET on 3\\/14\\/14\\r\\n<\\/center>\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(893,1,'widget','core.menu-mini',100,2,'[\"[]\"]',NULL),(895,59,'container','main',NULL,1,NULL,NULL),(896,59,'container','middle',895,1,NULL,NULL),(900,43,'widget','wall.feed',759,3,'{\"title\":\"What\'s New\"}',NULL),(902,54,'widget','wall.feed',860,3,'{\"title\":\"What\'s New\"}',NULL),(906,44,'widget','wall.feed',765,3,'{\"title\":\"What\'s New\"}',NULL),(911,53,'widget','wall.feed',852,3,'{\"title\":\"What\'s New\"}',NULL),(914,2,'widget','core.html-block',200,4,'{\"title\":\"\",\"data\":\"<center>\\r\\n<img src=\\\"\\/wealthment\\/public\\/admin\\/footer.gif\\\" alt=\\\"Wealthment Banner\\\">\",\"nomobile\":\"0\",\"name\":\"core.html-block\"}',NULL),(915,5,'widget','core.statistics',510,5,'{\"title\":\"Statistics\"}',NULL),(916,5,'widget','core.profile-links',510,6,'{\"title\":\"Links\",\"titleCount\":true}',NULL);
/*!40000 ALTER TABLE `engine4_core_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_geotags`
--

DROP TABLE IF EXISTS `engine4_core_geotags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_geotags` (
  `geotag_id` int(11) unsigned NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  PRIMARY KEY (`geotag_id`),
  KEY `latitude` (`latitude`,`longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_geotags`
--

LOCK TABLES `engine4_core_geotags` WRITE;
/*!40000 ALTER TABLE `engine4_core_geotags` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_geotags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_jobs`
--

DROP TABLE IF EXISTS `engine4_core_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_jobs` (
  `job_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `jobtype_id` int(10) unsigned NOT NULL,
  `state` enum('pending','active','sleeping','failed','cancelled','completed','timeout') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `is_complete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `progress` decimal(5,4) unsigned NOT NULL DEFAULT '0.0000',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `started_date` datetime DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `priority` mediumint(9) NOT NULL DEFAULT '100',
  `data` text COLLATE utf8_unicode_ci,
  `messages` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`job_id`),
  KEY `jobtype_id` (`jobtype_id`),
  KEY `state` (`state`),
  KEY `is_complete` (`is_complete`,`priority`,`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_jobs`
--

LOCK TABLES `engine4_core_jobs` WRITE;
/*!40000 ALTER TABLE `engine4_core_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_jobtypes`
--

DROP TABLE IF EXISTS `engine4_core_jobtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_jobtypes` (
  `jobtype_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `form` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `priority` mediumint(9) NOT NULL DEFAULT '100',
  `multi` tinyint(3) unsigned DEFAULT '1',
  PRIMARY KEY (`jobtype_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_jobtypes`
--

LOCK TABLES `engine4_core_jobtypes` WRITE;
/*!40000 ALTER TABLE `engine4_core_jobtypes` DISABLE KEYS */;
INSERT INTO `engine4_core_jobtypes` VALUES (1,'Download File','file_download','core','Core_Plugin_Job_FileDownload','Core_Form_Admin_Job_FileDownload',1,100,1),(2,'Upload File','file_upload','core','Core_Plugin_Job_FileUpload','Core_Form_Admin_Job_FileUpload',1,100,1),(3,'Rebuild Activity Privacy','activity_maintenance_rebuild_privacy','activity','Activity_Plugin_Job_Maintenance_RebuildPrivacy',NULL,1,50,1),(4,'Rebuild Member Privacy','user_maintenance_rebuild_privacy','user','User_Plugin_Job_Maintenance_RebuildPrivacy',NULL,1,50,1),(5,'Rebuild Network Membership','network_maintenance_rebuild_membership','network','Network_Plugin_Job_Maintenance_RebuildMembership',NULL,1,50,1),(6,'Storage Transfer','storage_transfer','core','Storage_Plugin_Job_Transfer','Core_Form_Admin_Job_Generic',1,100,1),(7,'Storage Cleanup','storage_cleanup','core','Storage_Plugin_Job_Cleanup','Core_Form_Admin_Job_Generic',1,100,1),(8,'Rebuild Album Privacy','album_maintenance_rebuild_privacy','album','Album_Plugin_Job_Maintenance_RebuildPrivacy',NULL,1,50,1),(9,'Rebuild Group Privacy','group_maintenance_rebuild_privacy','group','Group_Plugin_Job_Maintenance_RebuildPrivacy',NULL,1,50,1);
/*!40000 ALTER TABLE `engine4_core_jobtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_languages`
--

DROP TABLE IF EXISTS `engine4_core_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_languages` (
  `language_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(8) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fallback` varchar(8) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `order` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_languages`
--

LOCK TABLES `engine4_core_languages` WRITE;
/*!40000 ALTER TABLE `engine4_core_languages` DISABLE KEYS */;
INSERT INTO `engine4_core_languages` VALUES (1,'en','English','en',1);
/*!40000 ALTER TABLE `engine4_core_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_likes`
--

DROP TABLE IF EXISTS `engine4_core_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_likes` (
  `like_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`like_id`),
  KEY `resource_type` (`resource_type`,`resource_id`),
  KEY `poster_type` (`poster_type`,`poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_likes`
--

LOCK TABLES `engine4_core_likes` WRITE;
/*!40000 ALTER TABLE `engine4_core_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_links`
--

DROP TABLE IF EXISTS `engine4_core_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_links` (
  `link_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `owner_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `view_count` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`link_id`),
  KEY `owner` (`owner_type`,`owner_id`),
  KEY `parent` (`parent_type`,`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_links`
--

LOCK TABLES `engine4_core_links` WRITE;
/*!40000 ALTER TABLE `engine4_core_links` DISABLE KEYS */;
INSERT INTO `engine4_core_links` VALUES (1,'http://www.fool.com/investing/general/2014/02/27/from-startup-to-billion-dollar-biotech-an-inside-l.aspx?source=ihpsitth0000001','From Startup to Billion-Dollar Biotech: An Inside Look at Vertex Pharmaceuticals (VRTX)','An interview with Barry Werth, author of \"The Antidote: Inside the World of New Pharma.\" - Max Macaluso - Health Care',0,'user',2,'user',2,0,'2014-03-04 16:32:51',1),(2,'http://espn.go.com','ESPN: The Worldwide Leader In Sports','ESPN.com provides comprehensive sports coverage.  Complete sports information including NFL, MLB, NBA, College Football, College Basketball scores and news.',19,'user',1,'user',1,0,'2014-03-06 04:20:08',1);
/*!40000 ALTER TABLE `engine4_core_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_listitems`
--

DROP TABLE IF EXISTS `engine4_core_listitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_listitems` (
  `listitem_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_listitems`
--

LOCK TABLES `engine4_core_listitems` WRITE;
/*!40000 ALTER TABLE `engine4_core_listitems` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_listitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_lists`
--

DROP TABLE IF EXISTS `engine4_core_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_lists` (
  `list_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `owner_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `child_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `child_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`list_id`),
  KEY `owner_type` (`owner_type`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_lists`
--

LOCK TABLES `engine4_core_lists` WRITE;
/*!40000 ALTER TABLE `engine4_core_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_log`
--

DROP TABLE IF EXISTS `engine4_core_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_log` (
  `message_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `plugin` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timestamp` datetime NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `priority` smallint(2) NOT NULL DEFAULT '6',
  `priorityName` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'INFO',
  PRIMARY KEY (`message_id`),
  KEY `domain` (`domain`,`timestamp`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_log`
--

LOCK TABLES `engine4_core_log` WRITE;
/*!40000 ALTER TABLE `engine4_core_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_mail`
--

DROP TABLE IF EXISTS `engine4_core_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_mail` (
  `mail_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('system','zend') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `priority` smallint(3) DEFAULT '100',
  `recipient_count` int(11) unsigned DEFAULT '0',
  `recipient_total` int(10) NOT NULL DEFAULT '0',
  `creation_time` datetime NOT NULL,
  PRIMARY KEY (`mail_id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_mail`
--

LOCK TABLES `engine4_core_mail` WRITE;
/*!40000 ALTER TABLE `engine4_core_mail` DISABLE KEYS */;
INSERT INTO `engine4_core_mail` VALUES (1,'system','a:2:{s:4:\"type\";s:12:\"core_welcome\";s:6:\"params\";a:7:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1393950654;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:11:\"object_link\";s:27:\"/wealthment/index.php/login\";}}',100,1,0,'2014-03-04 16:30:54'),(2,'system','a:2:{s:4:\"type\";s:6:\"invite\";s:6:\"params\";a:10:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:27:\"ershad.qazi.jamil@gmail.com\";s:4:\"date\";i:1393950831;s:12:\"sender_email\";s:17:\"spargos@gmail.com\";s:12:\"sender_title\";s:11:\"Jeffrey Lee\";s:11:\"sender_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:7:\"message\";s:41:\"You are being invited to join Wealthment.\";s:11:\"object_link\";s:84:\"/wealthment/index.php/invite/signup?code=21a4467&email=ershad.qazi.jamil%40gmail.com\";s:4:\"code\";s:7:\"21a4467\";}}',100,1,0,'2014-03-04 16:33:51'),(3,'system','a:2:{s:4:\"type\";s:16:\"notify_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1393984438;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:41:\"/wealthment/index.php/posts/7/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:209:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Wow what a great story about a local Mass company! </span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-05 01:53:58'),(4,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1393984469;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:41:\"/wealthment/index.php/posts/7/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:209:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Wow what a great story about a local Mass company! </span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-05 01:54:29'),(5,'system','a:2:{s:4:\"type\";s:6:\"invite\";s:6:\"params\";a:10:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1393984500;s:12:\"sender_email\";s:20:\"wealthment@gmail.com\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:7:\"message\";s:49:\"You are being invited to join our social network.\";s:11:\"object_link\";s:75:\"/wealthment/index.php/invite/signup?code=9431f85&email=ersh2121%40yahoo.com\";s:4:\"code\";s:7:\"9431f85\";}}',100,1,0,'2014-03-05 01:55:00'),(6,'system','a:2:{s:4:\"type\";s:21:\"notify_friend_request\";s:6:\"params\";a:13:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1393984887;s:15:\"recipient_title\";s:11:\"ershadjamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:82:\"//wealthment/application/modules/User/externals/images/nophoto_user_thumb_icon.png\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";s:11:\"ershadjamil\";s:11:\"object_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:0:\"\";}}',100,1,0,'2014-03-05 02:01:27'),(7,'system','a:2:{s:4:\"type\";s:12:\"core_welcome\";s:6:\"params\";a:7:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1393984887;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:11:\"object_link\";s:27:\"/wealthment/index.php/login\";}}',100,1,0,'2014-03-05 02:01:27'),(8,'system','a:2:{s:4:\"type\";s:22:\"notify_friend_accepted\";s:6:\"params\";a:13:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1393984905;s:15:\"recipient_title\";s:15:\"wealthmentadmin\";s:14:\"recipient_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";s:15:\"wealthmentadmin\";s:11:\"object_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"object_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:18:\"object_description\";s:0:\"\";}}',100,1,0,'2014-03-05 02:01:45'),(9,'system','a:2:{s:4:\"type\";s:21:\"notify_friend_request\";s:6:\"params\";a:13:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1393984941;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";s:11:\"Jeffrey Lee\";s:11:\"object_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"object_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:18:\"object_description\";s:0:\"\";}}',100,1,0,'2014-03-05 02:02:21'),(10,'system','a:2:{s:4:\"type\";s:22:\"notify_friend_accepted\";s:6:\"params\";a:13:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394042676;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:11:\"Jeffrey Lee\";s:11:\"sender_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"object_title\";s:12:\"Ershad Jamil\";s:11:\"object_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"object_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:18:\"object_description\";s:0:\"\";}}',100,1,0,'2014-03-05 18:04:36'),(11,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394048331;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:11:\"Jeffrey Lee\";s:11:\"sender_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/9/ershad-jamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:153:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a> has just signed up. Say hello!\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-05 19:38:51'),(12,'system','a:2:{s:4:\"type\";s:16:\"notify_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394048341;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:11:\"Jeffrey Lee\";s:11:\"sender_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/9/ershad-jamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:153:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a> has just signed up. Say hello!\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-05 19:39:01'),(13,'system','a:2:{s:4:\"type\";s:15:\"notify_wall_tag\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394079654;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:46:\"/wealthment/index.php/posts/17/wealthmentadmin\";s:12:\"object_photo\";N;s:18:\"object_description\";s:178:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_1\" href=\"/wealthment/index.php/profile/wealthmentadmin\">wealthmentadmin</a> <span class=\"feed_item_bodytext\">Testing </span>\";s:19:\"object_parent_title\";s:15:\"wealthmentadmin\";s:18:\"object_parent_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:15:\"wealthmentadmin\";s:17:\"object_owner_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:24:\"object_owner_description\";s:0:\"\";i:0;s:0:\"\";}}',100,1,0,'2014-03-06 04:20:54'),(14,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1394122010;s:15:\"recipient_title\";s:15:\"wealthmentadmin\";s:14:\"recipient_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:11:\"Jeffrey Lee\";s:11:\"sender_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"object_title\";N;s:11:\"object_link\";s:41:\"/wealthment/index.php/posts/2/apple-stock\";s:12:\"object_photo\";N;s:18:\"object_description\";s:150:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_1\" href=\"/wealthment/index.php/profile/wealthmentadmin\">wealthmentadmin</a> created a new group:\";s:19:\"object_parent_title\";s:11:\"Apple Stock\";s:18:\"object_parent_link\";s:29:\"/wealthment/index.php/group/1\";s:19:\"object_parent_photo\";N;s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Apple Stock\";s:17:\"object_owner_link\";s:29:\"/wealthment/index.php/group/1\";s:18:\"object_owner_photo\";N;s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-06 16:06:50'),(15,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1394163897;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/18/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:170:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Go Tesla Go!</span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-07 03:44:57'),(16,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394225453;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:43:\"/wealthment/index.php/posts/20/ershad-jamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:372:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a> shared <a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a>\'s <a href=\"/wealthment/index.php/posts/19/ershad-jamil\">post</a>. <span class=\"feed_item_bodytext\">Test of share</span>\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-07 20:50:53'),(17,'system','a:2:{s:4:\"type\";s:16:\"notify_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1394225463;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/18/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:170:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Go Tesla Go!</span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-07 20:51:03'),(18,'system','a:2:{s:4:\"type\";s:22:\"notify_liked_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394225463;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:15:\"wealthmentadmin\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/18/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:170:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Go Tesla Go!</span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-07 20:51:03'),(19,'system','a:2:{s:4:\"type\";s:24:\"notify_hequestion_answer\";s:6:\"params\";a:21:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394402562;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:11:\"Jeffrey Lee\";s:11:\"sender_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"object_title\";s:21:\"What is a good stock?\";s:11:\"object_link\";s:59:\"/wealthment/index.php/question-view/83/what-is-a-good-stock\";s:12:\"object_photo\";N;s:18:\"object_description\";s:0:\"\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";}}',100,1,0,'2014-03-09 22:02:42'),(20,'system','a:2:{s:4:\"type\";s:18:\"notify_message_new\";s:6:\"params\";a:13:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1394405817;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";s:12:\"test message\";s:11:\"object_link\";s:40:\"/wealthment/index.php/messages/view/id/1\";s:12:\"object_photo\";N;s:18:\"object_description\";s:27:\"test message, kitchen photo\";}}',100,1,0,'2014-03-09 22:56:57'),(21,'system','a:2:{s:4:\"type\";s:16:\"notify_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1394410074;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/18/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:170:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Go Tesla Go!</span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-10 00:07:54'),(22,'system','a:2:{s:4:\"type\";s:26:\"notify_commented_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1394410074;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";N;s:11:\"object_link\";s:42:\"/wealthment/index.php/posts/18/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:170:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">Go Tesla Go!</span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:37:\"/wealthment/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-10 00:07:54'),(23,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1394583310;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:24:\"Wealthment Administrator\";s:11:\"sender_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:46:\"/wealthment_dev/index.php/posts/26/jeffrey-lee\";s:12:\"object_photo\";N;s:18:\"object_description\";s:211:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_2\" href=\"/wealthment_dev/index.php/profile/jefflee\">Jeffrey Lee</a> <span class=\"feed_item_bodytext\">100% return on Tesla in 3 months! #tesla #stocks </span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:51:\"/wealthment_dev/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:51:\"/wealthment_dev/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-12 00:15:10'),(24,'system','a:2:{s:4:\"type\";s:18:\"notify_message_new\";s:6:\"params\";a:13:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1394584536;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";s:14:\"Test message 2\";s:11:\"object_link\";s:44:\"/wealthment_dev/index.php/messages/view/id/2\";s:12:\"object_photo\";N;s:18:\"object_description\";s:31:\"this is a test of the messaging\";}}',100,1,0,'2014-03-12 00:35:36'),(25,'system','a:2:{s:4:\"type\";s:15:\"notify_wall_tag\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1394602022;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";N;s:11:\"object_link\";s:47:\"/wealthment_dev/index.php/posts/34/ershad-jamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:195:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment_dev/index.php/profile/ershadjamil\">Ershad Jamil</a> <span class=\"feed_item_bodytext\">Test for #tesla buy or sell </span>\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";i:0;s:0:\"\";}}',100,1,0,'2014-03-12 05:27:02'),(26,'system','a:2:{s:4:\"type\";s:24:\"notify_hequestion_answer\";s:6:\"params\";a:21:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1394684283;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:24:\"Wealthment Administrator\";s:11:\"sender_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";s:40:\"What is better, 401k or IRA or IRA Roth?\";s:11:\"object_link\";s:74:\"/wealthment_dev/index.php/question-view/84/what-is-better-401k-or-ira-or-i\";s:12:\"object_photo\";N;s:18:\"object_description\";s:0:\"\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";}}',100,1,0,'2014-03-13 04:18:03'),(27,'system','a:2:{s:4:\"type\";s:12:\"core_contact\";s:6:\"params\";a:11:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1394753766;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:27:\"Ershad Jamil (Ershad Jamil)\";s:12:\"sender_email\";s:39:\"ersh2121@yahoo.com (ersh2121@yahoo.com)\";s:7:\"message\";s:4:\"test\";s:12:\"error_report\";s:0:\"\";s:11:\"sender_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";}}',100,1,0,'2014-03-13 23:36:06'),(28,'system','a:2:{s:4:\"type\";s:13:\"notify_shared\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:17:\"spargos@gmail.com\";s:4:\"date\";i:1394757521;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/0c/000c_ade2.jpg?c=a24a\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";N;s:11:\"object_link\";s:58:\"/wealthment_dev/index.php/profile/ershadjamil/action_id/37\";s:12:\"object_photo\";N;s:18:\"object_description\";s:321:\"<a class=\"feed_item_username\" href=\"/wealthment_dev/index.php/profile/ershadjamil\">Ershad Jamil</a> shared <a class=\"feed_item_username\" href=\"/wealthment_dev/index.php/profile/jefflee\">Jeffrey Lee</a>\'s <a href=\"/wealthment_dev/index.php/posts/24/jeffrey-lee\">post</a>. <span class=\"feed_item_bodytext\">Re-posting</span>\";s:19:\"object_parent_title\";s:11:\"Jeffrey Lee\";s:18:\"object_parent_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";s:19:\"object_parent_photo\";s:51:\"/wealthment_dev/public/user/0c/000c_ade2.jpg?c=a24a\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:11:\"Jeffrey Lee\";s:17:\"object_owner_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";s:18:\"object_owner_photo\";s:51:\"/wealthment_dev/public/user/0c/000c_ade2.jpg?c=a24a\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-14 00:38:41'),(29,'system','a:2:{s:4:\"type\";s:12:\"core_welcome\";s:6:\"params\";a:7:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:23:\"jeffrey_lee@harvard.edu\";s:4:\"date\";i:1394926348;s:15:\"recipient_title\";s:11:\"Jeffrey Lee\";s:14:\"recipient_link\";s:44:\"/wealthment_dev/index.php/profile/JeffreyLee\";s:15:\"recipient_photo\";N;s:11:\"object_link\";s:31:\"/wealthment_dev/index.php/login\";}}',100,1,0,'2014-03-15 23:32:28'),(30,'system','a:2:{s:4:\"type\";s:12:\"core_welcome\";s:6:\"params\";a:7:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:19:\"ejamil@deloitte.com\";s:4:\"date\";i:1394932118;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:47:\"/wealthment_dev/index.php/profile/ErshadJamil12\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/31/0031_d3ef.jpg?c=d8d5\";s:11:\"object_link\";s:31:\"/wealthment_dev/index.php/login\";}}',100,1,0,'2014-03-16 01:08:38'),(31,'system','a:2:{s:4:\"type\";s:12:\"core_contact\";s:6:\"params\";a:11:{s:4:\"host\";s:14:\"wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1394934475;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:22:\"Jeff Lee (Jeffrey Lee)\";s:12:\"sender_email\";s:43:\"jeffrey_lee@harvard.edu (spargos@gmail.com)\";s:7:\"message\";s:4:\"Test\";s:12:\"error_report\";s:0:\"\";s:11:\"sender_link\";s:41:\"/wealthment_dev/index.php/profile/jefflee\";}}',100,1,0,'2014-03-16 01:47:55'),(32,'system','a:2:{s:4:\"type\";s:12:\"core_contact\";s:6:\"params\";a:11:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1395172538;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:26:\"Ershad Test (Ershad Jamil)\";s:12:\"sender_email\";s:40:\"ersh2121@yahoo.com (ejamil@deloitte.com)\";s:7:\"message\";s:4:\"test\";s:12:\"error_report\";s:0:\"\";s:11:\"sender_link\";s:47:\"/wealthment_dev/index.php/profile/ErshadJamil12\";}}',100,1,0,'2014-03-18 19:55:38'),(33,'system','a:2:{s:4:\"type\";s:12:\"core_contact\";s:6:\"params\";a:11:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1395173099;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:21:\"test 2 (Ershad Jamil)\";s:12:\"sender_email\";s:39:\"ersh2121@yahoo.com (ersh2121@yahoo.com)\";s:7:\"message\";s:34:\"test again to wealthment@gmail.com\";s:12:\"error_report\";s:0:\"\";s:11:\"sender_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";}}',100,1,0,'2014-03-18 20:04:59'),(34,'system','a:2:{s:4:\"type\";s:12:\"core_contact\";s:6:\"params\";a:11:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1395246938;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:21:\"ershad (Ershad Jamil)\";s:12:\"sender_email\";s:49:\"ershad.qazi.jamil@gmail.com (ejamil@deloitte.com)\";s:7:\"message\";s:7:\"testest\";s:12:\"error_report\";s:0:\"\";s:11:\"sender_link\";s:47:\"/wealthment_dev/index.php/profile/ErshadJamil12\";}}',100,1,0,'2014-03-19 16:35:38'),(35,'system','a:2:{s:4:\"type\";s:18:\"notify_message_new\";s:6:\"params\";a:13:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1395678308;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:45:\"/wealthment_dev/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:51:\"/wealthment_dev/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:24:\"Wealthment Administrator\";s:11:\"sender_link\";s:49:\"/wealthment_dev/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:51:\"/wealthment_dev/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";s:14:\"Test message 2\";s:11:\"object_link\";s:44:\"/wealthment_dev/index.php/messages/view/id/2\";s:12:\"object_photo\";N;s:18:\"object_description\";s:2:\"ok\";}}',100,1,0,'2014-03-24 16:25:08'),(36,'system','a:2:{s:4:\"type\";s:12:\"core_contact\";s:6:\"params\";a:11:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1395844159;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:33:\"ershad (Wealthment Administrator)\";s:12:\"sender_email\";s:41:\"ersh2121@yahoo.com (wealthment@gmail.com)\";s:7:\"message\";s:14:\"test from prod\";s:12:\"error_report\";s:0:\"\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";}}',100,1,0,'2014-03-26 14:29:19'),(37,'system','a:2:{s:4:\"type\";s:12:\"notify_liked\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1395926776;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:24:\"Wealthment Administrator\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:43:\"/wealthment/index.php/posts/50/ershad-jamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:175:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a> <span class=\"feed_item_bodytext\">#test2 test </span>\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-27 13:26:16'),(38,'system','a:2:{s:4:\"type\";s:16:\"notify_commented\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:18:\"ersh2121@yahoo.com\";s:4:\"date\";i:1395926787;s:15:\"recipient_title\";s:12:\"Ershad Jamil\";s:14:\"recipient_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"sender_title\";s:24:\"Wealthment Administrator\";s:11:\"sender_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"object_title\";N;s:11:\"object_link\";s:43:\"/wealthment/index.php/posts/50/ershad-jamil\";s:12:\"object_photo\";N;s:18:\"object_description\";s:175:\"<a class=\"feed_item_username wall_liketips\" rev=\"user_3\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a> <span class=\"feed_item_bodytext\">#test2 test </span>\";s:19:\"object_parent_title\";s:12:\"Ershad Jamil\";s:18:\"object_parent_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:12:\"Ershad Jamil\";s:17:\"object_owner_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-27 13:26:27'),(39,'system','a:2:{s:4:\"type\";s:13:\"notify_shared\";s:6:\"params\";a:22:{s:4:\"host\";s:18:\"www.wealthment.com\";s:5:\"email\";s:20:\"wealthment@gmail.com\";s:4:\"date\";i:1395926954;s:15:\"recipient_title\";s:24:\"Wealthment Administrator\";s:14:\"recipient_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:15:\"recipient_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:12:\"sender_title\";s:12:\"Ershad Jamil\";s:11:\"sender_link\";s:41:\"/wealthment/index.php/profile/ershadjamil\";s:12:\"sender_photo\";s:47:\"/wealthment/public/user/10/0010_5ac5.JPG?c=824f\";s:12:\"object_title\";N;s:11:\"object_link\";s:54:\"/wealthment/index.php/profile/ershadjamil/action_id/56\";s:12:\"object_photo\";N;s:18:\"object_description\";s:350:\"<a class=\"feed_item_username\" href=\"/wealthment/index.php/profile/ershadjamil\">Ershad Jamil</a> shared <a class=\"feed_item_username\" href=\"/wealthment/index.php/profile/wealthmentadmin\">Wealthment Administrator</a>\'s <a href=\"/wealthment/index.php/posts/54/wealthment-administrator\">post</a>. <span class=\"feed_item_bodytext\">What does this do</span>\";s:19:\"object_parent_title\";s:24:\"Wealthment Administrator\";s:18:\"object_parent_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:19:\"object_parent_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:25:\"object_parent_description\";s:0:\"\";s:18:\"object_owner_title\";s:24:\"Wealthment Administrator\";s:17:\"object_owner_link\";s:45:\"/wealthment/index.php/profile/wealthmentadmin\";s:18:\"object_owner_photo\";s:47:\"/wealthment/public/user/04/0004_8bc2.JPG?c=8d1c\";s:24:\"object_owner_description\";s:0:\"\";s:5:\"label\";s:4:\"post\";}}',100,1,0,'2014-03-27 13:29:14');
/*!40000 ALTER TABLE `engine4_core_mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_mailrecipients`
--

DROP TABLE IF EXISTS `engine4_core_mailrecipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_mailrecipients` (
  `recipient_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mail_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`recipient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_mailrecipients`
--

LOCK TABLES `engine4_core_mailrecipients` WRITE;
/*!40000 ALTER TABLE `engine4_core_mailrecipients` DISABLE KEYS */;
INSERT INTO `engine4_core_mailrecipients` VALUES (1,1,2,NULL),(2,2,NULL,'ershad.qazi.jamil@gmail.com'),(3,3,2,NULL),(4,4,2,NULL),(5,5,NULL,'ersh2121@yahoo.com'),(6,6,3,NULL),(7,7,3,NULL),(8,8,1,NULL),(9,9,2,NULL),(10,10,3,NULL),(11,11,3,NULL),(12,12,3,NULL),(13,13,3,NULL),(14,14,1,NULL),(15,15,2,NULL),(16,16,3,NULL),(17,17,2,NULL),(18,18,3,NULL),(19,19,3,NULL),(20,20,2,NULL),(21,21,2,NULL),(22,22,1,NULL),(23,23,2,NULL),(24,24,1,NULL),(25,25,1,NULL),(26,26,3,NULL),(27,27,NULL,'wealthment@gmail.com'),(28,28,2,NULL),(29,29,4,NULL),(30,30,5,NULL),(31,31,NULL,'wealthment@gmail.com'),(32,32,NULL,'wealthment@gmail.com'),(33,33,NULL,'wealthment@gmail.com'),(34,34,NULL,'wealthment@gmail.com'),(35,35,3,NULL),(36,36,NULL,'wealthment@gmail.com'),(37,37,3,NULL),(38,38,3,NULL),(39,39,1,NULL);
/*!40000 ALTER TABLE `engine4_core_mailrecipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_mailtemplates`
--

DROP TABLE IF EXISTS `engine4_core_mailtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_mailtemplates` (
  `mailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `vars` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mailtemplate_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_mailtemplates`
--

LOCK TABLES `engine4_core_mailtemplates` WRITE;
/*!40000 ALTER TABLE `engine4_core_mailtemplates` DISABLE KEYS */;
INSERT INTO `engine4_core_mailtemplates` VALUES (1,'header','core',''),(2,'footer','core',''),(3,'header_member','core',''),(4,'footer_member','core',''),(5,'core_contact','core','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message]'),(6,'core_verification','core','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link]'),(7,'core_verification_password','core','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link],[password]'),(8,'core_welcome','core','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link]'),(9,'core_welcome_password','core','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link],[password]'),(10,'notify_admin_user_signup','core','[host],[email],[date],[recipient_title],[object_title],[object_link]'),(11,'core_lostpassword','core','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link]'),(12,'notify_commented','activity','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(13,'notify_commented_commented','activity','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(14,'notify_liked','activity','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(15,'notify_liked_commented','activity','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(16,'user_account_approved','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo]'),(17,'notify_friend_accepted','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(18,'notify_friend_request','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(19,'notify_friend_follow_request','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(20,'notify_friend_follow_accepted','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(21,'notify_friend_follow','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(22,'notify_post_user','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(23,'notify_tagged','user','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(24,'notify_message_new','messages','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(25,'invite','invite','[host],[email],[sender_email],[sender_title],[sender_link],[sender_photo],[message],[object_link],[code]'),(26,'invite_code','invite','[host],[email],[sender_email],[sender_title],[sender_link],[sender_photo],[message],[object_link],[code]'),(27,'payment_subscription_active','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(28,'payment_subscription_cancelled','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(29,'payment_subscription_expired','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(30,'payment_subscription_overdue','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(31,'payment_subscription_pending','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(32,'payment_subscription_recurrence','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(33,'payment_subscription_refunded','payment','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[subscription_title],[subscription_description],[object_link]'),(34,'notify_group_accepted','group','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(35,'notify_group_approve','group','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(36,'notify_group_discussion_reply','group','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(37,'notify_group_discussion_response','group','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(38,'notify_group_invite','group','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(39,'notify_group_promote','group','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),(40,'notify_wall_tag','wall','[host],[email],[recipient_title],[object_title],[object_link]'),(41,'notify_hequestion_answer','hequestion','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link]'),(42,'notify_hequestion_ask','hequestion','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link]'),(43,'notify_hequestion_follow','hequestion','[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link]'),(44,'like_suggest_page','like','[user],[link]'),(45,'like_suggest_user','like','[user],[link]');
/*!40000 ALTER TABLE `engine4_core_mailtemplates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_menuitems`
--

DROP TABLE IF EXISTS `engine4_core_menuitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_menuitems` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  `menu` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `submenu` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `custom` tinyint(1) NOT NULL DEFAULT '0',
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `LOOKUP` (`name`,`order`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_menuitems`
--

LOCK TABLES `engine4_core_menuitems` WRITE;
/*!40000 ALTER TABLE `engine4_core_menuitems` DISABLE KEYS */;
INSERT INTO `engine4_core_menuitems` VALUES (1,'core_main_home','core','Home','User_Plugin_Menus','','core_main','',0,0,1),(2,'core_sitemap_home','core','Home','','{\"route\":\"default\"}','core_sitemap','',1,0,1),(3,'core_footer_privacy','core','Privacy & Terms','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"privacy\"}','core_footer','',1,0,3),(4,'core_footer_terms','core','Terms of Service','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"terms\"}','core_footer','',0,0,4),(5,'core_footer_contact','core','Contact Us','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"contact\"}','core_footer','',1,0,1),(6,'core_mini_admin','core','Admin','User_Plugin_Menus','','core_mini','',1,0,6),(7,'core_mini_profile','user','My Profile','User_Plugin_Menus','','core_mini','',1,0,5),(8,'core_mini_settings','user','Settings','User_Plugin_Menus','','core_mini','',0,0,3),(9,'core_mini_auth','user','Auth','User_Plugin_Menus','','core_mini','',1,0,2),(10,'core_mini_signup','user','Signup','User_Plugin_Menus','','core_mini','',1,0,1),(11,'core_admin_main_home','core','Home','','{\"route\":\"admin_default\"}','core_admin_main','',1,0,1),(12,'core_admin_main_manage','core','Manage','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_manage',1,0,2),(13,'core_admin_main_settings','core','Settings','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_settings',1,0,3),(14,'core_admin_main_plugins','core','Plugins','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_plugins',1,0,4),(15,'core_admin_main_layout','core','Layout','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_layout',1,0,5),(16,'core_admin_main_ads','core','Ads','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_ads',1,0,6),(17,'core_admin_main_stats','core','Stats','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_stats',1,0,8),(18,'core_admin_main_manage_levels','core','Member Levels','','{\"route\":\"admin_default\",\"module\":\"authorization\",\"controller\":\"level\"}','core_admin_main_manage','',1,0,2),(19,'core_admin_main_manage_networks','network','Networks','','{\"route\":\"admin_default\",\"module\":\"network\",\"controller\":\"manage\"}','core_admin_main_manage','',1,0,3),(20,'core_admin_main_manage_announcements','announcement','Announcements','','{\"route\":\"admin_default\",\"module\":\"announcement\",\"controller\":\"manage\"}','core_admin_main_manage','',1,0,4),(21,'core_admin_message_mail','core','Email All Members','','{\"route\":\"admin_default\",\"module\":\"core\",\"controller\":\"message\",\"action\":\"mail\"}','core_admin_main_manage','',1,0,5),(22,'core_admin_main_manage_reports','core','Abuse Reports','','{\"route\":\"admin_default\",\"module\":\"core\",\"controller\":\"report\"}','core_admin_main_manage','',1,0,6),(23,'core_admin_main_manage_packages','core','Packages & Plugins','','{\"route\":\"admin_default\",\"module\":\"core\",\"controller\":\"packages\"}','core_admin_main_manage','',1,0,7),(24,'core_admin_main_settings_general','core','General Settings','','{\"route\":\"core_admin_settings\",\"action\":\"general\"}','core_admin_main_settings','',1,0,1),(25,'core_admin_main_settings_locale','core','Locale Settings','','{\"route\":\"core_admin_settings\",\"action\":\"locale\"}','core_admin_main_settings','',1,0,1),(26,'core_admin_main_settings_fields','fields','Profile Questions','','{\"route\":\"admin_default\",\"module\":\"user\",\"controller\":\"fields\"}','core_admin_main_settings','',1,0,2),(27,'core_admin_main_wibiya','core','Wibiya Integration','','{\"route\":\"admin_default\", \"action\":\"wibiya\", \"controller\":\"settings\", \"module\":\"core\"}','core_admin_main_settings','',1,0,4),(28,'core_admin_main_settings_spam','core','Spam & Banning Tools','','{\"route\":\"core_admin_settings\",\"action\":\"spam\"}','core_admin_main_settings','',1,0,5),(29,'core_admin_main_settings_mailtemplates','core','Mail Templates','','{\"route\":\"admin_default\",\"controller\":\"mail\",\"action\":\"templates\"}','core_admin_main_settings','',1,0,6),(30,'core_admin_main_settings_mailsettings','core','Mail Settings','','{\"route\":\"admin_default\",\"controller\":\"mail\",\"action\":\"settings\"}','core_admin_main_settings','',1,0,7),(31,'core_admin_main_settings_performance','core','Performance & Caching','','{\"route\":\"core_admin_settings\",\"action\":\"performance\"}','core_admin_main_settings','',1,0,8),(32,'core_admin_main_settings_password','core','Admin Password','','{\"route\":\"core_admin_settings\",\"action\":\"password\"}','core_admin_main_settings','',1,0,9),(33,'core_admin_main_settings_tasks','core','Task Scheduler','','{\"route\":\"admin_default\",\"controller\":\"tasks\"}','core_admin_main_settings','',1,0,10),(34,'core_admin_main_layout_content','core','Layout Editor','','{\"route\":\"admin_default\",\"controller\":\"content\"}','core_admin_main_layout','',1,0,1),(35,'core_admin_main_layout_themes','core','Theme Editor','','{\"route\":\"admin_default\",\"controller\":\"themes\"}','core_admin_main_layout','',1,0,2),(36,'core_admin_main_layout_files','core','File & Media Manager','','{\"route\":\"admin_default\",\"controller\":\"files\"}','core_admin_main_layout','',1,0,3),(37,'core_admin_main_layout_language','core','Language Manager','','{\"route\":\"admin_default\",\"controller\":\"language\"}','core_admin_main_layout','',1,0,4),(38,'core_admin_main_layout_menus','core','Menu Editor','','{\"route\":\"admin_default\",\"controller\":\"menus\"}','core_admin_main_layout','',1,0,5),(39,'core_admin_main_ads_manage','core','Manage Ad Campaigns','','{\"route\":\"admin_default\",\"controller\":\"ads\"}','core_admin_main_ads','',1,0,1),(40,'core_admin_main_ads_create','core','Create New Campaign','','{\"route\":\"admin_default\",\"controller\":\"ads\",\"action\":\"create\"}','core_admin_main_ads','',1,0,2),(41,'core_admin_main_ads_affiliate','core','SE Affiliate Program','','{\"route\":\"admin_default\",\"controller\":\"settings\",\"action\":\"affiliate\"}','core_admin_main_ads','',1,0,3),(42,'core_admin_main_ads_viglink','core','VigLink','','{\"route\":\"admin_default\",\"controller\":\"settings\",\"action\":\"viglink\"}','core_admin_main_ads','',1,0,4),(43,'core_admin_main_stats_statistics','core','Site-wide Statistics','','{\"route\":\"admin_default\",\"controller\":\"stats\"}','core_admin_main_stats','',1,0,1),(44,'core_admin_main_stats_url','core','Referring URLs','','{\"route\":\"admin_default\",\"controller\":\"stats\",\"action\":\"referrers\"}','core_admin_main_stats','',1,0,2),(45,'core_admin_main_stats_resources','core','Server Information','','{\"route\":\"admin_default\",\"controller\":\"system\"}','core_admin_main_stats','',1,0,3),(46,'core_admin_main_stats_logs','core','Log Browser','','{\"route\":\"admin_default\",\"controller\":\"log\",\"action\":\"index\"}','core_admin_main_stats','',1,0,3),(47,'core_admin_banning_general','core','Spam & Banning Tools','','{\"route\":\"core_admin_settings\",\"action\":\"spam\"}','core_admin_banning','',1,0,1),(48,'adcampaign_admin_main_edit','core','Edit Settings','','{\"route\":\"admin_default\",\"module\":\"core\",\"controller\":\"ads\",\"action\":\"edit\"}','adcampaign_admin_main','',1,0,1),(49,'adcampaign_admin_main_manageads','core','Manage Advertisements','','{\"route\":\"admin_default\",\"module\":\"core\",\"controller\":\"ads\",\"action\":\"manageads\"}','adcampaign_admin_main','',1,0,2),(50,'core_admin_main_settings_activity','activity','Activity Feed Settings','','{\"route\":\"admin_default\",\"module\":\"activity\",\"controller\":\"settings\",\"action\":\"index\"}','core_admin_main_settings','',1,0,4),(51,'core_admin_main_settings_notifications','activity','Default Email Notifications','','{\"route\":\"admin_default\",\"module\":\"activity\",\"controller\":\"settings\",\"action\":\"notifications\"}','core_admin_main_settings','',1,0,11),(52,'authorization_admin_main_manage','authorization','View Member Levels','','{\"route\":\"admin_default\",\"module\":\"authorization\",\"controller\":\"level\"}','authorization_admin_main','',1,0,1),(53,'authorization_admin_main_level','authorization','Member Level Settings','','{\"route\":\"admin_default\",\"module\":\"authorization\",\"controller\":\"level\",\"action\":\"edit\"}','authorization_admin_main','',1,0,3),(54,'authorization_admin_level_main','authorization','Level Info','','{\"route\":\"admin_default\",\"module\":\"authorization\",\"controller\":\"level\",\"action\":\"edit\"}','authorization_admin_level','',1,0,1),(55,'core_main_user','user','Members','','{\"route\":\"user_general\",\"action\":\"browse\"}','core_main','',0,0,8),(56,'core_sitemap_user','user','Members','','{\"route\":\"user_general\",\"action\":\"browse\"}','core_sitemap','',1,0,2),(57,'user_home_updates','user','View Recent Updates','','{\"route\":\"recent_activity\",\"icon\":\"application/modules/User/externals/images/links/updates.png\"}','user_home','',1,0,1),(58,'user_home_view','user','View My Profile','User_Plugin_Menus','{\"route\":\"user_profile_self\",\"icon\":\"application/modules/User/externals/images/links/profile.png\"}','user_home','',1,0,2),(59,'user_home_edit','user','Edit My Profile','User_Plugin_Menus','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"edit\",\"action\":\"profile\",\"icon\":\"application/modules/User/externals/images/links/edit.png\"}','user_home','',1,0,3),(60,'user_home_friends','user','Browse Members','','{\"route\":\"user_general\",\"controller\":\"index\",\"action\":\"browse\",\"icon\":\"application/modules/User/externals/images/links/search.png\"}','user_home','',1,0,4),(61,'user_profile_edit','user','Edit Profile','User_Plugin_Menus','','user_profile','',1,0,1),(62,'user_profile_friend','user','Friends','User_Plugin_Menus','','user_profile','',1,0,3),(63,'user_profile_block','user','Block','User_Plugin_Menus','','user_profile','',1,0,4),(64,'user_profile_report','user','Report User','User_Plugin_Menus','','user_profile','',1,0,5),(65,'user_profile_admin','user','Admin Settings','User_Plugin_Menus','','user_profile','',1,0,9),(66,'user_edit_profile','user','Personal Info','','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"edit\",\"action\":\"profile\"}','user_edit','',1,0,1),(67,'user_edit_photo','user','Edit My Photo','','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"edit\",\"action\":\"photo\"}','user_edit','',1,0,2),(68,'user_edit_style','user','Profile Style','User_Plugin_Menus','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"edit\",\"action\":\"style\"}','user_edit','',1,0,3),(69,'user_settings_general','user','General','','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"settings\",\"action\":\"general\"}','user_settings','',1,0,1),(70,'user_settings_privacy','user','Privacy','','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"settings\",\"action\":\"privacy\"}','user_settings','',1,0,2),(71,'user_settings_notifications','user','Notifications','','{\"route\":\"user_extended\",\"module\":\"user\",\"controller\":\"settings\",\"action\":\"notifications\"}','user_settings','',1,0,3),(72,'user_settings_password','user','Change Password','','{\"route\":\"user_extended\", \"module\":\"user\", \"controller\":\"settings\", \"action\":\"password\"}','user_settings','',1,0,5),(73,'user_settings_delete','user','Delete Account','User_Plugin_Menus::canDelete','{\"route\":\"user_extended\", \"module\":\"user\", \"controller\":\"settings\", \"action\":\"delete\"}','user_settings','',1,0,6),(74,'core_admin_main_manage_members','user','Members','','{\"route\":\"admin_default\",\"module\":\"user\",\"controller\":\"manage\"}','core_admin_main_manage','',1,0,1),(75,'core_admin_main_signup','user','Signup Process','','{\"route\":\"admin_default\", \"controller\":\"signup\", \"module\":\"user\"}','core_admin_main_settings','',1,0,3),(76,'core_admin_main_facebook','user','Facebook Integration','','{\"route\":\"admin_default\", \"action\":\"facebook\", \"controller\":\"settings\", \"module\":\"user\"}','core_admin_main_settings','',1,0,4),(77,'core_admin_main_twitter','user','Twitter Integration','','{\"route\":\"admin_default\", \"action\":\"twitter\", \"controller\":\"settings\", \"module\":\"user\"}','core_admin_main_settings','',1,0,4),(78,'core_admin_main_janrain','user','Janrain Integration','','{\"route\":\"admin_default\", \"action\":\"janrain\", \"controller\":\"settings\", \"module\":\"user\"}','core_admin_main_settings','',1,0,4),(79,'core_admin_main_settings_friends','user','Friendship Settings','','{\"route\":\"admin_default\",\"module\":\"user\",\"controller\":\"settings\",\"action\":\"friends\"}','core_admin_main_settings','',1,0,6),(80,'user_admin_banning_logins','user','Login History','','{\"route\":\"admin_default\",\"module\":\"user\",\"controller\":\"logins\",\"action\":\"index\"}','core_admin_banning','',1,0,2),(81,'authorization_admin_level_user','user','Members','','{\"route\":\"admin_default\",\"module\":\"user\",\"controller\":\"settings\",\"action\":\"level\"}','authorization_admin_level','',1,0,2),(82,'core_mini_messages','messages','Messages','Messages_Plugin_Menus','','core_mini','',1,0,4),(83,'user_profile_message','messages','Send Message','Messages_Plugin_Menus','','user_profile','',1,0,2),(84,'authorization_admin_level_messages','messages','Messages','','{\"route\":\"admin_default\",\"module\":\"messages\",\"controller\":\"settings\",\"action\":\"level\"}','authorization_admin_level','',1,0,3),(85,'messages_main_inbox','messages','Inbox','','{\"route\":\"messages_general\",\"action\":\"inbox\"}','messages_main','',1,0,1),(86,'messages_main_outbox','messages','Sent Messages','','{\"route\":\"messages_general\",\"action\":\"outbox\"}','messages_main','',1,0,2),(87,'messages_main_compose','messages','Compose Message','','{\"route\":\"messages_general\",\"action\":\"compose\"}','messages_main','',1,0,3),(88,'user_settings_network','network','Networks','','{\"route\":\"user_extended\", \"module\":\"user\", \"controller\":\"settings\", \"action\":\"network\"}','user_settings','',1,0,3),(89,'core_main_invite','invite','Invite','Invite_Plugin_Menus::canInvite','{\"route\":\"default\",\"module\":\"invite\"}','core_main','',0,0,7),(90,'user_home_invite','invite','Invite Your Friends','Invite_Plugin_Menus::canInvite','{\"route\":\"default\",\"module\":\"invite\",\"icon\":\"application/modules/Invite/externals/images/invite.png\"}','user_home','',1,0,5),(91,'core_admin_main_settings_storage','core','Storage System','','{\"route\":\"admin_default\",\"module\":\"storage\",\"controller\":\"services\",\"action\":\"index\"}','core_admin_main_settings','',1,0,11),(92,'user_settings_payment','user','Subscription','Payment_Plugin_Menus','{\"route\":\"default\", \"module\":\"payment\", \"controller\":\"settings\", \"action\":\"index\"}','user_settings','',1,0,4),(93,'core_admin_main_payment','payment','Billing','','{\"uri\":\"javascript:void(0);this.blur();\"}','core_admin_main','core_admin_main_payment',1,0,7),(94,'core_admin_main_payment_transactions','payment','Transactions','','{\"route\":\"admin_default\",\"module\":\"payment\",\"controller\":\"index\",\"action\":\"index\"}','core_admin_main_payment','',1,0,1),(95,'core_admin_main_payment_settings','payment','Settings','','{\"route\":\"admin_default\",\"module\":\"payment\",\"controller\":\"settings\",\"action\":\"index\"}','core_admin_main_payment','',1,0,2),(96,'core_admin_main_payment_gateways','payment','Gateways','','{\"route\":\"admin_default\",\"module\":\"payment\",\"controller\":\"gateway\",\"action\":\"index\"}','core_admin_main_payment','',1,0,3),(97,'core_admin_main_payment_packages','payment','Plans','','{\"route\":\"admin_default\",\"module\":\"payment\",\"controller\":\"package\",\"action\":\"index\"}','core_admin_main_payment','',1,0,4),(98,'core_admin_main_payment_subscriptions','payment','Subscriptions','','{\"route\":\"admin_default\",\"module\":\"payment\",\"controller\":\"subscription\",\"action\":\"index\"}','core_admin_main_payment','',1,0,5),(99,'core_main_album','album','Albums','','{\"route\":\"album_general\",\"action\":\"browse\"}','core_main','',0,0,9),(100,'core_sitemap_album','album','Albums','','{\"route\":\"album_general\",\"action\":\"browse\"}','core_sitemap','',1,0,3),(101,'album_main_browse','album','Browse Albums','Album_Plugin_Menus::canViewAlbums','{\"route\":\"album_general\",\"action\":\"browse\"}','album_main','',1,0,1),(102,'album_main_manage','album','My Albums','Album_Plugin_Menus::canCreateAlbums','{\"route\":\"album_general\",\"action\":\"manage\"}','album_main','',1,0,2),(103,'album_main_upload','album','Add New Photos','Album_Plugin_Menus::canCreateAlbums','{\"route\":\"album_general\",\"action\":\"upload\"}','album_main','',1,0,3),(104,'album_quick_upload','album','Add New Photos','Album_Plugin_Menus::canCreateAlbums','{\"route\":\"album_general\",\"action\":\"upload\",\"class\":\"buttonlink icon_photos_new\"}','album_quick','',1,0,1),(105,'core_admin_main_plugins_album','album','Photo Albums','','{\"route\":\"admin_default\",\"module\":\"album\",\"controller\":\"manage\",\"action\":\"index\"}','core_admin_main_plugins','',1,0,999),(106,'album_admin_main_manage','album','View Albums','','{\"route\":\"admin_default\",\"module\":\"album\",\"controller\":\"manage\"}','album_admin_main','',1,0,1),(107,'album_admin_main_settings','album','Global Settings','','{\"route\":\"admin_default\",\"module\":\"album\",\"controller\":\"settings\"}','album_admin_main','',1,0,2),(108,'album_admin_main_level','album','Member Level Settings','','{\"route\":\"admin_default\",\"module\":\"album\",\"controller\":\"level\"}','album_admin_main','',1,0,3),(109,'album_admin_main_categories','album','Categories','','{\"route\":\"admin_default\",\"module\":\"album\",\"controller\":\"settings\", \"action\":\"categories\"}','album_admin_main','',1,0,4),(110,'authorization_admin_level_album','album','Photo Albums','','{\"route\":\"admin_default\",\"module\":\"album\",\"controller\":\"level\",\"action\":\"index\"}','authorization_admin_level','',1,0,999),(111,'mobi_browse_album','album','Albums','','{\"route\":\"album_general\",\"action\":\"browse\"}','mobi_browse','',1,0,2),(112,'core_main_group','group','Groups','','{\"route\":\"group_general\"}','core_main','',0,0,10),(113,'core_sitemap_group','group','Groups','','{\"route\":\"group_general\"}','core_sitemap','',1,0,6),(114,'group_main_browse','group','Browse Groups','','{\"route\":\"group_general\",\"action\":\"browse\"}','group_main','',1,0,1),(115,'group_main_manage','group','My Groups','Group_Plugin_Menus','{\"route\":\"group_general\",\"action\":\"manage\"}','group_main','',1,0,2),(116,'group_main_create','group','Create New Group','Group_Plugin_Menus','{\"route\":\"group_general\",\"action\":\"create\"}','group_main','',1,0,3),(117,'group_quick_create','group','Create New Group','Group_Plugin_Menus::canCreateGroups','{\"route\":\"group_general\",\"action\":\"create\",\"class\":\"buttonlink icon_group_new\"}','group_quick','',1,0,1),(118,'group_profile_edit','group','Edit Profile','Group_Plugin_Menus','','group_profile','',1,0,1),(119,'group_profile_style','group','Edit Styles','Group_Plugin_Menus','','group_profile','',1,0,2),(120,'group_profile_member','group','Member','Group_Plugin_Menus','','group_profile','',1,0,3),(121,'group_profile_report','group','Report Group','Group_Plugin_Menus','','group_profile','',1,0,4),(122,'group_profile_share','group','Share','Group_Plugin_Menus','','group_profile','',1,0,5),(123,'group_profile_invite','group','Invite','Group_Plugin_Menus','','group_profile','',1,0,6),(124,'group_profile_message','group','Message Members','Group_Plugin_Menus','','group_profile','',1,0,7),(125,'core_admin_main_plugins_group','group','Groups','','{\"route\":\"admin_default\",\"module\":\"group\",\"controller\":\"manage\"}','core_admin_main_plugins','',1,0,999),(126,'group_admin_main_manage','group','Manage Groups','','{\"route\":\"admin_default\",\"module\":\"group\",\"controller\":\"manage\"}','group_admin_main','',1,0,1),(127,'group_admin_main_settings','group','Global Settings','','{\"route\":\"admin_default\",\"module\":\"group\",\"controller\":\"settings\"}','group_admin_main','',1,0,2),(128,'group_admin_main_level','group','Member Level Settings','','{\"route\":\"admin_default\",\"module\":\"group\",\"controller\":\"settings\",\"action\":\"level\"}','group_admin_main','',1,0,3),(129,'group_admin_main_categories','group','Categories','','{\"route\":\"admin_default\",\"module\":\"group\",\"controller\":\"settings\",\"action\":\"categories\"}','group_admin_main','',1,0,4),(130,'authorization_admin_level_group','group','Groups','','{\"route\":\"admin_default\",\"module\":\"group\",\"controller\":\"settings\",\"action\":\"level\"}','authorization_admin_level','',1,0,999),(131,'mobi_browse_group','group','Groups','','{\"route\":\"group_general\"}','mobi_browse','',1,0,8),(132,'core_admin_main_plugins_hecore','hecore','Hire-Experts','','{\"route\":\"admin_default\",\"module\":\"hecore\",\"controller\":\"index\"}','core_admin_main_plugins','',1,0,887),(133,'hecore_admin_main_settings','hecore','hecore_Global Settings','','{\"route\":\"admin_default\",\"module\":\"hecore\",\"controller\":\"settings\"}','hecore_admin_main','',1,0,2),(134,'hecore_admin_main_plugins','hecore','hecore_Plugins','','{\"route\":\"admin_default\",\"module\":\"hecore\",\"controller\":\"index\"}','hecore_admin_main','',1,0,3),(135,'hecore_admin_main_featureds','hecore','hecore_Featured Members','','{\"route\":\"admin_default\",\"module\":\"hecore\",\"controller\":\"featureds\"}','hecore_admin_main','',1,0,1),(136,'wall_admin_main_setting','wall','WALL_ADMIN_MAIN_SETTING','','{\"route\":\"admin_default\", \"module\": \"wall\", \"controller\": \"setting\", \"action\": \"index\"}','wall_admin_main',NULL,1,0,2),(137,'wall_admin_main_activity','wall','WALL_ADMIN_MAIN_ACTIVITY','','{\"route\":\"admin_default\",\"module\":\"activity\",\"controller\":\"settings\",\"action\":\"index\"}','wall_admin_main',NULL,1,0,1),(138,'wall_admin_main_plugins_wall','wall','WALL_ADMIN_MAIN_PLUGINS_WALL','','{\"route\":\"admin_default\", \"module\": \"wall\", \"controller\": \"setting\", \"action\": \"index\"}','core_admin_main_plugins',NULL,1,0,888),(139,'core_admin_main_plugins_pinfeed','pinfeed','HE - Pin-Feed',NULL,'{\"route\":\"admin_default\",\"module\":\"pinfeed\",\"controller\":\"index\"}','core_admin_main_plugins',NULL,1,0,888),(140,'custom_140','core','Stocks','','{\"uri\":\"index.php\\/pages\\/stockwealthposts\",\"icon\":\"\"}','core_main','',1,1,2),(141,'custom_141','core','Real Estate','','{\"uri\":\"index.php\\/pages\\/realestatewealthposts\",\"icon\":\"\"}','core_main','',1,1,3),(142,'custom_142','core','Retirement','','{\"uri\":\"index.php\\/pages\\/retirementwealthposts\",\"icon\":\"\"}','core_main','',1,1,4),(143,'custom_143','core','Other Savings','','{\"uri\":\"index.php\\/pages\\/othersavingswealthposts\",\"icon\":\"\"}','core_main','',1,1,5),(144,'core_admin_main_plugins_mobile','mobile','HE - Mobile',NULL,'{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"content\",\"action\":\"index\"}','core_admin_main_plugins','',1,0,888),(145,'mobile_admin_main_content','mobile','Layout Editor','','{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"content\",\"action\":\"index\"}','mobile_admin_main','',1,0,1),(146,'mobile_admin_main_themes','mobile','Theme Editor',NULL,'{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"themes\",\"action\":\"index\"}','mobile_admin_main',NULL,1,0,2),(147,'mobile_admin_main_menus','mobile','MOBILE_MENU_EDITOR',NULL,'{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"menus\",\"action\":\"index\"}','mobile_admin_main',NULL,1,0,3),(148,'mobile_admin_main_plugin_settings','mobile','MOBILE_PLUGIN_SETTINGS','Mobile_Plugin_Menus','{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"plugin-settings\",\"action\":\"index\"}','mobile_admin_main',NULL,1,0,4),(149,'custom_149','core','FAQ','','{\"uri\":\"index.php\\/pages\\/faq\",\"icon\":\"\"}','core_footer','',1,1,2),(150,'custom_150','core','Our Mission','','{\"uri\":\"index.php\\/pages\\/ourmission\",\"icon\":\"\"}','core_mini','',1,1,9),(151,'custom_151','core','How This Works','','{\"uri\":\"index.php\\/pages\\/howthisworks\",\"icon\":\"\"}','core_mini','',1,1,8),(152,'custom_152','core','Settings','','{\"uri\":\"index.php\\/members\\/settings\\/general\",\"icon\":\"\",\"target\":\"\",\"enabled\":\"1\"}','core_footer','',1,1,999),(153,'core_main_hequestion','hequestion','HEQUESTION_QUESTION','','{\"route\":\"hequestion_general\"}','core_main','',0,0,6),(154,'core_sitemap_hequestion','hequestion','HEQUESTION_QUESTION','','{\"route\":\"hequestion_general\"}','core_sitemap','',1,0,5),(155,'core_admin_main_plugins_hequestion','hequestion','HEQUESTION_ADMIN_HEQUESTION','','{\"route\":\"admin_default\",\"module\":\"hequestion\",\"controller\":\"manage\"}','core_admin_main_plugins','',1,0,889),(156,'hequestion_admin_main_manage','hequestion','HEQUESTION_ADMIN_MANAGE','','{\"route\":\"admin_default\",\"module\":\"hequestion\",\"controller\":\"manage\"}','hequestion_admin_main','',1,0,1),(157,'hequestion_admin_main_settings','hequestion','Global Settings','','{\"route\":\"admin_default\",\"module\":\"hequestion\",\"controller\":\"settings\"}','hequestion_admin_main','',1,0,2),(158,'hequestion_admin_main_level','hequestion','HEQUESTION_ADMIN_LEVELS','','{\"route\":\"admin_default\",\"module\":\"hequestion\",\"controller\":\"settings\",\"action\":\"level\"}','hequestion_admin_main','',1,0,3),(159,'authorization_admin_level_hequestion','hequestion','HEQUESTION_ADMIN_HEQUESTION','','{\"route\":\"admin_default\",\"module\":\"hequestion\",\"controller\":\"settings\",\"action\":\"level\"}','authorization_admin_level','',1,0,999),(160,'hequestion_main_browse','hequestion','HEQUESTION_BROWSE',NULL,'{\"route\":\"hequestion_general\"}','hequestion_main',NULL,1,0,1),(161,'hequestion_main_manage','hequestion','HEQUESTION_MY','Hequestion_Plugin_Menus','{\"route\":\"hequestion_general\", \"action\": \"manage\"}','hequestion_main',NULL,1,0,2),(162,'core_admin_main_plugins_hashtag','hashtag','HE - Hashtags',NULL,'{\"route\":\"admin_default\",\"module\":\"hashtag\",\"controller\":\"index\"}','core_admin_main_plugins',NULL,1,0,888),(163,'hashtag_admin_main_settings','hashtag','Period settings','','{\"route\":\"admin_default\",\"module\":\"hashtag\",\"controller\":\"index\",\"action\":\"index\"}','hashtag_admin_main',NULL,1,0,1),(164,'hashtag_admin_main_settings_count','hashtag','Count settings','','{\"route\":\"admin_default\",\"module\":\"hashtag\",\"controller\":\"index\",\"action\":\"count\"}','hashtag_admin_main',NULL,1,0,2),(165,'user_edit_interests','like','like_Profile Interests','Like_Plugin_Menus','{\"route\":\"like_interests\",\"action\":\"index\"}','user_edit','',1,0,4),(166,'store_product_profile_promote','like','LIKE_PromoteProduct','Like_Plugin_Menus','','store_product_profile','',1,0,2),(167,'core_admin_main_plugins_like','like','HE - Like','','{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"settings\"}','core_admin_main_plugins','',1,0,888),(168,'like_admin_main_level','like','Level Settings','','{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"level\"}','like_admin_main','',1,0,2),(169,'like_admin_main_settings','like','Settings','','{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"settings\"}','like_admin_main','',1,0,3),(170,'like_admin_main_faq','like','FAQ','','{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"faq\"}','like_admin_main','',1,0,4),(171,'core_admin_main_plugins_heloginpopup','heloginpopup','He - Login Popup',NULL,'{\"route\":\"admin_default\",\"module\":\"heloginpopup\",\"controller\":\"index\",\"action\":\"index\"}','core_admin_main_plugins',NULL,1,0,888),(172,'core_admin_main_plugins_welcome','welcome','HE - Welcome','','{\"route\":\"admin_default\",\"module\":\"welcome\",\"controller\":\"slideshow\"}','core_admin_main_plugins','',1,0,888),(173,'advancedsearch_admin_main_types','advancedsearch','AS_Search types',NULL,'{\"route\":\"admin_default\",\"module\":\"advancedsearch\",\"controller\":\"index\"}','advancedsearch_admin_main','',1,0,999),(174,'advancedsearch_admin_main_icons','advancedsearch','AS_Items icons',NULL,'{\"route\":\"admin_default\",\"module\":\"advancedsearch\",\"controller\":\"index\",\"action\":\"icons\"}','advancedsearch_admin_main','',1,0,999),(175,'core_admin_main_plugins_advancedsearch','advancedsearch','AS_Advanced Search',NULL,'{\"route\":\"admin_default\",\"module\":\"advancedsearch\",\"controller\":\"index\"}','core_admin_main_plugins','',1,0,888),(176,'custom_176','core','Wealth Maturity','','{\"uri\":\"index.php\\/pages\\/wealthmaturity\",\"icon\":\"\"}','core_mini','',1,1,7);
/*!40000 ALTER TABLE `engine4_core_menuitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_menus`
--

DROP TABLE IF EXISTS `engine4_core_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` enum('standard','hidden','custom') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'standard',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `order` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_menus`
--

LOCK TABLES `engine4_core_menus` WRITE;
/*!40000 ALTER TABLE `engine4_core_menus` DISABLE KEYS */;
INSERT INTO `engine4_core_menus` VALUES (1,'core_main','standard','Main Navigation Menu',1),(2,'core_mini','standard','Mini Navigation Menu',2),(3,'core_footer','standard','Footer Menu',3),(4,'core_sitemap','standard','Sitemap',4),(5,'user_home','standard','Member Home Quick Links Menu',999),(6,'user_profile','standard','Member Profile Options Menu',999),(7,'user_edit','standard','Member Edit Profile Navigation Menu',999),(8,'user_settings','standard','Member Settings Navigation Menu',999),(9,'messages_main','standard','Messages Main Navigation Menu',999),(10,'album_main','standard','Album Main Navigation Menu',999),(11,'album_quick','standard','Album Quick Navigation Menu',999),(12,'group_main','standard','Group Main Navigation Menu',999),(13,'group_profile','standard','Group Profile Options Menu',999);
/*!40000 ALTER TABLE `engine4_core_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_migrations`
--

DROP TABLE IF EXISTS `engine4_core_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_migrations` (
  `package` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `current` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`package`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_migrations`
--

LOCK TABLES `engine4_core_migrations` WRITE;
/*!40000 ALTER TABLE `engine4_core_migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_modules`
--

DROP TABLE IF EXISTS `engine4_core_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_modules` (
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `version` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('core','standard','extra') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'extra',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_modules`
--

LOCK TABLES `engine4_core_modules` WRITE;
/*!40000 ALTER TABLE `engine4_core_modules` DISABLE KEYS */;
INSERT INTO `engine4_core_modules` VALUES ('activity','Activity','Activity','4.7.0',1,'core'),('advancedsearch','Advanced Search Plugin','Hire-Experts Advanced Search Plugin','4.5.1p6',0,'extra'),('album','Albums','Albums','4.7.0',1,'extra'),('announcement','Announcements','Announcements','4.7.0',1,'standard'),('authorization','Authorization','Authorization','4.7.0',1,'core'),('core','Core','Core','4.7.0',1,'core'),('fields','Fields','Fields','4.7.0',1,'core'),('group','Groups','Groups','4.7.0',1,'extra'),('hashtag','Hashtag','Hashtag','4.5.1p3',1,'extra'),('hecore','Hire-Experts Core Module','Hire-Experts Core Module','4.2.0p9',1,'extra'),('heloginpopup','HE - Loginpopup','Hire-Experts Loginpopup Plugin','4.5.0p2',1,'extra'),('hequestion','Questions','Questions','4.2.5',0,'extra'),('invite','Invite','Invite','4.5.0',1,'standard'),('like','Like','Like Plugin','4.2.2p4',1,'extra'),('messages','Messages','Messages','4.7.0',1,'standard'),('mobile','Mobile','Mobile','4.1.8p7',1,'extra'),('network','Networks','Networks','4.7.0',1,'standard'),('payment','Payment','Payment','4.7.0',1,'standard'),('pinfeed','Pinfeed','Pinfeed','4.5.0',1,'extra'),('storage','Storage','Storage','4.7.0',1,'core'),('user','Members','Members','4.7.0',1,'core'),('wall','Wall','Wall','4.2.6p8',1,'extra'),('wealthment','Wealthment','Everything about wealthment customization','4.0.0',1,'extra'),('welcome','Welcome','Welcome Slideshow','4.2.0p3',1,'extra');
/*!40000 ALTER TABLE `engine4_core_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_nodes`
--

DROP TABLE IF EXISTS `engine4_core_nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_nodes` (
  `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `signature` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `first_seen` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  PRIMARY KEY (`node_id`),
  UNIQUE KEY `signature` (`signature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_nodes`
--

LOCK TABLES `engine4_core_nodes` WRITE;
/*!40000 ALTER TABLE `engine4_core_nodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_pages`
--

DROP TABLE IF EXISTS `engine4_core_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_pages` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `custom` tinyint(1) NOT NULL DEFAULT '1',
  `fragment` tinyint(1) NOT NULL DEFAULT '0',
  `layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `levels` text COLLATE utf8_unicode_ci,
  `provides` text COLLATE utf8_unicode_ci,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_pages`
--

LOCK TABLES `engine4_core_pages` WRITE;
/*!40000 ALTER TABLE `engine4_core_pages` DISABLE KEYS */;
INSERT INTO `engine4_core_pages` VALUES (1,'header','Site Header',NULL,'','','',0,1,'',NULL,'header-footer',0,0),(2,'footer','Site Footer',NULL,'','','',0,1,'',NULL,'header-footer',0,0),(3,'core_index_index','Landing Page',NULL,'Landing Page','Landing Page','',0,0,'default-simple',NULL,'no-viewer;no-subject',0,0),(4,'user_index_home','Member Home Page',NULL,'Member Home Page','This is the home page for members.','',0,0,'',NULL,'viewer;no-subject',0,0),(5,'user_profile_index','Member Profile',NULL,'Member Profile','This is a member\'s profile.','',0,0,'',NULL,'subject=user',0,0),(6,'core_help_contact','Contact Page',NULL,'Contact Us','This is the contact page','',0,0,'',NULL,'no-viewer;no-subject',0,0),(7,'core_help_privacy','Privacy Page',NULL,'Privacy Policy','This is the privacy policy page','',0,0,'',NULL,'no-viewer;no-subject',0,0),(8,'core_help_terms','Terms of Service Page',NULL,'Terms of Service','This is the terms of service page','',0,0,'',NULL,'no-viewer;no-subject',0,0),(9,'core_error_requireuser','Sign-in Required Page',NULL,'Sign-in Required','','',0,0,'',NULL,NULL,0,0),(10,'core_search_index','Search Page',NULL,'Searc','','',0,0,'',NULL,NULL,0,0),(11,'user_auth_login','Sign-in Page',NULL,'Sign-in','This is the site sign-in page.','',0,0,'',NULL,NULL,0,0),(12,'user_signup_index','Sign-up Page',NULL,'Sign-up','This is the site sign-up page.','',0,0,'',NULL,NULL,0,0),(13,'user_auth_forgot','Forgot Password Page',NULL,'Forgot Password','This is the site forgot password page.','',0,0,'',NULL,NULL,0,0),(14,'user_settings_general','User General Settings Page',NULL,'General','This page is the user general settings page.','',0,0,'',NULL,NULL,0,0),(15,'user_settings_privacy','User Privacy Settings Page',NULL,'Privacy','This page is the user privacy settings page.','',0,0,'',NULL,NULL,0,0),(16,'user_settings_network','User Networks Settings Page',NULL,'Networks','This page is the user networks settings page.','',0,0,'',NULL,NULL,0,0),(17,'user_settings_notifications','User Notifications Settings Page',NULL,'Notifications','This page is the user notification settings page.','',0,0,'',NULL,NULL,0,0),(18,'user_settings_password','User Change Password Settings Page',NULL,'Change Password','This page is the change password page.','',0,0,'',NULL,NULL,0,0),(19,'user_settings_delete','User Delete Account Settings Page',NULL,'Delete Account','This page is the delete accout page.','',0,0,'',NULL,NULL,0,0),(20,'invite_index_index','Invite Page',NULL,'Invite','','',0,0,'',NULL,NULL,0,0),(21,'messages_messages_compose','Messages Compose Page',NULL,'Compose','','',0,0,'',NULL,NULL,0,0),(22,'messages_messages_inbox','Messages Inbox Page',NULL,'Inbox','','',0,0,'',NULL,NULL,0,0),(23,'messages_messages_outbox','Messages Outbox Page',NULL,'Inbox','','',0,0,'',NULL,NULL,0,0),(24,'messages_messages_search','Messages Search Page',NULL,'Search','','',0,0,'',NULL,NULL,0,0),(25,'messages_messages_view','Messages View Page',NULL,'My Message','','',0,0,'',NULL,NULL,0,0),(26,'album_photo_view','Album Photo View Page',NULL,'Album Photo View','This page displays an album\'s photo.','',0,0,'',NULL,'subject=album_photo',0,0),(27,'album_album_view','Album View Page',NULL,'Album View','This page displays an album\'s photos.','',0,0,'',NULL,'subject=album',0,0),(28,'album_index_browse','Album Browse Page',NULL,'Album Browse','This page lists album entries.','',0,0,'',NULL,NULL,0,0),(29,'album_index_upload','Album Create Page',NULL,'Add New Photos','This page is the album create page.','',0,0,'',NULL,NULL,0,0),(30,'album_index_manage','Album Manage Page',NULL,'My Albums','This page lists album a user\'s albums.','',0,0,'',NULL,NULL,0,0),(31,'group_profile_index','Group Profile',NULL,'Group Profile','This is the profile for an group.','',0,0,'',NULL,'subject=group',0,0),(32,'mobi_group_profile','Mobile Group Profile',NULL,'Mobile Group Profile','This is the mobile verison of a group profile.','',0,0,'',NULL,NULL,0,0),(33,'group_index_browse','Group Browse Page',NULL,'Group Browse','This page lists groups.','',0,0,'',NULL,NULL,0,0),(34,'group_index_create','Group Create Page',NULL,'Group Create','This page allows users to create groups.','',0,0,'',NULL,NULL,0,0),(35,'group_index_manage','Group Manage Page',NULL,'My Groups','This page lists a user\'s groups.','',0,0,'',NULL,NULL,0,0),(36,'wall_index_view','Wall Posts',NULL,'Posts','','',0,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(37,'wall_index_welcome','Wall Welcome',NULL,'Welcome','','',0,0,'',NULL,'no-subject',0,0),(38,'pinfeed_index_index','Pin-Feed page','','Member Home Page','This is pint version of member home page','',1,0,'','',NULL,0,0),(39,NULL,'How This Works','howthisworks','How This Works','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(40,NULL,'Our Mission','ourmission','Our Mission','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(43,NULL,'Retirement Wealthposts','retirementwealthposts','Retirement Wealthposts','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(44,NULL,'Other Savings Wealthposts','othersavingswealthposts','Other Savings Wealthposts','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(45,NULL,'FAQ','faq','FAQ','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(46,'hequestion_index_index','Questions Home',NULL,'Browse Questions','','',0,0,'',NULL,'no-subject',0,0),(47,'hequestion_index_view','Question Profile',NULL,'Question','','',0,0,'',NULL,'no-subject',0,0),(48,'hequestion_index_box','Question Box',NULL,'Question','','',0,0,'',NULL,'no-subject',0,0),(49,'hequestion_index_manage','Questions Manage',NULL,'My Questions','','',0,0,'',NULL,'no-subject',0,0),(50,NULL,'Welcome to Wealthment','welcome','Welcome to Wealthment','','',1,0,'default-simple','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(51,NULL,'Stock Insights','stockinsights','Stock Insights','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(53,NULL,'Stock Wealthposts','stockwealthposts','Stock Wealthposts','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(54,NULL,'Real Estate Wealthposts','realestatewealthposts','Real Estate Wealthposts','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(55,NULL,'Wealth Maturity','wealthmaturity','Wealth Maturity','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(56,NULL,'Real Estate Insights','realestateinsights','Real Estate Insights','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(57,NULL,'Retirement Insights','retirementinsights','Retirement Insights','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(58,NULL,'Other Savings Insights','othersavingsinsights','Other Savings Insights','','',1,0,'','[\"1\",\"2\",\"3\",\"4\",\"5\"]','no-subject',0,0),(59,NULL,'Feedback and Bug Reporting',NULL,'','','',1,0,'',NULL,'no-subject',0,0);
/*!40000 ALTER TABLE `engine4_core_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_processes`
--

DROP TABLE IF EXISTS `engine4_core_processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_processes` (
  `pid` int(10) unsigned NOT NULL,
  `parent_pid` int(10) unsigned NOT NULL DEFAULT '0',
  `system_pid` int(10) unsigned NOT NULL DEFAULT '0',
  `started` int(10) unsigned NOT NULL,
  `timeout` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_processes`
--

LOCK TABLES `engine4_core_processes` WRITE;
/*!40000 ALTER TABLE `engine4_core_processes` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_processes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_referrers`
--

DROP TABLE IF EXISTS `engine4_core_referrers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_referrers` (
  `host` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `query` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `value` int(11) unsigned NOT NULL,
  PRIMARY KEY (`host`,`path`,`query`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_referrers`
--

LOCK TABLES `engine4_core_referrers` WRITE;
/*!40000 ALTER TABLE `engine4_core_referrers` DISABLE KEYS */;
INSERT INTO `engine4_core_referrers` VALUES ('wealthment.com','/wealthment/index.php/members/home','',2);
/*!40000 ALTER TABLE `engine4_core_referrers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_reports`
--

DROP TABLE IF EXISTS `engine4_core_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `subject_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `subject_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `category` (`category`),
  KEY `user_id` (`user_id`),
  KEY `read` (`read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_reports`
--

LOCK TABLES `engine4_core_reports` WRITE;
/*!40000 ALTER TABLE `engine4_core_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_routes`
--

DROP TABLE IF EXISTS `engine4_core_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_routes` (
  `name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `config` text COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_routes`
--

LOCK TABLES `engine4_core_routes` WRITE;
/*!40000 ALTER TABLE `engine4_core_routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_search`
--

DROP TABLE IF EXISTS `engine4_core_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_search` (
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hidden` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`type`,`id`),
  FULLTEXT KEY `LOOKUP` (`title`,`description`,`keywords`,`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_search`
--

LOCK TABLES `engine4_core_search` WRITE;
/*!40000 ALTER TABLE `engine4_core_search` DISABLE KEYS */;
INSERT INTO `engine4_core_search` VALUES ('group',1,'Apple Stock','','',''),('group',2,'Auto Stocks','This Group is for discussions about the auto industry','',''),('user',2,'Jeffrey Lee','','',''),('core_link',1,'From Startup to Billion-Dollar Biotech: An Inside Look at Vertex Pharmaceuticals (VRTX)','An interview with Barry Werth, author of \"The Antidote: Inside the World of New Pharma.\" - Max Macaluso - Health Care','',''),('user',3,'Ershad Jamil','','',''),('album',1,'Wall Photos','','',''),('core_link',2,'ESPN: The Worldwide Leader In Sports','ESPN.com provides comprehensive sports coverage.  Complete sports information including NFL, MLB, NBA, College Football, College Basketball scores and news.','',''),('user',1,'Wealthment Administrator','','',''),('album',2,'Wall Photos','','',''),('hequestion',83,'What is a good stock?','','',''),('hequestion',84,'What is better, 401k or IRA or IRA Roth?','','',''),('user',4,'Jeffrey Lee','','',''),('user',5,'Ershad Jamil','','','');
/*!40000 ALTER TABLE `engine4_core_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_serviceproviders`
--

DROP TABLE IF EXISTS `engine4_core_serviceproviders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_serviceproviders` (
  `serviceprovider_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `class` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`serviceprovider_id`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_serviceproviders`
--

LOCK TABLES `engine4_core_serviceproviders` WRITE;
/*!40000 ALTER TABLE `engine4_core_serviceproviders` DISABLE KEYS */;
INSERT INTO `engine4_core_serviceproviders` VALUES (1,'MySQL','database','mysql','Engine_ServiceLocator_Plugin_Database_Mysql',1),(2,'PDO MySQL','database','mysql_pdo','Engine_ServiceLocator_Plugin_Database_MysqlPdo',1),(3,'MySQLi','database','mysqli','Engine_ServiceLocator_Plugin_Database_Mysqli',1),(4,'File','cache','file','Engine_ServiceLocator_Plugin_Cache_File',1),(5,'APC','cache','apc','Engine_ServiceLocator_Plugin_Cache_Apc',1),(6,'Memcache','cache','memcached','Engine_ServiceLocator_Plugin_Cache_Memcached',1),(7,'Simple','captcha','image','Engine_ServiceLocator_Plugin_Captcha_Image',1),(8,'ReCaptcha','captcha','recaptcha','Engine_ServiceLocator_Plugin_Captcha_Recaptcha',1),(9,'SMTP','mail','smtp','Engine_ServiceLocator_Plugin_Mail_Smtp',1),(10,'Sendmail','mail','sendmail','Engine_ServiceLocator_Plugin_Mail_Sendmail',1),(11,'GD','image','gd','Engine_ServiceLocator_Plugin_Image_Gd',1),(12,'Imagick','image','imagick','Engine_ServiceLocator_Plugin_Image_Imagick',1),(13,'Akismet','akismet','standard','Engine_ServiceLocator_Plugin_Akismet',1);
/*!40000 ALTER TABLE `engine4_core_serviceproviders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_services`
--

DROP TABLE IF EXISTS `engine4_core_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_services` (
  `service_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `profile` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'default',
  `config` text COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `type` (`type`,`profile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_services`
--

LOCK TABLES `engine4_core_services` WRITE;
/*!40000 ALTER TABLE `engine4_core_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_servicetypes`
--

DROP TABLE IF EXISTS `engine4_core_servicetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_servicetypes` (
  `servicetype_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `interface` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`servicetype_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_servicetypes`
--

LOCK TABLES `engine4_core_servicetypes` WRITE;
/*!40000 ALTER TABLE `engine4_core_servicetypes` DISABLE KEYS */;
INSERT INTO `engine4_core_servicetypes` VALUES (1,'Database','database','Zend_Db_Adapter_Abstract',1),(2,'Cache','cache','Zend_Cache_Backend',1),(3,'Captcha','captcha','Zend_Captcha_Adapter',1),(4,'Mail Transport','mail','Zend_Mail_Transport_Abstract',1),(5,'Image','image','Engine_Image_Adapter_Abstract',1),(6,'Akismet','akismet','Zend_Service_Akismet',1);
/*!40000 ALTER TABLE `engine4_core_servicetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_session`
--

DROP TABLE IF EXISTS `engine4_core_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_session` (
  `id` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_session`
--

LOCK TABLES `engine4_core_session` WRITE;
/*!40000 ALTER TABLE `engine4_core_session` DISABLE KEYS */;
INSERT INTO `engine4_core_session` VALUES ('0fml9pvu7nu9fn745kj9dng4t5',1397563433,86400,'facebook_lock|i:0;Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:3:\"162\";',1),('0lv9d2l02gedapurt3j8sirom1',1396631599,86400,'',NULL),('0n28c19eoimdgu8ubk1fpdedd6',1397342869,86400,'',NULL),('1c9f7e114c83ec57d4e084888b5c384e',1394110972,86400,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"07776dc9cad0364d430b2e317731ae59\";}Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:1:\"8\";',1),('1lhg9rc76rf3egcn16o1i1s3i7',1396658258,86400,'',NULL),('1s6i6ieo3cihpbjdneb2545rh1',1395197237,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('2aothvcd6h747r73drq8fld8m0',1394125895,86400,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"083af7bf3b9cb4b4a7ae2bb7f7926241\";}Zend_Auth|a:1:{s:7:\"storage\";i:3;}login_id|s:2:\"11\";standard-mobile-mode|a:1:{s:4:\"mode\";s:8:\"standard\";}',3),('2sersgdnu7m8nrkml697i9otf6',1396358420,86400,'',NULL),('3a484gms2fhlgcv76ik1utjsb7',1394938699,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}standard-mobile-mode|a:1:{s:4:\"mode\";s:8:\"standard\";}',3),('3btmj3v64a5bjtjp6018h62hf3',1394080541,1209600,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"98b45382535e98825f79543a9bd95466\";}twitter_uid|b:0;__ZF|a:1:{s:33:\"Zend_Form_Element_Hash_salt_token\";a:1:{s:4:\"ENNH\";i:1;}}Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:1:\"7\";',1),('3i5mmv4gar6id4jpfoep8lo121',1397265777,86400,'',NULL),('3qh4vojk3ua6fmd43pfs3uaj65',1394125564,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('41vnr0hhebppm0fimdhe46u4n5',1395778328,86400,'facebook_lock|i:0;',NULL),('4j3sps0hmq93s5he2pujlq7ri5',1398044615,86400,'facebook_lock|i:0;',NULL),('59sqkop3odd2lemjb4cevtkag0',1394680900,86400,'',NULL),('59ucq33pqh1nb5s6p29c1o4np5',1395718960,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('5hbfdgt57vtgu9p05n087pen34',1396337374,86400,'',NULL),('5ppa0u59meq7392n4qmpes29i5',1397057021,86400,'',NULL),('65asl63cv65jsc8531niurddb4',1395857345,86400,'',NULL),('74ksqqnivadc9g5lsl7kjnjgp6',1395778319,86400,'facebook_lock|i:0;',NULL),('7b4f9ann4ff62vn38dc0ad41g5',1397103207,86400,'',NULL),('7lb7mjattnc34o3jd8t49lrt90',1394338152,86400,'User_Plugin_Signup_Account|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Fields|a:1:{s:6:\"active\";b:1;}User_Plugin_Signup_Photo|a:1:{s:6:\"active\";b:1;}User_Plugin_Signup_Invite|a:1:{s:6:\"active\";b:1;}',2),('820f3hb1qd95rruh7r6r2vj326',1394402615,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:2;}login_id|s:2:\"43\";',2),('850hbok7k0tad8mvvg7o21b9b5',1395778327,86400,'facebook_lock|i:0;',NULL),('85oksv24fb2laii9vbimsc53r7',1398546510,86400,'facebook_lock|i:0;',NULL),('8egt2s1mm6ifn7a73i36sb1hc1',1394932946,86400,'facebook_lock|i:0;',NULL),('8i0fda9v403clujt1n8ff51t34',1398979416,86400,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"8844e53cb7740e81e76b07e1cdefc189\";}facebook_lock|i:1;Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:3:\"164\";facebook_uid|b:0;twitter_lock|i:1;twitter_uid|b:0;',1),('9bkc88rv5dnvjklnndpe7efap0',1396189211,86400,'',NULL),('9q4s6lmumu5a1pm2deckq79ui7',1396977361,1209600,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"5e3b98242c570dc0e595e52875f0a23a\";}twitter_uid|b:0;Payment_Plugin_Signup_Subscription|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Account|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Fields|a:1:{s:6:\"active\";b:1;}User_Plugin_Signup_Photo|a:1:{s:6:\"active\";b:1;}User_Plugin_Signup_Invite|a:1:{s:6:\"active\";b:1;}facebook_lock|i:3;Zend_Auth|a:1:{s:7:\"storage\";i:3;}login_id|s:3:\"158\";facebook_uid|b:0;twitter_lock|i:3;',3),('av8159b3abld0rjc2dadp5tis7',1395964686,86400,'',NULL),('cnvvo4vef5mqlhdrj6i8kqbrp0',1395799555,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('d1eraimocuvreh3b6edb4tv607',1395293791,86400,'',NULL),('d69qeo0e8m6umkh0c82us6hm26',1395792783,1209600,'standard-mobile-mode|a:1:{s:4:\"mode\";s:8:\"standard\";}twitter_uid|b:0;ActivityFormToken|a:1:{s:5:\"token\";s:32:\"787374a15a7ad3e847903fb27260217c\";}facebook_signup|b:1;wall_service_facebook|a:1:{s:5:\"state\";s:32:\"d8c03a227118b0cad649689dd0826b3a\";}__ZF|a:1:{s:33:\"Zend_Form_Element_Hash_salt_token\";a:1:{s:4:\"ENNH\";i:1;}}facebook_lock|i:3;Zend_Auth|a:1:{s:7:\"storage\";i:3;}login_id|s:3:\"130\";facebook_uid|b:0;twitter_lock|i:3;',3),('e5jpotc2id8pjsi68daaer7r21',1394121941,86400,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"ee09bdae82965a8e89cd5dd6ace905ae\";}Zend_Auth|a:1:{s:7:\"storage\";i:2;}login_id|s:1:\"6\";__ZF|a:1:{s:33:\"Zend_Form_Element_Hash_salt_token\";a:1:{s:4:\"ENNH\";i:1;}}',2),('evhr8p5plva67e78ulh8mmce83',1396066401,86400,'',NULL),('f2halnlih9nuk45qclgs7nmgm0',1396916764,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('faph517p5pklflf87l90r39050',1394454149,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:2:\"51\";',1),('fi17kcsqef1kr0rme9rdoptbl4',1394926432,86400,'facebook_lock|i:0;fb_714823741874437_state|s:32:\"5bc41eb7819bcf853aa228bb85e5b2f4\";',NULL),('garppposarivkagq7i5pho2pl5',1394211902,86400,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"9fe0115261c14a6d1765b2e52e267162\";}twitter_uid|b:0;Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:2:\"13\";twitter_lock|i:1;',1),('gf7pbq6f23oke7d8qijtptpka3',1396924484,86400,'',NULL),('grbbdttegged753jctqlup31l4',1395971595,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('h25n20ges4gcsfrsv5q4ac0dp7',1394936774,86400,'facebook_lock|i:0;fb_714823741874437_state|s:32:\"0a4b1da90128e8b6fe1ee1a2c494fcef\";Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:2:\"97\";',1),('h3kkmdm31vd7iks2mealg25ij4',1395230528,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('h520orfq1rka1uisqlobkha875',1395841655,86400,'',NULL),('i511s3afcoq98htjc355iptib2',1393276247,1209600,'Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:1:\"1\";twitter_lock|i:1;twitter_uid|b:0;ActivityFormToken|a:1:{s:5:\"token\";s:32:\"f8ba16ce9c04de7d94aa5fa8a91cf94b\";}',1),('i8tgame3u11qottvk76hbchh13',1395292679,86400,'',NULL),('iklh8id13sdc4cpdvhiac7plg6',1396501470,86400,'',NULL),('inu1ncqdens0a2l96u8ef7cv27',1394225249,86400,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"003263d930480274ee6f6da675938852\";}',NULL),('j0fhin5f8so4rutoi0999b6mo6',1398997271,86400,'facebook_lock|i:0;Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:3:\"165\";',1),('j3nke84bg06e50pq9saom1pif2',1397166806,1209600,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"4173582a0e343a7f695e7bb9ed043334\";}twitter_uid|b:0;facebook_lock|i:0;',1),('j7v8jqn72njietcv0u6uhnut03',1395778320,86400,'facebook_lock|i:0;',NULL),('j85nsikrl1rf6j2bank8cfnar1',1396635095,86400,'',NULL),('jbfr597e13bbeaq1flm7enm4m5',1394475038,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('jjp09uo2d9sctdmj2nkblh18a3',1396403302,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('jv3sifl63fcqt53rho1ikairl6',1394592293,1209600,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}login_id|s:2:\"65\";',3),('khhrj1hf5g3qbl4ta0eibr74k5',1394780147,86400,'standard-mobile-mode|a:1:{s:4:\"mode\";s:6:\"mobile\";}Zend_Auth|a:1:{s:7:\"storage\";i:3;}login_id|s:2:\"82\";',3),('l1mimee0pbgvq2bgae9pkgo6s4',1396280450,86400,'',NULL),('mbur5i73go7o4a3qq01odmov94',1395169183,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('mgju49numaljt37m4leatg0o53',1395380849,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('og55aotn78pncmfedi79qd1b05',1395701128,86400,'facebook_lock|i:1;Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:3:\"127\";ActivityFormToken|a:1:{s:5:\"token\";s:32:\"3bc4ef13cbdbb25e0e91608318bb173b\";}facebook_uid|b:0;twitter_lock|i:1;twitter_uid|b:0;',1),('on66uh1br5jdfriju7n60hbt57',1396833362,86400,'facebook_lock|i:0;',NULL),('p31a82pk86ccu69p0tepu1km35',1394334305,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}standard-mobile-mode|a:1:{s:4:\"mode\";s:6:\"mobile\";}',3),('p7fhc740fq999s523r36ic0mc0',1397339142,86400,'',NULL),('pb30k7r6rabegbsbt0lnmu0805',1396037177,86400,'',NULL),('pu1f3kto1mahbs9jpjmucu49o6',1395506511,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('qu5uvf52t58pmna424a8sdp6g5',1394932945,86400,'facebook_lock|i:0;',NULL),('rad4vjjs7frj1mbk959o4q1o33',1393988059,1209600,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"890e9b10be2435630cb643ca857380b5\";}Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:1:\"4\";',1),('ru7qi07qtsp45q2lv73mol3gd5',1396999595,86400,'',NULL),('rvddb98778v9el5kr4o83mn0g7',1395858498,1209600,'ActivityFormToken|a:1:{s:5:\"token\";s:32:\"a7beae4d83b0b0cf30827108ab80ea0a\";}twitter_uid|b:0;standard-mobile-mode|a:1:{s:4:\"mode\";s:8:\"standard\";}Payment_Plugin_Signup_Subscription|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Account|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Invite|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Photo|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}User_Plugin_Signup_Fields|a:2:{s:6:\"active\";b:1;s:4:\"data\";N;}facebook_lock|i:0;Zend_Auth|a:1:{s:7:\"storage\";i:1;}login_id|s:3:\"139\";',1),('s81dcqhccpralldvgp6p2qmbi7',1395016795,86400,'facebook_lock|i:0;',NULL),('ssm7uut8cs66rme6h73ohir691',1395266177,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('tink6vuldtvfmtbeohaqvbrpl4',1395635029,86400,'',NULL),('u4b999u68r5qst0mpqmq131b27',1394111087,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:2;}',2),('ua02sus3e0ioaf39rnkq5q7b77',1394596469,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}',3),('uugl2mfcf33f2a3deq1g62qb36',1395008344,86400,'Zend_Auth|a:1:{s:7:\"storage\";i:3;}standard-mobile-mode|a:1:{s:4:\"mode\";s:8:\"standard\";}',3);
/*!40000 ALTER TABLE `engine4_core_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_settings`
--

DROP TABLE IF EXISTS `engine4_core_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_settings` (
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_settings`
--

LOCK TABLES `engine4_core_settings` WRITE;
/*!40000 ALTER TABLE `engine4_core_settings` DISABLE KEYS */;
INSERT INTO `engine4_core_settings` VALUES ('activity.content','everyone'),('activity.disallowed','N'),('activity.filter','1'),('activity.length','40'),('activity.liveupdate','120000'),('activity.publish','1'),('activity.userdelete','1'),('activity.userlength','25'),('advancedsearch.typeslist','hequestion,user'),('core.admin.mode','none'),('core.admin.password',''),('core.admin.reauthenticate','0'),('core.admin.timeout','600'),('core.analytics.code',''),('core.doctype','XHTML1_STRICT'),('core.facebook.appid','714823741874437'),('core.facebook.enable','login'),('core.facebook.key',''),('core.facebook.secret','6d276be67d305deb0df7a109530fb3d4'),('core.general.analytics',''),('core.general.browse','1'),('core.general.commenthtml',''),('core.general.includes',''),('core.general.notificationupdate','120000'),('core.general.portal','1'),('core.general.profile','1'),('core.general.quota','0'),('core.general.search','1'),('core.general.site.description','The non-financial professional lacks knowledge or easy access to peer support to manage wealth efficiently and comfortablyâ€¦\r\n\r\n...We want to help non-financial professionals CONNECT to their peers to gain insights and perspectives on managing wealth'),('core.general.site.keywords','Wealth management, social community, connect peers'),('core.general.site.title','Wealthment'),('core.general.staticBaseUrl',''),('core.license.email','email@domain.com'),('core.license.key','6679-5176-7091-0000'),('core.license.statistics','1'),('core.locale.locale','auto'),('core.locale.timezone','US/Pacific'),('core.log.adapter','file'),('core.mail.contact','wealthment@gmail.com'),('core.mail.count','25'),('core.mail.enabled','1'),('core.mail.from','wealthment@gmail.com'),('core.mail.name','Wealthment Site Admin'),('core.mail.queueing','1'),('core.secret','aa4e79fe17fcba7d24e5f1529307e62cadec4c8e'),('core.site.counter','67'),('core.site.creation','2014-02-16 03:54:45'),('core.site.title','Social Network'),('core.spam.censor',''),('core.spam.comment','0'),('core.spam.contact','0'),('core.spam.invite','0'),('core.spam.ipbans',''),('core.spam.login','0'),('core.spam.signup','0'),('core.static.baseurl',''),('core.tasks.count','1'),('core.tasks.interval','60'),('core.tasks.jobs','3'),('core.tasks.key','094b0c06'),('core.tasks.last','1398997268'),('core.tasks.mode','curl'),('core.tasks.pid',''),('core.tasks.processes','2'),('core.tasks.time','120'),('core.tasks.timeout','900'),('core.thumbnails.icon.height','48'),('core.thumbnails.icon.mode','crop'),('core.thumbnails.icon.width','48'),('core.thumbnails.main.height','720'),('core.thumbnails.main.mode','resize'),('core.thumbnails.main.width','720'),('core.thumbnails.normal.height','160'),('core.thumbnails.normal.mode','resize'),('core.thumbnails.normal.width','140'),('core.thumbnails.profile.height','400'),('core.thumbnails.profile.mode','resize'),('core.thumbnails.profile.width','200'),('core.translate.adapter','csv'),('core.twitter.enable','none'),('core.twitter.key',''),('core.twitter.secret',''),('group.bbcode','1'),('group.html','1'),('hecore.featured.count','9'),('hecore.module.check.licenses','1395778791'),('heloginpopup.max.day','5'),('invite.allowCustomMessage','1'),('invite.fromEmail',''),('invite.fromName',''),('invite.max','10'),('invite.message','You are being invited to join our social network.'),('invite.subject','Join Us'),('mobile.show.rate-browse','1'),('mobile.show.rate-widget','1'),('payment.benefit','all'),('payment.currency','USD'),('payment.secret','8ec81143882d8f25e921304e7052254d'),('Pinfeed.use.homepage','1'),('Pinfeed.width','1'),('storage.service.mirrored.counter','0'),('storage.service.mirrored.index','0'),('storage.service.roundrobin.counter','0'),('user.friends.direction','1'),('user.friends.eligible','2'),('user.friends.lists','1'),('user.friends.verification','1'),('user.signup.adminemail','1'),('user.signup.approve','1'),('user.signup.checkemail','1'),('user.signup.inviteonly','0'),('user.signup.photo','1'),('user.signup.random','0'),('user.signup.terms','1'),('user.signup.username','1'),('user.signup.verifyemail','0'),('user.support.links','1'),('wall.composers.disabled','smile'),('wall.content.autoload','1'),('wall.content.bitly','1'),('wall.content.dialogconfirm','1'),('wall.content.frendlistenable','1'),('wall.content.liketips','1'),('wall.content.listenable','1'),('wall.content.profilehome',''),('wall.content.rolldownload','1'),('wall.content.smile','1'),('wall.list.default',''),('wall.list.disabled','group'),('wall.list.user.save',''),('wall.privacy.disabled',''),('wall.service.facebook.clientid',''),('wall.service.facebook.clientsecret',''),('wall.service.facebook.enabled',''),('wall.service.linkedin.consumerkey',''),('wall.service.linkedin.consumersecret',''),('wall.service.linkedin.enabled',''),('wall.service.twitter.consumerkey',''),('wall.service.twitter.consumersecret',''),('wall.service.twitter.enabled',''),('wall.tab.default','social'),('wall.tab.disabled','welcome');
/*!40000 ALTER TABLE `engine4_core_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_statistics`
--

DROP TABLE IF EXISTS `engine4_core_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_statistics` (
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `date` datetime NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_statistics`
--

LOCK TABLES `engine4_core_statistics` WRITE;
/*!40000 ALTER TABLE `engine4_core_statistics` DISABLE KEYS */;
INSERT INTO `engine4_core_statistics` VALUES ('core.comments','2014-03-05 01:00:00',1),('core.comments','2014-03-05 19:00:00',1),('core.comments','2014-03-07 03:00:00',1),('core.comments','2014-03-07 20:00:00',1),('core.comments','2014-03-09 23:00:00',1),('core.comments','2014-03-10 00:00:00',1),('core.comments','2014-03-27 13:00:00',1),('core.likes','2014-03-05 01:00:00',1),('core.likes','2014-03-05 19:00:00',1),('core.likes','2014-03-06 16:00:00',1),('core.likes','2014-03-07 03:00:00',1),('core.likes','2014-03-07 20:00:00',1),('core.likes','2014-03-08 01:00:00',1),('core.likes','2014-03-09 02:00:00',2),('core.likes','2014-03-09 23:00:00',1),('core.likes','2014-03-12 00:00:00',1),('core.likes','2014-03-25 02:00:00',1),('core.likes','2014-03-27 13:00:00',2),('core.views','2014-02-17 00:00:00',3),('core.views','2014-02-17 01:00:00',5),('core.views','2014-02-17 02:00:00',8),('core.views','2014-02-17 18:00:00',20),('core.views','2014-02-24 21:00:00',1),('core.views','2014-03-03 22:00:00',4),('core.views','2014-03-04 00:00:00',43),('core.views','2014-03-04 16:00:00',19),('core.views','2014-03-05 01:00:00',11),('core.views','2014-03-05 02:00:00',18),('core.views','2014-03-05 17:00:00',5),('core.views','2014-03-05 18:00:00',7),('core.views','2014-03-05 19:00:00',3),('core.views','2014-03-06 03:00:00',25),('core.views','2014-03-06 04:00:00',20),('core.views','2014-03-06 12:00:00',4),('core.views','2014-03-06 13:00:00',5),('core.views','2014-03-06 16:00:00',29),('core.views','2014-03-06 17:00:00',17),('core.views','2014-03-07 03:00:00',9),('core.views','2014-03-07 04:00:00',5),('core.views','2014-03-07 05:00:00',2),('core.views','2014-03-07 18:00:00',1),('core.views','2014-03-07 19:00:00',10),('core.views','2014-03-07 20:00:00',47),('core.views','2014-03-07 22:00:00',1),('core.views','2014-03-08 00:00:00',11),('core.views','2014-03-08 01:00:00',49),('core.views','2014-03-08 03:00:00',9),('core.views','2014-03-08 15:00:00',94),('core.views','2014-03-08 16:00:00',15),('core.views','2014-03-08 18:00:00',28),('core.views','2014-03-08 20:00:00',51),('core.views','2014-03-08 21:00:00',35),('core.views','2014-03-08 22:00:00',19),('core.views','2014-03-08 23:00:00',26),('core.views','2014-03-09 01:00:00',45),('core.views','2014-03-09 02:00:00',78),('core.views','2014-03-09 03:00:00',49),('core.views','2014-03-09 04:00:00',9),('core.views','2014-03-09 07:00:00',23),('core.views','2014-03-09 21:00:00',2),('core.views','2014-03-09 22:00:00',28),('core.views','2014-03-09 23:00:00',16),('core.views','2014-03-10 00:00:00',16),('core.views','2014-03-10 15:00:00',10),('core.views','2014-03-10 17:00:00',1),('core.views','2014-03-10 18:00:00',6),('core.views','2014-03-10 19:00:00',5),('core.views','2014-03-10 21:00:00',15),('core.views','2014-03-10 22:00:00',48),('core.views','2014-03-10 23:00:00',7),('core.views','2014-03-11 01:00:00',33),('core.views','2014-03-11 02:00:00',60),('core.views','2014-03-11 03:00:00',8),('core.views','2014-03-11 12:00:00',8),('core.views','2014-03-11 15:00:00',14),('core.views','2014-03-11 19:00:00',8),('core.views','2014-03-11 20:00:00',3),('core.views','2014-03-11 21:00:00',26),('core.views','2014-03-11 22:00:00',93),('core.views','2014-03-12 00:00:00',44),('core.views','2014-03-12 01:00:00',9),('core.views','2014-03-12 02:00:00',17),('core.views','2014-03-12 03:00:00',59),('core.views','2014-03-12 04:00:00',52),('core.views','2014-03-12 05:00:00',25),('core.views','2014-03-12 07:00:00',9),('core.views','2014-03-12 14:00:00',1),('core.views','2014-03-12 18:00:00',55),('core.views','2014-03-12 19:00:00',22),('core.views','2014-03-12 21:00:00',2),('core.views','2014-03-12 22:00:00',10),('core.views','2014-03-12 23:00:00',24),('core.views','2014-03-13 01:00:00',3),('core.views','2014-03-13 04:00:00',2),('core.views','2014-03-13 21:00:00',14),('core.views','2014-03-13 23:00:00',13),('core.views','2014-03-14 00:00:00',48),('core.views','2014-03-14 01:00:00',11),('core.views','2014-03-14 02:00:00',32),('core.views','2014-03-14 06:00:00',9),('core.views','2014-03-14 11:00:00',8),('core.views','2014-03-14 16:00:00',5),('core.views','2014-03-14 17:00:00',12),('core.views','2014-03-15 00:00:00',1),('core.views','2014-03-15 19:00:00',18),('core.views','2014-03-15 20:00:00',12),('core.views','2014-03-15 21:00:00',22),('core.views','2014-03-15 23:00:00',40),('core.views','2014-03-16 00:00:00',8),('core.views','2014-03-16 01:00:00',77),('core.views','2014-03-16 02:00:00',50),('core.views','2014-03-16 15:00:00',4),('core.views','2014-03-16 17:00:00',2),('core.views','2014-03-16 22:00:00',3),('core.views','2014-03-17 00:00:00',1),('core.views','2014-03-17 15:00:00',10),('core.views','2014-03-17 22:00:00',5),('core.views','2014-03-18 15:00:00',4),('core.views','2014-03-18 16:00:00',18),('core.views','2014-03-18 18:00:00',2),('core.views','2014-03-18 19:00:00',11),('core.views','2014-03-18 20:00:00',22),('core.views','2014-03-18 21:00:00',35),('core.views','2014-03-18 22:00:00',21),('core.views','2014-03-18 23:00:00',3),('core.views','2014-03-19 02:00:00',6),('core.views','2014-03-19 03:00:00',1),('core.views','2014-03-19 11:00:00',3),('core.views','2014-03-19 12:00:00',1),('core.views','2014-03-19 16:00:00',10),('core.views','2014-03-19 21:00:00',5),('core.views','2014-03-20 03:00:00',1),('core.views','2014-03-20 05:00:00',2),('core.views','2014-03-20 17:00:00',1),('core.views','2014-03-20 20:00:00',1),('core.views','2014-03-21 05:00:00',2),('core.views','2014-03-21 20:00:00',1),('core.views','2014-03-22 04:00:00',2),('core.views','2014-03-22 15:00:00',1),('core.views','2014-03-22 16:00:00',1),('core.views','2014-03-23 07:00:00',25),('core.views','2014-03-23 12:00:00',8),('core.views','2014-03-23 15:00:00',3),('core.views','2014-03-23 16:00:00',1),('core.views','2014-03-24 04:00:00',1),('core.views','2014-03-24 14:00:00',1),('core.views','2014-03-24 16:00:00',50),('core.views','2014-03-24 19:00:00',5),('core.views','2014-03-24 20:00:00',41),('core.views','2014-03-24 21:00:00',1),('core.views','2014-03-24 22:00:00',26),('core.views','2014-03-25 02:00:00',20),('core.views','2014-03-25 03:00:00',2),('core.views','2014-03-25 14:00:00',5),('core.views','2014-03-25 20:00:00',57),('core.views','2014-03-25 21:00:00',3),('core.views','2014-03-25 23:00:00',1),('core.views','2014-03-26 00:00:00',112),('core.views','2014-03-26 01:00:00',7),('core.views','2014-03-26 02:00:00',10),('core.views','2014-03-26 13:00:00',1),('core.views','2014-03-26 14:00:00',140),('core.views','2014-03-26 16:00:00',10),('core.views','2014-03-26 17:00:00',6),('core.views','2014-03-26 18:00:00',28),('core.views','2014-03-26 19:00:00',8),('core.views','2014-03-26 23:00:00',76),('core.views','2014-03-27 00:00:00',38),('core.views','2014-03-27 12:00:00',5),('core.views','2014-03-27 13:00:00',10),('core.views','2014-03-27 15:00:00',1),('core.views','2014-03-27 18:00:00',1),('core.views','2014-03-27 20:00:00',5),('core.views','2014-03-27 22:00:00',1),('core.views','2014-03-27 23:00:00',6),('core.views','2014-03-28 01:00:00',4),('core.views','2014-03-28 16:00:00',1),('core.views','2014-03-28 20:00:00',1),('core.views','2014-03-29 04:00:00',1),('core.views','2014-03-29 17:00:00',3),('core.views','2014-03-30 13:00:00',2),('core.views','2014-03-30 14:00:00',6),('core.views','2014-03-31 03:00:00',1),('core.views','2014-03-31 15:00:00',1),('core.views','2014-04-01 07:00:00',1),('core.views','2014-04-01 13:00:00',1),('core.views','2014-04-01 22:00:00',1),('core.views','2014-04-02 01:00:00',2),('core.views','2014-04-03 05:00:00',1),('core.views','2014-04-03 23:00:00',1),('core.views','2014-04-04 17:00:00',1),('core.views','2014-04-04 18:00:00',1),('core.views','2014-04-04 23:00:00',1),('core.views','2014-04-05 00:00:00',1),('core.views','2014-04-07 01:00:00',1),('core.views','2014-04-07 22:00:00',2),('core.views','2014-04-08 00:00:00',3),('core.views','2014-04-08 02:00:00',1),('core.views','2014-04-08 16:00:00',1),('core.views','2014-04-08 23:00:00',1),('core.views','2014-04-09 12:00:00',3),('core.views','2014-04-09 15:00:00',2),('core.views','2014-04-10 04:00:00',1),('core.views','2014-04-10 13:00:00',1),('core.views','2014-04-10 21:00:00',3),('core.views','2014-04-10 22:00:00',1),('core.views','2014-04-11 17:00:00',1),('core.views','2014-04-12 01:00:00',1),('core.views','2014-04-12 21:00:00',1),('core.views','2014-04-12 22:00:00',1),('core.views','2014-04-13 01:00:00',1),('core.views','2014-04-15 12:00:00',2),('core.views','2014-04-21 01:00:00',1),('core.views','2014-04-26 21:00:00',1),('core.views','2014-05-01 02:00:00',17),('core.views','2014-05-01 21:00:00',10),('core.views','2014-05-02 02:00:00',9),('messages.creations','2014-03-09 22:00:00',1),('messages.creations','2014-03-12 00:00:00',1),('messages.creations','2014-03-24 16:00:00',1),('user.creations','2014-03-04 16:00:00',1),('user.creations','2014-03-05 02:00:00',1),('user.creations','2014-03-15 23:00:00',1),('user.creations','2014-03-16 01:00:00',1),('user.friendships','2014-03-05 02:00:00',1),('user.friendships','2014-03-05 18:00:00',1),('user.logins','2014-02-17 00:00:00',1),('user.logins','2014-03-03 22:00:00',1),('user.logins','2014-03-04 00:00:00',1),('user.logins','2014-03-05 01:00:00',1),('user.logins','2014-03-05 17:00:00',1),('user.logins','2014-03-06 03:00:00',1),('user.logins','2014-03-06 12:00:00',1),('user.logins','2014-03-06 13:00:00',1),('user.logins','2014-03-06 16:00:00',2),('user.logins','2014-03-06 17:00:00',2),('user.logins','2014-03-07 03:00:00',1),('user.logins','2014-03-07 04:00:00',1),('user.logins','2014-03-07 19:00:00',1),('user.logins','2014-03-08 01:00:00',1),('user.logins','2014-03-08 03:00:00',1),('user.logins','2014-03-08 15:00:00',1),('user.logins','2014-03-08 18:00:00',1),('user.logins','2014-03-08 20:00:00',4),('user.logins','2014-03-08 21:00:00',3),('user.logins','2014-03-08 23:00:00',1),('user.logins','2014-03-09 01:00:00',3),('user.logins','2014-03-09 02:00:00',6),('user.logins','2014-03-09 03:00:00',2),('user.logins','2014-03-09 07:00:00',2),('user.logins','2014-03-09 22:00:00',3),('user.logins','2014-03-09 23:00:00',3),('user.logins','2014-03-10 00:00:00',2),('user.logins','2014-03-10 12:00:00',1),('user.logins','2014-03-10 18:00:00',1),('user.logins','2014-03-10 22:00:00',2),('user.logins','2014-03-11 01:00:00',1),('user.logins','2014-03-11 02:00:00',4),('user.logins','2014-03-11 12:00:00',1),('user.logins','2014-03-11 15:00:00',2),('user.logins','2014-03-11 20:00:00',1),('user.logins','2014-03-11 22:00:00',1),('user.logins','2014-03-12 00:00:00',2),('user.logins','2014-03-12 02:00:00',1),('user.logins','2014-03-12 03:00:00',1),('user.logins','2014-03-12 04:00:00',1),('user.logins','2014-03-12 05:00:00',2),('user.logins','2014-03-12 18:00:00',3),('user.logins','2014-03-12 19:00:00',2),('user.logins','2014-03-14 00:00:00',1),('user.logins','2014-03-14 01:00:00',3),('user.logins','2014-03-14 02:00:00',3),('user.logins','2014-03-14 06:00:00',1),('user.logins','2014-03-14 11:00:00',1),('user.logins','2014-03-14 16:00:00',1),('user.logins','2014-03-15 19:00:00',3),('user.logins','2014-03-15 20:00:00',2),('user.logins','2014-03-15 21:00:00',2),('user.logins','2014-03-15 23:00:00',1),('user.logins','2014-03-16 01:00:00',3),('user.logins','2014-03-16 02:00:00',1),('user.logins','2014-03-16 22:00:00',1),('user.logins','2014-03-17 22:00:00',1),('user.logins','2014-03-18 18:00:00',1),('user.logins','2014-03-18 20:00:00',1),('user.logins','2014-03-18 21:00:00',1),('user.logins','2014-03-19 02:00:00',1),('user.logins','2014-03-19 12:00:00',1),('user.logins','2014-03-19 21:00:00',1),('user.logins','2014-03-21 05:00:00',1),('user.logins','2014-03-22 04:00:00',1),('user.logins','2014-03-23 12:00:00',1),('user.logins','2014-03-24 16:00:00',2),('user.logins','2014-03-24 20:00:00',2),('user.logins','2014-03-24 22:00:00',4),('user.logins','2014-03-25 03:00:00',1),('user.logins','2014-03-25 20:00:00',2),('user.logins','2014-03-26 00:00:00',9),('user.logins','2014-03-26 02:00:00',1),('user.logins','2014-03-26 14:00:00',3),('user.logins','2014-03-26 18:00:00',2),('user.logins','2014-03-26 19:00:00',1),('user.logins','2014-03-26 23:00:00',6),('user.logins','2014-03-27 00:00:00',4),('user.logins','2014-03-27 13:00:00',1),('user.logins','2014-03-27 20:00:00',1),('user.logins','2014-03-28 01:00:00',1),('user.logins','2014-04-02 01:00:00',1),('user.logins','2014-04-07 22:00:00',1),('user.logins','2014-04-08 00:00:00',1),('user.logins','2014-04-09 12:00:00',1),('user.logins','2014-04-10 22:00:00',1),('user.logins','2014-04-15 12:00:00',1),('user.logins','2014-05-01 02:00:00',1),('user.logins','2014-05-01 21:00:00',1),('user.logins','2014-05-02 02:00:00',1);
/*!40000 ALTER TABLE `engine4_core_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_status`
--

DROP TABLE IF EXISTS `engine4_core_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_status` (
  `status_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_status`
--

LOCK TABLES `engine4_core_status` WRITE;
/*!40000 ALTER TABLE `engine4_core_status` DISABLE KEYS */;
INSERT INTO `engine4_core_status` VALUES (1,'user',1,'Test','2014-02-17 18:43:43'),(2,'user',1,'test ','2014-03-04 00:15:14'),(3,'user',2,'I hope to grow my portfolio by 50% this year! ','2014-03-04 16:31:22'),(4,'user',1,'test on 3/5 ','2014-03-06 04:20:30'),(5,'user',1,'Testing ','2014-03-06 04:20:54'),(6,'user',2,'Go Tesla Go!','2014-03-06 13:04:47'),(7,'user',3,'Test from mobile device!','2014-03-06 17:06:00'),(8,'user',3,'test #what is this. Â ok ','2014-03-09 01:45:22'),(9,'user',2,'test2 #what is this ','2014-03-09 01:53:18'),(10,'user',2,'Tesla is bad ass! #tesla #stocks #awesomebuy ','2014-03-09 02:02:09'),(11,'user',2,'100% return on Tesla in 3 months! #tesla #stocks ','2014-03-09 02:02:59'),(12,'user',3,'the stock #cisco sucks ','2014-03-09 03:49:25'),(13,'user',3,'NJ, TX, and Az won\'t allow telsa to sell directly to consumers. What a bunch of BS!','2014-03-11 20:25:48'),(14,'user',3,'Posting from iPadÂ  ','2014-03-12 02:40:43'),(15,'user',3,'Test for #tesla buy or sell ','2014-03-12 05:27:02'),(16,'user',3,'test of status','2014-03-14 02:16:31'),(17,'user',3,'Test of Pin Feed ','2014-03-14 16:53:28'),(18,'user',3,'A good Warren Buffet quote - &quot;The\nstock market just offers\nyou so many opportunities-- the thousands and thousands of different businesses. You don\'t have to be an expert on every\none of \'em.\nYou don\'t have to be an expert on 10% of them, even. You just have to have\nsome conviction that either a given company or a group of companies, and I would suggest\nfor most people it should be a group of companies\nyou have to have every conviction that\nthose companies are likely to earn more\nmoney five or ten or 20 years from now\nthan they\'re earning now. And that is not a difficult decision to come to.â€ ','2014-03-15 21:06:11'),(19,'user',5,'test ','2014-03-16 01:22:25'),(20,'user',2,'Tesla is not doing so well. #tesla ','2014-03-16 01:42:55'),(21,'user',3,'testing ','2014-03-18 21:55:34'),(22,'user',3,'#test ','2014-03-24 20:15:22'),(23,'user',3,'#test2 ','2014-03-25 02:37:31'),(24,'user',3,'#test2 test ','2014-03-25 02:37:38'),(25,'user',5,':) test ','2014-03-25 20:11:58'),(26,'user',1,'test again ','2014-03-26 14:24:30'),(27,'user',1,'#test 2 test again ','2014-03-26 14:27:51'),(28,'user',1,'testing the #test2 feature again ','2014-03-26 14:46:45'),(29,'user',1,'creating a new #helloÂ  ','2014-03-26 14:46:55');
/*!40000 ALTER TABLE `engine4_core_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_styles`
--

DROP TABLE IF EXISTS `engine4_core_styles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_styles` (
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `id` int(11) unsigned NOT NULL,
  `style` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`type`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_styles`
--

LOCK TABLES `engine4_core_styles` WRITE;
/*!40000 ALTER TABLE `engine4_core_styles` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_styles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_tagmaps`
--

DROP TABLE IF EXISTS `engine4_core_tagmaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_tagmaps` (
  `tagmap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `tagger_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `tagger_id` int(11) unsigned NOT NULL,
  `tag_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  `extra` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`tagmap_id`),
  KEY `resource_type` (`resource_type`,`resource_id`),
  KEY `tagger_type` (`tagger_type`,`tagger_id`),
  KEY `tag_type` (`tag_type`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_tagmaps`
--

LOCK TABLES `engine4_core_tagmaps` WRITE;
/*!40000 ALTER TABLE `engine4_core_tagmaps` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_tagmaps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_tags`
--

DROP TABLE IF EXISTS `engine4_core_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_tags` (
  `tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_tags`
--

LOCK TABLES `engine4_core_tags` WRITE;
/*!40000 ALTER TABLE `engine4_core_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_core_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_tasks`
--

DROP TABLE IF EXISTS `engine4_core_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_tasks` (
  `task_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `timeout` int(11) unsigned NOT NULL DEFAULT '60',
  `processes` smallint(3) unsigned NOT NULL DEFAULT '1',
  `semaphore` smallint(3) NOT NULL DEFAULT '0',
  `started_last` int(11) NOT NULL DEFAULT '0',
  `started_count` int(11) unsigned NOT NULL DEFAULT '0',
  `completed_last` int(11) NOT NULL DEFAULT '0',
  `completed_count` int(11) unsigned NOT NULL DEFAULT '0',
  `failure_last` int(11) NOT NULL DEFAULT '0',
  `failure_count` int(11) unsigned NOT NULL DEFAULT '0',
  `success_last` int(11) NOT NULL DEFAULT '0',
  `success_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  UNIQUE KEY `plugin` (`plugin`),
  KEY `module` (`module`),
  KEY `started_last` (`started_last`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_tasks`
--

LOCK TABLES `engine4_core_tasks` WRITE;
/*!40000 ALTER TABLE `engine4_core_tasks` DISABLE KEYS */;
INSERT INTO `engine4_core_tasks` VALUES (1,'Job Queue','core','Core_Plugin_Task_Jobs',5,1,0,0,0,0,0,0,0,0,0),(2,'Background Mailer','core','Core_Plugin_Task_Mail',15,1,0,0,0,0,0,0,0,0,0),(3,'Cache Prefetch','core','Core_Plugin_Task_Prefetch',300,1,0,0,0,0,0,0,0,0,0),(4,'Statistics','core','Core_Plugin_Task_Statistics',43200,1,0,0,0,0,0,0,0,0,0),(5,'Log Rotation','core','Core_Plugin_Task_LogRotation',7200,1,0,0,0,0,0,0,0,0,0),(6,'Member Data Maintenance','user','User_Plugin_Task_Cleanup',60,1,0,0,0,0,0,0,0,0,0),(7,'Payment Maintenance','user','Payment_Plugin_Task_Cleanup',43200,1,0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `engine4_core_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_core_themes`
--

DROP TABLE IF EXISTS `engine4_core_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_core_themes` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`theme_id`),
  UNIQUE KEY `name` (`name`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_core_themes`
--

LOCK TABLES `engine4_core_themes` WRITE;
/*!40000 ALTER TABLE `engine4_core_themes` DISABLE KEYS */;
INSERT INTO `engine4_core_themes` VALUES (1,'default','Default','',0),(2,'midnight','Midnight','',0),(3,'clean','Clean','',0),(4,'modern','Modern','',0),(5,'bamboo','Bamboo','',0),(6,'digita','Digita','',0),(7,'grid-blue','Grid Blue','',0),(8,'grid-brown','Grid Brown','',0),(9,'grid-dark','Grid Dark','',0),(10,'grid-gray','Grid Gray','',0),(11,'grid-green','Grid Green','',0),(12,'grid-pink','Grid Pink','',0),(13,'grid-purple','Grid Purple','',0),(14,'grid-red','Grid Red','',0),(15,'kandy-cappuccino','Kandy Cappuccino','',0),(16,'kandy-limeorange','Kandy Limeorange','',0),(17,'kandy-mangoberry','Kandy Mangoberry','',0),(18,'kandy-watermelon','Kandy Watermelon','',0),(19,'musicbox-blue','Musicbox Blue','',0),(20,'musicbox-brown','Musicbox Brown','',0),(21,'musicbox-gray','Musicbox Gray','',0),(22,'musicbox-green','Musicbox Green','',0),(23,'musicbox-pink','Musicbox Pink','',0),(24,'musicbox-purple','Musicbox Purple','',0),(25,'musicbox-red','Musicbox Red','',0),(26,'musicbox-yellow','Musicbox Yellow','',0),(27,'quantum-beige','Quantum Beige','',0),(28,'quantum-blue','Quantum Blue','',0),(29,'quantum-gray','Quantum Gray','',0),(30,'quantum-green','Quantum Green','',0),(31,'quantum-orange','Quantum Orange','',0),(32,'quantum-pink','Quantum Pink','',0),(33,'quantum-purple','Quantum Purple','',0),(34,'quantum-red','Quantum Red','',0),(35,'slipstream','Slipstream','',0),(36,'snowbot','Snowbot','',0),(37,'wealthmenttesttheme','Wealthment Test Theme','Testing different CSS',1),(39,'wealthmenttesttheme2','Wealthment Test Theme 2','',0);
/*!40000 ALTER TABLE `engine4_core_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_albums`
--

DROP TABLE IF EXISTS `engine4_group_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `collectible_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_albums`
--

LOCK TABLES `engine4_group_albums` WRITE;
/*!40000 ALTER TABLE `engine4_group_albums` DISABLE KEYS */;
INSERT INTO `engine4_group_albums` VALUES (1,1,'','','2014-02-17 18:45:13','2014-02-17 18:45:13',1,0,0,0,0),(2,2,'','','2014-03-04 16:27:57','2014-03-04 16:27:57',1,0,0,0,1);
/*!40000 ALTER TABLE `engine4_group_albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_categories`
--

DROP TABLE IF EXISTS `engine4_group_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_categories`
--

LOCK TABLES `engine4_group_categories` WRITE;
/*!40000 ALTER TABLE `engine4_group_categories` DISABLE KEYS */;
INSERT INTO `engine4_group_categories` VALUES (1,'Animals'),(2,'Business & Finance'),(3,'Computers & Internet'),(4,'Cultures & Community'),(5,'Dating & Relationships'),(6,'Entertainment & Arts'),(7,'Family & Home'),(8,'Games'),(9,'Government & Politics'),(10,'Health & Wellness'),(11,'Hobbies & Crafts'),(12,'Music'),(13,'Recreation & Sports'),(14,'Regional'),(15,'Religion & Beliefs'),(16,'Schools & Education'),(17,'Science');
/*!40000 ALTER TABLE `engine4_group_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_groups`
--

DROP TABLE IF EXISTS `engine4_group_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_groups` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `invite` tinyint(1) NOT NULL DEFAULT '1',
  `approval` tinyint(1) NOT NULL DEFAULT '0',
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `member_count` smallint(6) unsigned NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_groups`
--

LOCK TABLES `engine4_group_groups` WRITE;
/*!40000 ALTER TABLE `engine4_group_groups` DISABLE KEYS */;
INSERT INTO `engine4_group_groups` VALUES (1,1,'Apple Stock','',0,1,1,0,0,'2014-02-17 18:45:13','2014-02-17 18:45:13',2,2),(2,1,'Auto Stocks','This Group is for discussions about the auto industry',0,1,1,0,5,'2014-03-04 16:27:57','2014-03-04 16:27:57',1,0);
/*!40000 ALTER TABLE `engine4_group_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_listitems`
--

DROP TABLE IF EXISTS `engine4_group_listitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_listitems` (
  `listitem_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_listitems`
--

LOCK TABLES `engine4_group_listitems` WRITE;
/*!40000 ALTER TABLE `engine4_group_listitems` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_group_listitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_lists`
--

DROP TABLE IF EXISTS `engine4_group_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_lists` (
  `list_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_lists`
--

LOCK TABLES `engine4_group_lists` WRITE;
/*!40000 ALTER TABLE `engine4_group_lists` DISABLE KEYS */;
INSERT INTO `engine4_group_lists` VALUES (1,'GROUP_OFFICERS',1,0),(2,'GROUP_OFFICERS',2,0);
/*!40000 ALTER TABLE `engine4_group_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_membership`
--

DROP TABLE IF EXISTS `engine4_group_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `resource_approved` tinyint(1) NOT NULL DEFAULT '0',
  `user_approved` tinyint(1) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci,
  `title` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`resource_id`,`user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_membership`
--

LOCK TABLES `engine4_group_membership` WRITE;
/*!40000 ALTER TABLE `engine4_group_membership` DISABLE KEYS */;
INSERT INTO `engine4_group_membership` VALUES (1,1,1,1,1,NULL,NULL),(1,2,1,1,1,NULL,NULL),(2,1,1,1,1,NULL,NULL);
/*!40000 ALTER TABLE `engine4_group_membership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_photos`
--

DROP TABLE IF EXISTS `engine4_group_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_photos`
--

LOCK TABLES `engine4_group_photos` WRITE;
/*!40000 ALTER TABLE `engine4_group_photos` DISABLE KEYS */;
INSERT INTO `engine4_group_photos` VALUES (1,2,2,1,'','',2,5,'2014-03-04 16:27:57','2014-03-04 16:27:57',0,0);
/*!40000 ALTER TABLE `engine4_group_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_posts`
--

DROP TABLE IF EXISTS `engine4_group_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_posts` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_posts`
--

LOCK TABLES `engine4_group_posts` WRITE;
/*!40000 ALTER TABLE `engine4_group_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_group_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_topics`
--

DROP TABLE IF EXISTS `engine4_group_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_topics` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `post_count` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `lastpost_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lastposter_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_topics`
--

LOCK TABLES `engine4_group_topics` WRITE;
/*!40000 ALTER TABLE `engine4_group_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_group_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_group_topicwatches`
--

DROP TABLE IF EXISTS `engine4_group_topicwatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_group_topicwatches` (
  `resource_id` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `watch` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`resource_id`,`topic_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_group_topicwatches`
--

LOCK TABLES `engine4_group_topicwatches` WRITE;
/*!40000 ALTER TABLE `engine4_group_topicwatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_group_topicwatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hashtag_maps`
--

DROP TABLE IF EXISTS `engine4_hashtag_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hashtag_maps` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `hashtagger_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hashtagger_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hashtag_maps`
--

LOCK TABLES `engine4_hashtag_maps` WRITE;
/*!40000 ALTER TABLE `engine4_hashtag_maps` DISABLE KEYS */;
INSERT INTO `engine4_hashtag_maps` VALUES (1,'activity_action',23,'user',3,'2014-03-09 01:45:22'),(2,'activity_action',24,'user',2,'2014-03-09 01:53:18'),(8,'activity_action',48,'user',3,'2014-03-24 20:15:22'),(9,'activity_action',49,'user',3,'2014-03-25 02:37:32'),(10,'activity_action',50,'user',3,'2014-03-25 02:37:38'),(11,'activity_action',53,'user',1,'2014-03-26 14:27:51'),(12,'activity_action',54,'user',1,'2014-03-26 14:46:45'),(13,'activity_action',55,'user',1,'2014-03-26 14:46:55');
/*!40000 ALTER TABLE `engine4_hashtag_maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hashtag_tags`
--

DROP TABLE IF EXISTS `engine4_hashtag_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hashtag_tags` (
  `tag_id` int(100) NOT NULL AUTO_INCREMENT,
  `hashtag` varchar(250) DEFAULT NULL,
  `map_id` int(100) DEFAULT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hashtag_tags`
--

LOCK TABLES `engine4_hashtag_tags` WRITE;
/*!40000 ALTER TABLE `engine4_hashtag_tags` DISABLE KEYS */;
INSERT INTO `engine4_hashtag_tags` VALUES (1,'what',1),(2,'what',2),(4,'stocks',3),(5,'awesomebuy',3),(7,'stocks',4),(11,'test',8),(12,'test2',9),(13,'test2',10),(14,'test',11),(15,'test2',12),(16,'helloÂ ',13);
/*!40000 ALTER TABLE `engine4_hashtag_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hecore_featureds`
--

DROP TABLE IF EXISTS `engine4_hecore_featureds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hecore_featureds` (
  `featured_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`featured_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hecore_featureds`
--

LOCK TABLES `engine4_hecore_featureds` WRITE;
/*!40000 ALTER TABLE `engine4_hecore_featureds` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_hecore_featureds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hecore_modules`
--

DROP TABLE IF EXISTS `engine4_hecore_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hecore_modules` (
  `module_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `version` varchar(32) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `installed` tinyint(1) NOT NULL DEFAULT '0',
  `modified_stamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hecore_modules`
--

LOCK TABLES `engine4_hecore_modules` WRITE;
/*!40000 ALTER TABLE `engine4_hecore_modules` DISABLE KEYS */;
INSERT INTO `engine4_hecore_modules` VALUES (1,'wall','4.2.6p8','CA3F1D35182133D8',1,1393884091),(2,'pinfeed','4.5.0','FD97874CABB85813',1,1393884206),(3,'mobile','4.1.8p7','9A19DC3F5FB53303',1,1394110972),(4,'questions','4.2.5','767FD351385A17C0',1,1394311833),(5,'hashtag','4.5.1p3','9B4FCA889252801C',1,1394311873),(6,'likes','4.2.2p4','2D26AE920F511012',1,1394311894),(7,'heloginpopup','4.5.0p2','C6F979752EE50524',1,1394311932),(8,'welcome','4.2.0p3','82BC1F59A7AA6FB1',1,1394311954),(9,'advancedsearch','4.5.1p6','8F58C4B7CA59B106',1,1394454149);
/*!40000 ALTER TABLE `engine4_hecore_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hecore_user_settings`
--

DROP TABLE IF EXISTS `engine4_hecore_user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hecore_user_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `setting` varchar(255) DEFAULT '',
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hecore_user_settings`
--

LOCK TABLES `engine4_hecore_user_settings` WRITE;
/*!40000 ALTER TABLE `engine4_hecore_user_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_hecore_user_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hequestion_followers`
--

DROP TABLE IF EXISTS `engine4_hequestion_followers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hequestion_followers` (
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hequestion_followers`
--

LOCK TABLES `engine4_hequestion_followers` WRITE;
/*!40000 ALTER TABLE `engine4_hequestion_followers` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_hequestion_followers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hequestion_options`
--

DROP TABLE IF EXISTS `engine4_hequestion_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hequestion_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `is_custom` tinyint(1) NOT NULL DEFAULT '0',
  `question_id` int(11) NOT NULL,
  `vote_count` int(11) NOT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=393 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hequestion_options`
--

LOCK TABLES `engine4_hequestion_options` WRITE;
/*!40000 ALTER TABLE `engine4_hequestion_options` DISABLE KEYS */;
INSERT INTO `engine4_hequestion_options` VALUES (385,'cisco',3,0,83,1),(386,'apple',3,0,83,0),(387,'tesla',3,0,83,2),(388,'bank of america',3,0,83,2),(389,'fsdf',3,0,83,0),(390,'401k',3,0,84,2),(391,'Ira',3,0,84,0),(392,'Ira Roth ',3,0,84,0);
/*!40000 ALTER TABLE `engine4_hequestion_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hequestion_questions`
--

DROP TABLE IF EXISTS `engine4_hequestion_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hequestion_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `action_id` int(11) DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `follower_count` int(11) NOT NULL DEFAULT '0',
  `vote_count` int(11) NOT NULL DEFAULT '0',
  `can_add` tinyint(1) NOT NULL DEFAULT '1',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `owner_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `parent_type` varchar(90) COLLATE utf8_unicode_ci DEFAULT '',
  `parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hequestion_questions`
--

LOCK TABLES `engine4_hequestion_questions` WRITE;
/*!40000 ALTER TABLE `engine4_hequestion_questions` DISABLE KEYS */;
INSERT INTO `engine4_hequestion_questions` VALUES (83,3,0,'What is a good stock?',0,2,1,'2014-03-09 03:53:13','2014-03-09 03:53:13','user',3,'',0),(84,3,0,'What is better, 401k or IRA or IRA Roth?',0,2,1,'2014-03-12 02:44:45','2014-03-12 02:44:45','user',3,'',0);
/*!40000 ALTER TABLE `engine4_hequestion_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_hequestion_votes`
--

DROP TABLE IF EXISTS `engine4_hequestion_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_hequestion_votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`vote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_hequestion_votes`
--

LOCK TABLES `engine4_hequestion_votes` WRITE;
/*!40000 ALTER TABLE `engine4_hequestion_votes` DISABLE KEYS */;
INSERT INTO `engine4_hequestion_votes` VALUES (312,3,83,385,'2014-03-09 03:53:31'),(313,3,83,388,'2014-03-09 03:55:45'),(314,3,83,387,'2014-03-09 03:55:47'),(315,2,83,387,'2014-03-09 22:02:42'),(316,2,83,388,'2014-03-09 22:02:47'),(317,1,84,390,'2014-03-13 04:18:03'),(318,3,84,390,'2014-03-13 21:41:39');
/*!40000 ALTER TABLE `engine4_hequestion_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_invites`
--

DROP TABLE IF EXISTS `engine4_invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_invites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `recipient` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `timestamp` datetime NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `new_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `user_id` (`user_id`),
  KEY `recipient` (`recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_invites`
--

LOCK TABLES `engine4_invites` WRITE;
/*!40000 ALTER TABLE `engine4_invites` DISABLE KEYS */;
INSERT INTO `engine4_invites` VALUES (1,2,'ershad.qazi.jamil@gmail.com','21a4467','2014-03-04 16:33:51','You are being invited to join Wealthment.',0),(2,1,'ersh2121@yahoo.com','9431f85','2014-03-05 01:55:00','You are being invited to join our social network.',3);
/*!40000 ALTER TABLE `engine4_invites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_like_likes`
--

DROP TABLE IF EXISTS `engine4_like_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_like_likes` (
  `like_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `resource_title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `poster_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`like_id`),
  KEY `poster_type` (`poster_type`,`poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_like_likes`
--

LOCK TABLES `engine4_like_likes` WRITE;
/*!40000 ALTER TABLE `engine4_like_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_like_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_messages_conversations`
--

DROP TABLE IF EXISTS `engine4_messages_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_messages_conversations` (
  `conversation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL,
  `recipients` int(11) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `resource_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `resource_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`conversation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_messages_conversations`
--

LOCK TABLES `engine4_messages_conversations` WRITE;
/*!40000 ALTER TABLE `engine4_messages_conversations` DISABLE KEYS */;
INSERT INTO `engine4_messages_conversations` VALUES (1,'test message',3,1,'2014-03-09 22:56:57',0,NULL,0),(2,'Test message 2',3,1,'2014-03-24 16:25:08',0,NULL,0);
/*!40000 ALTER TABLE `engine4_messages_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_messages_messages`
--

DROP TABLE IF EXISTS `engine4_messages_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_messages_messages` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `attachment_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `attachment_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  UNIQUE KEY `CONVERSATIONS` (`conversation_id`,`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_messages_messages`
--

LOCK TABLES `engine4_messages_messages` WRITE;
/*!40000 ALTER TABLE `engine4_messages_messages` DISABLE KEYS */;
INSERT INTO `engine4_messages_messages` VALUES (1,1,3,'test message','test message, kitchen photo','2014-03-09 22:56:57','album_photo',3),(2,2,3,'Test message 2','this is a test of the messaging','2014-03-12 00:35:36','',0),(3,2,1,'','ok','2014-03-24 16:25:08','',0);
/*!40000 ALTER TABLE `engine4_messages_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_messages_recipients`
--

DROP TABLE IF EXISTS `engine4_messages_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_messages_recipients` (
  `user_id` int(11) unsigned NOT NULL,
  `conversation_id` int(11) unsigned NOT NULL,
  `inbox_message_id` int(11) unsigned DEFAULT NULL,
  `inbox_updated` datetime DEFAULT NULL,
  `inbox_read` tinyint(1) DEFAULT NULL,
  `inbox_deleted` tinyint(1) DEFAULT NULL,
  `outbox_message_id` int(11) unsigned DEFAULT NULL,
  `outbox_updated` datetime DEFAULT NULL,
  `outbox_deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`conversation_id`),
  KEY `INBOX_UPDATED` (`user_id`,`conversation_id`,`inbox_updated`),
  KEY `OUTBOX_UPDATED` (`user_id`,`conversation_id`,`outbox_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_messages_recipients`
--

LOCK TABLES `engine4_messages_recipients` WRITE;
/*!40000 ALTER TABLE `engine4_messages_recipients` DISABLE KEYS */;
INSERT INTO `engine4_messages_recipients` VALUES (1,2,2,'2014-03-12 00:35:36',1,0,3,'2014-03-24 16:25:08',0),(2,1,1,'2014-03-09 22:56:57',0,0,0,NULL,1),(3,1,NULL,NULL,1,1,1,'2014-03-09 22:56:57',0),(3,2,3,'2014-03-24 16:25:08',1,0,2,'2014-03-12 00:35:36',0);
/*!40000 ALTER TABLE `engine4_messages_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_mobile_content`
--

DROP TABLE IF EXISTS `engine4_mobile_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_mobile_content` (
  `content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_content_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `params` text COLLATE utf8_unicode_ci,
  `attribs` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`content_id`),
  KEY `page_id` (`page_id`,`order`)
) ENGINE=InnoDB AUTO_INCREMENT=831 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_mobile_content`
--

LOCK TABLES `engine4_mobile_content` WRITE;
/*!40000 ALTER TABLE `engine4_mobile_content` DISABLE KEYS */;
INSERT INTO `engine4_mobile_content` VALUES (100,1,'container','main',NULL,2,'[\"\"]',NULL),(200,2,'container','main',NULL,2,'[\"\"]',NULL),(210,2,'widget','mobile.menu-footer',200,2,'{\"title\":\"\",\"name\":\"mobile.menu-footer\"}',NULL),(300,3,'container','main',NULL,2,'[\"\"]',NULL),(312,3,'container','middle',300,6,'[\"\"]',NULL),(400,4,'container','main',NULL,2,'[\"\"]',NULL),(412,4,'container','middle',400,6,'[\"\"]',NULL),(440,4,'widget','mobile.list-announcements',412,3,'{\"title\":\"Announcements\"}',NULL),(441,4,'widget','mobile.activity-feed',412,4,'{\"title\":\"\",\"limit\":\"15\",\"name\":\"mobile.feed\"}',NULL),(500,5,'container','main',NULL,2,'[\"\"]',NULL),(511,5,'container','middle',500,6,'[\"\"]',NULL),(549,6,'container','main',NULL,2,'[\"\"]',NULL),(550,6,'container','middle',549,6,'[\"\"]',NULL),(552,6,'widget','mobile.container-tabs',550,6,'{\"max\":\"6\"}',NULL),(556,6,'widget','mobile.event-profile-info',552,8,'{\"title\":\"Event Details\"}',NULL),(559,6,'widget','mobile.event-profile-members',552,9,'{\"title\":\"Guests\",\"titleCount\":\"true\"}',NULL),(560,6,'widget','mobile.event-profile-photos',552,10,'{\"title\":\"Photos\",\"titleCount\":\"true\"}',NULL),(641,1,'widget','mobile.menu-main',100,3,'{\"count\":\"8\",\"title\":\"\",\"name\":\"mobile.menu-main\"}',NULL),(659,5,'widget','mobile.activity-feed',663,6,'{\"title\":\"What\'s New\",\"limit\":\"10\",\"name\":\"mobile.activity-feed\"}',NULL),(663,5,'widget','mobile.container-tabs',511,5,'{\"max\":\"6\"}',NULL),(664,5,'widget','mobile.album-profile-albums',663,12,'{\"title\":\"Albums\",\"titleCount\":\"true\"}',NULL),(666,1,'widget','mobile.main-header',100,2,NULL,NULL),(670,7,'container','main',NULL,2,NULL,NULL),(671,7,'container','middle',670,6,NULL,NULL),(673,7,'widget','mobile.site-map',671,3,'{\"type\":\"list\",\"title\":\"\",\"name\":\"mobile.site-map\"}',NULL),(680,5,'widget','mobile.blog-profile-blogs',663,14,'{\"title\":\"Blogs\",\"titleCount\":\"true\"}',NULL),(681,5,'widget','mobile.event-profile-events',663,9,'{\"title\":\"Events\",\"titleCount\":\"true\"}',NULL),(684,6,'widget','mobile.activity-feed',552,7,'{\"title\":\"What\'s New\",\"limit\":\"15\"}',NULL),(686,8,'container','main',NULL,2,NULL,NULL),(689,8,'container','middle',686,6,'[\"\"]',NULL),(691,8,'widget','mobile.container-tabs',689,5,'{\"max\":\"6\"}',NULL),(702,8,'widget','mobile.activity-feed',691,6,'{\"title\":\"What\'s New\",\"limit\":\"15\"}',NULL),(706,8,'widget','mobile.group-profile-info',691,7,'{\"title\":\"Info\",\"name\":\"mobile.group-profile-info\"}',NULL),(707,8,'widget','mobile.group-profile-members',691,8,'{\"title\":\"Members\",\"name\":\"mobile.group-profile-members\",\"titleCount\":\"true\",\"itemCountPerPage\":\"10\"}',NULL),(708,8,'widget','mobile.group-profile-photos',691,9,'{\"title\":\"Photos\",\"itemCountPerPage\":\"10\",\"titleCount\":\"true\"}',NULL),(709,8,'widget','mobile.group-profile-events',691,10,'{\"title\":\"Events\",\"titleCount\":\"true\",\"itemCountPerPage\":\"10\"}',NULL),(710,5,'widget','mobile.classified-profile-classifieds',663,15,'{\"title\":\"Classifieds\",\"titleCount\":\"true\"}',NULL),(720,5,'widget','mobile.group-profile-groups',663,10,'{\"title\":\"Groups\",\"titleCount\":\"true\"}',NULL),(724,3,'widget','mobile.user-login-or-signup',312,3,NULL,NULL),(730,5,'widget','mobile.user-profile-friends',663,8,'{\"title\":\"Friends\",\"titleCount\":\"true\"}',NULL),(735,2,'widget','mobile.mode-switcher',200,3,'{\"standard\":\"Standard Site\",\"mobile\":\"Mobile Site\"}',NULL),(736,4,'widget','mobile.autorecommendations',412,5,'{\"title\":\"Recommendations\"}',NULL),(738,5,'widget','mobile.like-box',663,18,'{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}',NULL),(739,5,'widget','mobile.like-profile-likes',663,17,'{\"title\":\"like_Likes\",\"titleCount\":\"true\"}',NULL),(741,5,'widget','mobile.user-profile-widgets',511,3,'{\"left\":[\"mobile.user-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.user-profile-options\"],\"title\":\"\",\"name\":\"mobile.user-profile-widgets\"}',NULL),(745,6,'widget','mobile.event-profile-rsvp',550,4,NULL,NULL),(748,6,'widget','mobile.like-box',552,11,'{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}',NULL),(750,8,'widget','mobile.like-box',691,11,'{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}',NULL),(758,9,'container','main',NULL,2,NULL,NULL),(759,9,'container','middle',758,6,NULL,NULL),(782,9,'widget','mobile.container-tabs',759,6,'{\"max\":\"6\"}',NULL),(783,9,'widget','mobile.page-feed',782,7,'{\"title\":\"Updates\",\"titleCount\":\"false\"}',NULL),(788,9,'widget','mobile.page-profile-fields',782,8,'{\"title\":\"Info\",\"titleCount\":\"true\"}',NULL),(791,9,'widget','mobile.page-profile-note',759,5,'{\"title\":\"Page Note\",\"titleCount\":\"false\"}',NULL),(794,9,'widget','mobile.page-profile-admins',782,9,'{\"title\":\"Team\",\"titleCount\":\"false\"}',NULL),(797,9,'widget','mobile.rate-widget',759,4,'{\"title\":\"Rate This\",\"titleCount\":\"true\"}',NULL),(805,9,'widget','mobile.page-profile-blog',782,11,'{\"title\":\"Blogs\",\"titleCount\":\"true\"}',NULL),(806,9,'widget','mobile.page-profile-discussion',782,12,'{\"title\":\"Discussions\",\"titleCount\":\"true\"}',NULL),(807,9,'widget','mobile.page-profile-event',782,13,'{\"title\":\"Events\",\"titleCount\":\"true\"}',NULL),(808,9,'widget','mobile.page-review',782,14,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}',NULL),(811,9,'widget','mobile.page-profile-album',782,10,'{\"title\":\"Albums\",\"titleCount\":\"true\",\"url_params\":{\"route\":\"default\",\"module\":\"pagealbum\",\"controller\":\"index\",\"action\":\"index\"}}',NULL),(813,9,'widget','mobile.page-profile-widgets',759,3,'{\"left\":[\"mobile.page-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.page-profile-options\"],\"title\":\"\",\"name\":\"mobile.page-profile-widgets\"}',NULL),(814,9,'widget','mobile.like-box',782,15,'{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}',NULL),(817,5,'widget','mobile.question-profile-questions',663,16,'{\"title\":\"Q&A\",\"titleCount\":\"false\"}',NULL),(818,5,'widget','mobile.article-profile-articles',663,13,'{\"title\":\"Articles\",\"titleCount\":\"true\"}',NULL),(819,5,'widget','mobile.page-profile-pages',663,11,'{\"title\":\"Pages\",\"titleCount\":\"true\"}',NULL),(820,5,'widget','mobile.rate-widget',511,4,'{\"title\":\"Rate This\",\"titleCount\":\"true\"}',NULL),(823,5,'widget','mobile.user-profile-info',663,7,'{\"title\":\"Profile Info\",\"titleCount\":\"true\"}',NULL),(825,8,'widget','mobile.rate-widget',689,4,'{\"title\":\"Rate This\",\"titleCount\":\"true\"}',NULL),(828,6,'widget','mobile.event-profile-widgets',550,3,'{\"left\":[\"mobile.event-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.event-profile-options\"]}',NULL),(829,8,'widget','mobile.group-profile-widgets',689,3,'{\"left\":[\"mobile.group-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.group-profile-options\"]}',NULL),(830,6,'widget','mobile.rate-widget',550,5,'{\"title\":\"Rate This\",\"titleCount\":\"true\"}',NULL);
/*!40000 ALTER TABLE `engine4_mobile_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_mobile_menuitems`
--

DROP TABLE IF EXISTS `engine4_mobile_menuitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_mobile_menuitems` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  `menu` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `submenu` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `custom` tinyint(1) NOT NULL DEFAULT '0',
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  KEY `LOOKUP` (`name`,`order`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_mobile_menuitems`
--

LOCK TABLES `engine4_mobile_menuitems` WRITE;
/*!40000 ALTER TABLE `engine4_mobile_menuitems` DISABLE KEYS */;
INSERT INTO `engine4_mobile_menuitems` VALUES (1,'user_footer_privacy','core','Privacy','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"privacy\"}','user_footer','',1,0,1),(2,'user_footer_terms','core','Terms of Service','','{\"route\":\"default\",\"core\":\"user\",\"controller\":\"help\",\"action\":\"terms\"}','user_footer','',1,0,2),(3,'user_footer_contact','core','Contact','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"contact\"}','user_footer','',1,0,3),(4,'user_home_updates','user','View Recent Updates','','{\"route\":\"recent_activity\",\"icon\":\"application/modules/User/externals/images/links/updates.png\"}','user_home','',1,0,1),(5,'user_home_view','user','View My Profile','User_Plugin_Menus','{\"route\":\"user_profile_self\",\"icon\":\"application/modules/User/externals/images/links/profile.png\"}','user_home','',1,0,2),(6,'user_home_friends','user','Browse Members','','{\"route\":\"user_general\",\"controller\":\"index\",\"action\":\"browse\",\"icon\":\"application/modules/User/externals/images/links/search.png\"}','user_home','',1,0,4),(7,'user_home_invite','invite','Invite Your Friends','Invite_Plugin_Menus::canInvite','{\"route\":\"default\",\"module\":\"invite\",\"icon\":\"application/modules/Invite/externals/images/invite.png\"}','user_home','',1,0,5),(8,'user_profile_friend','user','Friends','User_Plugin_Menus','','user_profile','',1,0,2),(9,'user_profile_message','messages','Send Message','Messages_Plugin_Menus','','user_profile','',1,0,3),(10,'core_footer_privacy','core','Privacy','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"privacy\"}','core_footer','',1,0,1),(11,'core_footer_contact','core','Contact','','{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"contact\"}','core_footer','',1,0,3),(12,'album_main_browse','album','Everyone\'s Albums','Album_Plugin_Menus::canViewAlbums','{\"route\":\"album_general\",\"action\":\"browse\"}','album_main','',1,0,1),(13,'album_main_manage','album','My Albums','Album_Plugin_Menus::canCreateAlbums','{\"route\":\"album_general\",\"action\":\"manage\"}','album_main','',1,0,2),(14,'blog_main_browse','blog','Browse Entries','Blog_Plugin_Menus::canViewBlogs','{\"route\":\"blog_general\"}','blog_main','',1,0,1),(15,'blog_main_manage','blog','My Entries','Blog_Plugin_Menus::canCreateBlogs','{\"route\":\"blog_general\",\"action\":\"manage\"}','blog_main','',1,0,2),(16,'blog_gutter_list','blog','View All Entries','Blog_Plugin_Menus','{\"route\":\"blog_view\",\"class\":\"buttonlink icon_blog_viewall\"}','blog_gutter','',1,0,1),(17,'blog_gutter_delete','blog','Delete This Entry','Blog_Plugin_Menus','{\"route\":\"blog_specific\",\"action\":\"delete\",\"class\":\"buttonlink icon_blog_delete\"}','blog_gutter','',1,0,4),(18,'core_mini_auth','user','Auth','User_Plugin_Menus','','core_footer','',1,0,998),(19,'event_main_upcoming','event','Upcoming Events','','{\"route\":\"event_upcoming\"}','event_main','',1,0,1),(20,'event_main_past','event','Past Events','','{\"route\":\"event_past\"}','event_main','',1,0,2),(21,'event_main_manage','event','My Events','','{\"route\":\"event_general\",\"action\":\"manage\"}','event_main','',1,0,3),(22,'core_main_home','core','Home','User_Plugin_Menus','','core_main','',1,0,1),(23,'core_mini_profile','user','My Profile','User_Plugin_Menus','','core_main','',1,0,2),(24,'core_mini_messages','messages','Messages','Messages_Plugin_Menus','','core_main','',1,0,3),(25,'activity_requests','activity','My Requests','','{\"route\":\"default\",\"action\":\"requests\", \"module\":\"activity\",\"controller\":\"notifications\"}','activity_main','',1,0,3),(26,'activity_notifications','activity','My Notifications','','{\"route\":\"recent_activity\"}','activity_main','',1,0,2),(27,'classified_main_browse','classified','Browse Listings','Classified_Plugin_Menus::canViewClassifieds','{\"route\":\"classified_general\"}','classified_main','',1,0,1),(28,'classified_main_manage','classified','My Listings','Classified_Plugin_Menus::canCreateClassifieds','{\"route\":\"classified_general\",\"action\":\"manage\"}','classified_main','',1,0,2),(29,'group_profile_member','group','Member','Group_Plugin_Menus','','group_profile','',1,0,3),(30,'group_profile_share','group','Share','Group_Plugin_Menus','','group_profile','',1,0,5),(31,'group_main_browse','group','Browse Groups','','{\"route\":\"group_general\",\"action\":\"browse\"}','group_main','',1,0,1),(32,'group_main_manage','group','My Groups','Group_Plugin_Menus','{\"route\":\"group_general\",\"action\":\"manage\"}','group_main','',1,0,2),(33,'event_profile_member','event','Member','Event_Plugin_Menus','','event_profile','',1,0,3),(34,'event_profile_share','event','Share','Event_Plugin_Menus','','event_profile','',1,0,5),(35,'page_profile_suggest','suggest','Suggest To Friends','Suggest_Plugin_Menus','','page_profile','',1,0,11),(36,'user_profile_suggest','suggest','Suggest To Friends','Suggest_Plugin_Menus','','user_profile','',1,0,11),(37,'group_profile_suggest','suggest','Suggest To Friends','Suggest_Plugin_Menus','','group_profile','',1,0,11),(38,'event_profile_suggest','suggest','Suggest To Friends','Suggest_Plugin_Menus','','event_profile','',1,0,11),(39,'classified_gutter_suggest','suggest','Suggest To Friends','Suggest_Plugin_Menus','','classified_gutter','',1,0,11),(40,'blog_gutter_suggest','suggest','Suggest To Friends','Suggest_Plugin_Menus','','blog_gutter','',1,0,11),(41,'page_profile_share','page','Share Page','Page_Plugin_Menus','','page_profile','',1,0,5),(42,'pageevent_past','pageevent','PAGEEVENT_PAST','Pageevent_Plugin_Menus','','pageevent','',1,0,2),(43,'pageevent_user','pageevent','PAGEEVENT_USER','Pageevent_Plugin_Menus','','pageevent','',1,0,3),(44,'pagealbum_all','pagealbum','All','Pagealbum_Plugin_Menus','','pagealbum','',1,0,1),(45,'pagealbum_mine','pagealbum','Mine','Pagealbum_Plugin_Menus','','pagealbum','',1,0,2),(46,'pageblog_all','pageblog','All','Pageblog_Plugin_Menus','','pageblog','',1,0,1),(47,'pageblog_mine','pageblog','Mine','Pageblog_Plugin_Menus','','pageblog','',1,0,2),(48,'core_sitemap_home','core','Home','','{\"route\":\"default\"}','core_sitemap','',1,0,1),(49,'core_sitemap_user','user','Members','','{\"route\":\"user_general\",\"action\":\"browse\"}','core_sitemap','',1,0,2),(50,'core_sitemap_event','event','Events','','{\"route\":\"event_general\"}','core_sitemap','',1,0,3),(51,'core_sitemap_group','group','Groups','','{\"route\":\"group_general\"}','core_sitemap','',1,0,4),(52,'core_sitemap_page','page','Pages','','{\"route\":\"page_browse\"}','core_sitemap','',1,0,5),(53,'core_sitemap_album','album','Albums','','{\"route\":\"album_general\",\"action\":\"browse\"}','core_sitemap','',1,0,6),(54,'core_sitemap_article','article','Articles','','{\"route\":\"article_browse\"}','core_sitemap','',1,0,7),(55,'core_sitemap_blog','blog','Blogs','','{\"route\":\"blog_general\"}','core_sitemap','',1,0,8),(56,'core_sitemap_classified','classified','Classifieds','','{\"route\":\"classified_general\"}','core_sitemap','',1,0,9),(57,'core_sitemap_question','question','Questions & Answers','','{\"route\":\"default\",\"module\":\"question\"}','core_sitemap','',1,0,10);
/*!40000 ALTER TABLE `engine4_mobile_menuitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_mobile_menus`
--

DROP TABLE IF EXISTS `engine4_mobile_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_mobile_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` enum('standard','hidden','custom') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'standard',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `order` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_mobile_menus`
--

LOCK TABLES `engine4_mobile_menus` WRITE;
/*!40000 ALTER TABLE `engine4_mobile_menus` DISABLE KEYS */;
INSERT INTO `engine4_mobile_menus` VALUES (1,'core_main','standard','Main Navigation Menu',1),(2,'core_mini','standard','Mini Navigation Menu',2),(3,'core_footer','standard','Footer Menu',3),(4,'core_sitemap','standard','Sitemap',4),(5,'user_home','standard','Member Home Quick Links Menu',999),(6,'user_profile','standard','Member Profile Options Menu',999),(7,'album_main','standard','Album Main Navigation Menu',999),(8,'blog_main','standard','Blog Main Navigation Menu',999),(9,'event_main','standard','Event Main Navigation Menu',999),(10,'event_profile','standard','Event Profile Options Menu',999),(11,'music_main','standard','Music Main Navigation Menu',999),(12,'poll_main','standard','Poll Main Navigation Menu',999),(13,'video_main','standard','Video Main Navigation Menu',999),(14,'group_main','standard','Group Main Navigation Menu',999),(15,'group_profile','standard','Group Profile Options Menu',999),(16,'classified_main','standard','Classified Main Navigation Menu',999),(17,'activity_main','standard','Activity Main Navigation Menu',999);
/*!40000 ALTER TABLE `engine4_mobile_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_mobile_pages`
--

DROP TABLE IF EXISTS `engine4_mobile_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_mobile_pages` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `custom` tinyint(1) NOT NULL DEFAULT '1',
  `fragment` tinyint(1) NOT NULL DEFAULT '0',
  `layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `levels` text COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_mobile_pages`
--

LOCK TABLES `engine4_mobile_pages` WRITE;
/*!40000 ALTER TABLE `engine4_mobile_pages` DISABLE KEYS */;
INSERT INTO `engine4_mobile_pages` VALUES (1,'header','Site Header',NULL,'','','',0,1,'',0,'0','core'),(2,'footer','Site Footer',NULL,'','','',0,1,'',0,'0','core'),(3,'core_index_index','Home Page',NULL,'Home Page','This is the home page.','',0,0,'',0,'0','core'),(4,'user_index_home','Member Home Page',NULL,'Member Home Page','This is the home page for members.','',0,0,'',0,'0','core'),(5,'user_profile_index','Member Profile',NULL,'Member Profile','This is a member\'s profile.','',0,0,'',0,'0','core'),(6,'event_profile_index','Event Profile',NULL,'Event Profile','This is the profile for an event.','',0,0,'',0,'0','event'),(7,'mobile_index_index','Dashboard',NULL,'Dashboard','This is the dashboard','',0,0,'default',0,'0','core'),(8,'group_profile_index','Group Profile',NULL,'Group Profile','This is the profile for an group.','',0,0,'',0,'','group'),(9,'page_index_view','Page Profile',NULL,'Page Profile','This is the profile for an profile.','',0,0,'',0,'','page');
/*!40000 ALTER TABLE `engine4_mobile_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_mobile_themes`
--

DROP TABLE IF EXISTS `engine4_mobile_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_mobile_themes` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`theme_id`),
  UNIQUE KEY `name` (`name`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_mobile_themes`
--

LOCK TABLES `engine4_mobile_themes` WRITE;
/*!40000 ALTER TABLE `engine4_mobile_themes` DISABLE KEYS */;
INSERT INTO `engine4_mobile_themes` VALUES (6,'default','Default Theme','',1),(7,'midnight','Midnight','',0),(8,'bamboo','Bamboo Theme','',0),(9,'snowbot','Snowbot Theme','',0),(10,'brightblue','Bright Blue','',0);
/*!40000 ALTER TABLE `engine4_mobile_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_network_membership`
--

DROP TABLE IF EXISTS `engine4_network_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_network_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `resource_approved` tinyint(1) NOT NULL DEFAULT '0',
  `user_approved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`resource_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_network_membership`
--

LOCK TABLES `engine4_network_membership` WRITE;
/*!40000 ALTER TABLE `engine4_network_membership` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_network_membership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_network_networks`
--

DROP TABLE IF EXISTS `engine4_network_networks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_network_networks` (
  `network_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field_id` int(11) unsigned NOT NULL DEFAULT '0',
  `pattern` text COLLATE utf8_unicode_ci,
  `member_count` int(11) unsigned NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `assignment` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`network_id`),
  KEY `assignment` (`assignment`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_network_networks`
--

LOCK TABLES `engine4_network_networks` WRITE;
/*!40000 ALTER TABLE `engine4_network_networks` DISABLE KEYS */;
INSERT INTO `engine4_network_networks` VALUES (1,'North America','',0,NULL,0,0,0),(2,'South America','',0,NULL,0,0,0),(3,'Europe','',0,NULL,0,0,0),(4,'Asia','',0,NULL,0,0,0),(5,'Africa','',0,NULL,0,0,0),(6,'Australia','',0,NULL,0,0,0),(7,'Antarctica','',0,NULL,0,0,0);
/*!40000 ALTER TABLE `engine4_network_networks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_payment_gateways`
--

DROP TABLE IF EXISTS `engine4_payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_payment_gateways` (
  `gateway_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `config` mediumblob,
  `test_mode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`gateway_id`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_payment_gateways`
--

LOCK TABLES `engine4_payment_gateways` WRITE;
/*!40000 ALTER TABLE `engine4_payment_gateways` DISABLE KEYS */;
INSERT INTO `engine4_payment_gateways` VALUES (1,'2Checkout',NULL,0,'Payment_Plugin_Gateway_2Checkout',NULL,0),(2,'PayPal',NULL,0,'Payment_Plugin_Gateway_PayPal',NULL,0),(3,'Testing',NULL,0,'Payment_Plugin_Gateway_Testing',NULL,1);
/*!40000 ALTER TABLE `engine4_payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_payment_orders`
--

DROP TABLE IF EXISTS `engine4_payment_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_payment_orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `gateway_id` int(10) unsigned NOT NULL,
  `gateway_order_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `state` enum('pending','cancelled','failed','incomplete','complete') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
  `creation_date` datetime NOT NULL,
  `source_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`,`gateway_order_id`),
  KEY `state` (`state`),
  KEY `source_type` (`source_type`,`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_payment_orders`
--

LOCK TABLES `engine4_payment_orders` WRITE;
/*!40000 ALTER TABLE `engine4_payment_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_payment_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_payment_packages`
--

DROP TABLE IF EXISTS `engine4_payment_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_payment_packages` (
  `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `level_id` int(10) unsigned NOT NULL,
  `downgrade_level_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(16,2) unsigned NOT NULL,
  `recurrence` int(11) unsigned NOT NULL,
  `recurrence_type` enum('day','week','month','year','forever') COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(11) unsigned NOT NULL,
  `duration_type` enum('day','week','month','year','forever') COLLATE utf8_unicode_ci NOT NULL,
  `trial_duration` int(11) unsigned NOT NULL DEFAULT '0',
  `trial_duration_type` enum('day','week','month','year','forever') COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `signup` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_payment_packages`
--

LOCK TABLES `engine4_payment_packages` WRITE;
/*!40000 ALTER TABLE `engine4_payment_packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_payment_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_payment_products`
--

DROP TABLE IF EXISTS `engine4_payment_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_payment_products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extension_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `extension_id` int(10) unsigned DEFAULT NULL,
  `sku` bigint(20) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(16,2) unsigned NOT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `extension_type` (`extension_type`,`extension_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_payment_products`
--

LOCK TABLES `engine4_payment_products` WRITE;
/*!40000 ALTER TABLE `engine4_payment_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_payment_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_payment_subscriptions`
--

DROP TABLE IF EXISTS `engine4_payment_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_payment_subscriptions` (
  `subscription_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `package_id` int(11) unsigned NOT NULL,
  `status` enum('initial','trial','pending','active','cancelled','expired','overdue','refunded') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'initial',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `gateway_id` int(10) unsigned DEFAULT NULL,
  `gateway_profile_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY `gateway_id` (`gateway_id`,`gateway_profile_id`),
  KEY `user_id` (`user_id`),
  KEY `package_id` (`package_id`),
  KEY `status` (`status`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_payment_subscriptions`
--

LOCK TABLES `engine4_payment_subscriptions` WRITE;
/*!40000 ALTER TABLE `engine4_payment_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_payment_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_payment_transactions`
--

DROP TABLE IF EXISTS `engine4_payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_payment_transactions` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `gateway_id` int(10) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `state` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `gateway_parent_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `gateway_order_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `amount` decimal(16,2) NOT NULL,
  `currency` char(3) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`transaction_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `type` (`type`),
  KEY `state` (`state`),
  KEY `gateway_transaction_id` (`gateway_transaction_id`),
  KEY `gateway_parent_transaction_id` (`gateway_parent_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_payment_transactions`
--

LOCK TABLES `engine4_payment_transactions` WRITE;
/*!40000 ALTER TABLE `engine4_payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_storage_chunks`
--

DROP TABLE IF EXISTS `engine4_storage_chunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_storage_chunks` (
  `chunk_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(11) unsigned NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (`chunk_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_storage_chunks`
--

LOCK TABLES `engine4_storage_chunks` WRITE;
/*!40000 ALTER TABLE `engine4_storage_chunks` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_storage_chunks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_storage_files`
--

DROP TABLE IF EXISTS `engine4_storage_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_storage_files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_file_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `service_id` int(10) unsigned NOT NULL DEFAULT '1',
  `storage_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mime_major` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `mime_minor` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `hash` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `parent_file_id` (`parent_file_id`,`type`),
  KEY `PARENT` (`parent_type`,`parent_id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_storage_files`
--

LOCK TABLES `engine4_storage_files` WRITE;
/*!40000 ALTER TABLE `engine4_storage_files` DISABLE KEYS */;
INSERT INTO `engine4_storage_files` VALUES (1,NULL,NULL,'user',1,1,'2014-03-04 00:19:15','2014-03-04 00:19:15',1,'public/user/01/0001_35b8.JPG','JPG','Alessia Jamil 1_m.JPG','image','jpeg',25240,'da0b35b82eeff784eb115274eceb3fcf'),(2,1,'thumb.profile','user',1,1,'2014-03-04 00:19:16','2014-03-04 00:19:16',1,'public/user/02/0002_e160.JPG','JPG','Alessia Jamil 1_p.JPG','image','jpeg',4567,'a705e160c52a31996885f48b5b15d8d3'),(3,1,'thumb.normal','user',1,1,'2014-03-04 00:19:16','2014-03-04 00:19:16',1,'public/user/03/0003_ff65.JPG','JPG','Alessia Jamil 1_in.JPG','image','jpeg',2965,'b701ff65323cc72fdf28b71740fdac88'),(4,1,'thumb.icon','user',1,1,'2014-03-04 00:19:16','2014-03-04 00:19:16',1,'public/user/04/0004_8bc2.JPG','JPG','Alessia Jamil 1_is.JPG','image','jpeg',1260,'8d1c8bc2338363fa607ae5df993e26c7'),(5,NULL,NULL,'group',2,1,'2014-03-04 16:27:57','2014-03-04 16:27:57',1,'public/group/05/0005_8cd5.jpg','jpg','m_tesla-badge.jpg','image','jpeg',22369,'c94a8cd5577f31bbbd0f556adc90b863'),(6,5,'thumb.profile','group',2,1,'2014-03-04 16:27:57','2014-03-04 16:27:57',1,'public/group/06/0006_375e.jpg','jpg','p_tesla-badge.jpg','image','jpeg',6003,'04a9375eb8f65dcc4bcbda1d327be3b9'),(7,5,'thumb.normal','group',2,1,'2014-03-04 16:27:57','2014-03-04 16:27:57',1,'public/group/07/0007_61a5.jpg','jpg','in_tesla-badge.jpg','image','jpeg',3736,'922b61a56ce96de19e68aa8ed87f8fa3'),(8,5,'thumb.icon','group',2,1,'2014-03-04 16:27:57','2014-03-04 16:27:57',1,'public/group/08/0008_8568.jpg','jpg','is_tesla-badge.jpg','image','jpeg',1447,'3a6b85682ce0780cc3fa355d9352cfd3'),(9,NULL,NULL,'user',2,NULL,'2014-03-04 16:30:47','2014-03-04 16:30:47',1,'public/user/09/0009_4d0a.jpg','jpg','m_Poppy Ornament 2013.jpg','image','jpeg',82964,'46f44d0ae166374a1847c2c44ba38021'),(10,9,'thumb.profile','user',2,NULL,'2014-03-04 16:30:47','2014-03-04 16:30:47',1,'public/user/0a/000a_3e81.jpg','jpg','p_Poppy Ornament 2013.jpg','image','jpeg',9628,'29653e815c44f31acb2492371d8efdaa'),(11,9,'thumb.normal','user',2,NULL,'2014-03-04 16:30:47','2014-03-04 16:30:47',1,'public/user/0b/000b_b6cf.jpg','jpg','n_Poppy Ornament 2013.jpg','image','jpeg',1464,'c344b6cf3f923bf89349404376e57922'),(12,9,'thumb.icon','user',2,NULL,'2014-03-04 16:30:47','2014-03-04 16:30:47',1,'public/user/0c/000c_ade2.jpg','jpg','s_Poppy Ornament 2013.jpg','image','jpeg',1471,'a24aade2866e3ab1223897b7fbed480f'),(13,NULL,NULL,'user',3,NULL,'2014-03-05 02:01:22','2014-03-05 02:01:22',1,'public/user/0d/000d_4326.JPG','JPG','m_IMG_0488.JPG','image','jpeg',43256,'95744326509620d5b5f0ccce39c31688'),(14,13,'thumb.profile','user',3,NULL,'2014-03-05 02:01:22','2014-03-05 02:01:22',1,'public/user/0e/000e_0420.JPG','JPG','p_IMG_0488.JPG','image','jpeg',10347,'379904205eb12036191beff61a6a7758'),(15,13,'thumb.normal','user',3,NULL,'2014-03-05 02:01:22','2014-03-05 02:01:22',1,'public/user/0f/000f_d290.JPG','JPG','n_IMG_0488.JPG','image','jpeg',1647,'8205d2904c34e3d610e993cbe077689b'),(16,13,'thumb.icon','user',3,NULL,'2014-03-05 02:01:22','2014-03-05 02:01:22',1,'public/user/10/0010_5ac5.JPG','JPG','s_IMG_0488.JPG','image','jpeg',1480,'824f5ac54787ce302d4705792c29a908'),(17,NULL,NULL,'album_photo',1,1,'2014-03-06 04:19:27','2014-03-06 04:19:27',1,'public/album_photo/11/0011_c769.png','png','Yelp Logo_m.png','image','png',16190,'b12cc769d2d9a7f21ab7af21ac4ed7aa'),(18,17,'thumb.normal','album_photo',1,1,'2014-03-06 04:19:27','2014-03-06 04:19:27',1,'public/album_photo/12/0012_aa86.png','png','Yelp Logo_in.png','image','png',16338,'f560aa86cd8ba3c4b123f3c03cf78ca0'),(19,NULL,NULL,'core_link',2,1,'2014-03-06 04:20:08','2014-03-06 04:20:08',1,'public/core_link/13/0013_f50b.jpeg','jpeg','thumb_90ec9ff1e2c0a84f350226d548ee1b09..jpeg','image','jpeg',3543,'2fa4f50b94dc43a8feb2ce5d7fa926df'),(20,NULL,NULL,'album_photo',2,3,'2014-03-08 16:03:40','2014-03-08 16:03:40',1,'public/album_photo/14/0014_1e12.jpg','jpg','Kitchen Target_m.jpg','image','jpeg',12753,'3f831e12b1b5f7a9fa7c9ebeeb1a1307'),(21,20,'thumb.normal','album_photo',2,3,'2014-03-08 16:03:40','2014-03-08 16:03:40',1,'public/album_photo/15/0015_661f.jpg','jpg','Kitchen Target_in.jpg','image','jpeg',4367,'a1d9661f270b24185c6e4813d0828794'),(22,NULL,NULL,'welcome',1,1,'2014-03-09 01:54:57','2014-03-09 01:54:57',1,'public/welcome/16/0016_41b3.png','png','Green icon.png','image','png',1434,'750141b3f8a397a69c1ec48836e91a6b'),(23,22,'thumb.normal','welcome',1,1,'2014-03-09 01:54:57','2014-03-09 01:54:57',1,'public/welcome/17/0017_4520.png','png','tGreen icon.png','image','png',3452,'b2d545203618e575b14bc5d5c843fd1e'),(24,NULL,NULL,'welcome',2,1,'2014-03-09 01:55:35','2014-03-09 01:55:35',1,'public/welcome/18/0018_59db.png','png','Blue Icon.png','image','png',1507,'eb6859db1c2aac1d77fd8445e3885c04'),(25,24,'thumb.normal','welcome',2,1,'2014-03-09 01:55:35','2014-03-09 01:55:35',1,'public/welcome/19/0019_e67b.png','png','tBlue Icon.png','image','png',3704,'a6b9e67b62f1f97d67d2c625ddd58863'),(26,NULL,NULL,'welcome',3,1,'2014-03-09 01:56:00','2014-03-09 01:56:00',1,'public/welcome/1a/001a_7148.png','png','Orange icon.png','image','png',1472,'cbd371488be7a15b9a5ef3c990065daf'),(27,26,'thumb.normal','welcome',3,1,'2014-03-09 01:56:00','2014-03-09 01:56:00',1,'public/welcome/1b/001b_a196.png','png','tOrange icon.png','image','png',4059,'284ea196fce88787fda8cf02aee95655'),(28,NULL,NULL,'album_photo',3,3,'2014-03-09 22:56:52','2014-03-09 22:56:52',1,'public/album_photo/1c/001c_6889.jpg','jpg','Kitchen Month X_m.jpg','image','jpeg',10297,'b76b688910e8363155155c60f3cd2050'),(29,28,'thumb.normal','album_photo',3,3,'2014-03-09 22:56:52','2014-03-09 22:56:52',1,'public/album_photo/1d/001d_c851.jpg','jpg','Kitchen Month X_in.jpg','image','jpeg',3848,'545ac851b0c4b5076c738c51a3429f29'),(32,NULL,NULL,'welcome',4,1,'2014-03-10 21:53:47','2014-03-10 21:53:47',1,'public/welcome/20/0020_e3fe.gif','gif','stockstarter.gif','image','gif',31097,'17efe3fe4ae7b25f4a1c558ef842f615'),(33,32,'thumb.normal','welcome',4,1,'2014-03-10 21:53:48','2014-03-10 21:53:48',1,'public/welcome/21/0021_1a55.gif','gif','tstockstarter.gif','image','gif',879,'16241a5568b947a1a32708c3b314ce2a'),(34,NULL,NULL,'welcome',5,1,'2014-03-10 22:01:36','2014-03-10 22:01:36',1,'public/welcome/22/0022_a9c0.gif','gif','stocksdabbler.gif','image','gif',29147,'e3d8a9c00cb583c84f9196323cff9df6'),(35,34,'thumb.normal','welcome',5,1,'2014-03-10 22:01:36','2014-03-10 22:01:36',1,'public/welcome/23/0023_bff0.gif','gif','tstocksdabbler.gif','image','gif',806,'ac2cbff04eb4fcb9dfdc59bd45275446'),(37,NULL,NULL,'album_photo',4,3,'2014-03-14 02:19:53','2014-03-14 02:19:53',1,'public/album_photo/25/0025_9696.jpg','jpg','8c1bd63bd9e0dfdddc7a59715f0cbe65_m.jpg','image','jpeg',37258,'c04d96969d158e4668eaaeea6b4f2988'),(38,37,'thumb.normal','album_photo',4,3,'2014-03-14 02:19:53','2014-03-14 02:19:53',1,'public/album_photo/26/0026_8d1a.jpg','jpg','8c1bd63bd9e0dfdddc7a59715f0cbe65_in.jpg','image','jpeg',3385,'b3d48d1a48999a56cf43191c983dd4c5'),(40,NULL,NULL,'album_photo',5,1,'2014-03-15 20:48:57','2014-03-15 20:48:57',1,'public/album_photo/28/0028_abb3.jpg','jpg','d723de909b6b95a6dc3208426dbc66fe_m.jpg','image','jpeg',24886,'c366abb3334b6105129753d57934b222'),(41,40,'thumb.normal','album_photo',5,1,'2014-03-15 20:48:57','2014-03-15 20:48:57',1,'public/album_photo/29/0029_55c0.jpg','jpg','d723de909b6b95a6dc3208426dbc66fe_in.jpg','image','jpeg',3152,'fb8855c00350f26e7b2e3f4b3e36775f'),(42,NULL,NULL,'temporary',1,NULL,'2014-03-15 23:32:17','2014-03-15 23:32:17',1,'public/temporary/2a/002a_91e6.jpg','jpg','m_d66b81ada67b045f08ec66572232f860.jpg','image','gif',919,'fe0291e6c8055ce1eb0e24e3979460ad'),(43,42,'thumb.profile','temporary',1,NULL,'2014-03-15 23:32:17','2014-03-15 23:32:17',1,'public/temporary/2b/002b_91e6.jpg','jpg','p_d66b81ada67b045f08ec66572232f860.jpg','image','gif',919,'fe0291e6c8055ce1eb0e24e3979460ad'),(44,42,'thumb.normal','temporary',1,NULL,'2014-03-15 23:32:17','2014-03-15 23:32:17',1,'public/temporary/2c/002c_ef20.jpg','jpg','n_d66b81ada67b045f08ec66572232f860.jpg','image','gif',365,'95a6ef2006abcf1ffffa55fc6c127774'),(45,42,'thumb.icon','temporary',1,NULL,'2014-03-15 23:32:17','2014-03-15 23:32:17',1,'public/temporary/2d/002d_b16c.jpg','jpg','s_d66b81ada67b045f08ec66572232f860.jpg','image','gif',367,'9b88b16c069233fc6c8e0b3ba15d7d26'),(46,NULL,NULL,'user',5,NULL,'2014-03-16 01:08:26','2014-03-16 01:08:26',1,'public/user/2e/002e_4399.jpg','jpg','m_96d154bfb096bbf16430be54dfd48ded.jpg','image','jpeg',9800,'5de143991a46b8d271e2f4621f086ac4'),(47,46,'thumb.profile','user',5,NULL,'2014-03-16 01:08:26','2014-03-16 01:08:26',1,'public/user/2f/002f_4399.jpg','jpg','p_96d154bfb096bbf16430be54dfd48ded.jpg','image','jpeg',9800,'5de143991a46b8d271e2f4621f086ac4'),(48,46,'thumb.normal','user',5,NULL,'2014-03-16 01:08:26','2014-03-16 01:08:26',1,'public/user/30/0030_d3ef.jpg','jpg','n_96d154bfb096bbf16430be54dfd48ded.jpg','image','jpeg',1360,'d8d5d3efdb38d46e38907f35711a526e'),(49,46,'thumb.icon','user',5,NULL,'2014-03-16 01:08:26','2014-03-16 01:08:26',1,'public/user/31/0031_d3ef.jpg','jpg','s_96d154bfb096bbf16430be54dfd48ded.jpg','image','jpeg',1360,'d8d5d3efdb38d46e38907f35711a526e');
/*!40000 ALTER TABLE `engine4_storage_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_storage_mirrors`
--

DROP TABLE IF EXISTS `engine4_storage_mirrors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_storage_mirrors` (
  `file_id` bigint(20) unsigned NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`file_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_storage_mirrors`
--

LOCK TABLES `engine4_storage_mirrors` WRITE;
/*!40000 ALTER TABLE `engine4_storage_mirrors` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_storage_mirrors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_storage_services`
--

DROP TABLE IF EXISTS `engine4_storage_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_storage_services` (
  `service_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `servicetype_id` int(10) unsigned NOT NULL,
  `config` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_storage_services`
--

LOCK TABLES `engine4_storage_services` WRITE;
/*!40000 ALTER TABLE `engine4_storage_services` DISABLE KEYS */;
INSERT INTO `engine4_storage_services` VALUES (1,1,NULL,1,1);
/*!40000 ALTER TABLE `engine4_storage_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_storage_servicetypes`
--

DROP TABLE IF EXISTS `engine4_storage_servicetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_storage_servicetypes` (
  `servicetype_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `form` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`servicetype_id`),
  UNIQUE KEY `plugin` (`plugin`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_storage_servicetypes`
--

LOCK TABLES `engine4_storage_servicetypes` WRITE;
/*!40000 ALTER TABLE `engine4_storage_servicetypes` DISABLE KEYS */;
INSERT INTO `engine4_storage_servicetypes` VALUES (1,'Local Storage','Storage_Service_Local','Storage_Form_Admin_Service_Local',1),(2,'Database Storage','Storage_Service_Db','Storage_Form_Admin_Service_Db',0),(3,'Amazon S3','Storage_Service_S3','Storage_Form_Admin_Service_S3',1),(4,'Virtual File System','Storage_Service_Vfs','Storage_Form_Admin_Service_Vfs',1),(5,'Round-Robin','Storage_Service_RoundRobin','Storage_Form_Admin_Service_RoundRobin',0),(6,'Mirrored','Storage_Service_Mirrored','Storage_Form_Admin_Service_Mirrored',0);
/*!40000 ALTER TABLE `engine4_storage_servicetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_block`
--

DROP TABLE IF EXISTS `engine4_user_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_block` (
  `user_id` int(11) unsigned NOT NULL,
  `blocked_user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`blocked_user_id`),
  KEY `REVERSE` (`blocked_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_block`
--

LOCK TABLES `engine4_user_block` WRITE;
/*!40000 ALTER TABLE `engine4_user_block` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_facebook`
--

DROP TABLE IF EXISTS `engine4_user_facebook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_facebook` (
  `user_id` int(11) unsigned NOT NULL,
  `facebook_uid` bigint(20) unsigned NOT NULL,
  `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `expires` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `facebook_uid` (`facebook_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_facebook`
--

LOCK TABLES `engine4_user_facebook` WRITE;
/*!40000 ALTER TABLE `engine4_user_facebook` DISABLE KEYS */;
INSERT INTO `engine4_user_facebook` VALUES (4,100007999565215,'CAAKKIN82IQUBAJZB6Hk5QCFRwap8WxrXOFwbOSeUdW6zhxlUWbcTcFZCDkrI0ZCrGkumN86YGUhnRRD7Dptcb1PNvYF9Trf686DoUZBoXIlg65kwNC5TcPwz3yzgz3tUPUKSU8cppJsOzovS3HZCjUVuSZBQeZB3L8FJlSZBUw3GkikxbxyS9yuE','',0),(5,14210486,'CAAKKIN82IQUBACPFMxzp5BPZAKYuxsT2EGh4cdSQWjV5Q3QqDWChfCBfxMLz7ZBP5Ran5s3zUq8Xt4x97gWch4P3jpJaDAp3E42i6yhZB6rDpAy2hNQxzUOnYq4Ko7EjpHzcp6fAmMSjx23Mw8Rku6FHHq0R9VujCV3UxvCeeNoqECbUisHlArHQHQZCHUMZD','',0);
/*!40000 ALTER TABLE `engine4_user_facebook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_fields_maps`
--

DROP TABLE IF EXISTS `engine4_user_fields_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_fields_maps`
--

LOCK TABLES `engine4_user_fields_maps` WRITE;
/*!40000 ALTER TABLE `engine4_user_fields_maps` DISABLE KEYS */;
INSERT INTO `engine4_user_fields_maps` VALUES (0,0,1,1),(1,1,2,1),(1,1,3,2),(1,1,4,3),(1,1,5,4),(1,1,14,5),(1,1,15,6),(1,1,16,7),(1,1,17,8),(1,1,18,12),(1,1,19,9),(1,1,20,10),(1,1,21,11);
/*!40000 ALTER TABLE `engine4_user_fields_maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_fields_meta`
--

DROP TABLE IF EXISTS `engine4_user_fields_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_fields_meta` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_fields_meta`
--

LOCK TABLES `engine4_user_fields_meta` WRITE;
/*!40000 ALTER TABLE `engine4_user_fields_meta` DISABLE KEYS */;
INSERT INTO `engine4_user_fields_meta` VALUES (1,'profile_type','Profile Type','','profile_type',1,0,0,2,1,999,'',NULL,NULL,NULL,NULL),(2,'heading','Basic Information','','',0,1,0,0,1,999,'{\"submit\":\"\"}',NULL,NULL,NULL,NULL),(3,'first_name','First Name','','first_name',1,1,0,2,1,999,'[]','[[\"StringLength\",false,[1,32]]]',NULL,'',''),(4,'last_name','Last Name','','last_name',1,1,0,2,1,999,'','[[\"StringLength\",false,[1,32]]]',NULL,NULL,NULL),(5,'gender','Gender','','gender',1,1,0,1,1,999,'[]',NULL,NULL,'',''),(14,'occupation','Occupation','','occupation',1,1,0,1,1,999,'[]',NULL,NULL,'',''),(15,'heading','Wealth Information','','',0,1,0,0,1,999,'{\"submit\":\"\"}',NULL,NULL,NULL,NULL),(16,'text','Learning Goal','What are you interested in learning about (e.g., stocks, real estate, etc.)?','',0,1,0,0,1,999,'[]',NULL,NULL,'',''),(17,'integer','Stock Knowledge','What is your stock knowledge (Scale of 1-10, 10 being the highest)?','',0,1,0,0,1,999,'[]',NULL,NULL,'',''),(18,'textarea','Wealth 5 Year Goal','What is your wealth goal in the next five years?','',0,1,0,0,1,999,'[]',NULL,NULL,'',''),(19,'integer','Real Estate Knowledge','What is your real estate knowledge (Scale of 1-10, 10 being the highest)?','',0,1,0,0,1,999,'[]',NULL,NULL,'',''),(20,'integer','Retirement Knowledge','What is your retirement knowledge (Scale of 1-10, 10 being the highest)?','',0,1,0,0,1,999,'[]',NULL,NULL,'',''),(21,'integer','Other Savings Knowledge','What is your other savings knowledge (Scale of 1-10, 10 being the highest)?','',0,1,0,0,1,999,'[]',NULL,NULL,'','');
/*!40000 ALTER TABLE `engine4_user_fields_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_fields_options`
--

DROP TABLE IF EXISTS `engine4_user_fields_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_fields_options`
--

LOCK TABLES `engine4_user_fields_options` WRITE;
/*!40000 ALTER TABLE `engine4_user_fields_options` DISABLE KEYS */;
INSERT INTO `engine4_user_fields_options` VALUES (1,1,'Regular Member',1),(2,5,'Male',1),(3,5,'Female',2);
/*!40000 ALTER TABLE `engine4_user_fields_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_fields_search`
--

DROP TABLE IF EXISTS `engine4_user_fields_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_fields_search` (
  `item_id` int(11) unsigned NOT NULL,
  `profile_type` smallint(11) unsigned DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('2','3') COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `occupation` enum('admn','arch','crea','educ','mngt','fash','fina','labr','lawe','legl','medi','nonp','poli','retl','retr','sale','self','stud','tech','trav','othr') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `profile_type` (`profile_type`),
  KEY `last_name` (`last_name`),
  KEY `birthdate` (`birthdate`),
  KEY `occupation` (`occupation`),
  KEY `gender` (`gender`),
  KEY `first_name` (`first_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_fields_search`
--

LOCK TABLES `engine4_user_fields_search` WRITE;
/*!40000 ALTER TABLE `engine4_user_fields_search` DISABLE KEYS */;
INSERT INTO `engine4_user_fields_search` VALUES (1,1,'Wealthment','Administrator','2',NULL,NULL),(2,1,'Jeffrey','Lee','2','1978-01-01',NULL),(3,1,'Ershad','Jamil','2','0000-00-00',NULL),(4,1,'Jeffrey','Lee','2',NULL,NULL),(5,1,'Ershad','Jamil','2',NULL,'othr');
/*!40000 ALTER TABLE `engine4_user_fields_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_fields_values`
--

DROP TABLE IF EXISTS `engine4_user_fields_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_fields_values`
--

LOCK TABLES `engine4_user_fields_values` WRITE;
/*!40000 ALTER TABLE `engine4_user_fields_values` DISABLE KEYS */;
INSERT INTO `engine4_user_fields_values` VALUES (1,1,0,'1',NULL),(1,3,0,'Wealthment','everyone'),(1,4,0,'Administrator','everyone'),(1,5,0,'2','everyone'),(1,14,0,'mngt','everyone'),(1,16,0,'Stocks and Retirement','everyone'),(1,17,0,'7','everyone'),(1,18,0,'Accumulate wealth in the stock market and learn how to save for my child and family.','everyone'),(1,19,0,'5','everyone'),(1,20,0,'6','everyone'),(1,21,0,'7','everyone'),(2,1,0,'1',NULL),(2,3,0,'Jeffrey','everyone'),(2,4,0,'Lee','everyone'),(2,5,0,'2','everyone'),(2,14,0,'','everyone'),(2,16,0,'Learning how to evaluate companies','everyone'),(2,17,0,'','everyone'),(2,18,0,'','everyone'),(2,19,0,'','everyone'),(2,20,0,'','everyone'),(2,21,0,'','everyone'),(3,1,0,'1',NULL),(3,3,0,'Ershad','everyone'),(3,4,0,'Jamil','everyone'),(3,5,0,'2','everyone'),(3,14,0,'mngt','everyone'),(3,16,0,'Stocks','everyone'),(3,17,0,'5','everyone'),(3,18,0,'Make money!','everyone'),(3,19,0,'6','everyone'),(3,20,0,'2','everyone'),(3,21,0,'4','everyone'),(4,1,0,'1',NULL),(4,3,0,'Jeffrey',NULL),(4,4,0,'Lee',NULL),(4,5,0,'2',NULL),(4,14,0,'tech',NULL),(4,16,0,'',NULL),(4,17,0,'',NULL),(4,18,0,'',NULL),(4,19,0,'',NULL),(4,20,0,'',NULL),(4,21,0,'',NULL),(5,1,0,'1',NULL),(5,3,0,'Ershad','everyone'),(5,4,0,'Jamil','everyone'),(5,5,0,'2','everyone'),(5,14,0,'othr','everyone'),(5,16,0,'','everyone'),(5,17,0,'','everyone'),(5,18,0,'','everyone'),(5,19,0,'','everyone'),(5,20,0,'','everyone'),(5,21,0,'','everyone');
/*!40000 ALTER TABLE `engine4_user_fields_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_forgot`
--

DROP TABLE IF EXISTS `engine4_user_forgot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_forgot` (
  `user_id` int(11) unsigned NOT NULL,
  `code` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_forgot`
--

LOCK TABLES `engine4_user_forgot` WRITE;
/*!40000 ALTER TABLE `engine4_user_forgot` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_forgot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_janrain`
--

DROP TABLE IF EXISTS `engine4_user_janrain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_janrain` (
  `user_id` int(11) unsigned NOT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_janrain`
--

LOCK TABLES `engine4_user_janrain` WRITE;
/*!40000 ALTER TABLE `engine4_user_janrain` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_janrain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_listitems`
--

DROP TABLE IF EXISTS `engine4_user_listitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_listitems` (
  `listitem_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_listitems`
--

LOCK TABLES `engine4_user_listitems` WRITE;
/*!40000 ALTER TABLE `engine4_user_listitems` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_listitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_lists`
--

DROP TABLE IF EXISTS `engine4_user_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_lists` (
  `list_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_lists`
--

LOCK TABLES `engine4_user_lists` WRITE;
/*!40000 ALTER TABLE `engine4_user_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_logins`
--

DROP TABLE IF EXISTS `engine4_user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_logins` (
  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varbinary(16) NOT NULL,
  `timestamp` datetime NOT NULL,
  `state` enum('success','no-member','bad-password','disabled','unpaid','third-party','v3-migration','unknown') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'unknown',
  `source` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login_id`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_logins`
--

LOCK TABLES `engine4_user_logins` WRITE;
/*!40000 ALTER TABLE `engine4_user_logins` DISABLE KEYS */;
INSERT INTO `engine4_user_logins` VALUES (1,1,'wealthment@gmail.com','bš5Ò','2014-02-17 00:58:32','success',NULL,1),(2,1,'wealthment@gmail.com','Œ÷À,','2014-03-03 22:00:51','success',NULL,0),(3,1,'wealthment@gmail.com','Ç½','2014-03-04 00:13:23','success',NULL,0),(4,1,'wealthment@gmail.com','bš5Ò','2014-03-05 01:50:27','success',NULL,1),(5,NULL,'jeffrey.lee@ptminconline.com','Œ÷À,','2014-03-05 17:30:02','no-member',NULL,0),(6,2,'spargos@gmail.com','Œ÷À,','2014-03-05 17:30:12','success',NULL,1),(7,1,'wealthment@gmail.com','bš5Ò','2014-03-06 03:45:05','success',NULL,1),(8,1,'wealthment@gmail.com','€1Õ','2014-03-06 12:45:58','success',NULL,1),(9,2,'spargos@gmail.com','Œ÷À,','2014-03-06 16:06:17','success',NULL,0),(10,1,'wealthment@gmail.com','Œ÷À,','2014-03-06 16:07:09','success',NULL,1),(11,3,'ersh2121@yahoo.com','bš5Ò','2014-03-06 17:04:22','success',NULL,1),(12,3,'ersh2121@yahoo.com','bš5Ò','2014-03-07 03:37:31','success',NULL,0),(13,1,'wealthment@gmail.com','bš5Ò','2014-03-07 04:01:48','success',NULL,1),(14,1,'wealthment@gmail.com','bš5Ò','2014-03-07 19:33:46','success',NULL,0),(15,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 01:29:14','success',NULL,0),(16,1,'wealthment@gmail.com','bš5Ò','2014-03-08 03:21:36','success',NULL,0),(17,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 15:08:01','success',NULL,0),(18,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 18:47:24','success',NULL,0),(19,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 20:39:38','success',NULL,0),(20,1,'wealthment@gmail.com','€1Õ','2014-03-08 20:47:18','success',NULL,0),(21,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 20:54:13','success',NULL,0),(22,NULL,'jeffrey.lee@ptminconline.com','€1Õ','2014-03-08 20:54:46','no-member',NULL,0),(23,2,'spargos@gmail.com','€1Õ','2014-03-08 20:58:07','bad-password',NULL,0),(24,2,'spargos@gmail.com','€1Õ','2014-03-08 20:58:14','success',NULL,0),(25,1,'wealthment@gmail.com','€1Õ','2014-03-08 21:02:24','success',NULL,0),(26,1,'wealthment@gmail.com','€1Õ','2014-03-08 21:20:07','success',NULL,1),(27,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 21:45:55','success',NULL,0),(28,3,'ersh2121@yahoo.com','bš5Ò','2014-03-08 23:20:05','success',NULL,0),(29,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 01:30:14','success',NULL,0),(30,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 01:39:48','success',NULL,0),(31,2,'spargos@gmail.com','€1Õ','2014-03-09 01:52:30','success',NULL,0),(32,1,'wealthment@gmail.com','€1Õ','2014-03-09 02:07:03','bad-password',NULL,0),(33,1,'wealthment@gmail.com','€1Õ','2014-03-09 02:07:15','success',NULL,1),(34,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 02:16:43','success',NULL,0),(35,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 02:26:51','success',NULL,0),(36,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 02:29:01','success',NULL,0),(37,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 02:34:27','success',NULL,0),(38,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 03:48:40','success',NULL,0),(39,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 03:51:34','success',NULL,0),(40,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 07:21:24','success',NULL,0),(41,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 07:24:32','success',NULL,0),(42,2,'spargos@gmail.com','€1Õ','2014-03-09 22:01:33','bad-password',NULL,0),(43,2,'spargos@gmail.com','€1Õ','2014-03-09 22:01:44','success',NULL,1),(44,1,'wealthment@gmail.com','bš5Ò','2014-03-09 22:50:29','success',NULL,0),(45,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 22:56:06','success',NULL,0),(46,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 23:17:45','success',NULL,0),(47,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 23:20:25','success',NULL,0),(48,3,'ersh2121@yahoo.com','bš5Ò','2014-03-09 23:23:04','success',NULL,0),(49,3,'ersh2121@yahoo.com','bš5Ò','2014-03-10 00:07:06','success',NULL,0),(50,3,'ersh2121@yahoo.com','bš5Ò','2014-03-10 00:14:48','success',NULL,0),(51,1,'wealthment@gmail.com','€1Õ','2014-03-10 12:22:09','success',NULL,1),(52,3,'ersh2121@yahoo.com','bš5Ò','2014-03-10 22:48:04','success',NULL,0),(53,3,'ersh2121@yahoo.com','bš5Ò','2014-03-10 22:59:14','success',NULL,0),(54,3,'ersh2121@yahoo.com','bš5Ò','2014-03-11 01:14:50','success',NULL,0),(55,3,'ersh2121@yahoo.com','bš5Ò','2014-03-11 02:03:22','success',NULL,0),(56,3,'ersh2121@yahoo.com','bš5Ò','2014-03-11 02:35:27','success',NULL,0),(57,3,'ersh2121@yahoo.com','bš5Ò','2014-03-11 02:44:57','success',NULL,0),(58,3,'ersh2121@yahoo.com','bš5Ò','2014-03-11 02:57:51','success',NULL,0),(59,1,'wealthment@gmail.com','€1Õ','2014-03-11 12:25:05','success',NULL,1),(60,1,'wealthment@gmail.com','bš5Ò','2014-03-11 15:45:40','success',NULL,0),(61,1,'wealthment@gmail.com','bš5Ò','2014-03-11 15:46:14','success',NULL,0),(62,1,'wealthment@gmail.com','bš5Ò','2014-03-11 22:55:32','success',NULL,0),(63,1,'wealthment@gmail.com','bš5Ò','2014-03-12 00:12:48','success',NULL,0),(64,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 00:15:42','success',NULL,0),(65,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 02:38:30','success',NULL,1),(66,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 03:51:39','success',NULL,0),(67,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 04:25:32','success',NULL,0),(68,1,'wealthment@gmail.com','bš5Ò','2014-03-12 05:20:06','success',NULL,0),(69,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 05:22:26','success',NULL,0),(70,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 18:32:50','success',NULL,0),(71,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 18:48:24','success',NULL,0),(72,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 18:50:20','success',NULL,0),(73,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 19:04:11','success',NULL,0),(74,3,'ersh2121@yahoo.com','bš5Ò','2014-03-12 19:04:46','success',NULL,0),(75,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 00:41:01','success',NULL,0),(76,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 01:02:22','success',NULL,0),(77,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 01:02:50','success',NULL,0),(78,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 01:09:27','success',NULL,0),(79,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 02:08:32','success',NULL,0),(80,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 02:13:31','success',NULL,0),(81,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 02:52:46','success',NULL,0),(82,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 06:54:51','success',NULL,1),(83,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 11:33:25','success',NULL,0),(84,3,'ersh2121@yahoo.com','bš5Ò','2014-03-14 16:38:14','success',NULL,0),(85,3,'ersh2121@yahoo.com','bš5Ò','2014-03-15 19:32:45','success',NULL,0),(86,3,'ersh2121@yahoo.com','bš5Ò','2014-03-15 19:34:40','success',NULL,0),(87,3,'ersh2121@yahoo.com','bš5Ò','2014-03-15 19:45:08','success',NULL,0),(88,2,'spargos@gmail.com','€1Õ','2014-03-15 20:42:21','success',NULL,0),(89,1,'wealthment@gmail.com','€1Õ','2014-03-15 20:48:01','success',NULL,0),(90,3,'ersh2121@yahoo.com','bš5Ò','2014-03-15 21:02:35','success',NULL,0),(91,1,'wealthment@gmail.com','€1Õ','2014-03-15 23:30:52','success',NULL,1),(92,4,NULL,'€1Õ','2014-03-15 23:32:38','success','facebook',0),(93,4,NULL,'€1Õ','2014-03-15 23:38:33','success','facebook',0),(94,5,NULL,'bš5Ò','2014-03-16 01:09:24','success','facebook',0),(95,3,'ersh2121@yahoo.com','bš5Ò','2014-03-16 01:09:43','success',NULL,0),(96,5,NULL,'bš5Ò','2014-03-16 01:09:55','success','facebook',0),(97,1,'wealthment@gmail.com','€1Õ','2014-03-16 01:11:44','success',NULL,1),(98,5,NULL,'bš5Ò','2014-03-16 01:14:01','success','facebook',0),(99,5,NULL,'bš5Ò','2014-03-16 01:15:57','success','facebook',0),(100,2,'spargos@gmail.com','€1Õ','2014-03-16 01:25:17','bad-password',NULL,0),(101,2,'spargos@gmail.com','€1Õ','2014-03-16 01:25:22','success',NULL,1),(102,5,NULL,'bš5Ò','2014-03-16 02:39:14','success','facebook',0),(103,3,'ersh2121@yahoo.com','bš5Ò','2014-03-16 02:39:23','success',NULL,0),(104,5,NULL,'bš5Ò','2014-03-16 15:47:32','success','facebook',0),(105,5,NULL,'bš5Ò','2014-03-17 15:03:12','success','facebook',0),(106,2,'spargos@gmail.com','Œ÷\0c','2014-03-17 22:26:08','bad-password',NULL,0),(107,1,'wealthment@gmail.com','Œ÷\0c','2014-03-17 22:26:23','success',NULL,1),(108,5,NULL,'bš5Ò','2014-03-18 16:19:28','success','facebook',0),(109,3,'ersh2121@yahoo.com','bš5Ò','2014-03-18 20:04:29','success',NULL,0),(110,3,'ersh2121@yahoo.com','bš5Ò','2014-03-18 21:50:17','success',NULL,0),(111,5,NULL,'bš5Ò','2014-03-18 22:10:42','success','facebook',0),(112,5,NULL,'bš5Ò','2014-03-23 07:11:32','success','facebook',0),(113,2,'spargos@gmail.com','Bü3¼','2014-03-23 12:15:56','bad-password',NULL,0),(114,NULL,'jeffrey.lee@ptminconline.com','Bü3¼','2014-03-23 12:16:08','no-member',NULL,0),(115,NULL,'jeffrey.lee@ptminconline.com','Bü3¼','2014-03-23 12:16:12','no-member',NULL,0),(116,2,'spargos@gmail.com','Bü3¼','2014-03-23 12:16:25','success',NULL,1),(117,1,'wealthment@gmail.com','bš5Ò','2014-03-24 16:40:51','success',NULL,0),(118,1,'wealthment@gmail.com','bš5Ò','2014-03-24 16:44:20','success',NULL,0),(119,3,'ersh2121@yahoo.com','bš5Ò','2014-03-24 20:03:21','success',NULL,0),(120,3,'ersh2121@yahoo.com','bš5Ò','2014-03-24 20:03:56','success',NULL,0),(121,2,'spargos@gmail.com','Bü3¼','2014-03-24 22:13:42','bad-password',NULL,0),(122,2,'spargos@gmail.com','Bü3¼','2014-03-24 22:13:52','success',NULL,0),(123,1,'wealthment@gmail.com','Bü3¼','2014-03-24 22:14:30','success',NULL,0),(124,2,'spargos@gmail.com','Bü3¼','2014-03-24 22:19:36','bad-password',NULL,0),(125,1,'wealthment@gmail.com','Bü3¼','2014-03-24 22:20:25','success',NULL,0),(126,2,'spargos@gmail.com','Bü3¼','2014-03-24 22:21:36','bad-password',NULL,0),(127,1,'wealthment@gmail.com','Bü3¼','2014-03-24 22:21:51','success',NULL,1),(128,5,NULL,'bš5Ò','2014-03-25 20:07:48','success','facebook',0),(129,3,'ersh2121@yahoo.com','bš5Ò','2014-03-25 20:17:59','success',NULL,0),(130,3,'ersh2121@yahoo.com','bš5Ò','2014-03-25 20:57:18','success',NULL,1),(131,3,'ersh2121@yahoo.com','bš5Ò','2014-03-26 00:17:44','success',NULL,0),(132,1,'wealthment@gmail.com','bš5Ò','2014-03-26 00:40:49','success',NULL,0),(133,1,'wealthment@gmail.com','bš5Ò','2014-03-26 00:43:22','success',NULL,0),(134,3,'ersh2121@yahoo.com','bš5Ò','2014-03-26 00:45:24','success',NULL,0),(135,3,'ersh2121@yahoo.com','bš5Ò','2014-03-26 00:47:44','success',NULL,0),(136,1,'wealthment@gmail.com','bš5Ò','2014-03-26 00:48:30','success',NULL,0),(137,1,'wealthment@gmail.com','bš5Ò','2014-03-26 00:55:39','success',NULL,0),(138,1,'wealthment@gmail.com','bš5Ò','2014-03-26 00:56:10','success',NULL,0),(139,1,'wealthment@gmail.com','bš5Ò','2014-03-26 00:59:07','success',NULL,1),(140,1,'wealthment@gmail.com','bš5Ò','2014-03-26 14:26:25','success',NULL,0),(141,1,'wealthment@gmail.com','bš5Ò','2014-03-26 14:35:34','success',NULL,0),(142,5,NULL,'bš5Ò','2014-03-26 14:39:24','success','facebook',0),(143,1,'wealthment@gmail.com','bš5Ò','2014-03-26 14:39:36','success',NULL,0),(144,1,'wealthment@gmail.com','bš5Ò','2014-03-26 18:44:09','success',NULL,0),(145,1,'wealthment@gmail.com','bš5Ò','2014-03-26 18:53:47','success',NULL,0),(146,1,'wealthment@gmail.com','bš5Ò','2014-03-26 19:18:19','success',NULL,0),(147,1,'wealthment@gmail.com','bš5Ò','2014-03-26 23:30:31','success',NULL,0),(148,1,'wealthment@gmail.com','bš5Ò','2014-03-26 23:36:31','success',NULL,0),(149,1,'wealthment@gmail.com','bš5Ò','2014-03-26 23:37:27','success',NULL,0),(150,1,'wealthment@gmail.com','bš5Ò','2014-03-26 23:41:20','success',NULL,0),(151,1,'wealthment@gmail.com','bš5Ò','2014-03-26 23:49:18','success',NULL,0),(152,1,'wealthment@gmail.com','bš5Ò','2014-03-26 23:49:47','success',NULL,0),(153,1,'wealthment@gmail.com','bš5Ò','2014-03-27 00:01:04','success',NULL,0),(154,1,'wealthment@gmail.com','bš5Ò','2014-03-27 00:08:24','success',NULL,0),(155,1,'wealthment@gmail.com','bš5Ò','2014-03-27 00:13:46','success',NULL,0),(156,1,'wealthment@gmail.com','bš5Ò','2014-03-27 00:16:32','success',NULL,0),(157,3,'ersh2121@yahoo.com','bš5Ò','2014-03-27 13:26:42','success',NULL,0),(158,3,'ersh2121@yahoo.com','bš5Ò','2014-03-27 20:48:16','success',NULL,1),(159,1,'wealthment@gmail.com','€1Õ','2014-04-07 22:52:47','success',NULL,1),(160,3,'ersh2121@yahoo.com','bš5Ò','2014-04-09 12:11:36','success',NULL,1),(161,3,'ersh2121@yahoo.com','bš5Ò','2014-04-10 22:18:38','success',NULL,1),(162,1,'wealthment@gmail.com','bš5Ò','2014-04-15 12:03:48','success',NULL,1),(163,1,'wealthment@gmail.com','€1Õ','2014-05-01 02:01:02','success',NULL,1),(164,1,'wealthment@gmail.com','Œ÷Â,','2014-05-01 21:22:45','success',NULL,1),(165,1,'wealthment@gmail.com','LA/','2014-05-02 02:16:35','success',NULL,1);
/*!40000 ALTER TABLE `engine4_user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_membership`
--

DROP TABLE IF EXISTS `engine4_user_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `resource_approved` tinyint(1) NOT NULL DEFAULT '0',
  `user_approved` tinyint(1) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`resource_id`,`user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_membership`
--

LOCK TABLES `engine4_user_membership` WRITE;
/*!40000 ALTER TABLE `engine4_user_membership` DISABLE KEYS */;
INSERT INTO `engine4_user_membership` VALUES (1,3,1,1,1,NULL,NULL),(2,3,1,1,1,NULL,NULL),(3,1,1,1,1,NULL,NULL),(3,2,1,1,1,NULL,NULL);
/*!40000 ALTER TABLE `engine4_user_membership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_online`
--

DROP TABLE IF EXISTS `engine4_user_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_online` (
  `ip` varbinary(16) NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `active` datetime NOT NULL,
  PRIMARY KEY (`ip`,`user_id`),
  KEY `LOOKUP` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_online`
--

LOCK TABLES `engine4_user_online` WRITE;
/*!40000 ALTER TABLE `engine4_user_online` DISABLE KEYS */;
INSERT INTO `engine4_user_online` VALUES ('bš5Ò',0,'2014-02-17 00:58:32'),('Œ÷À,',0,'2014-03-03 22:00:51'),('Ç½',0,'2014-03-04 00:13:23'),('€1Õ',0,'2014-03-06 12:45:58'),('ÆäÉ',0,'2014-03-06 13:04:13'),('ÆäØ',0,'2014-03-09 02:52:12'),('ÆäØ\Z',0,'2014-03-11 20:24:57'),('6à,o',0,'2014-03-16 01:22:25'),('­üJp',0,'2014-03-16 01:22:26'),('Œ÷\0c',0,'2014-03-17 22:26:22'),('¦‰Ò,',0,'2014-03-18 18:59:42'),('Bü3¼',0,'2014-03-23 12:16:25'),('2—^',0,'2014-03-25 20:11:59'),('­üJq',0,'2014-03-25 20:11:59'),('­üMr',0,'2014-03-25 20:12:07'),('­üMq',0,'2014-03-25 20:12:08'),('bš5Ò',3,'2014-03-27 21:48:38'),('¦‰Ò',0,'2014-03-31 15:40:48'),('¦‰Ð\Z',0,'2014-04-01 07:29:34'),('¦‰Ð',0,'2014-04-01 13:20:19'),('Ÿ™3',3,'2014-04-01 22:20:12'),('¦‰Ò#',0,'2014-04-02 01:48:21'),('¦‰Ò#',3,'2014-04-02 01:48:22'),('§Û\0Œ',3,'2014-04-03 23:56:08'),('¦‰Ò',0,'2014-04-04 18:11:35'),('¦‰Ò\Z',0,'2014-04-05 00:37:38'),('¦‰Ò\'',0,'2014-04-08 02:34:44'),('¦‰Ò+',0,'2014-04-12 21:45:41'),('bš5Ò',1,'2014-04-15 12:03:53'),('€1Õ',1,'2014-05-01 02:07:27'),('Œ÷Â,',0,'2014-05-01 21:22:45'),('Œ÷Â,',1,'2014-05-01 21:23:36'),('LA/',0,'2014-05-02 02:16:35'),('LA/',1,'2014-05-02 02:21:11');
/*!40000 ALTER TABLE `engine4_user_online` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_settings`
--

DROP TABLE IF EXISTS `engine4_user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_settings` (
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_settings`
--

LOCK TABLES `engine4_user_settings` WRITE;
/*!40000 ALTER TABLE `engine4_user_settings` DISABLE KEYS */;
INSERT INTO `engine4_user_settings` VALUES (5,'wall-linked-pages','');
/*!40000 ALTER TABLE `engine4_user_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_signup`
--

DROP TABLE IF EXISTS `engine4_user_signup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_signup` (
  `signup_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  `enable` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`signup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_signup`
--

LOCK TABLES `engine4_user_signup` WRITE;
/*!40000 ALTER TABLE `engine4_user_signup` DISABLE KEYS */;
INSERT INTO `engine4_user_signup` VALUES (1,'User_Plugin_Signup_Account',1,1),(2,'User_Plugin_Signup_Fields',2,1),(3,'User_Plugin_Signup_Photo',3,1),(4,'User_Plugin_Signup_Invite',4,1),(5,'Payment_Plugin_Signup_Subscription',0,0);
/*!40000 ALTER TABLE `engine4_user_signup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_twitter`
--

DROP TABLE IF EXISTS `engine4_user_twitter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_twitter` (
  `user_id` int(10) unsigned NOT NULL,
  `twitter_uid` bigint(20) unsigned NOT NULL,
  `twitter_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitter_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `twitter_uid` (`twitter_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_twitter`
--

LOCK TABLES `engine4_user_twitter` WRITE;
/*!40000 ALTER TABLE `engine4_user_twitter` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_twitter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_user_verify`
--

DROP TABLE IF EXISTS `engine4_user_verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_user_verify` (
  `user_id` int(11) unsigned NOT NULL,
  `code` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_user_verify`
--

LOCK TABLES `engine4_user_verify` WRITE;
/*!40000 ALTER TABLE `engine4_user_verify` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_user_verify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_users`
--

DROP TABLE IF EXISTS `engine4_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` text COLLATE utf8_unicode_ci,
  `status_date` datetime DEFAULT NULL,
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `salt` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'auto',
  `language` varchar(8) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'en_US',
  `timezone` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'America/Los_Angeles',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `show_profileviewers` tinyint(1) NOT NULL DEFAULT '1',
  `level_id` int(11) unsigned NOT NULL,
  `invites_used` int(11) unsigned NOT NULL DEFAULT '0',
  `extra_invites` int(11) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `creation_date` datetime NOT NULL,
  `creation_ip` varbinary(16) NOT NULL,
  `modified_date` datetime NOT NULL,
  `lastlogin_date` datetime DEFAULT NULL,
  `lastlogin_ip` varbinary(16) DEFAULT NULL,
  `update_date` int(11) DEFAULT NULL,
  `member_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `EMAIL` (`email`),
  UNIQUE KEY `USERNAME` (`username`),
  KEY `MEMBER_COUNT` (`member_count`),
  KEY `CREATION_DATE` (`creation_date`),
  KEY `search` (`search`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_users`
--

LOCK TABLES `engine4_users` WRITE;
/*!40000 ALTER TABLE `engine4_users` DISABLE KEYS */;
INSERT INTO `engine4_users` VALUES (1,'wealthment@gmail.com','wealthmentadmin','Wealthment Administrator',1,'creating a new #helloÂ  ','2014-03-26 14:46:55','530fac3740cfc0ade029902b0e5a1acc','4764653','auto','en_US','US/Pacific',1,1,1,1,0,1,1,1,'2014-02-16 04:08:20','411054549','2014-03-26 14:46:55','2014-05-02 02:16:35','LA/',NULL,1,4),(2,'spargos@gmail.com','jefflee','Jeffrey Lee',9,'Tesla is not doing so well. #tesla ','2014-03-16 01:42:55','add3f1dfba58340ab40eabffc4ebd76c','4862800','English','English','US/Eastern',1,1,4,1,0,1,1,1,'2014-03-04 16:30:54','Œ÷À,','2014-03-16 01:42:55','2014-03-24 22:13:52','Bü3¼',NULL,1,10),(3,'ersh2121@yahoo.com','ershadjamil','Ershad Jamil',13,'#test2 test ','2014-03-25 02:37:38','f860ab4c3541c531a3ba8b85b19146ec','8726206','English','English','US/Pacific',1,1,4,0,0,1,1,1,'2014-03-05 02:01:27','bš5Ò','2014-03-27 13:29:13','2014-04-10 22:18:38','bš5Ò',NULL,2,13),(4,'jeffrey_lee@harvard.edu','JeffreyLee','Jeffrey Lee',0,NULL,NULL,'','9184400','English','English','US/Eastern',1,1,4,0,0,1,1,1,'2014-03-15 23:32:27','€1Õ','2014-03-15 23:32:27','2014-03-15 23:38:33','€1Õ',NULL,0,1),(5,'ejamil@deloitte.com','ErshadJamil12','Ershad Jamil',46,':) test ','2014-03-25 20:11:58','','3263203','English','English','US/Pacific',1,1,4,0,0,1,1,1,'2014-03-16 01:08:37','bš5Ò','2014-03-25 20:11:58','2014-03-26 14:39:24','bš5Ò',NULL,0,1);
/*!40000 ALTER TABLE `engine4_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_fbpages`
--

DROP TABLE IF EXISTS `engine4_wall_fbpages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_fbpages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fbpage_id` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(200) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_fbpages`
--

LOCK TABLES `engine4_wall_fbpages` WRITE;
/*!40000 ALTER TABLE `engine4_wall_fbpages` DISABLE KEYS */;
INSERT INTO `engine4_wall_fbpages` VALUES (199,'23448089006',5,'Wealthment','CAAKKIN82IQUBAI2OfTj6jiPO3QAeYcCV0qKhFS6uLtTlAGUOfZAhS76u9S02pgSutkRDtrTDAasSceoUwqecmcMQc3A32onrgJJJDd5PdE0QuVKWZCZBlYB5P59BjMjEZA1O4uiAo5mbBIR9MY4F4rC1vxZBiBtwIxWqOw92vup8euffKLZAFR'),(200,'21662865839',5,'AnimalBump','CAAKKIN82IQUBAEZBm93vzZAI31XAxygY70Bfr9c9rE9bhW2RpGMCEvpxxmchTtn8pISbLDkcZCPAaG3F8cE8oxYlndVDbzZBtiLqjZC2xMNDvNlZC7EnYy0mrpvsSe5vHjA7wzNh4LpfZAHMPiclfApQf2mcuElyU3uDYL4gtrFXa8XlPdFtj9n');
/*!40000 ALTER TABLE `engine4_wall_fbpages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_listitems`
--

DROP TABLE IF EXISTS `engine4_wall_listitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_listitems` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `object_type` (`object_type`,`object_id`,`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_listitems`
--

LOCK TABLES `engine4_wall_listitems` WRITE;
/*!40000 ALTER TABLE `engine4_wall_listitems` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_wall_listitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_lists`
--

DROP TABLE IF EXISTS `engine4_wall_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_lists` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `label` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_lists`
--

LOCK TABLES `engine4_wall_lists` WRITE;
/*!40000 ALTER TABLE `engine4_wall_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_wall_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_mute`
--

DROP TABLE IF EXISTS `engine4_wall_mute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_mute` (
  `mute_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mute_id`),
  UNIQUE KEY `user_id` (`user_id`,`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_mute`
--

LOCK TABLES `engine4_wall_mute` WRITE;
/*!40000 ALTER TABLE `engine4_wall_mute` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_wall_mute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_privacy`
--

DROP TABLE IF EXISTS `engine4_wall_privacy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_privacy` (
  `action_id` int(11) NOT NULL,
  `privacy` varchar(30) NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_privacy`
--

LOCK TABLES `engine4_wall_privacy` WRITE;
/*!40000 ALTER TABLE `engine4_wall_privacy` DISABLE KEYS */;
INSERT INTO `engine4_wall_privacy` VALUES (3,'everyone'),(6,'everyone'),(7,'everyone'),(14,'everyone'),(15,'everyone'),(16,'everyone'),(17,'everyone'),(21,'everyone'),(22,'everyone'),(23,'everyone'),(24,'everyone'),(25,'everyone'),(26,'everyone'),(27,'everyone'),(28,'everyone'),(32,'everyone'),(33,'everyone'),(34,'everyone'),(37,'everyone'),(39,'everyone'),(40,'everyone'),(41,'everyone'),(42,'everyone'),(45,'everyone'),(46,'everyone'),(47,'everyone'),(48,'everyone'),(49,'everyone'),(50,'everyone'),(51,'everyone'),(52,'everyone'),(53,'everyone'),(54,'everyone'),(55,'everyone');
/*!40000 ALTER TABLE `engine4_wall_privacy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_smiles`
--

DROP TABLE IF EXISTS `engine4_wall_smiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_smiles` (
  `smile_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `file_src` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default` tinyint(1) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`smile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_smiles`
--

LOCK TABLES `engine4_wall_smiles` WRITE;
/*!40000 ALTER TABLE `engine4_wall_smiles` DISABLE KEYS */;
INSERT INTO `engine4_wall_smiles` VALUES (1,' :) ','Smile',NULL,'application/modules/Wall/externals/images/smiles/smile.png',1,1),(2,' :)) ','Big Smile',NULL,'application/modules/Wall/externals/images/smiles/smile-big.png',1,1),(3,' :D ','Grin',NULL,'application/modules/Wall/externals/images/smiles/grin.png',1,1),(4,' :laugh: ','Laugh',NULL,'application/modules/Wall/externals/images/smiles/laugh.png',1,1),(5,' :-( ','Frown',NULL,'application/modules/Wall/externals/images/smiles/frown.png',1,1),(6,' :-(( ','Big Frown',NULL,'application/modules/Wall/externals/images/smiles/frown-big.png',1,1),(7,' :( ','Cry',NULL,'application/modules/Wall/externals/images/smiles/crying.png',1,1),(8,' :| ','Neutral',NULL,'application/modules/Wall/externals/images/smiles/neutral.png',1,1),(9,' ;) ','Wink',NULL,'application/modules/Wall/externals/images/smiles/wink.png',1,1),(10,' :-* ','Kiss',NULL,'application/modules/Wall/externals/images/smiles/kiss.png',1,1),(11,' :P ','Razz',NULL,'application/modules/Wall/externals/images/smiles/razz.png',1,1),(12,' :chic: ','Chic',NULL,'application/modules/Wall/externals/images/smiles/chic.png',1,1),(13,' 8-) ','Cool',NULL,'application/modules/Wall/externals/images/smiles/cool.png',1,1),(14,' :-X ','Angry',NULL,'application/modules/Wall/externals/images/smiles/angry.png',1,1),(15,' :reallyangry: ','Really Angry',NULL,'application/modules/Wall/externals/images/smiles/really-angry.png',1,1),(16,' :-? ','Confused',NULL,'application/modules/Wall/externals/images/smiles/confused.png',1,1),(17,' ?:-) ','Question',NULL,'application/modules/Wall/externals/images/smiles/question.png',1,1),(18,' :-/ ','Thinking',NULL,'application/modules/Wall/externals/images/smiles/thinking.png',1,1),(19,' :pain: ','Pain',NULL,'application/modules/Wall/externals/images/smiles/pain.png',1,1),(20,' :shock: ','Shock',NULL,'application/modules/Wall/externals/images/smiles/shock.png',1,1),(21,' :yes: ','Yes',NULL,'application/modules/Wall/externals/images/smiles/thumbs-up.png',1,1),(22,' :no: ','No',NULL,'application/modules/Wall/externals/images/smiles/thumbs-down.png',1,1),(23,' :rotfl: ','LOL',NULL,'application/modules/Wall/externals/images/smiles/rotfl.png',1,1),(24,' :silly: ','Silly',NULL,'application/modules/Wall/externals/images/smiles/silly.png',1,1),(25,' :beauty: ','Beauty',NULL,'application/modules/Wall/externals/images/smiles/beauty.png',1,1),(26,' :lashes: ','Lashes',NULL,'application/modules/Wall/externals/images/smiles/lashes.png',1,1),(27,' :cute: ','Cute',NULL,'application/modules/Wall/externals/images/smiles/cute.png',1,1),(28,' :shy: ','Shy',NULL,'application/modules/Wall/externals/images/smiles/bashful.png',1,1),(29,' :blush: ','Blush',NULL,'application/modules/Wall/externals/images/smiles/blush.png',1,1),(30,' :kissed: ','Kissed',NULL,'application/modules/Wall/externals/images/smiles/kissed.png',1,1),(31,' :inlove: ','In Love',NULL,'application/modules/Wall/externals/images/smiles/in-love.png',1,1),(32,' :drool: ','Drool',NULL,'application/modules/Wall/externals/images/smiles/drool.png',1,1),(33,' :giggle: ','Giggle',NULL,'application/modules/Wall/externals/images/smiles/giggle.png',1,1),(34,' :snicker: ','Snicker',NULL,'application/modules/Wall/externals/images/smiles/snicker.png',1,1),(35,' :heh: ','Heh!',NULL,'application/modules/Wall/externals/images/smiles/curl-lip.png',1,1),(36,' :smirk: ','Smirk',NULL,'application/modules/Wall/externals/images/smiles/smirk.png',1,1),(37,' :wilt: ','Wilt',NULL,'application/modules/Wall/externals/images/smiles/wilt.png',1,1),(38,' :weep: ','Weep',NULL,'application/modules/Wall/externals/images/smiles/weep.png',1,1),(39,' :idk: ','IDK',NULL,'application/modules/Wall/externals/images/smiles/dont-know.png',1,1),(40,' :struggle: ','Struggle',NULL,'application/modules/Wall/externals/images/smiles/struggle.png',1,1),(41,' :sidefrown: ','Side Frown',NULL,'application/modules/Wall/externals/images/smiles/sidefrown.png',1,1),(42,' :dazed: ','Dazed',NULL,'application/modules/Wall/externals/images/smiles/dazed.png',1,1),(43,' :hypnotized: ','Hypnotized',NULL,'application/modules/Wall/externals/images/smiles/hypnotized.png',1,1),(44,' :sweat: ','Sweat',NULL,'application/modules/Wall/externals/images/smiles/sweat.png',1,1),(45,' :eek: ','Eek!',NULL,'application/modules/Wall/externals/images/smiles/bug-eyes.png',1,1),(46,' :roll: ','Roll Eyes',NULL,'application/modules/Wall/externals/images/smiles/eyeroll.png',1,1),(47,' :sarcasm: ','Sarcasm',NULL,'application/modules/Wall/externals/images/smiles/sarcastic.png',1,1),(48,' :disdain: ','Disdain',NULL,'application/modules/Wall/externals/images/smiles/disdain.png',1,1),(49,' :smug: ','Smug',NULL,'application/modules/Wall/externals/images/smiles/arrogant.png',1,1),(50,' :-$ ','Money Mouth',NULL,'application/modules/Wall/externals/images/smiles/moneymouth.png',1,1),(51,' :footmouth: ','Foot in Mouth',NULL,'application/modules/Wall/externals/images/smiles/foot-in-mouth.png',1,1),(52,' :shutmouth: ','Shut Mouth',NULL,'application/modules/Wall/externals/images/smiles/shut-mouth.png',1,1),(53,' :quiet: ','Quiet',NULL,'application/modules/Wall/externals/images/smiles/quiet.png',1,1),(54,' :shame: ','Shame',NULL,'application/modules/Wall/externals/images/smiles/shame.png',1,1),(55,' :beatup: ','Beat Up',NULL,'application/modules/Wall/externals/images/smiles/beat-up.png',1,1),(56,' :mean: ','Mean',NULL,'application/modules/Wall/externals/images/smiles/mean.png',1,1),(57,' :evilgrin: ','Evil Grin',NULL,'application/modules/Wall/externals/images/smiles/evil-grin.png',1,1),(58,' :teeth: ','Grit Teeth',NULL,'application/modules/Wall/externals/images/smiles/teeth.png',1,1),(59,' :shout: ','Shout',NULL,'application/modules/Wall/externals/images/smiles/shout.png',1,1),(60,' :pissedoff: ','Pissed Off',NULL,'application/modules/Wall/externals/images/smiles/pissed-off.png',1,1),(61,' :reallypissed: ','Really Pissed',NULL,'application/modules/Wall/externals/images/smiles/really-pissed.png',1,1),(62,' :razzmad: ','Mad Razz',NULL,'application/modules/Wall/externals/images/smiles/razz-mad.png',1,1),(63,' :X-P: ','Drunken Razz',NULL,'application/modules/Wall/externals/images/smiles/razz-drunk.png',1,1),(64,' :sick: ','Sick',NULL,'application/modules/Wall/externals/images/smiles/sick.png',1,1),(65,' :yawn: ','Yawn',NULL,'application/modules/Wall/externals/images/smiles/yawn.png',1,1),(66,' :ZZZ: ','Sleepy',NULL,'application/modules/Wall/externals/images/smiles/sleepy.png',1,1),(67,' :dance: ','Dance',NULL,'application/modules/Wall/externals/images/smiles/dance.png',1,1),(68,' :clap: ','Clap',NULL,'application/modules/Wall/externals/images/smiles/clap.png',1,1),(69,' :jump: ','Jump',NULL,'application/modules/Wall/externals/images/smiles/jump.png',1,1),(70,' :handshake: ','Handshake',NULL,'application/modules/Wall/externals/images/smiles/handshake.png',1,1),(71,' :highfive: ','High Five',NULL,'application/modules/Wall/externals/images/smiles/highfive.png',1,1),(72,' :hugleft: ','Hug Left',NULL,'application/modules/Wall/externals/images/smiles/hug-left.png',1,1),(73,' :hugright: ','Hug Right',NULL,'application/modules/Wall/externals/images/smiles/hug-right.png',1,1),(74,' :kissblow: ','Kiss Blow',NULL,'application/modules/Wall/externals/images/smiles/kiss-blow.png',1,1),(75,' :kissing: ','Kissing',NULL,'application/modules/Wall/externals/images/smiles/kissing.png',1,1),(76,' :bye: ','Bye',NULL,'application/modules/Wall/externals/images/smiles/bye.png',1,1),(77,' :goaway: ','Go Away',NULL,'application/modules/Wall/externals/images/smiles/go-away.png',1,1),(78,' :callme: ','Call Me',NULL,'application/modules/Wall/externals/images/smiles/call-me.png',1,1),(79,' :onthephone: ','On the Phone',NULL,'application/modules/Wall/externals/images/smiles/on-the-phone.png',1,1),(80,' :secret: ','Secret',NULL,'application/modules/Wall/externals/images/smiles/secret.png',1,1),(81,' :meeting: ','Meeting',NULL,'application/modules/Wall/externals/images/smiles/meeting.png',1,1),(82,' :waving: ','Waving',NULL,'application/modules/Wall/externals/images/smiles/waving.png',1,1),(83,' :stop: ','Stop',NULL,'application/modules/Wall/externals/images/smiles/stop.png',1,1),(84,' :timeout: ','Time Out',NULL,'application/modules/Wall/externals/images/smiles/time-out.png',1,1),(85,' :talktothehand: ','Talk to the Hand',NULL,'application/modules/Wall/externals/images/smiles/talktohand.png',1,1),(86,' :loser: ','Loser',NULL,'application/modules/Wall/externals/images/smiles/loser.png',1,1),(87,' :lying: ','Lying',NULL,'application/modules/Wall/externals/images/smiles/lying.png',1,1),(88,' :doh: ','DOH!',NULL,'application/modules/Wall/externals/images/smiles/doh.png',1,1),(89,' :fingersxd: ','Fingers Crossed',NULL,'application/modules/Wall/externals/images/smiles/fingers-xd.png',1,1),(90,' :waiting: ','Waiting',NULL,'application/modules/Wall/externals/images/smiles/waiting.png',1,1),(91,' :suspense: ','Suspense',NULL,'application/modules/Wall/externals/images/smiles/nailbiting.png',1,1),(92,' :tremble: ','Tremble',NULL,'application/modules/Wall/externals/images/smiles/tremble.png',1,1),(93,' :pray: ','Pray',NULL,'application/modules/Wall/externals/images/smiles/pray.png',1,1),(94,' :worship: ','Worship',NULL,'application/modules/Wall/externals/images/smiles/worship.png',1,1),(95,' :starving: ','Starving',NULL,'application/modules/Wall/externals/images/smiles/starving.png',1,1),(96,' :eat: ','Eat',NULL,'application/modules/Wall/externals/images/smiles/eat.png',1,1),(97,' :victory: ','Victory',NULL,'application/modules/Wall/externals/images/smiles/victory.png',1,1),(98,' :curse: ','Curse',NULL,'application/modules/Wall/externals/images/smiles/curse.png',1,1),(99,' :alien: ','Alien',NULL,'application/modules/Wall/externals/images/smiles/alien.png',1,1),(100,' O:-) ','Angel',NULL,'application/modules/Wall/externals/images/smiles/angel.png',1,1),(101,' :clown: ','Clown',NULL,'application/modules/Wall/externals/images/smiles/clown.png',1,1),(102,' :cowboy: ','Cowboy',NULL,'application/modules/Wall/externals/images/smiles/cowboy.png',1,1),(103,' :cyclops: ','Cyclops',NULL,'application/modules/Wall/externals/images/smiles/cyclops.png',1,1),(104,' :devil: ','Devil',NULL,'application/modules/Wall/externals/images/smiles/devil.png',1,1),(105,' :doctor: ','Doctor',NULL,'application/modules/Wall/externals/images/smiles/doctor.png',1,1),(106,' :fighterf: ','Female Fighter',NULL,'application/modules/Wall/externals/images/smiles/fighter-f.png',1,1),(107,' :fighterm: ','Male Fighter',NULL,'application/modules/Wall/externals/images/smiles/fighter-m.png',1,1),(108,' :mohawk: ','Mohawk',NULL,'application/modules/Wall/externals/images/smiles/mohawk.png',1,1),(109,' :music: ','Music',NULL,'application/modules/Wall/externals/images/smiles/music.png',1,1),(110,' :nerd: ','Nerd',NULL,'application/modules/Wall/externals/images/smiles/nerd.png',1,1),(111,' :party: ','Party',NULL,'application/modules/Wall/externals/images/smiles/party.png',1,1),(112,' :pirate: ','Pirate',NULL,'application/modules/Wall/externals/images/smiles/pirate.png',1,1),(113,' :skywalker: ','Skywalker',NULL,'application/modules/Wall/externals/images/smiles/skywalker.png',1,1),(114,' :snowman: ','Snowman',NULL,'application/modules/Wall/externals/images/smiles/snowman.png',1,1),(115,' :soldier: ','Soldier',NULL,'application/modules/Wall/externals/images/smiles/soldier.png',1,1),(116,' :vampire: ','Vampire',NULL,'application/modules/Wall/externals/images/smiles/vampire.png',1,1),(117,' :zombiekiller: ','Zombie Killer',NULL,'application/modules/Wall/externals/images/smiles/zombie-killer.png',1,1),(118,' :ghost: ','Ghost',NULL,'application/modules/Wall/externals/images/smiles/ghost.png',1,1),(119,' :skeleton: ','Skeleton',NULL,'application/modules/Wall/externals/images/smiles/skeleton.png',1,1),(120,' :bunny: ','Bunny',NULL,'application/modules/Wall/externals/images/smiles/bunny.png',1,1),(121,' :cat: ','Cat',NULL,'application/modules/Wall/externals/images/smiles/cat.png',1,1),(122,' :cat2: ','Cat 2',NULL,'application/modules/Wall/externals/images/smiles/cat2.png',1,1),(123,' :chick: ','Chick',NULL,'application/modules/Wall/externals/images/smiles/chick.png',1,1),(124,' :chicken: ','Chicken',NULL,'application/modules/Wall/externals/images/smiles/chicken.png',1,1),(125,' :chicken2: ','Chicken 2',NULL,'application/modules/Wall/externals/images/smiles/chicken2.png',1,1),(126,' :cow: ','Cow',NULL,'application/modules/Wall/externals/images/smiles/cow.png',1,1),(127,' :cow2: ','Cow 2',NULL,'application/modules/Wall/externals/images/smiles/cow2.png',1,1),(128,' :dog: ','Dog',NULL,'application/modules/Wall/externals/images/smiles/dog.png',1,1),(129,' :dog2: ','Dog 2',NULL,'application/modules/Wall/externals/images/smiles/dog2.png',1,1),(130,' :duck: ','Duck',NULL,'application/modules/Wall/externals/images/smiles/duck.png',1,1),(131,' :goat: ','Goat',NULL,'application/modules/Wall/externals/images/smiles/goat.png',1,1),(132,' :hippo: ','Hippo',NULL,'application/modules/Wall/externals/images/smiles/hippo.png',1,1),(133,' :koala: ','Koala',NULL,'application/modules/Wall/externals/images/smiles/koala.png',1,1),(134,' :lion: ','Lion',NULL,'application/modules/Wall/externals/images/smiles/lion.png',1,1),(135,' :monkey: ','Monkey',NULL,'application/modules/Wall/externals/images/smiles/monkey.png',1,1),(136,' :monkey2: ','Monkey 2',NULL,'application/modules/Wall/externals/images/smiles/monkey2.png',1,1),(137,' :mouse: ','Mouse',NULL,'application/modules/Wall/externals/images/smiles/mouse.png',1,1),(138,' :panda: ','Panda',NULL,'application/modules/Wall/externals/images/smiles/panda.png',1,1),(139,' :pig: ','Pig',NULL,'application/modules/Wall/externals/images/smiles/pig.png',1,1),(140,' :pig2: ','Pig 2',NULL,'application/modules/Wall/externals/images/smiles/pig2.png',1,1),(141,' :sheep: ','Sheep',NULL,'application/modules/Wall/externals/images/smiles/sheep.png',1,1),(142,' :sheep2: ','Sheep 2',NULL,'application/modules/Wall/externals/images/smiles/sheep2.png',1,1),(143,' :reindeer: ','Reindeer',NULL,'application/modules/Wall/externals/images/smiles/reindeer.png',1,1),(144,' :snail: ','Snail',NULL,'application/modules/Wall/externals/images/smiles/snail.png',1,1),(145,' :tiger: ','Tiger',NULL,'application/modules/Wall/externals/images/smiles/tiger.png',1,1),(146,' :turtle: ','Turtle',NULL,'application/modules/Wall/externals/images/smiles/turtle.png',1,1),(147,' :beer: ','Beer',NULL,'application/modules/Wall/externals/images/smiles/beer.png',1,1),(148,' :drink: ','Drink',NULL,'application/modules/Wall/externals/images/smiles/drink.png',1,1),(149,' :liquor: ','Liquor',NULL,'application/modules/Wall/externals/images/smiles/liquor.png',1,1),(150,' :coffee: ','Coffee',NULL,'application/modules/Wall/externals/images/smiles/coffee.png',1,1),(151,' :cake: ','Cake',NULL,'application/modules/Wall/externals/images/smiles/cake.png',1,1),(152,' :pizza: ','Pizza',NULL,'application/modules/Wall/externals/images/smiles/pizza.png',1,1),(153,' :watermelon: ','Watermelon',NULL,'application/modules/Wall/externals/images/smiles/watermelon.png',1,1),(154,' :bowl: ','Bowl',NULL,'application/modules/Wall/externals/images/smiles/bowl.png',1,1),(155,' :plate: ','Plate',NULL,'application/modules/Wall/externals/images/smiles/plate.png',1,1),(156,' :can: ','Can',NULL,'application/modules/Wall/externals/images/smiles/can.png',1,1),(157,' :female: ','Female',NULL,'application/modules/Wall/externals/images/smiles/female.png',1,1),(158,' :male: ','Male',NULL,'application/modules/Wall/externals/images/smiles/male.png',1,1),(159,' :heart: ','Heart',NULL,'application/modules/Wall/externals/images/smiles/heart.png',1,1),(160,' :brokenheart: ','Broken Heart',NULL,'application/modules/Wall/externals/images/smiles/heart-broken.png',1,1),(161,' :rose: ','Rose',NULL,'application/modules/Wall/externals/images/smiles/rose.png',1,1),(162,' :deadrose: ','Dead Rose',NULL,'application/modules/Wall/externals/images/smiles/rose-dead.png',1,1),(163,' :peace: ','Peace',NULL,'application/modules/Wall/externals/images/smiles/peace.png',1,1),(165,' :flagus: ','US Flag',NULL,'application/modules/Wall/externals/images/smiles/flag-us.png',1,1),(166,' :moon: ','Moon',NULL,'application/modules/Wall/externals/images/smiles/moon.png',1,1),(167,' :star: ','Star',NULL,'application/modules/Wall/externals/images/smiles/star.png',1,1),(168,' :sun: ','Sun',NULL,'application/modules/Wall/externals/images/smiles/sun.png',1,1),(169,' :cloudy: ','Cloudy',NULL,'application/modules/Wall/externals/images/smiles/cloudy.png',1,1),(170,' :rain: ','Rain',NULL,'application/modules/Wall/externals/images/smiles/rain.png',1,1),(171,' :thunder: ','Thunder',NULL,'application/modules/Wall/externals/images/smiles/thunder.png',1,1),(172,' :umbrella: ','Umbrella',NULL,'application/modules/Wall/externals/images/smiles/umbrella.png',1,1),(173,' :rainbow: ','Rainbow',NULL,'application/modules/Wall/externals/images/smiles/rainbow.png',1,1),(174,' :musicnote: ','Music Note',NULL,'application/modules/Wall/externals/images/smiles/music-note.png',1,1),(175,' :airplane: ','Airplane',NULL,'application/modules/Wall/externals/images/smiles/airplane.png',1,1),(176,' :car: ','Car',NULL,'application/modules/Wall/externals/images/smiles/car.png',1,1),(177,' :island: ','Island',NULL,'application/modules/Wall/externals/images/smiles/island.png',1,1),(178,' :announce: ','Announce',NULL,'application/modules/Wall/externals/images/smiles/announce.png',1,1),(179,' :brb: ','brb',NULL,'application/modules/Wall/externals/images/smiles/brb.png',1,1),(180,' :mail: ','Mail',NULL,'application/modules/Wall/externals/images/smiles/mail.png',1,1),(181,' :cell: ','Cell',NULL,'application/modules/Wall/externals/images/smiles/mobile.png',1,1),(182,' :phone: ','Phone',NULL,'application/modules/Wall/externals/images/smiles/phone.png',1,1),(183,' :camera: ','Camera',NULL,'application/modules/Wall/externals/images/smiles/camera.png',1,1),(184,' :film: ','Film',NULL,'application/modules/Wall/externals/images/smiles/film.png',1,1),(185,' :tv: ','TV',NULL,'application/modules/Wall/externals/images/smiles/tv.png',1,1),(186,' :clock: ','Clock',NULL,'application/modules/Wall/externals/images/smiles/clock.png',1,1),(187,' :lamp: ','Lamp',NULL,'application/modules/Wall/externals/images/smiles/lamp.png',1,1),(188,' :search: ','Search',NULL,'application/modules/Wall/externals/images/smiles/search.png',1,1),(189,' :coins: ','Coins',NULL,'application/modules/Wall/externals/images/smiles/coins.png',1,1),(190,' :computer: ','Computer',NULL,'application/modules/Wall/externals/images/smiles/computer.png',1,1),(191,' :console: ','Console',NULL,'application/modules/Wall/externals/images/smiles/console.png',1,1),(192,' :present: ','Present',NULL,'application/modules/Wall/externals/images/smiles/present.png',1,1),(193,' :soccer: ','Soccer',NULL,'application/modules/Wall/externals/images/smiles/soccerball.png',1,1),(194,' :clover: ','Clover',NULL,'application/modules/Wall/externals/images/smiles/clover.png',1,1),(195,' :pumpkin: ','Pumpkin',NULL,'application/modules/Wall/externals/images/smiles/pumpkin.png',1,1),(196,' :bomb: ','Bomb',NULL,'application/modules/Wall/externals/images/smiles/bomb.png',1,1),(197,' :hammer: ','Hammer',NULL,'application/modules/Wall/externals/images/smiles/hammer.png',1,1),(198,' :knife: ','Knife',NULL,'application/modules/Wall/externals/images/smiles/knife.png',1,1),(199,' :handcuffs: ','Handcuffs',NULL,'application/modules/Wall/externals/images/smiles/handcuffs.png',1,1),(200,' :pill: ','Pill',NULL,'application/modules/Wall/externals/images/smiles/pill.png',1,1),(201,' :poop: ','Poop',NULL,'application/modules/Wall/externals/images/smiles/poop.png',1,1),(202,' :cigarette: ','Cigarette',NULL,'application/modules/Wall/externals/images/smiles/cigarette.png',1,1);
/*!40000 ALTER TABLE `engine4_wall_smiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_tags`
--

DROP TABLE IF EXISTS `engine4_wall_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `object_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `is_people` tinyint(1) NOT NULL DEFAULT '0',
  `value` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_tags`
--

LOCK TABLES `engine4_wall_tags` WRITE;
/*!40000 ALTER TABLE `engine4_wall_tags` DISABLE KEYS */;
INSERT INTO `engine4_wall_tags` VALUES (118,17,3,'user',1,1,'');
/*!40000 ALTER TABLE `engine4_wall_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_tokens`
--

DROP TABLE IF EXISTS `engine4_wall_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `object_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `object_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `oauth_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `oauth_token_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `user_id` (`user_id`,`object_id`,`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_tokens`
--

LOCK TABLES `engine4_wall_tokens` WRITE;
/*!40000 ALTER TABLE `engine4_wall_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `engine4_wall_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_wall_usersettings`
--

DROP TABLE IF EXISTS `engine4_wall_usersettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_wall_usersettings` (
  `user_id` int(11) NOT NULL,
  `share_facebook_enabled` tinyint(1) NOT NULL,
  `share_twitter_enabled` tinyint(1) NOT NULL,
  `share_linkedin_enabled` tinyint(1) DEFAULT '0',
  `mode` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `list_id` int(11) NOT NULL,
  `privacy_user` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `privacy_page` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_wall_usersettings`
--

LOCK TABLES `engine4_wall_usersettings` WRITE;
/*!40000 ALTER TABLE `engine4_wall_usersettings` DISABLE KEYS */;
INSERT INTO `engine4_wall_usersettings` VALUES (1,0,0,0,'recent','',0,'everyone',''),(2,0,0,0,'recent','',0,'everyone',''),(3,0,0,0,'recent','',0,'everyone',''),(4,0,0,0,'recent','',0,'',''),(5,1,0,0,'recent','photo',0,'everyone','');
/*!40000 ALTER TABLE `engine4_wall_usersettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_welcome_effects`
--

DROP TABLE IF EXISTS `engine4_welcome_effects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_welcome_effects` (
  `effect_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`effect_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_welcome_effects`
--

LOCK TABLES `engine4_welcome_effects` WRITE;
/*!40000 ALTER TABLE `engine4_welcome_effects` DISABLE KEYS */;
INSERT INTO `engine4_welcome_effects` VALUES (1,'tabs','Tabs'),(2,'slider','Slider'),(3,'popup','Accordeon'),(4,'curtain','Curtain'),(5,'carousel','Carousel'),(6,'kenburns','KenBurns');
/*!40000 ALTER TABLE `engine4_welcome_effects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_welcome_settings`
--

DROP TABLE IF EXISTS `engine4_welcome_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_welcome_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `effect` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8_unicode_ci,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_welcome_settings`
--

LOCK TABLES `engine4_welcome_settings` WRITE;
/*!40000 ALTER TABLE `engine4_welcome_settings` DISABLE KEYS */;
INSERT INTO `engine4_welcome_settings` VALUES (1,'effect','wave','curtain','select','a:3:{s:4:\"wave\";s:4:\"Wave\";s:6:\"zipper\";s:6:\"Zipper\";s:7:\"curtain\";s:7:\"Curtain\";}','Type',''),(2,'strips','20','curtain','text','','Strips Count',''),(3,'titleOpacity','0.6','curtain','text','','Title Opacity(0.0-1.0)',''),(4,'position','curtain','curtain','select','a:4:{s:9:\"alternate\";s:9:\"Alternate\";s:3:\"top\";s:3:\"Top\";s:6:\"bottom\";s:6:\"Bottom\";s:7:\"curtain\";s:7:\"Curtain\";}','Position',''),(5,'direction','fountainAlternate','curtain','select','a:6:{s:17:\"fountainAlternate\";s:18:\"Fountain Alternate\";s:4:\"left\";s:4:\"Left\";s:5:\"right\";s:5:\"Right\";s:9:\"alternate\";s:9:\"Alternate\";s:6:\"random\";s:6:\"Random\";s:8:\"fountain\";s:8:\"Fountain\";}','Direction',''),(6,'defaultIndex','1','popup','text','','Default Index',''),(7,'expandMode','mouseover','popup','select','a:3:{s:9:\"mouseover\";s:13:\"On mouse over\";s:5:\"click\";s:14:\"On mouse click\";s:5:\"false\";s:13:\"Do not expand\";}','Expand Mode',''),(8,'pinMode','click','popup','select','a:3:{s:9:\"mouseover\";s:13:\"On mouse over\";s:5:\"click\";s:14:\"On mouse click\";s:5:\"false\";s:18:\"Do not stay opened\";}','Pin Mode',''),(9,'pause','3000','carousel','text','','Pause(ms)',''),(10,'speed','1000','carousel','text','','Speed(ms)',''),(11,'delay','5000','curtain','text','','Delay(ms)',''),(12,'interval','5000','tabs','text',NULL,'Pause(ms)',NULL),(13,'duration','2000','kenburns','text',NULL,'Duration(ms)',NULL);
/*!40000 ALTER TABLE `engine4_welcome_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_welcome_slideshows`
--

DROP TABLE IF EXISTS `engine4_welcome_slideshows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_welcome_slideshows` (
  `slideshow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `effect` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`slideshow_id`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_welcome_slideshows`
--

LOCK TABLES `engine4_welcome_slideshows` WRITE;
/*!40000 ALTER TABLE `engine4_welcome_slideshows` DISABLE KEYS */;
INSERT INTO `engine4_welcome_slideshows` VALUES (2,'Stock Insights','tabs',1100,158);
/*!40000 ALTER TABLE `engine4_welcome_slideshows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_welcome_slideshowsettings`
--

DROP TABLE IF EXISTS `engine4_welcome_slideshowsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_welcome_slideshowsettings` (
  `slideshowsetting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slideshow_id` int(11) unsigned NOT NULL,
  `setting_id` int(11) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`slideshowsetting_id`),
  KEY `slideshow_id` (`slideshow_id`,`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_welcome_slideshowsettings`
--

LOCK TABLES `engine4_welcome_slideshowsettings` WRITE;
/*!40000 ALTER TABLE `engine4_welcome_slideshowsettings` DISABLE KEYS */;
INSERT INTO `engine4_welcome_slideshowsettings` VALUES (1,2,9,'3000'),(2,2,10,'1000'),(3,2,13,'2000'),(4,2,12,'5000');
/*!40000 ALTER TABLE `engine4_welcome_slideshowsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engine4_welcome_steps`
--

DROP TABLE IF EXISTS `engine4_welcome_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engine4_welcome_steps` (
  `step_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slideshow_id` int(10) unsigned NOT NULL DEFAULT '1',
  `photo_id` int(11) NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`step_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engine4_welcome_steps`
--

LOCK TABLES `engine4_welcome_steps` WRITE;
/*!40000 ALTER TABLE `engine4_welcome_steps` DISABLE KEYS */;
INSERT INTO `engine4_welcome_steps` VALUES (1,0,22,'Starter Maturity Level','<p>Wealthment can help you ask the right questions so you can learn the best techniques to pick stocks.</p>\r\n<p>Based on each Wealthuser&rsquo;sexperience, you can post questions and thoughts on any question around stocks.</p>\r\n<p>Go ahead and give it a try, a few example questions are here&hellip; but what question do you have?</p>\r\n<p>&nbsp;</p>','','2014-03-09 01:54:57'),(2,0,24,'Dabbler Maturity Level','','','2014-03-09 01:55:35'),(3,0,26,'Player Maturity Level','','','2014-03-09 01:56:00'),(4,2,32,'','','http://www.wealthment.com/wealthment/index.php/pages/stockinsights','2014-03-10 21:53:07'),(5,2,34,'test','','http://www.wealthment.com/wealthment/index.php/pages/stockinsightsstarter','2014-03-10 22:01:36');
/*!40000 ALTER TABLE `engine4_welcome_steps` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-22 13:45:15
