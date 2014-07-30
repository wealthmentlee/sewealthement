<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10111 2013-10-31 05:05:49Z andres $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'messages',
    'version' => '4.7.0',
    'revision' => '$Revision: 10111 $',
    'path' => 'application/modules/Messages',
    'repository' => 'socialengine.com',
    'title' => 'Messages',
    'description' => 'Messages',
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
      'path' => 'application/modules/Messages/settings/install.php',
      'class' => 'Messages_Installer',
    ),
    'directories' => array(
      'application/modules/Messages',
    ),
    'files' => array(
      'application/languages/en/messages.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  // Items ---------------------------------------------------------------------
  'items' => array(
    'messages_message',
    'messages_conversation',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'messages_general' => array(
      'route' => 'messages/:action/*',
      'defaults' => array(
        'module' => 'messages',
        'controller' => 'messages',
        'action' => '(inbox|outbox|delete)',
      ),
      'reqs' => array(
        'action' => '\D+',
      )
    ),
  )
) ?>
