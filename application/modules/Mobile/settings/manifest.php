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
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'mobile',
    'version' => '4.1.8p7',
    'path' => 'application/modules/Mobile',
    'meta' => array(
      'title' => 'Mobile',
      'description' => 'Mobile Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'title' => 'Mobile',
    'description' => 'Mobile',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Mobile/settings/install.php',
      'class' => 'Mobile_Installer',
    ),
    'directories' => array(
      'application/modules/Mobile',
    ),
    'files' => array(
      'application/languages/en/mobile.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'dashboard' => array(
      'route' => '/dashboard',
      'defaults' => array(
        'module' => 'mobile',
        'controller' => 'index',
        'action'=>'index'
      ),
    ),
    'mode_switch' => array(
      'route' => '/mode-switch/:mode',
      'defaults' => array(
        'module' => 'mobile',
        'controller' => 'index',
        'action' => 'mode-switch',
        'mode'=>'standard',
      )
    ),
   'mobile_recent_activity' => array(
      'route' => 'activity/notifications/:action/*',
      'defaults' => array(
        'module' => 'activity',
        'controller' => 'notifications',
        'action' => 'index',
      )
    )
  ),
  // end routes
);