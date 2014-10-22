<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 14.08.12
 * Time: 19:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_ComponentController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexAction()
  {

    if (!$this->_hasParam('component'))
      return;

    if (!Engine_Api::_()->core()->hasSubject() && $this->_hasParam('subject') && $this->_getParam('subject')) {

      $subject = explode('_', $this->_getParam('subject'));
      $subject = @Engine_Api::_()->getItem($subject[0], $subject[1]);

      if ($subject instanceof Core_Model_Item_Abstract)
        Engine_Api::_()->core()->setSubject($subject);

    }

    $component = $this->_getParam('component');
    $this->view->component = $this->component()->renderComponent($component);


  }
}
