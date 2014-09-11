<?php

class Welcome_Form_Admin_SlideshowSettings extends Engine_Form
{
  private $slideshow;

  function __construct( $slideshow ){
    $this->slideshow = $slideshow;

    parent::__construct();
  }

  public function init()
  {
    $this->setTitle( 'Slideshow Settings' )
          ->setAttrib( 'class', 'global_form_popup' )
          ->setMethod( 'POST' );

    // Settings
    $effect = $this->slideshow->effect;

    $settings = Engine_Api::_()->getApi('core', 'welcome')->getSettings($effect);


    $order = -10;
    foreach ($settings as $name => $options) {
      $slideshow_setting = $this->slideshow->getSetting( $options['setting_id'] );
      if( $slideshow_setting == null ){
        $slideshow_setting = $this->slideshow->setSetting( $options['setting_id'], $options['value'] );
      }

      $element = array(
        'label' => $options['label'],
        'order' => $order,
        'description' => $options['description'],
        'value' => $slideshow_setting['value'],
      );
      $order++;
      if ($options['type'] == 'select' || $options['type'] == 'radio') {
        $element['multiOptions'] = $options['options'];
      }

      $this->addElement($options['type'], (String)$options['setting_id'], $element);
    }

    // Buttons
    if( $order == -10 ){
      $this->setDescription( "This animation type has no variable settings." );

      $this->addElement( 'Cancel', 'cancel',array(
        'label' => 'Ok',
        //'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array('ViewHelper')
      ));
    }
    else{
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

  public function saveSlideshowSettings()
  {
    $settings = $this->getValues();
    unset( $settings['submit'] );

    $this->slideshow->setSettings( $settings );
  }
}