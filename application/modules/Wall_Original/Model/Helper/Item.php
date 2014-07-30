<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Item.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Helper_Item extends Activity_Model_Helper_Abstract
{
  /**
   * Generates text representing an item
   * 
   * @param mixed $item The item or item guid
   * @param string $text (OPTIONAL)
   * @param string $href (OPTIONAL)
   * @return string
   */
  public function direct($item, $text = null, $href = null)
  {
    $item = $this->_getItem($item, false);

    // Check to make sure we have an item
    if( !($item instanceof Core_Model_Item_Abstract) )
    {
      return false;
    }

    if (Engine_Api::_()->wall()->isOwnerTeamMember($this->getAction()->getObject(), $this->getAction()->getSubject())){

      return $this->getAction()->getObject()->toString(array(
        'class' => 'feed_item_username wall_liketips',
        'rev' => $this->getAction()->getObject()->getGuid()
      ));
    }


    if( !isset($text) )
    {
      $text = $item->getTitle();
    }

    // translate text
    $translate = Zend_Registry::get('Zend_Translate');
    if( $translate instanceof Zend_Translate ) {
      $text = $translate->translate($text);
      // if the value is pluralized, only use the singular
      if (is_array($text))
        $text = $text[0];
    }

    if( !isset($href) )
    {
      $href = $item->getHref();
    }
    
    return '<a '
      . 'class="feed_item_username wall_liketips" rev="'.$item->getGuid().'" '
      . ( $href ? 'href="'.$href.'"' : '' )
      . '>'
      . $text
      . '</a>';
  }
}