<?php
/**
 * Created by PhpStorm.
 * User: Улан Омуркулов
 * Date: 21.08.14
 * Time: 11:17
 */

class Hecore_Plugin_Menus {
  public function mainMenuActivate($row)
  {
    $params = $row->params;
    if(Zend_Controller_Front::getInstance()->getRequest()->getModuleName() == $row->module)
      $params['active'] = true;

    return $params;
  }
} 