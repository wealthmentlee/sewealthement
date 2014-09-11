<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2011-02-14 06:58:57 mirlan $
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
)
?>