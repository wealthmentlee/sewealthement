<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 27.07.12
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_Templates extends Zend_View_Helper_Abstract
{
  protected static $rendered = false;
  protected static $ui = false;
  protected static $custom = false;
  protected static $common = false;

  public function templates()
  {
    return $this;
  }

  public function render()
  {
//    $general = '';
//    if (!self::$rendered) {
//      self::$rendered = true;
      $general = $this->view->render('application/modules/Apptouch/views/scripts/_templates/general.tpl');
//    }
  return $general . $this->ui() . $this->custom() . $this->common();
  }

  public function ui()
  {
//    if (!self::$ui) {
      self::$ui = true;
      return $this->view->render('application/modules/Apptouch/views/scripts/_templates/ui.tpl');
//    }

    //throw new Exception('Component Templates already have been Rendered');
  }

  public function custom()
  {
//    if (!self::$custom) {
      self::$custom = true;
      return $this->view->render('application/modules/Apptouch/views/scripts/_templates/custom.tpl');
//    }

    //throw new Exception('Custom Templates already have been Rendered');
  }

  public function common()
  {
//    if (!self::$common) {
      self::$common = true;
      return $this->view->render('application/modules/Apptouch/views/scripts/_templates/common.tpl');
//    }

    //throw new Exception('Custom Templates already have been Rendered');
  }
}
