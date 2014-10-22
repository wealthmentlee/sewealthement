<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 19.06.12
 * Time: 14:44
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Plugin_Tabs
{
  public function initTab($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getIdentity()) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $tab = explode('_', $row->name);
      unset($tab[0]); // todo
      unset($tab[1]); // todo
      $tab = array_values($tab);
      $tab = implode('-', $tab);
      $params = array(
        'label' => $row->label,
        'uri' => $subject->getHref() . '/tab/' . $tab
      );
      if ($request->getParam('tab') == $tab)
        $params['active'] = true;
      return $params;
    }
    return false;

  }

  public function __call($method, array $arguments = array())
  {
    return $this->initTab($arguments[0]);
  }

}
