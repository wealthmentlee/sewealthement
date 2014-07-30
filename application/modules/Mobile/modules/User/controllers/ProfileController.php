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
    
class User_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('id');

      // use viewer ID if not specified
      //if( is_null($id) )
      //  $id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if( null !== $id )
      {
        $subject = Engine_Api::_()->user()->getUser($id);
        if( $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('user');
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

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    // Check enabled
    if( !$subject->isEnabled() && !($viewer->isAdmin()))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Check block
    if( $viewer->isBlockedBy($subject) )
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Increment view count
    if( !$subject->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    
    // Check to see if profile styles is allowed
    $style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $subject->level_id, 'style');
    if($style_perm){
      // Get styles
      $table = Engine_Api::_()->getDbtable('styles', 'core');
      $select = $table->select()
        ->where('type = ?', $subject->getType())
        ->where('id = ?', $subject->getIdentity())
        ->limit();

      $row = $table->fetchRow($select);
      if( null !== $row && !empty($row->style) )
      {
        $this->view->headStyle()->appendStyle($row->style);
      }
    }

		$content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'mobile');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);
		
    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled();
  }
  
}