<?php
class Apptouch_Widget_AdminQuickMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $menu_name = $this->_getParam('menu_name', false);
    if (!$menu_name)
      return $this->setNoRender();
    $active_item = $this->_getParam('active');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation($menu_name, array(), $active_item);
  }
}
