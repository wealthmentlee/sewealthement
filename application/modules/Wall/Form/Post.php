<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Post.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Post extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->addDecorator('Form')
      ->setAttrib('class', 'activity')
      ->setAttrib('id', 'activity-form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'module' => 'activity',
        'controller' => 'index',
        'action' => 'post'),
      'default'))
    ;
    
    $this->addElement('Textarea', 'body', array(
      'id' => 'activity-post-body',
      //'value' => 'Post Something...',
      'alt' => 'Post Something...',
      //'required' => true,
      'rows' => '1',
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
      //'onfocus' => "document.getElementById('activity-submit').style.display = 'block';this.value = '';",
      //'onblur' => "if( this.value == '' ) { document.getElementById('activity-submit').style.display = 'none';this.value = 'Post Something...'; }",
    ));

    $this->addElement('Select','cat',array(
        'Label' => 'Category',
        'multiOptions' => array(
            '0' => 'All',
            '1' => 'Stocks',
            '2' => 'Real Estate',
            '3' => 'Retirement',
            '4' => 'Others'
        )
    ));
/*
    if (Engine_Api::_()->user()->getViewer()->level_id == 1){
      $this->getElement('body')->removeFilter('HtmlSpecialChars');
    }
*/

    $submit = new Engine_Form_Element_Button('submitme', array(
    ));
    $this->addElement('Button', 'submitme', array(
      'type' => 'submit',
      'label' => 'Post',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag2', array('tag' => 'div')),
        array('HtmlTag', array('tag' => 'div', 'id' => 'activity-post-submit')),
      )
    ));
    
    $this->addElement('hidden', 'subject');

    $this->addElement('hidden', 'return_url', array(
        'order' => 990,
        'value' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array())
    ));

    $this->addElement('Hidden', 'attachment_type', array(
      'order' => 991,
      'validators' => array(
        // @todo make validator for this
        //'Alnum'
      )
    ));

    $this->addElement('Hidden', 'attachment_id', array(
      'order' => 992,
      'validators' => array(
        'Int'
      )
    ));
  }

  public function setActivityObject(Core_Model_Item_Abstract $object)
  {
    $this->subject->setValue($object->getGuid(false));
    return $this;
  }
}

