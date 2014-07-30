<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_LikeStatusController extends Engine_Content_Widget_Abstract
{	
  public function indexAction()
  {
    $this->view->module_enabled = $module_enabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like');

    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!$module_enabled){
      return ;
    }

    $this->view->is_liked = Engine_Api::_()->like()->isLike($subject);
    $this->view->is_enabled = (bool)( $viewer->getIdentity() && Engine_Api::_()->like()->isAllowed($subject));

    $this->view->auth = ( $subject->authorization()->isAllowed(null, 'view') );
    $this->view->is_owner = ($subject->getType() == 'page') ? $subject->isTeamMember($viewer) : $viewer->isOwner($subject);
  }
}