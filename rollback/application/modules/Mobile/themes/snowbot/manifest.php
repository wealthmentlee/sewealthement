<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'snowbot',
    'version' => '4.0.0',
    'revision' => '$Revision: 6973 $',
    'path' => 'application/modules/Mobile/themes/snowbot',
    'repository' => 'socialengine.net',
    'meta' => array(
      'title' => 'Snowbot Theme',
      'thumb' => 'snowbot_theme.jpg',
      'author' => 'Webligo Developments'
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/modules/Mobile/themes/snowbot',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>