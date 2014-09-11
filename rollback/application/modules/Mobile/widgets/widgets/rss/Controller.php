<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_RssController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $url = $this->_getParam('url');
    if( !$url ) {
      return $this->setNoRender();
    }
    $this->view->url = $url;

    // Zend_Feed requires DOMDocument
    if( !class_exists('DOMDocument', false) ) {
      return $this->setNoRender();
    }

    $rss = Zend_Feed::import($url);
    $channel = array(
      'title'       => $rss->title(),
      'link'        => $rss->link(),
      'description' => $rss->description(),
      'items'       => array()
    );

    $max = $this->_getParam('max', 4);
    $count = 0;

    // Loop over each channel item and store relevant data
    foreach( $rss as $item )
    {
      if( $count++ >= $max ) break;
      $channel['items'][] = array(
        'title'       => $item->title(),
        'link'        => $item->link(),
        'description' => $item->description(),
        'pubDate'     => $item->pubDate(),
        'guid'        => $item->guid(),
      );
    }

    $this->view->channel = $channel;
  }
}