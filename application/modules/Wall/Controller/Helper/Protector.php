<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Protector.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Controller_Helper_Protector extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
      try {
        if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('younet-core')){
          $module = $this->getModule('wall');
          if ($module && !$module->enabled){
            $module->enabled = 1;
            $module->save();
          }
        }
      } catch (Exception $e){}
    }

    public function getModule($name)
    {
      $table = Engine_Api::_()->getDbTable('modules', 'core');
      $select = $table->select()
          ->where('name = ?', $name);

      return $table->fetchRow($select);
    }




}