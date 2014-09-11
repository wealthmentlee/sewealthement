<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hashtag
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2013-01-17 15:22:44 ratbek $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Hashtag
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'hashtag',
    'version' => '4.5.1p3',
    'path' => 'application/modules/Hashtag',
    'title' => 'Hashtag',
    'description' => 'Hashtag',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
      array(
        'type' => 'module',
        'name' => 'wall',
        'minVersion' => '4.2.6',
      ),
    ),
    'callback' =>
    array(
      'class' => 'Hashtag_Installer',
      'path' => 'application/modules/Hashtag/settings/install.php',
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array (
      0 => 'application/modules/Hashtag',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/hashtag.csv',
    ),
  ),

  'wall_composer' => array(
    array(
      'script' => array('compose/hashtag.tpl', 'hashtag'),
      'plugin' => 'Hashtag_Plugin_Composer_Core',
      'module' => 'hashtag',
      'type' => 'hashtag',
      'composer' => TRUE
    ),
  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onactivity_actionCreateAfter',
      'resource'  => 'Hashtag_Plugin_Core'
    ),
    array(
      'event' => 'onActivityAttachmentCreateAfter',
      'resource'  => 'Hashtag_Plugin_Core'
    ),
    array(
      'event' => 'onActivityActionCreateAfter',
      'resource'  => 'Hashtag_Plugin_Core'
    ),
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Hashtag_Plugin_Core',
    ),

  ),

); ?>