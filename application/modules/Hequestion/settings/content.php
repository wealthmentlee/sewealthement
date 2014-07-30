<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


return array(

  array(
    'title' => 'Asked By',
    'description' => 'Displays the owner and options of question.',
    'category' => 'Questions',
    'type' => 'widget',
    'name' => 'hequestion.asked',
    'requirements' => array(
      'subject' => 'hequestion',
    ),
  ),

  array(
    'title' => 'Friends\'s Questions',
    'description' => 'Displays friend\'s questions.',
    'category' => 'Questions',
    'type' => 'widget',
    'name' => 'hequestion.friend-questions',
    'defaultParams' => array(
      'title' => 'HEQUESTION_FRIEND_QUESTIONS'
    ),
    'requirements' => array(
    ),
  ),

  array(
    'title' => 'Popular Questions',
    'description' => 'Displays popular questions.',
    'category' => 'Questions',
    'type' => 'widget',
    'name' => 'hequestion.popular-questions',
    'defaultParams' => array(
      'title' => 'HEQUESTION_POPULAR_QUESTIONS'
    ),
    'requirements' => array(
    ),
  ),

  array(
    'title' => 'Recent Answers',
    'description' => 'Displays recent answers.',
    'category' => 'Questions',
    'type' => 'widget',
    'name' => 'hequestion.recent-answers',
    'defaultParams' => array(
      'title' => 'HEQUESTION_RECENT_ANSWERS'
    ),
    'requirements' => array(
    ),
  ),


  array(
    'title' => 'Profile Questions',
    'description' => 'Displays all of the questions by user, event, group, page and etc.',
    'category' => 'Questions',
    'type' => 'widget',
    'name' => 'hequestion.profile-questions',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'HEQUESTION_PROFILE_QUESTIONS',
      'titleCount' => true
    ),
    'requirements' => array(
      'subject' => 'subject',
    ),
  ),


  array(
    'title' => 'Browse Menu',
    'description' => 'Displays navigation menu on browse page.',
    'category' => 'Questions',
    'type' => 'widget',
    'name' => 'hequestion.browse-menu',
    'defaultParams' => array(
    ),
    'requirements' => array(
    ),
  )





);