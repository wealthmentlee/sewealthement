<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Token.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Token extends Core_Model_Item_Abstract
{

  public function publicArray()
  {
    $params = array(
      'object_id' => $this->object_id,
      'object_name' => $this->object_name,
      'provider' => $this->provider
    );

    return $params;
  }

  public function check()
  {
    $service = Engine_Api::_()->wall()->getServiceClass($this->provider);

    if (!$service){
      return false;
    }

    $check = $service->check($this);
    if (!$check){
      //$this->delete();
    }
    return $check;
  }

}
