<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Offertype.php 2012-10-01 19:53 taalay $
 * @author     TJ
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Model_Offertype extends Core_Model_Item_Abstract
{
  public $value = 0;

  public function changeOrder($mode)
  {
    $direction = ($mode) ? '<' : '>';
    $order = ($mode) ? "DESC" : "ASC";

    $tbl = Engine_Api::_()->getDbTable('offertypes', 'rate');

    $select = $tbl->select()
      ->where('category_id = ?', $this->category_id)
      ->where('`order` ' . $direction . ' ?', $this->order)
      ->order('order ' . $order)
      ->limit(1);

    $next = $tbl->fetchRow($select);

    if (!$next) {
      return false;
    }

    $current_order = $this->order;
    $next_order = $next->order;

    $this->order = 0;
    $this->save();

    $next->order = $current_order;
    $next->save();

    $this->order = $next_order;
    $this->save();
  }

  public function delete()
  {
    $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
    $tbl->delete(array(
      'type_id = ?' => $this->getIdentity()
    ));
    parent::delete();
  }
}

