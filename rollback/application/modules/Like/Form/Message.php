<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Message.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Form_Message extends Engine_Form
{
	public function init()
	{
		$this
      ->setTitle('like_Send an Update')
      ->setDescription('like__LIKE_SEND_UPDATE_DESC_')
      ->setAttrib('id', 'page_send_update')
      ->loadDefaultDecorators();
      
    $this->addElement('Text', 'title', array(
      'label' => 'Subject',
    	'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      )
    ));
    
    $this->addElement('Textarea', 'body', array(
      'label' => 'Message',
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      )
    ));
    
    $this->addElement('Hidden', 'object', array('order' => 990));
    $this->addElement('Hidden', 'object_id', array('order' => 991));
    $this->addElement('Hidden', 'format', array('value' => 'smoothbox'));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'like_Send',
      'type' => 'submit',
      'ignore' => true
    ));
	}
}