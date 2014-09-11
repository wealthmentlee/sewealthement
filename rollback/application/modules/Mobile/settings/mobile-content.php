<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: mobile-content.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
return array(
	// Mobile widgets
	array(
		'title' => 'Mobile Mode Switcher',
		'description' => 'Shows switch links for Standard/Mobile modes. Recommended to put it in Site Footer.',
		'category' => 'Mobile',
		'type' => 'widget',
		'name' => 'mobile.mode-switcher',
    'defaultParams' => array(
      'standard' => 'Standard Site',
			'mobile' => 'Mobile Site',
    ),
		'adminForm' => array(
      'elements' => array(
  			array(
          'Text',
          'standard',
          array(
            'label' => 'Standard Site Link Label',
            'default' => 'Standard Site',
          )
        ),
  			array(
          'Text',
          'mobile',
          array(
            'label' => 'Mobile Site Link Label',
            'default' => 'Mobile Site',
          )
        ),
      ),
  	),
	),
  array(
    'title' => 'Mobile Footer Menu',
    'description' => 'Shows the site-wide footer menu in mobile mode.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.menu-footer',
  ),
  array(
    'title' => 'Mobile Main Menu',
    'description' => 'Shows the site-wide main menu in mobile mode.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.menu-main',
    'defaultParams' => array(
      'count' => 3
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
          'Text',
          'count',
          array(
            'label' => 'Display Number',
						'Description' => 'Other menus will be inserted into more menu links.',
          )
        ),
      ),
  	),
	),
  array(
    'title' => 'Mobile Site Map',
    'description' => 'Shows the site map.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.site-map',
    'defaultParams' => array(
      'type' => 'list',
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
          'Select',
          'type',
          array(
            'label' => 'Site Map Display Type',
            'description' => 'Displays Site Map.',
            'default' => 'list',
            'multiOptions' => array(
							'links' => 'Links',
              'list' => 'List',
            )
          )
        ),
      ),
  	),
	),
  array(
    'title' => 'Mobile Mini Menu',
    'description' => 'Shows the site-wide mini menu in mobile mode.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.menu-mini',
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
          'Text',
          'count',
          array(
            'label' => 'Display Number',
						'Description' => 'Other menus will be inserted into more menu links.',
          )
        ),
      ),
  	),
  ),
  array(
    'title' => 'Mobile Site Logo',
    'description' => 'Shows your site-wide main logo or title in mobile mode.  Images are uploaded via the <a href="admin/files" target="_parent">File Media Manager</a>.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.menu-logo',
    'adminForm' => 'Core_Form_Admin_Widget_Logo',
  ),
	array(
		'title' => 'Mobile Main Header',
		'description' => 'Shows your site-wide main logo and site-wide mini menu',
		'category' => 'Mobile',
		'type' => 'widget',
		'name' => 'mobile.main-header',
        'adminForm' => 'Core_Form_Admin_Widget_Logo'
		),
  array(
    'title' => 'Tab Container',
    'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.container-tabs',
    'defaultParams' => array(
      'max' => 6
    ),
    'canHaveChildren' => true,
    'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
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
          'max',
          array(
            'label' => 'Max Tab Count',
            'description' => 'Show sub menu at x containers.',
            'default' => 4,
            'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
            )
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'HTML Block',
    'description' => 'Inserts any HTML of your choice.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobile.html-block',
    'special' => 1,
    'autoEdit' => true,
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
          'Textarea',
          'data',
          array(
            'label' => 'HTML'
          )
        ),
      )
    ),
  ),
)
?>