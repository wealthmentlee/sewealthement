
<?php

class Welcome_Form_Widget_Slideshow extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
    parent::init();

    $slideshows = Engine_Api::_()->getDbTable( 'slideshows', 'welcome' )->getArray();

    $this->addElement('Select','slideshow_id',array(
      'label' => 'Slideshow',
      'multiOptions' => $slideshows
    ));
  }
}

?>