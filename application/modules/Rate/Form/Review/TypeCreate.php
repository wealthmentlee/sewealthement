<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TypeCreate.php 2010-07-02 19:27 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Form_Review_TypeCreate extends Engine_Form
{

  public function init(){

    $this
        ->setTitle('RATE_REVIEW_TYPECREATE_TITLE')
        ->setDescription('RATE_REVIEW_TYPECREATE_DESCRIPTION')
        ->addAttribs(array('class' => 'he_review_typecreate'));

    $this->addElement('Text', 'label', array(
      'label' => 'RATE_REVIEW_TYPECREATEFORM_LABEL',
      'required' => true,
      'validators' => array(array('NotEmpty', true))
    ));

    $this->addElement('Hidden', 'category',  array('order' => 990));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'order' => 991,
      'ignore' => true
    ));

  }

}