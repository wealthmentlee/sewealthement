<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Body.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Helper_Body extends Activity_Model_Helper_Abstract
{
  /**
   * Body helper
   * 
   * @param string $body
   * @return string
   */
  public function direct($body)
  {

    if( Zend_Registry::isRegistered('Zend_View') ) {
      $view = Zend_Registry::get('Zend_View');
      $body = $view->wallViewMore($body,null,null,null,true);
    }

    $body = $this->replaceSmiles($body);
    $body = $this->replaceTags($body);

    return '<span class="feed_item_bodytext">' . $body . '</span>';
  }



  public function replaceSmiles($body)
  {
    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.smile', 1)){
      $view = Zend_Registry::get('Zend_View');
      $body = $view->wallSmiles()->replaceContent($body);
    }
    return $body;
  }

  public function replaceTags($body)
  {
    $tags = $this->getAction()->getTags();

    $object_data = array();
    foreach ($tags as $item){
      $object_data[] = array(
        'type' => $item->object_type,
        'id' => $item->object_id
      );
    }
    $object_finish = array();
    foreach (Engine_Api::_()->wall()->getItems($object_data) as $item){
      $object_finish[$item->getGuid()] = $item;
    }
    foreach ($tags as $tag){
      $guid = $tag->object_type . '_' . $tag->object_id;
      if (!empty($object_finish[$guid])){
        $item = $object_finish[$guid];
        $link = '<a href="' . $item->getHref() . '" class="wall_liketips wall_item_tagged" rev="' . $item->getGuid() . '">' . $item->getTitle() . '</a>';
        $body = str_replace($tag->value, $link, $body);
      }
    }
    return $body;
  }



}