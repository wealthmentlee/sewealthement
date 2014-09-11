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
    
class Mobile_Widget_PageProfileBlogController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
  protected $_widget_url;
	
  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->mobile()->checkPageWidget($subject_id, 'mobile.page-profile-blog')){
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')){
      return $this->setNoRender();
    }

    $this->_widget_url = $this->view->url(array(
      'module' => 'pageblog',
      'controller' => 'index',
      'action' => 'index',
      'page_id' => $subject->getIdentity()
    ),'default');

   	$this->view->blogs = $blogs = $this->getTable()->getBlogs(array('page_id' => $subject->getIdentity(), 'ipp' => 10, 'p' => 1));
  	
  	if ($this->_getParam('titleCount', false) && $blogs->getTotalItemCount() > 0){
      $this->_childCount = $blogs->getTotalItemCount();
    }

  }
  
  public function getApi()
  {
		return $this->api = Engine_Api::_()->getApi('core', 'pageblog');
  }
  
	public function getTable()
  {
  	return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
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