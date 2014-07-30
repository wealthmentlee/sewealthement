<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Share.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Share extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Share')
      ->setDescription('Share this by re-posting it with your own message.')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    
    $this->addElement('Textarea', 'body', array(
      //'required' => true,
      //'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Share',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $buttons[] = 'cancel';

    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}