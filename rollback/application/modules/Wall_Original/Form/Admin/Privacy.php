<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Privacy.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */




class Wall_Form_Admin_Privacy extends Wall_Form_Subform
{
  public function init()
  {
    $this->setTitle('WALL_ADMIN_PRIVACY_TITLE');
    $this->setDescription('WALL_ADMIN_PRIVACY_DESCRIPTION');


    $this->addElement('Dummy', 'content', array('content' => $this->getView()->partial('admin-setting/privacy.tpl', 'wall')));
    $this->getElement('content')->getDecorator('label')->setOption('tagOptions', array('class' => 'form-label', 'style' => 'width:0'));

  }


  public function applyDefaults()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');


  }

  public function saveValues()
  {
    $privacy_keys = array();
    $privacy = Engine_Api::_()->wall()->getPrivacyList();
    foreach ($privacy as $key => $item){
      foreach ($item as $sub){
        $privacy_keys[] = $key . '_' . $sub;
      }
    }

    $enabled_privacy = array();
    foreach (Zend_Controller_Front::getInstance()->getRequest()->getParam('privacy_enabled') as $key => $value){
      if (empty($value) || !in_array($key, $privacy_keys)){
        continue ;
      }
      $enabled_privacy[] = $key;
    }


    $privacy_disabled = array_diff($privacy_keys, $enabled_privacy);
    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.privacy.disabled', implode($privacy_disabled, ','));

    $this->getElement('content')->setOptions(array('content' => $this->getView()->partial('admin-setting/privacy.tpl', 'wall')));

  }


  
}