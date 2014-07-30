<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Abstract.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


abstract class Wall_Plugin_Type_Abstract
{
  public $feed_config = array();

  public function getItems(User_Model_User $user)
  {
    return null;
  }

  public function getTypes(User_Model_User $user)
  {
    return null;
  }

  public $customStream = false;

  public function getCustomStream($viewer = null, $params = array())
  {
    return null;
  }

  public function getName()
  {
    $matches = explode("_", get_class($this));
    return strtolower(array_pop($matches));
  }


}