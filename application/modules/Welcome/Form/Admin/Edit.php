<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_Form_Admin_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Step')
      ->setDescription('Edit welcome step.');
    
    
    $this->addElement('Text', 'title', array(
      'label' => 'Slide Title'
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
    
    $this->addElement('Hidden', 'step');
    
    $this->addElement('File', 'filedata', array(
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'label' => 'Slide Image'
    ));

    // Slide link
    $this->addElement( 'Text', 'link', array(
      'label' => 'Slide link',
      'description' => 'Define url where user will be directed after clicking the slide.',
      'validators' => array(
        new Zend_Validate_Regex('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i')
      ),
    ));

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