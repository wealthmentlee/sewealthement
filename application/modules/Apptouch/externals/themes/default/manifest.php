<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Default
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9579 2012-01-06 00:00:44Z john $
 * @author     Alex
 */
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'default',
    'version' => '4.2.0',
    'path' => 'application/modules/Apptouch/externals/themes/default',
    'repository' => 'hire-experts.com',
    'title' => 'Default',
    'thumb' => 'default.png',
    'author' => 'Hire-Experts LLC',
    'changeLog' => array(),
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
      'application/modules/Apptouch/externals/themes/default',
    ),
  ),
) ?>