<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
				'class' => 'global_form_box'
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();
    $path = Engine_Api::_()->getModuleBootstrap('mobile')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
		
    $this->addElement('Text', 'search', array(
			'decorators' => array('ViewHelper'),
			'style'=>'width: 100%;',
    ));
		$this->search->addDecorator('SearchGroup', array('search'=>1));
		
		$this->addElement('Button', 'submit', array(
			'label' => 'Search',
			'type' => 'submit',
			'decorators' => array(
				'ViewHelper',
			),
		));
		$this->submit->addDecorator('SearchGroup', array('submit'=>1));

		$this->addDisplayGroup(array('search', 'submit'), 'elements');
		$this->elements->addDecorator('SearchGroup', array('group'=>1));
  }
}