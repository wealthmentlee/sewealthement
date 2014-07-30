<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: classified-content.php 2011-02-14 06:58:57 mirlan $
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
    'title' => 'Profile Classifieds',
    'description' => 'Displays a member\'s classifieds on their profile. Please put on Member Profile page.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'mobile.classified-profile-classifieds',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Classifieds',
      'titleCount' => true,
    ),
  ),
) ?>