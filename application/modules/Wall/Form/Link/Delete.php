<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Link_Delete extends Engine_Form
{
  public function init()
  {
    $this
    	->setTitle('Delete Link')
      ->setMethod('post')
      ->setAttrib('id', 'link_delete_form_info');

    $this
  		->addDecorator('FormElements')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
      ->addDecorator('FormMessages', array('placement' => 'PREPEND'))
      ->addDecorator('FormErrors', array('placement' => 'PREPEND'))
      ->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'form-description', 'escape' => false))
      ->addDecorator('FormTitle', array('placement' => 'PREPEND', 'tag' => 'h3'))
      ->addDecorator('FormWrapper', array('tag' => 'div'))
      ->addDecorator('FormContainer', array('tag' => 'div'))
      ->addDecorator('Form');

    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
  }
}