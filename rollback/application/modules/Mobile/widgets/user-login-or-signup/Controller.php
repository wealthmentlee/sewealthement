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
    
class Mobile_Widget_UserLoginOrSignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Do not show if logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
      $this->setNoRender();
      return;
    }

    // Display form
    $form = $this->view->form = new Mobile_Form_Login();;
    $form->setTitle(null)->setDescription(null);
    $form->removeElement('forgot');

    $form->removeElement('facebook');

  }
  
  public function getCacheKey()
  {
    return false;
  }
}