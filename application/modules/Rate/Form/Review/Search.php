<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2012-12-14 12:34 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Form_Review_Search extends Fields_Form_Search
{
  public function init()
  {
    $this->addAttribs(array('id' => 'reviews_filter_form', 'class' => 'global_form_box'));
    $this->loadDefaultDecorators();

    $this->addElement('Text', 'keyword', array(
      'order' => 1,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->getPageTypeElement();

    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'order' => 3,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }

  public function getPageTypeElement()
  {
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('page', 'profile_type');

    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) )
      return;

    $options = $profileTypeFields['profile_type']->getOptions();

    if( count($options) <= 1 ) {
      return;
    }

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('Select', 'profile_type', array(
      'label' => 'Category',
      'order' => 2,
      'class'=>'field_toggle' .' '. 'parent_0 option_0 field_'.$profileTypeFields['profile_type']->field_id,
      'onchange'=>'changeFields($(this));',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
      ),
      'multiOptions' => $multiOptions,
    ));

    return $this->profile_type;
  }
}