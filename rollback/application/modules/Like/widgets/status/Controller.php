<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Widget_StatusController extends Engine_Content_Widget_Abstract
{	
  public function indexAction()
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      $this->setNoRender();
    }
    
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->is_enabled = (bool)( $viewer->getIdentity());
    $this->view->is_allowed = (bool)(Engine_Api::_()->like()->isAllowed($subject));

    $this->view->auth = ( $subject->authorization()->isAllowed(null, 'view') );
    $this->view->is_owner = ($subject->getType() == 'page') ? $subject->isTeamMember($viewer) : $viewer->isOwner($subject);

  }
}