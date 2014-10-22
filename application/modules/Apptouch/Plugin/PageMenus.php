<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 16.08.12
 * Time: 11:27
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Plugin_PageMenus
{
  public function initMenu($row)
  {

    $subject = Engine_Api::_()->core()->getSubject();

    $params = $row->params;

    if ($subject instanceof Page_Model_Page) {

      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }

      $params['params']['page_id'] = $subject->getIdentity();

      return $params;
    }
    return true;

  }

  public function onMenuInitialize_PageQuickCreate($row)
  {
    if(Engine_Api::_()->apptouch()->isApp()){
      return false;
    };
    return $row;
  }

  public function __call($method, array $arguments = array())
  {
    return $this->initMenu($arguments[0]);
  }

}
