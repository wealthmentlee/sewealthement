<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 22.05.13
 * Time: 10:29
 * To change this template use File | Settings | File Templates.
 */

class Hashtag_Form_Admin_Settings_Count extends Engine_Form {
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Count settings');

    // Element: view
    $this->addElement('Radio', 'count', array(
      'label' => 'Top count hashtags?',
      'multiOptions' => array(
        5 => 'Top 5.',
        10 => 'Top 10',
        15 => 'Top 15',
      ),

    ));
    $this
      ->setTitle('Count settings');
    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));
  }
}