<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ProfileController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Group_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('group', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('group');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $subject,
      Engine_Api::_()->user()->getViewer(),
      'view'
    );
  }

  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $subject->getType())
      ->where('id = ?', $subject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) ) {
      $this->view->headStyle()->appendStyle($row->style);
    }

		$content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'mobile');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
}