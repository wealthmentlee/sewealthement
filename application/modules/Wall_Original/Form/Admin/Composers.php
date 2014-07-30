<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Composers.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */




class Wall_Form_Admin_Composers extends Wall_Form_Subform
{
  public function init()
  {
    $this->setTitle('WALL_ADMIN_COMPOSERS_TITLE');
    $this->setDescription('WALL_ADMIN_COMPOSERS_DESCRIPTION');


    $this->addElement('Dummy', 'content', array('content' => $this->getView()->partial('admin-setting/composers.tpl', 'wall')));
    $this->getElement('content')->getDecorator('label')->setOption('tagOptions', array('class' => 'form-label', 'style' => 'width:0'));

  }


  public function applyDefaults()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');


  }

  public function saveValues()
  {

    $composers = array();
    foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $key => $value){
      if (empty($value['can_disable'])){
        continue ;
      }
      $composers[] = $key;
    }

    $enabled_composers = array();
    foreach (Zend_Controller_Front::getInstance()->getRequest()->getParam('composers_enabled') as $key => $value){
      if (empty($value) || !in_array($key, $composers)){
        continue ;
      }
      $enabled_composers[] = $key;
    }


    $composers_disabled = array_diff($composers, $enabled_composers);
    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.composers.disabled', implode($composers_disabled, ','));

    $this->getElement('content')->setOptions(array('content' => $this->getView()->partial('admin-setting/composers.tpl', 'wall')));

  }


  
}