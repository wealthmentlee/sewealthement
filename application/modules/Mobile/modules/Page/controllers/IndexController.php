<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Page_IndexController extends Core_Controller_Action_Standard
{
	public function init() 
  {
    
  }

  public function getNavigation()
  {
    $navigation = new Zend_Navigation();
    $navigation->addPages(array(
      array(
        'label' => "Pages",
        'route' => 'page_browse',
        'action' => 'index'
      )));

    $viewer = Engine_Api::_()->user()->getViewer();
    
    if ($viewer->getIdentity()) {
      $navigation->addPage(array(
          'label' => 'My Pages',
          'route'=> 'page_manage',
          'action' => 'manage'
        ));
        
    }
    
    return $navigation;
  }

  public function indexAction()
  {
		$page_num = $this->_getParam('page', 1);
  	$table = Engine_Api::_()->getDbTable('pages', 'page');
  	$settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->formFilter = $formFilter = new Mobile_Form_Search();
    $ipp = $settings->getSetting('page.browse_count', 10);

    $this->view->params = $params = array();

    $this->view->user = $user = (int)$this->_getParam('user');
    $this->view->search = $search = $this->_getParam('search');

    $formFilter->search->setValue($search);
    $formFilter->setAction($this->view->url(array(
      'route' => 'page_browse',
    )) . '?user=' . $user );

    if ($user){
      $params['where'] = 'user_id='.$user;
      $this->view->userObj = Engine_Api::_()->user()->getUser($user);
    }
    if ($search){
      $params['keyword'] = $search;
    }

    $this->view->page_num = $page_num;

    $select = $table->getSelect($params);
    $select->order('featured DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    $this->view->count = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

		$page_ids = array();
		foreach ($paginator as $page){
			$page_ids[] = $page->getIdentity();
		}

		$this->view->page_tags = Engine_Api::_()->page()->getPageTags($page_ids);
		$this->view->page_likes = Engine_Api::_()->like()->getLikesCount('page', $page_ids);

		$this->view->navigation = $navigation = $this->getNavigation();
  }


  public function manageAction()
  {
  	if ( !$this->_helper->requireUser->isValid() ) return ;
  	
  	$this->view->navigation = $navigation = $this->getNavigation();

    $this->view->formFilter = $formFilter = new Mobile_Form_Search();

    $this->view->search = $search = $this->_getParam('search');
    $formFilter->search->setValue($search);
  	
    $viewer = $this->_helper->api()->user()->getViewer();
    $table = $this->_helper->api()->getDbtable('pages', 'page');
    
    $select = $table->select();
    $select->where('user_id = ?', $viewer->getIdentity());
    $this->view->owner = $owner = Engine_Api::_()->user()->getViewer();
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('page.browse_count', 10);

    $paginator->setItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }
	
	public function viewAction() 
	{
    $content_type = $this->_getParam('content');
    $content_id = (int)$this->_getParam('content_id');

    if ($content_type == 'pagealbum')
    {
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'album_id' => $content_id
      ), 'page_album'), array('prependBase' => false));
    }
    elseif ($content_type == 'pagealbumphoto')
    {
      $this->_redirect($this->view->url(array(
        'action' => 'view-photo',
        'photo_id' => $content_id
      ), 'page_album'), array('prependBase' => false));
    }
    elseif ($content_type == 'blog'){
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'blog_id' => $content_id
      ), 'page_blog'), array('prependBase' => false));
    }
    elseif ($content_type == 'page_event')
    {
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'event_id' => $content_id
      ), 'page_event'), array('prependBase' => false));
    }
    elseif ($content_type == 'review'){
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'review_id' => $content_id
      ), 'page_review'), array('prependBase' => false));
    }

		$page = $this->_getParam('page', $this->_getParam('page_id'));
		
		if ($page == null){
			$this->_redirectCustom(array('route' => 'page_browse'));
  		return ;
		}
		
		$pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $subject = null;

    $id = $this->_getParam('id', $this->_getParam('page_id'));
    if( null !== $id )
    {
		$select = $pageTable->select()->where('url = ?', $page);
		$subject = $pageObject = $pageTable->fetchRow($select);
    if( $subject && $subject->getIdentity() )
    {
      $subject->setContentInfo($content, $content_id);
      Engine_Api::_()->core()->setSubject($subject);
    }
    }

		
		if ($pageObject == null){
			$this->_redirectCustom(array('route' => 'page_browse'));
  		return ;
		}
		
//		$this->_helper->requireSubject('page');
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$this->_helper->requireSubject()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($pageObject, $viewer, 'view')->isValid() ) return;
		
		$pageObject->viewPage();
		$pageObject->description = stripslashes($pageObject->description);
		
    $content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'mobile');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;


		if (null !== $pageObject){

			return;
		}

		throw new Zend_Controller_Exception(sprintf('Page %s does not exist', $page), 404);
	}

}