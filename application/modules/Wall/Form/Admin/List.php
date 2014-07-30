<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: List.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */




class Wall_Form_Admin_List extends Wall_Form_Subform
{
  public function init()
  {
    $this->setTitle('WALL_ADMIN_LIST_TITLE');
    $this->setDescription('WALL_ADMIN_LIST_DESCRIPTION');


    $this->addElement('Dummy', 'content', array('content' => $this->getView()->partial('admin-setting/list.tpl', 'wall')));
    $this->getElement('content')->getDecorator('label')->setOption('tagOptions', array('class' => 'form-label', 'style' => 'width:0'));

    $this->addElement('Checkbox', 'user_save', array('description' => 'WALL_LIST_USER_SAVE'));

  }


  public function applyDefaults()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');
    $this->getElement('user_save')->setValue($setting->getSetting('wall.list.user_save'));
  }

  public function saveValues()
  {
    $types = array_merge(Engine_Api::_()->wall()->getManifestType('wall_type', true));

    $enabled_types = array();
    foreach (Zend_Controller_Front::getInstance()->getRequest()->getPost('list_enabled') as $key => $value){
      if (empty($value) || !in_array($key, $types)){
        continue ;
      }
      $enabled_types[] = $key;
    }

    $default = '';

    foreach (Zend_Controller_Front::getInstance()->getRequest()->getPost('list_default') as $key => $value){
      if (empty($value) || !in_array($key, $types)){
        continue ;
      }
      $default = $key;
    }


    $list_disabled = array_diff($types, $enabled_types);
    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.list.disabled', implode($list_disabled, ','));

    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.list.default', $default);

    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.list.user_save', (string) $this->getElement('user_save')->getValue());

    $this->getElement('content')->setOptions(array('content' => $this->getView()->partial('admin-setting/list.tpl', 'wall')));



    if ($default){
      Engine_Api::_()->getDbTable('userSettings', 'wall')->update(array('mode' => 'type', 'type' => $default), 1);
    } else {
      Engine_Api::_()->getDbTable('userSettings', 'wall')->update(array('mode' => 'recent'), 1);
    }

  }


  
}