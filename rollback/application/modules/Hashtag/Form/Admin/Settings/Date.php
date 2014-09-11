<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 03.05.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Hashtag_Form_Admin_Settings_Date extends Engine_Form {
  public function init()
  {
    // My stuff
    $this
      ->setTitle("TITLE_HASHTAG_ADMIN")->setDescription('DESCRIPTION_ADMIN_HASHTAG');

    $this->addElement('Text', 'period', array(
      'label' => 'TOP_HASHTAG_DAYS',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '100'))
      ),

    ));

    $this->addElement('Text', 'count', array(
      'label' => 'TOP_HASHTAG_COUNT',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '100'))
      ),

    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));

  }


}