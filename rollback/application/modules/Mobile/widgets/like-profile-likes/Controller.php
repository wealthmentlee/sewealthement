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
    
class Mobile_Widget_LikeProfileLikesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
      $this->setNoRender();
      return ;
    }

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $this->view->subject = $subject = $viewer;
    }

    if ($subject->getType() != 'user') {
      $this->setNoRender();
      return ;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'interest')) {
      $this->setNoRender();
      return ;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.profile_count', 9);

    $items = array();


    $paginator = Engine_Api::_()->like()->getLikedItems($subject);

    foreach ($paginator as $data) {
      $item = Engine_Api::_()->getItem($data->getType(), $data->getIdentity());
      if (!$item){
        continue ;
      }
      $items[] = $item;
    }

    $this->view->items = $items;
    shuffle($this->view->items);

    $this->view->total = $total = Engine_Api::_()->like()->getLikedCount($subject);

    if( $this->_getParam('titleCount', false) && $total > 0 ) {
      $this->_childCount = $total;
    }

    $this->view->ipp = $ipp;

    if (!$total || !count($this->view->items)) {
      //$this->setNoRender();
      return ;
    }
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}