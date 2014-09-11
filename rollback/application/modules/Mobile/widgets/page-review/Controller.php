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
    

class Mobile_Widget_PageReviewController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;
  protected $_widget_url;

  public function indexAction(){

    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->mobile()->checkPageWidget($subject_id, 'mobile.page-review')){
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->page_id = $page_id = $subject->getIdentity();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();


    $this->_widget_url = $this->view->url(array(
      'action' => 'index',
      'page_id' => $subject->getIdentity()
    ),'page_review');

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $this->view->paginator = $paginator = $tbl->getPaginator($page_id, $viewer->getIdentity(), $this->_getParam('page'));

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0){
      $this->_childCount = $this->view->paginator->getTotalItemCount();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getHref()
  {
    return $this->_widget_url;
  }

}