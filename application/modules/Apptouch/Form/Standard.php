<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 23.02.12
 * Time: 11:28
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Form_Standard extends Engine_Form
{
  public function __construct()
  {
    parent::__construct();
    $this->addElement('hidden', 'form_php_class_name', array(
      'order' => 99991,
      'class' => 'form_php_class_name',
      'value' => get_class($this)
    ));
  }
}
