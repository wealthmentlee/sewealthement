<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminMenusController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_AdminMenusController extends Core_Controller_Action_Admin
{
  protected $_menus;

  protected $_enabledModuleNames;
  
  public function init()
  {
    // Get list of menus
    $menusTable = Engine_Api::_()->getDbtable('menus', 'mobile');
    $menusSelect = $menusTable->select()
      ->where('type IN(?)', array('standard', 'hidden'));
    $this->view->menus = $this->_menus = $menusTable->fetchAll($menusSelect);

    $this->_enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
  }
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mobile_admin_main', array(), 'mobile_admin_main_menus');

    $this->view->name = $name = $this->_getParam('name', 'core_sitemap');

    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if( null === $selectedMenu ) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Make select options
    $menuList = array();
    foreach( $menus as $menu ) {
      $menuList[$menu->name] = $this->view->translate($menu->title);
    }
    $this->view->menuList = $menuList;

    // Get menu items
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'mobile');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('menu = ?', $name)
      ->order('order');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItems = $menuItems = $menuItemsTable->fetchAll($menuItemsSelect);
  }

  public function createAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if( null === $selectedMenu ) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemCreate();

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();
    $label = $values['label'];
    unset($values['label']);

    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'mobile');
    
    $menuItem = $menuItemsTable->createRow();
    $menuItem->label = $label;
    $menuItem->params = $values;
    $menuItem->menu = $name;
    $menuItem->module = 'core'; // Need to do this to prevent it from being hidden
    $menuItem->plugin = '';
    $menuItem->submenu = '';
    $menuItem->custom = 1;
    $menuItem->save();

    $menuItem->name = 'custom_' . sprintf('%d', $menuItem->id);
    $menuItem->save();

    $this->view->status = true;
    $this->view->form = null;
  }

  public function editAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'mobile');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name);
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemEdit();

    $form->removeElement('icon');
    $form->removeElement('uri');
    $form->removeElement('target');

    $style = 'margin-right:10px;margin-top: 10px;';
    $form->getElement('enabled')->getDecorator('label')->setOption('style', $style);


    // Make safe
    $menuItemData = $menuItem->toArray();
    if( isset($menuItemData['params']) && is_array($menuItemData['params']))
      $menuItemData = array_merge($menuItemData, $menuItemData['params']);
/*    if( !$menuItem->custom ) {
      $form->removeElement('uri');
    }*/

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      $form->populate($menuItemData);
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();
    $menuItem->label = $values['label'];
    unset($values['label']);
    if( $menuItem->custom ) {
      $menuItem->params = $values;
    } else if( !empty($values['target']) ) {
      $menuItem->params = array_merge($menuItem->params, array('target' => $values['target']));
    }
    if( isset($values['enabled']) ) {
      $menuItem->enabled = (bool) $values['enabled'];
    }
    $menuItem->save();
    
    $this->view->status = true;
    $this->view->form = null;
  }

  public function deleteAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'mobile');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name)
      ->order('order ASC');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem || !$menuItem->custom ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemDelete();
    
    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $menuItem->delete();

    $this->view->form = null;
    $this->view->status = true;
  }

  public function orderAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->viewRenderer->setNoRender(true);
    }
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $table = $this->_helper->api()->getDbtable('menuItems', 'mobile');
    $menuitems = $table->fetchAll($table->select()->where('menu = ?', $this->getRequest()->getParam('menu')));
    foreach ($menuitems as $menuitem)
    {
      $menuitem->order = $this->getRequest()->getParam('admin_menus_item_'.$menuitem->name);
      $menuitem->save();
    }
    return;
  }

}
