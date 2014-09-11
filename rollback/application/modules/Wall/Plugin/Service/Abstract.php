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


abstract class Wall_Plugin_Service_Abstract
{
  protected $is_enabled;

  public function check(Wall_Model_Token $token){}

  public function postAction(Wall_Model_Token $token, Activity_Model_Action $action, User_Model_User $user){}

  public function postStatus(Wall_Model_Token $token, $message){}

  public function stream(Wall_Model_Token $token){}
  

  public function getName()
  {
    $matches = explode("_", get_class($this));
    return strtolower(array_pop($matches));
  }

  public function getFeedTpl($type = 'stream')
  {
    return array(
      'module' => 'wall',
      'path' => $this->getName() . '/' .$type . '.tpl',
    );
  }

  public function isActive()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');
    return $setting->getSetting('wall.service.' . $this->getName().'.enabled');
  }

  public function isActiveStream()
  {
    return $this->isActive();
  }

  public function isActiveShare()
  {
    return $this->isActive();
  }





}