<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: activity-content.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
return array(
  array(
    'title' => 'Activity Feed',
    'description' => 'Displays the activity feed.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.activity-feed',
    'defaultParams' => array(
      'title' => 'What\'s New',
			'limit' => 15
    ),
    //'special' => 1,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'limit',
          array(
            'label' => 'Action count per page',
            'description' => 'Limit to display count of activities',
            'default' => 10,
            'multiOptions' => array(
              10 => 10,
              15 => 15,
              20 => 20,
              25 => 25,
              30 => 30,
            )
          )
        ),
      )
    ),
  )
) ?>