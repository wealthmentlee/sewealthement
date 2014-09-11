<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: user-content.php 2011-02-14 06:58:57 mirlan $
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
    'title' => 'User Photo',
    'description' => 'Displays the logged-in member\'s photo.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-home-photo',
  ),
  array(
    'title' => 'Online Users',
    'description' => 'Displays a list of online members.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-list-online',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => '%d Members Online',
    ),
  ),
  array(
    'title' => 'Popular Members',
    'description' => 'Displays the list of most popular members.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-list-popular',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Members',
    ),
  ),
  array(
    'title' => 'Recent Signups',
    'description' => 'Displays the list of most recent signups.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-list-signups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Signups',
    ),
  ),
  array(
    'title' => 'Login',
    'description' => 'Displays a login form for members that are not logged in.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-login-or-signup',
  ),
  array(
    'title' => 'Profile Photo',
    'description' => 'Displays a member\'s photo on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-profile-photo',
  ),
  array(
    'title' => 'Profile Status',
    'description' => 'Displays a member\'s name and most recent status on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-profile-status',
  ),
	array(
    'title' => 'Profile Fields',
    'description' => 'Displays a member\'s profile field data on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-profile-fields',
    'defaultParams' => array(
      'title' => 'Profile Fields',
      'titleCount' => true,
    ),
  ),
	array(
		'title' => 'Profile Widgets',
		'description' => 'Displays Profile Widgets in separated columns.',
		'category' => 'User',
		'type' => 'widget',
		'name' => 'mobile.user-profile-widgets',
		'defaultParams' => array(
      'left' => array('mobile.user-profile-photo'),
			'right' => array('mobile.user-profile-status', 'mobile.user-profile-fields'),
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
							'mobile.user-profile-photo' => 'Profile Photo',
							'mobile.user-profile-status' => 'Profile Status',
							'mobile.user-profile-info' => 'Profile Info',
							'mobile.user-profile-fields' => 'Profile Fields',
							'mobile.user-profile-options' => 'Profile Options',
							'mobile.like-status' => 'Profile Like Status',
            )
					)
        ),
				array(
          'MultiCheckbox',
          'right',
					array(
						'Label'=>'Right Column',
						'multiOptions' => array(
							'mobile.user-profile-photo' => 'Profile Photo',
							'mobile.user-profile-status' => 'Profile Status',
							'mobile.user-profile-info' => 'Profile Info',
							'mobile.user-profile-fields' => 'Profile Fields',
							'mobile.user-profile-options' => 'Profile Options',
							'mobile.like-status' => 'Profile Like Status',
            )
					)
        ),
      )
    ),
	),
  array(
    'title' => 'Profile Friends',
    'description' => 'Displays a member\'s friends on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-profile-friends',
    'defaultParams' => array(
      'title' => 'Friends',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Info',
    'description' => 'Displays a member\'s info (signup date, friend count, etc) on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-profile-info',
    'defaultParams' => array(
      'title' => 'Profile Info',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Options',
    'description' => 'Displays a list of actions that can be performed on a member on their profile (report, add as friend, etc).',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'mobile.user-profile-options',
  ),
) ?>