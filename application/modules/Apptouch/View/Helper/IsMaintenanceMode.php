<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 17.02.12
 * Time: 10:01
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_IsMaintenanceMode extends Zend_View_Helper_Abstract
{
  public function isMaintenanceMode()
  {
    return Engine_Api::_()->apptouch()->isMaintenanceMode();
  }
}
