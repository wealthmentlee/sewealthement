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


class Mobile_Widget_PageProfileAdminsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->mobile()->checkPageWidget($subject_id, 'mobile.page-profile-admins')){
      return $this->setNoRender();
    }

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('page');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Prepare data
    $this->view->viewer = $viewer;
    $this->view->page = $page = Engine_Api::_()->core()->getSubject(); 
    $this->view->admins = $admins = $page->getAdmins(6);
    $paginator = $admins;

    // Do not render if nothing to show and no search
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}