<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'hequestion',
    'version' => '4.2.5',
    'path' => 'application/modules/Hequestion',
    'title' => 'Questions',
    'description' => 'Questions',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'wall',
        'minVersion' => '4.2.5',
      ),
    ),
    'callback' =>
    array (
      'class' => 'Hequestion_Installer',
      'path' => 'application/modules/Hequestion/settings/install.php',
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
      0 => 'application/modules/Hequestion',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/hequestion.csv',
    ),
  ),

  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Hequestion_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Hequestion_Plugin_Core',
    ),
  ),

  'items' => array(
    'hequestion',
    'hequestion_option'
  ),
  'wall_composer' => array(
    array(
      'script' => array('_composeHequestion.tpl', 'hequestion'),
      'plugin' => 'Hequestion_Plugin_Composer',
      'auth' => array('hequestion', 'create'),
      'module' => 'hequestion',
      'type' => 'hequestion',
      'can_disable' => true
    )
  ),


  'routes' => array(

    'hequestion_general' => array(
      'route' => 'browse-questions/:action/*',
      'defaults' => array(
        'module' => 'hequestion',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        //'action' => '(index|create|manage)',
      ),
    ),
    'hequestion_view' => array(
      'route' => 'question-view/:question_id/:slug',
      'defaults' => array(
        'module' => 'hequestion',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'question_id' => '\d+'
      ),
    ),
    'hequestion_box' => array(
      'route' => 'question-box/:question_id/:slug',
      'defaults' => array(
        'module' => 'hequestion',
        'controller' => 'index',
        'action' => 'box',
        'slug' => '',
      ),
      'reqs' => array(
        'question_id' => '\d+'
      ),
    ),
  ),


); ?>