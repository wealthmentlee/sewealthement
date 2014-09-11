<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Form_Comment_Create extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
			->setAction($this->getView()->url(array('controller'=>'comment', 'action'=>'create', 'module'=>'core'), 'default', true));

    //$allowed_html = Engine_Api::_()->getApi('settings', 'core')->core_general_commenthtml;
    // Member Level specific 
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowed_html = "";
    if($viewer->getIdentity()){
      $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
    }
    $this->addElement('Textarea', 'body', array(
      'rows' => 1,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)),
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) {
      $this->addElement('captcha', 'captcha', array(
        'description' => 'Please type the characters you see in the image.',
        'captcha' => 'image',
        'required' => true,
        'captchaOptions' => array(
          'wordLen' => 6,
          'fontSize' => '30',
          'timeout' => 300,
          'imgDir' => APPLICATION_PATH . '/public/temporary/',
          'imgUrl' => $this->getView()->baseUrl().'/public/temporary',
          'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
        )));
    }

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'label' => 'Post Comment',
      'decorators' => array(
        'ViewHelper',
      )
    ));
    
    $this->addElement('Hidden', 'type', array(
      'order' => 990,
      'validators' => array(
        // @todo won't work now that item types can have underscores >.>
        // 'Alnum'
      ),
    ));
  
    $this->addElement('Hidden', 'id', array(
      'order' => 991,
      'validators' => array(
        'Int'
      ),
    ));
  }
}