<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Performance.php 2011-12-14 14:06:00 ulan $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Form_Admin_Settings_Performance extends Engine_Form
{
  public function init()
  {
    // Set form attributes
    $this->setTitle('Performance & Caching');
    $this->setDescription(strtoupper(get_class($this) . '_description'));

    // disable form if not in production mode
    $attribs = array();
    if (APPLICATION_ENV != 'production') {
      //$attribs = array('disabled' => 'disabled', 'readonly' => 'readonly');
      $this->addError('APPTOUCH_Note: Your site is in development mode. So all previous client side caches will be cleared at each page refresh.');
    }

    $this->addElement('Radio', 'enable', array(
      'label' => 'APPTOUCH_Use Client Side Cache?',
      'description' => strtoupper(get_class($this) . '_enable_description'),
      'required' => true,
      'multiOptions' => array(
        1 => 'Yes, enable caching.',
        0 => 'No, do not enable caching.',
      ),
      'attribs' => $attribs,
    ));

    $this->addElement('Text', 'min_lifetime', array(
      'label' => 'APPTOUCH_Cache Minimum Lifetime',
      'description' => strtoupper(get_class($this) . '_min_lifetime_description'),
      'size' => 5,
      'maxlength' => 4,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('Int'),
      ),
      'attribs' => $attribs,
    ));

    $this->addElement('Text', 'max_lifetime', array(
      'label' => 'APPTOUCH_Cache Maximum Lifetime',
      'description' => strtoupper(get_class($this) . '_max_lifetime_description'),
      'size' => 5,
      'maxlength' => 4,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('Int'),
      ),
      'attribs' => $attribs,
    ));

    $this->addElement('Radio', 'type', array(
      'label' => 'Caching Feature',
      'description' => strtoupper(get_class($this) . '_TYPE_DESCRIPTION'),
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        'auto' => 'Auto',
        'local' => 'On Local Device',
        'session' => 'On Every Session (Recommended)',
      ),
      'attribs' => $attribs,
    ));

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'attribs' => $attribs,
    ));

  }

  public function populate($current_cache = array())
  {

    $this->getElement('enable')->setValue(true);
    if (isset($current_cache['enable']))
      $this->getElement('enable')->setValue($current_cache['enable']);

    if (isset($current_cache['type'])) {
      $this->getElement('type')->setValue($current_cache['type']);
    }

    if (isset($current_cache['min_lifetime'])) {
      $minlifetime = $current_cache['min_lifetime'];
    } else {
      $minlifetime = 30; // 30 seconds
    }
    if (isset($current_cache['max_lifetime'])) {
      $maxlifetime = $current_cache['max_lifetime'];
    } else {
      $maxlifetime = 6000; // 10 minutes
    }
    $this->getElement('min_lifetime')->setValue($minlifetime);
    $this->getElement('max_lifetime')->setValue($maxlifetime);
  }
}