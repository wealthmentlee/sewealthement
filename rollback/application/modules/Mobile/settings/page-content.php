<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: page-content.php 2011-02-14 06:58:57 mirlan $
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
    'title' => 'Page Rate',
    'description' => 'Displays the page\'s rate information.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'rate.widget-rate',
		'defaultParams' => array(
      'title' => 'Page Rate',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Activity Feed',
    'description' => 'Displays the page\'s activity feed(wall).',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-feed',
  	'defaultParams' => array(
      'title' => 'Updates',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Photo',
    'description' => 'Displays the page\'s photo(logo).',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-photo',
		'defaultParams' => array(
      'title' => 'Page Photo',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Note',
    'description' => 'Displays the page\'s note - informative/welcome text, team members are allowed to edit this note.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-note',
		'defaultParams' => array(
      'title' => 'Page Note',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Team',
    'description' => 'Displays the page\'s team members.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-admins',
  	'defaultParams' => array(
      'title' => 'Team',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Options',
    'description' => 'Displays the page\'s options.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-options',
		'defaultParams' => array(
      'title' => 'Page Options',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Info',
    'description' => 'Displays the page\'s detailed information.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-fields',
  	'defaultParams' => array(
      'title' => 'Info',
  		'titleCount' => true
    ),
  ),
/*	array(
		'title' => 'Search',
		'description' => 'Displays search box.',
		'category' => 'Page',
		'type' => 'widget',
		'name' => 'mobile.page-search',
		'defaultParams' => array(
      'title' => 'Search',
  		'titleCount' => false
    ),
  ),
	array(
		'title' => 'Tag Cloud',
		'description' => 'Displays tags cloud box.',
		'category' => 'Page',
		'type' => 'widget',
		'name' => 'mobile.page-tag-cloud',
		'defaultParams' => array(
      'title' => 'Tag Cloud',
  		'titleCount' => false
    ),
  ),*/
  array(
    'title' => 'Albums',
    'description' => 'Displays the page\'s albums.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-album',
    'defaultParams' => array(
      'title' => 'Albums',
      'titleCount' => true,
      'url_params' => array(
        'route' => 'default',
        'module' => 'pagealbum',
        'controller' => 'index',
        'action' => 'index'
      ),
    )
  ),
  array(
    'title' => 'Blogs',
    'description' => 'Displays the page\'s blogs.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-blog',
    'defaultParams' => array(
      'title' => 'Blogs',
      'titleCount' => true,
    )
  ),
  array(
    'title' => 'Discussions',
    'description' => 'Displays the page\'s discussions.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-discussion',
    'defaultParams' => array(
      'title' => 'Discussions',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Events',
    'description' => 'Displays the page\'s events.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-event',
    'defaultParams' => array(
      'title' => 'Events',
      'titleCount' => true
    )
  ),
  array(
  'title' => 'Reviews',
	'description' => 'Displays the page\'s reviews.',
	'category' => 'Page',
	'type' => 'widget',
	'name' => 'mobile.page-review',
	'defaultParams' => array(
		'title' => 'Reviews',
		'titleCount' => true
	  )
  ),
  	array(
		'title' => 'Page Profile Widgets',
		'description' => 'Displays Page Profile Widgets in separated columns.',
		'category' => 'Page',
		'type' => 'widget',
		'name' => 'mobile.page-profile-widgets',
		'defaultParams' => array(
      'left' => array('mobile.page-profile-photo'),
			'right' => array('mobile.like-status', 'mobile.page-profile-options'),
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
							'mobile.page-profile-photo' => 'Page Profile Photo',
              'mobile.like-status' => 'Profile Like Status',
              'mobile.page-profile-status' => 'Page Profile Status',
              'mobile.page-profile-info' => 'Page Profile Info',
              'mobile.page-profile-options' => 'Page Profile Options',
            )
					)
        ),
				array(
          'MultiCheckbox',
          'right',
					array(
						'Label'=>'Right Column',
						'multiOptions' => array(
							'mobile.page-profile-photo' => 'Page Profile Photo',
              'mobile.like-status' => 'Profile Like Status',
              'mobile.page-profile-status' => 'Page Profile Status',
              'mobile.page-profile-info' => 'Page Profile Info',
              'mobile.page-profile-options' => 'Page Profile Options',
            )
					)
        ),
      )
    ),
	),
  array(
    'title' => 'Member Pages',
    'description' => 'Displays member pages. Please drag it on Member Profile page.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'mobile.page-profile-pages',
    'defaultParams' => array(
      'title' => 'Pages',
      'titleCount' => true
    )
  ),
);