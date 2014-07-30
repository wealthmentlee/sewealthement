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
    
class Blog_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // only show to member_level if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid() ) return;
  }

  public function indexAction()
  {
    // Enable content helper?
    //$this->_helper->content->setEnabled();
    
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('blog_main');

    // Prepare data
    $viewer = $this->_helper->api()->user()->getViewer();
    //if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid()) return;
    
    $this->view->form = $form = new Mobile_Form_Search();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

		// Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $this->view->formValues = array_filter($values);
    $values['draft'] = "0";
    $values['visible'] = "1";

    $this->view->userObj = ($values['user']) ? Engine_Api::_()->user()->getUser($values['user']) : null;

    //die($this->_getParam('page',1)."");
    $this->view->assign($values);

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogCatsTbl = Engine_Api::_()->getDbtable('categories', 'blog');

    if (method_exists($blogApi, 'getBlogsPaginator')) {
      $paginator = $blogApi->getBlogsPaginator($values);
    } else {
      $paginator = $blogsTbl->getBlogsPaginator($values);
    }

    $paginator->setItemCountPerPage(5);

    $this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page', 1));

    if (!empty($values['category'])) {
      if (method_exists($blogApi, 'getCategory')) {
        $this->view->categoryObject = $blogApi->getCategory($values['category']);
      } else {
        $this->view->categoryObject = $blogCatsTbl->find($values['category'])->current();
      }
    }
  }
  
  public function viewAction()
  {
    // Check permission
    $viewer = $this->_helper->api()->user()->getViewer();
    $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));

		if( $blog ) {
      Engine_Api::_()->core()->setSubject($blog);
    }

    if( !$this->_helper->requireSubject()->isValid() ) return;
  	if( !$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'view')->isValid()) return;

    // Prepare data
    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');

    // Get paginator
    if (method_exists($blogApi, 'getArchiveList')) {
      $archiveList = $blogApi->getArchiveList($blog->owner_id);
    } else {
      $archiveList = $blogsTbl->getArchiveList($blog->owner_id);
    }

    $this->view->archive_list = $this->_handleArchiveList($archiveList);
    $this->view->viewer = $viewer;
    $blog->view_count++;
    $blog->save();

    $this->view->blog = $blog;

    $this->view->blogTags = $blog->tags()->getTagMaps();
    $this->view->userTags = $blog->tags()->getTagsByTagger($blog->getOwner());
    //$this->view->blogTags = Engine_Api::_()->blog()->getBlogTags($blog_id);
    //$this->view->userTags = Engine_Api::_()->blog()->getUserTags($blog->owner_id);

    // Prepare data
    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogCatsTbl = Engine_Api::_()->getDbtable('categories', 'blog');

    if ($blog->category_id != 0) {
      if (method_exists($blogApi, 'getCategory')) {
        $this->view->category = $blogApi->getCategory($blog->category_id);
      } else {
        $this->view->category = $blogCatsTbl->find($blog->category_id)->current();
      }
    }

    if (method_exists($blogApi, 'getUserCategories')) {
      $this->view->userCategories = $blogApi->getUserCategories($this->view->blog->owner_id);
    } else {
      $this->view->userCategories = $blogCatsTbl->getUserCategoriesAssoc($this->view->blog->owner_id);
    }

    // Get styles
    $this->view->owner = $user = $blog->getOwner();
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', 'user_blog')
      ->where('id = ?', $user->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) )
    {
      $this->view->headStyle()->appendStyle($row->style);
    }

  }

  // USER SPECIFIC METHODS
  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('blog_main');

    // Prepare data
    $viewer = $this->_helper->api()->user()->getViewer();

    $this->view->form = $form = new Mobile_Form_Search();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');

    // Get paginator
    if (method_exists($blogApi, 'getBlogsPaginator')) {
      $this->view->paginator = $paginator = $blogApi->getBlogsPaginator($values);
    } else {
      $this->view->paginator = $paginator = $blogsTbl->getBlogsPaginator($values);
    }

    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
    $paginator->setItemCountPerPage(5);
    $this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page', 1) );
  }

  public function listAction()
  {
    // Preload info
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));

    // Make form
    $this->view->form = $form = new Blog_Form_Search();
    $form->removeElement('show');

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogCatsTbl = Engine_Api::_()->getDbtable('categories', 'blog');

    // Populate form
    if (method_exists($blogApi, 'getCategories')) {
      $this->view->categories = $categories = $blogApi->getCategories();
    } else {
      $this->view->categories = $categories = $blogCatsTbl->getCategoriesAssoc();
    }

    foreach( $categories as $category ) {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }
		
    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $owner->getIdentity();
    $values['draft'] = "0";
    $values['visible'] = "1";


    $this->view->assign($values);

    // Get paginator
    if (method_exists($blogApi, 'getBlogsPaginator')) {
      $this->view->paginator = $paginator = $blogApi->getBlogsPaginator($values);
    } else {
      $this->view->paginator = $paginator = $blogsTbl->getBlogsPaginator($values);
    }

    $paginator->setItemCountPerPage(5);
    $this->view->paginator = $paginator->setCurrentPageNumber( $values['page'] );

    $this->view->userTags = Engine_Api::_()->getDbtable('tags', 'core')->getTagsByTagger('blog', $owner);

    if (method_exists($blogApi, 'getUserCategories')) {
      $this->view->userCategories = $blogApi->getUserCategories($owner->getIdentity());
    } else {
      $this->view->userCategories = $blogCatsTbl->getCategoriesAssoc($owner->getIdentity());
    }
  }

  public function deleteAction()
  {
    // Check permissions
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->blog = $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));
		$this->view->return_url = $return_url = $this->_getParam('return_url');

    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'delete')->isValid() ) return;
	
    // Check post/form
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $table = $blog->getTable();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $blog->delete();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

		$this->_forward('success', 'utility', 'mobile', array(
			'messages'=>$this->view->translate('The blog successfully has been deleted'),
			'return_url'=>urldecode($this->_getParam('return_url')),
		));
  }
	
  protected function _handleArchiveList($results)
  {
    $localeObject = Zend_Registry::get('Locale');

    $blog_dates = array();
    foreach ($results as $result)
      $blog_dates[] = strtotime($result->creation_date);

    // GEN ARCHIVE LIST
    $time = time();
    $archive_list = array();

    foreach( $blog_dates as $blog_date )
    {
      $ltime = localtime($blog_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      // LESS THAN A YEAR AGO - MONTHS
      if( $blog_date+31536000>$time )
      {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"]+1, 1, $ltime["tm_year"]);
        //$label = date('F Y', $blog_date);
        $type = 'month';

        $dateObject = new Zend_Date($blog_date);
        $format = $localeObject->getTranslation('MMMMd', 'dateitem', $localeObject);
        $label = $dateObject->toString($format, $localeObject);
      }

      // MORE THAN A YEAR AGO - YEARS
      else
      {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]+1);
        //$label = date('Y', $blog_date);
        $type = 'year';

        $dateObject = new Zend_Date($blog_date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if( !$format ) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if( !isset($archive_list[$date_start]) )
      {
        $archive_list[$date_start] = array(
          'type' => $type,
          'label' => $label,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'count' => 1
        );
      }
      else
      {
        $archive_list[$date_start]['count']++;
      }
    }

    //krsort($archive_list);
    return $archive_list;
  }	
}

