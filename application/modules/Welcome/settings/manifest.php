<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'welcome',
    'version' => '4.2.0p3',
    'path' => 'application/modules/Welcome',
    'title' => 'Welcome',
    'description' => 'Welcome Slideshow',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'Welcome',
      'description' => 'Welcome Slideshow',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array(
      'path' => 'application/modules/Welcome/settings/install.php',
      'class' => 'Welcome_Installer',
    ),
   'actions' => array(
       'preinstall',
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable'
     ),
    'directories' => array(
      'application/modules/Welcome',
    ),
    'files' => array(
      'application/languages/en/welcome.csv',
    ),
  ),
  // Content -------------------------------------------------------------------
  'content'=> array(
    'welcome_profile_steps' => array(
      'type' => 'action',
      'title' => 'Welcome Steps',
      'route' => array(
        'module' => 'welcome',
        'controller' => 'widget',
        'action' => 'steps',
      ), 
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'welcome_step',
    'welcome_effect',
    'welcome_setting',
    'welcome_slideshow',
    'welcome_slideshowsetting'
  ),
);