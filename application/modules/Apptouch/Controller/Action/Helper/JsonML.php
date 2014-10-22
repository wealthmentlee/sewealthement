<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 05.09.12
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_Helper_JsonML
  extends Zend_Controller_Action_Helper_Abstract
{
  public function direct()
  {
    return $this;
  }

  public function new_($name, array $attrs = array(), $text = '', $html = null)
  {
    return new Apptouch_Controller_Action_Helper_Dom_Element($name, $attrs, $text, $html);
  }
}



