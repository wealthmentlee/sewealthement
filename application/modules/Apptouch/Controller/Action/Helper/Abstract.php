<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 09.08.12
 * Time: 18:02
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_Helper_Abstract extends Zend_Controller_Action_Helper_Abstract
{
  /**
   * @var $bridge Apptouch_Controller_Action_Bridge
   */
  protected $_bridge;
  protected $view = null;

  public function direct()
  {
    $this->_bridge = $this->getBridge();
    $this->view = $this->getBridge()->view;

    return $this;
  }

  protected function _getParam($name, $default = null)
  {
    return $this->getActionController()->getRequest()->getParam($name, $default);
  }

  protected function _getAllParams()
  {
    return $this->getActionController()->getRequest()->getParams();
  }

  protected function _hasParam($name)
  {
    return null != $this->getActionController()->getRequest()->getParam($name);
  }

  /**
   * @return Apptouch_Controller_Action_Bridge
   */

  public function getBridge()
  {
    if ($this->_bridge instanceof Apptouch_Controller_Action_Bridge) {
      return $this->_bridge;
    }

    return $this->_bridge = $this->getActionController();
  }
}