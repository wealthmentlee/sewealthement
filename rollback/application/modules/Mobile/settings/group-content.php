<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: group-content.php 2011-02-14 06:58:57 mirlan $
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
    'title' => 'Profile Groups',
    'description' => 'Displays a member\'s groups on their profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-groups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Groups',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Group Profile Info',
    'description' => 'Displays a group\'s info (creation date, member count, leader, officers, etc) on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-info',
  ),
  array(
    'title' => 'Group Profile Members',
    'description' => 'Displays a group\'s members on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-members',
    'isPaginated' => true,
  ),
  array(
    'title' => 'Group Profile Options',
    'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a group on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-options',
  ),
  array(
    'title' => 'Group Profile Photo',
    'description' => 'Displays a group\'s photo on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-photo',
  ),
  array(
    'title' => 'Group Profile Photos',
    'description' => 'Displays a group\'s photos on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-photos',
    'isPaginated' => true,
  ),
  array(
    'title' => 'Group Profile Status',
    'description' => 'Displays a group\'s title on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-status',
  ),
  array(
    'title'=> 'Group Profile Events',
    'description' => 'Displays a group\'s events on its profile',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'mobile.group-profile-events',
    'isPaginated' => true,
  ),
	array(
		'title' => 'Group Profile Widgets',
		'description' => 'Displays Group Profile Widgets in separated columns.',
		'category' => 'Group',
		'type' => 'widget',
		'name' => 'mobile.group-profile-widgets',
		'defaultParams' => array(
      'left' => array('mobile.group-profile-photo'),
			'right' => array('mobile.like-status', 'mobile.group-profile-options'),
    ),
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
          'MultiCheckbox',
          'left',
					array(
						'Label'=>'Left Column',
						'multiOptions' => array(
							'mobile.group-profile-photo' => 'Group Profile Photo',
              'mobile.like-status' => 'Profile Like Status',
              'mobile.group-profile-status' => 'Group Profile Status',
              'mobile.group-profile-info' => 'Group Profile Info',
              'mobile.group-profile-options' => 'Group Profile Options',
            )
					)
        ),
				array(
          'MultiCheckbox',
          'right',
					array(
						'Label'=>'Right Column',
						'multiOptions' => array(
							'mobile.group-profile-photo' => 'Group Profile Photo',
              'mobile.like-status' => 'Profile Like Status',
							'mobile.group-profile-status' => 'Group Profile Status',
							'mobile.group-profile-info' => 'Group Profile Info',
							'mobile.group-profile-options' => 'Group Profile Options',
            )
					)
        ),
      )
    ),
	),
) ?>