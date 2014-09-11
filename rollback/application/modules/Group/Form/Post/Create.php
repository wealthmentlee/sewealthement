<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Create.php 9802 2012-10-20 16:56:13Z pamela $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Form_Post_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Reply')
      ->setAction(
        Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(array('action' => 'post', 'controller' => 'topic'), 'group_extended', true)
      );

    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $allowHtml = (bool) $settings->getSetting('group_html', 0);
    $allowBbcode = (bool) $settings->getSetting('group_bbcode', 0);
    
    if( !$allowHtml ) {
      $filter = new Engine_Filter_HtmlSpecialChars();
    } else {
      $filter = new Engine_Filter_Html();
      $filter->setForbiddenTags();
      $allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'group', 'commentHtml')));
      $filter->setAllowedTags($allowed_tags);
    }
    
    if ( $allowHtml || $allowBbcode ) {
      $this->addElement('TinyMce', 'body', array(
        'disableLoadDefaultDecorators' => true,
        'required' => true,
        'allowEmpty' => false,
        'decorators' => array(
          'ViewHelper'
        ),
        'editorOptions' => array(
          'bbcode' => (bool) $allowBbcode,
          'html' => (bool) $allowHtml,
        ),
        'filters' => array(
          new Engine_Filter_Censor(),
        )
      ));        
    } else {    
      $this->addElement('Textarea', 'body', array(
        'label' => 'Body',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
          new Engine_Filter_Censor(),
          new Engine_Filter_HtmlSpecialChars(),
          //new Engine_Filter_EnableLinks(),
        ),          
      ));
    }

    $this->addElement('Checkbox', 'watch', array(
      'label' => 'Send me notifications when other members reply to this topic.',
      'value' => '1',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Post Reply',
      'ignore' => true,
      'type' => 'submit',
    ));

    $this->addElement('Hidden', 'topic_id', array(
      'order' => '920',
      'filters' => array(
        'Int'
      )
    ));
    
    $this->addElement('Hidden', 'ref');
  }
}