<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: event-content.php 2011-02-14 06:58:57 mirlan $
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
    'title' => 'Upcoming Events',
    'description' => 'Displays the logged-in member\'s upcoming events.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-home-upcoming',
    'isPaginated' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Radio',
          'type',
          array(
            'label' => 'Show',
            'multiOptions' => array(
              '1' => 'Any upcoming events.',
              '2' => 'Current member\'s upcoming events.',
              '0' => 'Any upcoming events when member is logged out, that member\'s events when logged in.',
            ),
            'value' => '0',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Profile Events',
    'description' => 'Displays a member\'s events on their profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-events',
    'defaultParams' => array(
      'title' => 'Events',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Event Profile Info',
    'description' => 'Displays a event\'s info (creation date, member count, etc) on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-info',
  ),
  array(
    'title' => 'Event Profile Members',
    'description' => 'Displays a event\'s members on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-members',
  ),
  array(
    'title' => 'Event Profile Options',
    'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a event on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-options',
  ),
  array(
    'title' => 'Event Profile Photo',
    'description' => 'Displays a event\'s photo on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-photo',
  ),
  array(
    'title' => 'Event Profile Photos',
    'description' => 'Displays a event\'s photos on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-photos',
  ),
  array(
    'title' => 'Event Profile RSVP',
    'description' => 'Displays options for RSVP\'ing to an event on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-rsvp',
  ),
  array(
    'title' => 'Event Profile Status',
    'description' => 'Displays a event\'s title on it\'s profile.',
    'category' => 'Event',
    'type' => 'widget',
    'name' => 'mobile.event-profile-status',
  ),
  array(
		'title' => 'Event Profile Widgets',
		'description' => 'Displays Event Profile Widgets in separated columns.',
		'category' => 'Event',
		'type' => 'widget',
		'name' => 'mobile.event-profile-widgets',
		'defaultParams' => array(
      'left' => array('mobile.event-profile-photo'),
			'right' => array('mobile.like-status', 'mobile.event-profile-options'),
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
						'value' => array('mobile.event-profile-photo'),
						'multiOptions' => array(
							'mobile.event-profile-photo' => 'Event Profile Photo',
              'mobile.like-status' => 'Profile Like Status',
              'mobile.event-profile-status' => 'Event Profile Status',
              'mobile.event-profile-info' => 'Event Profile Info',
              'mobile.event-profile-options' => 'Event Profile Options',
            )
					)
        ),
				array(
          'MultiCheckbox',
          'right',
					array(
						'Label'=>'Right Column',
						'multiOptions' => array(
							'mobile.event-profile-photo' => 'Event Profile Photo',
              'mobile.like-status' => 'Profile Like Status',
              'mobile.event-profile-status' => 'Event Profile Status',
              'mobile.event-profile-info' => 'Event Profile Info',
              'mobile.event-profile-options' => 'Event Profile Options',
            )
					)
        ),
      )
    ),
	),
) ?>