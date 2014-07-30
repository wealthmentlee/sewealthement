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
    
class Mobile_Widget_PageProfileAlbumController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
  protected $_widget_url;
	
  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->mobile()->checkPageWidget($subject_id, 'mobile.page-profile-album')){
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')){
      return $this->setNoRender();
    }

    $this->_widget_url = $this->view->url(array(
      'action' => 'index',
      'page_id' => $subject->getIdentity()
    ),'page_album');

    $albums = $this->getTable()->getAlbums(array('page_id' => $subject->getIdentity(), 'ipp' => 10, 'p' => 1));

    if ($this->_getParam('titleCount', false) && $albums->getTotalItemCount() > 0){
      $this->_childCount = $albums->getTotalItemCount();
    }

  }

  public function getApi()
  {
		return $this->api = Engine_Api::_()->getApi('core', 'pagealbum');
  }
  
	public function getTable()
  {
  	return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
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