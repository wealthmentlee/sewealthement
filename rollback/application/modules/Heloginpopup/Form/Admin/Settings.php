<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 24.09.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heloginpopup_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $this->setTitle('')
      ->setDescription('');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $this->addElement('Text', 'maxday', array(
      'Label' => 'Max Days',
      'description' => 'How often Login Popup appears in the site.',
      'value' => $settings->getSetting('heloginpopup.max.day', 30),
      'filters'  => array(
        array('name' => 'Int'),
      ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit'
    ));
  }
}