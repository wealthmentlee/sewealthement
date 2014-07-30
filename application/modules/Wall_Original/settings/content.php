<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



$data = array(
  array(
    'title' => 'Wall Feed',
    'description' => 'Displays the activity feed.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.feed',
    'defaultParams' => array(
      'title' => 'What\'s New',
    ),
  ),
  array(
    'title' => 'Welcome: Headline',
    'description' => 'Displays a headline in Welcome Tab. Please put this widget on Wall Welcome page.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.welcome',
    'defaultParams' => array(
      'title' => 'WALL_WELCOME_WELCOME',
    ),
  ),
  array(
    'title' => 'Welcome: Text',
    'description' => 'Displays a greeting text in Welcome Tab. Please put this widget on Wall Welcome page.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.new-wall',
    'defaultParams' => array(
      'title' => 'WALL_WELCOME_NEWWALL',
    ),
  ),
  array(
    'title' => 'Welcome: Upload Profile Photo',
    'description' => 'Displays a form to upload profile photo if the member does not have profile photo. Please put this widget on Wall Welcome page.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.upload-photo',
    'defaultParams' => array(
      'title' => 'WALL_WELCOME_UPLOAD_PHOTO',
    ),
  ),
  array(
    'title' => 'Welcome: People You May Know',
    'description' => 'Suggests friends based on mutual friendship. Please put this widget on Wall Welcome page.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.people-know',
    'defaultParams' => array(
      'title' => 'WALL_WELCOME_PEOPLE_KNOW',
    ),
  ),

);

if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hegift')){

  $data[] = array(
    'title' => 'Welcome: Actual Gifts',
    'description' => 'Displays gifts which are actual to this date, like Christmas gifts which are displayed on Christmas. Please put this widget on Wall Welcome page.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.gift-actual',
    'defaultParams' => array(
      'title' => 'WALL_WELCOME_GIFTACTUAL',
    ),
  );

}

if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')){

  $data[] = array(
    'title' => 'Welcome: Most Liked Items',
    'description' => 'Displays most liked(popular) things - events, photos, videos, etc. Please put this widget on Wall Welcome page.',
    'category' => 'Wall',
    'type' => 'widget',
    'name' => 'wall.most-liked',
    'defaultParams' => array(
      'title' => 'WALL_WELCOME_LIKES',
    ),
  );

}

return $data; ?>