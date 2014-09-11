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
    
class Mobile_Widget_LikeBoxController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
      $this->setNoRender();
      return ;
    }
    
    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $this->view->subject = $subject = Engine_Api::_()->user()->getViewer();
    }

    if (!$subject->getIdentity()) {
      $this->setNoRender();
      return ;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.likes_count', 9);

     $this->view->likes = $likes = Engine_Api::_()->like()->getLikes($subject);
    if (!$likes) {
      $this->setNoRender();
       return ;
    }

    $likes->setItemCountPerPage($ipp);

    if (!Engine_Api::_()->like()->isAllowed($subject)){
      $this->setNoRender();
       return ;
    }

     if (!$likes || $likes->getTotalItemCount() <= 0){
       $this->setNoRender();
       return ;
     }

    if( $this->_getParam('titleCount', false) && $likes->getTotalItemCount() > 0 ) {
      $this->_childCount = $likes->getTotalItemCount();
    }
    
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}