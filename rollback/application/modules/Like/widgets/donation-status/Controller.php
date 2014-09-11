<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       14.08.12
 * @time       14:04
 */
class Like_Widget_DonationStatusController extends Engine_Content_Widget_Abstract
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

    $this->view->is_owner = $viewer->isOwner($subject);

    if (isset($subject->parent_id) && !empty($subject->parent_id)) {
      $this->view->parent = Engine_Api::_()->getItem('donation', $subject->parent_id);
    }
  }
}
