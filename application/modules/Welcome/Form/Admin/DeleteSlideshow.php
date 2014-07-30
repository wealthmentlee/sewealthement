
<?php

class Welcome_Form_Admin_DeleteSlideshow extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Delete Slideshow')
        ->setDescription('Are you sure you want to delete this slideshow?')
        ->setAttrib('class','global_form_popup')
        ->setMethod('POST');


    $this->addElement('Button','submit',array(
      'label' => 'Delete Slideshow',
      'type' => 'submit',
      'decorators' => array('ViewHelper')
    ));


    $this->addElement('Cancel','cancel',array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array('ViewHelper')
    ));


    $this->addDisplayGroup(array('submit','cancel'),'buttons');
  }

}

?>