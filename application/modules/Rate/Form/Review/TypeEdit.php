<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TypeEdit.php 2010-07-02 19:27 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Form_Review_TypeEdit extends Engine_Form
{

  public function init(){

    $module_path = Engine_Api::_()->getModuleBootstrap('rate')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this
        ->setTitle('RATE_REVIEW_TYPEEDIT_TITLE')
        ->setDescription('RATE_REVIEW_TYPEEDIT_DESCRIPTION')
        ->addAttribs(array('class' => 'he_review_typeedit'));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'order' => 990,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

  }

}