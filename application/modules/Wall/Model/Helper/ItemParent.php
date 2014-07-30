<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemParent.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Helper_ItemParent extends Activity_Model_Helper_Item
{
  public function direct($item, $type = null, $text = null, $href = null)
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


    $item = $item->getParent($type);
    return parent::direct($item, $text, $href);
  }
}