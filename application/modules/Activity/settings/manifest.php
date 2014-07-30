<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10111 2013-10-31 05:05:49Z andres $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'activity',
    'version' => '4.7.0',
    'revision' => '$Revision: 10111 $',
    'path' => 'application/modules/Activity',
    'repository' => 'socialengine.com',
    'title' => 'Activity',
    'description' => 'Activity',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       //'enable',
       //'disable',
     ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 4000,
    ),
    'directories' => array(
      'application/modules/Activity',
    ),
    'files' => array(
      'application/languages/en/activity.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'getActivity',
      'resource' => 'Activity_Plugin_Core',
    ),
    array(
      'event' => 'addActivity',
      'resource' => 'Activity_Plugin_Core',
    ),
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Activity_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'activity_action',
    'activity_comment',
    'activity_like',
    'activity_notification',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'recent_activity' => array(
      'route' => 'activity/notifications/',
      'defaults' => array(
        'module' => 'activity',
        'controller' => 'notifications',
      )
    )
  )
) ?>
