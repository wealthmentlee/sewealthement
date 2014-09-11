<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
return array(
  'package' => array(
    'type' => 'widget',
    'name' => 'rss',
    'version' => '4.0.2',
    'revision' => '$Revision: 7593 $',
    'path' => 'application/modules/Mobile/widgets/widgets/rss',
    'repository' => 'socialengine.net',
    'title' => 'RSS Feed',
    'description' => 'Displays an RSS feed.',
    'author' => 'Webligo Developments',
    'changeLog' => array(
      '4.0.2' => array(
        'index.tpl' => 'Added styles',
        'manifest.php' => 'Incremented version',
      ),
    ),
    'directories' => array(
      'application/modules/Mobile/widgets/widgets/rss',
    ),
  ),

  // Backwards compatibility
  'type' => 'widget',
  'name' => 'rss',
  'version' => '4.0.2',
  'revision' => '$Revision: 7593 $',
  'title' => 'RSS',
  'description' => 'Displays an RSS feed.',
  'category' => 'Widgets',
  'adminForm' => array(
    'elements' => array(
      array(
        'Text',
        'title',
        array(
          'label' => 'Title'
        )
      ),
      array(
        'Text',
        'url',
        array(
          'label' => 'URL'
        )
      ),
    ),
  ),
) ?>