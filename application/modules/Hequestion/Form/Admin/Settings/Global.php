<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hequestion_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('HEQUESTION_These settings affect all members in your community.');

    $this->addElement('Text', 'perpage', array(
      'label' => 'HEQUESTION_Questions Per Page',
      'description' => 'HEQUESTION_How many questions will be shown per page? (Enter a number between 1 and 999)',
      'validators' => array(
        array('Int', true),
        array('LessThan', true, 999),
        new Engine_Validate_AtLeast(1),
      ),
      'value' => 10,
    ));

    $this->addElement('Text', 'maxoptions', array(
      'label' => 'HEQUESTION_Maximum Options',
      'description' => 'HEQUESTION_How many possible question answers do you want to permit?',
      'value' => 15,
      'validators' => array(
        array('Int', true),
        array('LessThan', true, 100),
        new Engine_Validate_AtLeast(2),
      ),
    ));

/*    $this->addElement('Radio', 'canchangevote', array(
      'label' => 'Change Vote?',
      'description' => 'Do you want to permit your members to change their vote?',
      'multiOptions' => array(
        1 => 'Yes, members can change their vote.',
        0 => 'No, members cannot change their vote.',
      ),
      'value' => false,
    ));*/


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}