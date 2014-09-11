<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'blog',
    'version' => '4.8.6',
    'revision' => '$Revision: 10271 $',
    'path' => 'application/modules/Blog',
    'repository' => 'socialengine.com',
    'title' => 'Blogs',
    'description' => 'Blogs',
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
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Blog/settings/install.php',
      'class' => 'Blog_Installer',
    ),
    'directories' => array(
      'application/modules/Blog',
    ),
    'files' => array(
      'application/languages/en/blog.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Blog_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Blog_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'blog',
    'blog_category',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'blog_specific' => array(
      'route' => 'blogs/:action/:blog_id/*',
      'defaults' => array(
        'module' => 'blog',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'blog_id' => '\d+',
        'action' => '(delete|edit)',
      ),
    ),
    'blog_general' => array(
      'route' => 'blogs/:action/*',
      'defaults' => array(
        'module' => 'blog',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|create|manage|style|tag|upload-photo)',
      ),
    ),
    'blog_view' => array(
      'route' => 'blogs/:user_id/*',
      'defaults' => array(
        'module' => 'blog',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),
    'blog_entry_view' => array(
      'route' => 'blogs/:user_id/:blog_id/:slug',
      'defaults' => array(
        'module' => 'blog',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'blog_id' => '\d+'
      ),
    ),
  ),
);
