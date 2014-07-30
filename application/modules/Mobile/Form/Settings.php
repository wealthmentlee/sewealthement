<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Form_Settings extends Engine_Form
{

  public function init()
  {
    $this
      ->setTitle('MOBILE_SETTING_TITLE')
      ->setDescription('MOBILE_SETTING_DESCRIPTION');

    $this->addElement('Checkbox', 'mobile_show_rate_browse', array(
      'label' => 'MOBILE_SHOW_RATE_BROWSE_LABEL',
      'description' => 'MOBILE_SHOW_RATE_BROWSE',
    ));
    $this->addElement('Checkbox', 'mobile_show_rate_widget', array(
      'label' => 'MOBILE_SHOW_RATE_WIDGET_LABEL',
      'description' => 'MOBILE_SHOW_RATE_WIDGET'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));



  }

}