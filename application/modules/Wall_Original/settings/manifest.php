<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'wall',
    'version' => '4.2.6p8',
    'path' => 'application/modules/Wall',
    'title' => 'Wall',
    'description' => 'Wall',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.1.8',
      ),
    ),
    'callback' => 
    array (
      'class' => 'Wall_Installer',
      'path' => 'application/modules/Wall/settings/install.php',
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
      0 => 'application/modules/Wall',
      1 => 'application/libraries/Zend/Oauth'
    ),
    'files' => 
    array (
      0 => 'application/languages/en/wall.csv',
      1 => 'application/libraries/Zend/Oauth.php'
    ),
  ),

   'hooks' => array(
     array(
       'event' => 'onItemDeleteBefore',
       'resource' => 'Wall_Plugin_Core',
     ),
   ),

  'items' => array(
    'activity_action', // injection ... 
  ),

  'wall_tabs' => array(
    array('type' => 'welcome'),
    array('type' => 'social'),
    array('type' => 'facebook'),
    array('type' => 'twitter'),
    array('type' => 'linkedin')
  ),

  'wall_composer' => array(
    array(
      'script' => array('compose/photo.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Album',
      'auth' => array('album', 'create'),
      'module' => 'album',
      'type' => 'photo',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/advalbum.tpl', 'wall'),
      'plugin' => 'Advalbum_Plugin_Composer',
      'module' => 'advalbum',
      'type' => 'photo',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/link.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Core',
      'auth' => array('core_link', 'create'),
      'module' => 'core',
      'type' => 'link',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/tag.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Core',
      'module' => 'core',
      'composer' => true,
      'type' => 'tag'
    ),
    array(
      'script' => array('compose/music.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Music',
      'auth' => array('music_playlist', 'create'),
      'module' => 'music',
      'type' => 'music',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/ynmusic.tpl', 'wall'),
      'plugin' => 'Ynmusic_Plugin_Composer',
      'module' => 'ynmusic',
      'type' => 'music',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/mp3music.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Mp3music',
      'module' => 'mp3music',
      'type' => 'mp3music',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/video.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Video',
      'auth' => array('video', 'create'),
      'module' => 'video',
      'type' => 'video',
      'can_disable' => true
    ),
	  array(
      'script' => array('compose/avp.tpl', 'wall'),
      'plugin' => 'Avp_Plugin_Composer',
      'module' => 'avp',
      'type' => 'avp',
      'can_disable' => true
    ),
    array(
      'script' => array('compose/ynvideo.tpl', 'wall'),
      'plugin' => 'Ynvideo_Plugin_Composer',
      'auth' => array('video', 'create'),
      'type' => 'video',
      'module' => 'ynvideo',
      'can_disable' => true
    ),
/*    array(
      'script' => array('compose/question.tpl', 'wall'),
      'auth' => array('question', 'create'),
      'type' => 'question',
      'module' => 'question',
      'can_disable' => true
    ),*/
    array(
      'script' => array('compose/people.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer_Core',
      'module' => 'wall',
      'type' => 'people',
      'composer' => true,
      'can_disable' => true
    ),
    array(
      'script' => array('compose/smile.tpl', 'wall'),
      'plugin' => 'Wall_Plugin_Composer',
      'module' => 'wall',
      'type' => 'smile',
      'can_disable' => true
    ),
  ),

  'wall_type' => array(
/*    array(
      'plugin' => 'Wall_Plugin_Type_Popular',
      'type' => 'popular'
    ),*/
    array(
      'plugin' => 'Wall_Plugin_Type_Page',
      'module' => 'page',
      'type' => 'page'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Friend',
      'module' => 'user',
      'type' => 'friend'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Photo',
      'module' => array('album', 'advalbum', 'pagealbum'),
      'type' => 'photo'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Status',
      'module' => 'user',
      'type' => 'status'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Group',
      'module' => 'group',
      'type' => 'group'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Event',
      'module' => 'event',
      'type' => 'event'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Blog',
      'module' => 'blog',
      'type' => 'blog'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Poll',
      'module' => 'poll',
      'type' => 'poll'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Classified',
      'module' => 'classified',
      'type' => 'classified'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Forum',
      'module' => 'forum',
      'type' => 'forum'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Music',
      'module' => array('music', 'mp3music', 'ynmusic'),
      'type' => 'music'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Video',
      'module' => array('video', 'avp'),
      'type' => 'video'
    ),
    array(
      'plugin' => 'Wall_Plugin_Type_Signup',
      'module' => 'user',
      'type' => 'signup'
    )
  ),

  'wall_list' => array(
    array(
      'plugin' => 'Wall_Plugin_List_Member',
      'module' => 'user',
      'type' => 'member'
    ),
    array(
      'plugin' => 'Wall_Plugin_List_Page',
      'module' => 'page',
      'type' => 'page'
    ),
    array(
      'plugin' => 'Wall_Plugin_List_Group',
      'module' => 'group',
      'type' => 'group'
    )
  ),

  'wall_service' => array(
    array(
      'plugin' => 'Wall_Plugin_Service_Facebook',
      'type' => 'facebook'
    ),
    array(
      'plugin' => 'Wall_Plugin_Service_Twitter',
      'type' => 'twitter'
    ),
    array(
      'plugin' => 'Wall_Plugin_Service_Linkedin',
      'type' => 'linkedin'
    )
  ),

  'routes' => array(
    'wall_extended' => array(
			'route' => 'wall/:controller/:action/*',
			'defaults' => array(
				'module' => 'wall',
				'controller' => 'index',
				'action' => 'index',
			)
		),
    'wall_feed' => array(
			'route' => 'wall-feed/*',
			'defaults' => array(
				'module' => 'wall',
				'controller' => 'widget',
				'action' => 'index',
			)
		),
    'wall_list' => array(
			'route' => 'wall/list/:action/*',
			'defaults' => array(
				'module' => 'wall',
				'controller' => 'list',
				'action' => 'index',
			)
		),

    'wall_view' => array(
      'route' => '/posts/:id/:object/*',
      'defaults' => array(
        'module' => 'wall',
        'controller' => 'index',
        'action' => 'view',
        'id' => 0,
        'object' => ''
      )
    )



  )

); ?>
