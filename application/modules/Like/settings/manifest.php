<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'like',
    'version' => '4.2.2p4',
    'path' => 'application/modules/Like',
    'title' => 'Like',
    'description' => 'Like Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'Like',
      'description' => 'Like Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array(
       'path' => 'application/modules/Like/settings/install.php',
       'class' => 'Like_Installer'
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'directories' => array(
      'application/modules/Like',
    ),
    'files' => array(
      'application/languages/en/like.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Like_Plugin_Core',
    ),
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
    'like_default' => array(
      'route' => 'like/:action/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
    'like_club' => array(
      'route' => 'like-club/:action/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'club',
        'action' => 'index'
      )
    ),
    'like_box' => array(
      'route' => 'like-box/:object/:object_id',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'club',
        'action' => 'like-box'
      )
    ),
    'show_like_box'=> array(
      'route' => 'show-like-box/:object/:object_id',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'club',
        'action' => 'show-like-box'
      )
    ),
    'like_button' => array(
      'route' => 'like-button/:object/:object_id',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'club',
        'action' => 'like-button'
      )
    ),
    'like_remote' => array(
      'route' => 'like-remote/:action/:object/:object_id/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'remote',
        'action' => 'like'
      )
    ),
    'like_login' => array(
      'route' => 'like-login/:action/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'auth',
        'action' => 'index'
      )
    ),
    'like_comment' => array(
      'route' => 'like-comment/:action/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'comment',
        'action' => 'list'
      )
    ),
    'like_interests' => array(
      'route' => 'like-interests/:action/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'interests',
        'action' => 'index'
      )
    ),
    'admin_like_level' => array(
      'route' => 'like/level/*',
      'defaults' => array(
        'module' => 'like',
        'controller' => 'admin-level',
        'action' => 'index'
      )
    ),    
  ),
);