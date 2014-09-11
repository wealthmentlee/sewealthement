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
    
class Event_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init() 
  {
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid() ) return;
    //$this->getNavigation();
    
    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if( $id )
    {
      $event = Engine_Api::_()->getItem('event', $id);
      if( $event )
      {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
  }

  public function browseAction()
  {
    $filter = $this->_getParam('filter', 'future');
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('event_main');

		foreach ($navigation->getPages() as $page)
    {
      if( ($page->label == "Upcoming Events" && $filter == "future") || ($page->route == "event_past" && $filter == "past")) {
			$page->active = true;
      }
    }

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('event_quick');

    // Create form
    $this->view->formFilter = $formFilter = new Mobile_Form_Search();

    $this->view->user = $user = $this->_getParam('user');
    $this->view->group = $group = (int)$this->_getParam('group');

    $formFilter->addElement('Hidden', 'user', array('value' => $user));

    $formFilter->setAction($this->view->url(array(
      'action' => ($filter == "past") ? 'past' : 'upcoming',
      'group' => $group,
    ), 'event_general', true));


    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    $this->view->assign($values);

    // Prepare data
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->formValues = $values = $formFilter->getValues();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    $values['search'] = 1;

    if( $filter == "past" )
    {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

     // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    $eventApi = Engine_Api::_()->getApi('core', 'event');
    if (method_exists($eventApi, 'getEventSelect')) {
      $select = Engine_Api::_()->event()->getEventSelect($values);
    } else {
      $eventsTbl = Engine_Api::_()->getDbTable('events', 'event');
      $select = $eventsTbl->getEventSelect($values);
    }

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)){
      $select->where('title LIKE ? OR description = ?', '%'.$search.'%');
    }

    if ($group && Engine_Api::_()->hasItemType('group'))
    {
      $select
          ->where('parent_type = ?', 'group', 'STRING')
          ->where('parent_id = ?', $group);

      $this->view->groupObj = Engine_Api::_()->getItem('group', $group);

    }
    else if ($user_id)
    {
      $this->view->userObj = Engine_Api::_()->user()->getUser($user_id);
      if ($this->view->userObj){
        $select = Engine_Api::_()->getDbtable('membership', 'event')->getMembershipsOfSelect($this->view->userObj);
        $select->where("endtime > FROM_UNIXTIME(?)", time());
      }
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

  }

  public function manageAction()
  {
    // Create form
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('event_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('event_quick');

    $this->view->formFilter = $formFilter = new Mobile_Form_Search();

    $this->view->user = $user = $this->_getParam('user');
    $formFilter->addElement('Hidden', 'user', array('value' => $user));
    $formFilter->setAction($this->view->url(array(
      'action' => 'manage'
    ), 'event_general', true));

    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }
    $this->view->assign($values);

    $viewer = $this->_helper->api()->user()->getViewer();
    $table = $this->_helper->api()->getDbtable('events', 'event');
    $tableName = $table->info('name');

    $membership = Engine_Api::_()->getDbtable('membership', 'event');
    $select = $membership->getMembershipsOfSelect($viewer);

    //$select->where("endtime > FROM_UNIXTIME(?)", time());

    $select->order('starttime ASC');

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)){
      $select->where('title LIKE ? OR description = ?', '%'.$search.'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));


    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
  }
  /*
  public function getNavigation($filter = false)
  {
    $this->view->navigation = $navigation = new Zend_Navigation();
    $navigation->addPages(array(
      array(
        'label' => "Upcoming Events",
        'route' => 'event_general',
      ),
      array(
        'label' => "Past Events",
        'route' => 'event_past'
	    )));
  

    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity()) {
      $navigation->addPages(array(
        array(
          'label' => 'My Events',
          'route'=> 'event_general',
          'action' => 'manage',
          'controller' => 'index',
          'module' => 'event'
        ),
	array(
          'label' => 'Create New Event',
          'route'=>'event_general',
          'action' => 'create',
          'controller' => 'index',
          'module' => 'event'
	      )));
    }
    return $navigation;     
  }

*/
}