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
    

class Mobile_Widget_PageProfilePhotoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->mobile()->checkPageWidget($subject_id, 'mobile.page-profile-photo')){
      return $this->setNoRender();
    }
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('page');
  }
}