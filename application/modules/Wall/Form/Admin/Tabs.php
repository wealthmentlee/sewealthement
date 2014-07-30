<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Tabs.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */




class Wall_Form_Admin_Tabs extends Wall_Form_Subform
{
  public function init()
  {
    $this->setTitle('WALL_ADMIN_TABS_TITLE');
    $this->setDescription('WALL_ADMIN_TABS_DESCRIPTION');


    $this->addElement('Dummy', 'content', array('content' => $this->getView()->partial('admin-setting/tabs.tpl', 'wall')));
    $this->getElement('content')->getDecorator('label')->setOption('tagOptions', array('class' => 'form-label', 'style' => 'width:0'));

  }


  public function applyDefaults()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');


  }

  public function saveValues()
  {



    $tabs = array_keys(Engine_Api::_()->wall()->getManifestType('wall_tabs'));

    $enabled_tabs = array();
    foreach (Zend_Controller_Front::getInstance()->getRequest()->getParam('tabs_enabled') as $key => $value){
      if (empty($value) || !in_array($key, $tabs)){
        continue ;
      }
      $enabled_tabs[] = $key;
    }

    $default = 'social';

    foreach (Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_default') as $key => $value){
      if (empty($value) || !in_array($key, $tabs)){
        continue ;
      }
      $default = $key;
    }


    $tab_disabled = array_diff($tabs, $enabled_tabs);

    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.tab.disabled', implode($tab_disabled, ','));
    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.tab.default', $default);

    $this->getElement('content')->setOptions(array('content' => $this->getView()->partial('admin-setting/tabs.tpl', 'wall')));

  }



}