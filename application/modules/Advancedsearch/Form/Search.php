<?php
/***/
class Advancedsearch_Form_Search extends Engine_Form {
  public function init() {
    parent::init();
    $this->setTitle('Search');
    $this->addElement('Text', 'query', array(
      'decorators' => array(
        'ViewHelper',
      ),
      'attribs' => array(
        'style' => 'padding: 5px 6px 5px 105px;width: 200px;'
      )
    ));
    $this->addElement('Hidden', 'type', array(
      'value' => 1
    ));
  }
}