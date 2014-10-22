<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 16.05.12
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Form_Admin_Settings_General extends Engine_Form
{
  public function init()
  {
    $this->addElement('Checkbox', 'set_default', array(
      'label' => 'Yes',
      'description' => 'APTOUCH_Set Touch mode as default'
    ));

//    $this->addElement('Checkbox', 'integrations_only', array(
//      'label' => 'Yes',
//      'description' => 'APPTOUCH_Display only integrated Pages'
//    ));

    $this->addElement('Checkbox', 'include_tablets', array(
      'label' => 'Yes',
      'description' => 'APPTOUCH_Touch mode as default for tablets'
    ));

    $this->addElement('Checkbox', 'scrollajax', array(
      'label' => 'Yes',
      'description' => 'APPTOUCH_Loading activity on scroll down'
    ));

    $this->addElement('Checkbox', 'autoscroll', array(
      'label' => 'Yes',
      'description' => 'APPTOUCH_Loading content on scroll down'
    ));

//    $this->addElement('Checkbox', 'show_username', array(
//      'label' => 'Yes',
//      'description' => 'APPTOUCH_Show user\'s name on Dashboard menu'
//    ));

    $this->addElement('Text', 'cometchat_uri', array(
      'label' => 'Cometchat URI',
      'descrition' => 'If you use cometchat instead of standart chat, this uri will be used!'
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

  }
}
