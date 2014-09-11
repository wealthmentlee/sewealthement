<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_Form_Admin_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create New Step')
      ->setDescription('Here you can create new welcome slide to site visitors.');
    
    
    $this->addElement('Text', 'title', array(
      'label' => 'Slide Title',
      'required' => true,
    ));
    
    $this->addElement('TinyMce', 'body', array(
      'disableLoadDefaultDecorators' => true,
      'label' => 'Text',
      'required' => false,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Censor()
      )
    ));
    
    $this->addElement('File', 'filedata', array(
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'label' => 'Slide Image',
      'required' => true
    ));

    // Slide link
    $this->addElement( 'Text', 'link', array(
      'label' => 'Slide link',
      'description' => 'Define url where user will be directed after clicking the slide.',
      'validators' => array(
        new Zend_Validate_Regex('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i')
      ),
    ));
    //$this->link->setValue('http://');


    // Slideshow Id
    $this->addElement('Hidden','slideshow_id',array(
      'value' => 1,
    ));

  
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}