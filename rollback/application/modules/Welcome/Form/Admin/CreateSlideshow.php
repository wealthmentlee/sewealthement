
<?php


class Welcome_Form_Admin_CreateSlideshow extends Engine_Form
{
  public function init()
  {
    $this->setTitle( 'Create Slideshow' )
         ->setAttrib('class','global_form_popup')
         ->setMethod( 'POST' );

    // Add title textfield
    $this->addElement('Text','title',array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength( array( 'max' => '150') )
      )
    ));


    // Animation type
    $table = Engine_Api::_()->getDbTable('effects','welcome');
    $effects = $table->fetchAll( $table->select() );

    $options = array();
    //$options[''] = 'None';
    foreach( $effects as $effect ){
      $options[ $effect->value ] = $effect->label;
    }

    $this->addElement( 'Select', 'animation', array(
      'label' => 'Animation Type',
      'type' => 'select',
      'multiOptions' => $options,
    ));
    $this->animation->setAttrib( 'id', 'animation_type' );


    // Width,height
    $this->addElement( 'Text', 'width', array(
      'label' => 'Width',
      'required' => true,
      'validators' => array(
        new Zend_Validate_Digits(),
      ),
    ));
    $this->addElement( 'Text', 'height', array(
      'label' => 'Height',
      'required' => true,
      'validators' => array(
        new Zend_Validate_Digits(),
      ),
    ));


    // Buttons
    $this->addElement('Button','submit',array(
      'label' => 'Submit',
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