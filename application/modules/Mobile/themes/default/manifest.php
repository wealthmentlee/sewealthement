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
    'name' => 'default',
    'version' => '4.0.4',
    'revision' => '$Revision: 7306 $',
    'path' => 'application/modules/Mobile/themes/default',
    'repository' => 'socialengine.net',
    'meta' => array(
      'title' => 'Default Theme',
      'thumb' => 'default_theme.jpg',
      'author' => 'Webligo Developments',
      'changeLog' => array(
        '4.0.4' => array(
          'constants.css' => 'Added constant theme_pulldown_contents_list_background_color_active',
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Improved RTL support',
        ),
        '4.0.3' => array(
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Added styles for highlighted text in search',
        ),
        '4.0.2' => array(
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Added styles for delete comment link',
        ),
      ),
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
      'application/modules/Mobile/themes/default',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  ),
) ?>