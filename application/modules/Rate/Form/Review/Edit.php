<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-07-02 19:27 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Form_Review_Edit extends Engine_Form
{

  public function init(){

    $module_path = Engine_Api::_()->getModuleBootstrap('rate')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this
        ->setTitle('RATE_REVIEW_EDIT_TITLE')
        ->setDescription('RATE_REVIEW_EDIT_DESCRIPTION')
        ->setAttrib('onsubmit', 'return Review.editSubmit(this);');

    $this->addElement('Text', 'title', array(
      'label' => 'RATE_REVIEW_EDITFORM_TITLE',
      'order' => 990,
      'required' => true,
      'filters' => array(
        'StripTags',
      )
    ));
    $this->addElement('Textarea', 'body', array(
      'label' => 'RATE_REVIEW_EDITFORM_BODY',
      'order' => 991,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars()
      ),
    ));

    $this->addElement('Hidden', 'pagereview_id', array('order' => 992));

    $this->addElement('Button', 'submit', array(
      'label' => 'RATE_REVIEW_EDITFORM_SUBMIT',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onClick'=> 'javascript:Review.list();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'order' => 993,
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

  }

  public function addVotes($types){

    $counter = 0;
    $js = array();
    foreach ($types as $type){
      $name  = 'rate_'.$type->getIdentity();
      $uid = rand(11111, 99999);
      $container = 'review_starts_'.$uid;
      $this->addElement('Hidden', $name, array(
        'label' => $type->label,
        'value' => $type->value,
        'order' => $counter
      ));
      $this->getElement($name)->addDecorator('ReviewRate', array('container'=>$container));
      $js[] = 'new ReviewRate("'.$container.'");';
      $counter++;
    }
    return $js;

  }

}