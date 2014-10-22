<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 12.06.12
 * Time: 14:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PageeventController
  extends Apptouch_Controller_Action_Bridge
{
  public function eventsBrowseAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid()) return;

    $filter = $this->_getParam('filter', 'future');
    if ($filter != 'past' && $filter != 'future') $filter = 'future';

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pageevent.page', 10);

    $params['filter'] = $filter;
    $peTableName = Engine_Api::_()->getItemTable('pageevent')->info('name');
    $eTableName = Engine_Api::_()->getDbtable('events', 'event')->info('name');

    // Get paginator
    $select = Engine_Api::_()->getApi('core', 'pageevent')->getEventsSelect($params);

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

//    $paginator = Engine_Api::_()->getApi('core', 'pageevent')->getEventsSelect($params);
    if($filter == 'past'){
      $this->setPageTitle($this->view->translate('PAGEEVENT_PAST'));
    } else
      $this
        ->setPageTitle($this->view->translate('All Events'));

    $this
      ->addPageInfo('type', 'browse')
      ->add($this->component()->navigation('event_main', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('event_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareBrowseList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
    //
    //    $formValues = array();
    //
    //    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
    //    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
    //    if( !empty($params['show']) ) $formValues['show'] = $params['show'];
    //    if( !empty($params['category']) ) $formValues['category'] = $params['category'];
    //
    //    $this->view->formValues = $formValues;

    //    $this->_helper->content->setEnabled();
  }

  public function eventsManageAction()
  {
    if (!$this->_helper->requireUser->isValid()) return;

    $params = $this->_request->getParams();

    //    $form = new Pageevent_Form_Search();
    //    $form->removeElement('show');
    //    $form->populate($params);
    //    $this->view->form = $form;

    //Get settings
    //    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Get Params
    $params['owner'] = Engine_Api::_()->user()->getViewer();


    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params['ipp'] = $settings->getSetting('pageevent.page', 10);

    $paginator = Engine_Api::_()->getApi('core', 'pageevent')->getEventsPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//    $paginator->setItemCountPerPage(2);

    $this
      ->setPageTitle($this->view->translate('PAGEEVENT_USER'))
      ->addPageInfo('type', 'manage')
      ->add($this->component()->navigation('event_main', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('event_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareManageList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();

    //    $formValues = array();

    //    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
    //    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
    //    if( !empty($params['category']) ) $formValues['category'] = $params['category'];

    //    $this->view->formValues = $formValues;

    //    $this->_helper->content->setEnabled();
  }

  private function prepareBrowseList(Zend_Paginator $paginator)
  {
    $items = array();
    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
      }

      $item = Engine_Api::_()->getItem($page_pref . 'event', $p_item['event_id']);
      $owner = $item->getOwner();

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $this->view->translate('led by') . ' ' . $owner->getTitle()
        ),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl('thumb.normal'),
        'creation_date' => $this->view->locale()->toDateTime($item->starttime),
        'counter' => strtoupper($this->view->translate(array('%s guest', '%s guests', $item->membership()->getMemberCount()), $this->view->locale()->toNumber($item->membership()->getMemberCount()))),
        'owner_id' => $owner->getIdentity(),
        'owner' => $this->subject($owner)
      );

      if ($page_pref)
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    $component = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items
    );
    $searchKeyword = $this->_getParam('search', false);
    if ($searchKeyword) {
      $component['search'] = array(
        'keyword' => $searchKeyword . '', // to string
        'count' => $paginator->getTotalItemCount(),
      );
    }

    return $component;
  }

  private function prepareManageList(Zend_Paginator $paginator)
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $items = array();
    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
      }

      $item = Engine_Api::_()->getItem($page_pref . 'event', $p_item['event_id']);

      $owner = $item->getOwner();
      $options = array();
      if ($page_pref) {
        $options[] = array(
          'label' => $this->view->translate('Edit Event'),
          'href' => $item->getHref(),
          'class' => 'buttonlink icon_event_edit'
        );

        $options[] = array(
          'label' => $this->view->translate('Delete Event'),
          'href' => $this->view->url(array('action' => 'delete', 'pageevent_id' => $item->getIdentity()), 'page_events', true),
          'class' => 'buttonlink smoothbox icon_event_delete'
        );
        if ($viewer && $item->membership()->isMember($viewer) && !$item->isOwner($viewer)) {
          $options[] = array(
            'label' => $this->view->translate('Leave Event'),
            'href' => $this->view->url(array('action' => 'leave', 'pageevent_id' => $item->getIdentity()), 'page_events', true),
            'class' => 'buttonlink smoothbox icon_event_leave'
          );
        }
      } else {
        $options[] = $this->getOption($item, 0);
        $options[] = $this->getOption($item, 1);
        if ($viewer && $item->membership()->isMember($viewer) && !$item->isOwner($viewer)) {
          $options[] = $this->getOption($item, 2);
        }
      }

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
//          $this->view->translate('Posted') . ' ' . $this->view->translate('By') . ' ' . $owner->getTitle()
        ),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl('thumb.normal'),
        'creation_date' => $this->view->timestamp(strtotime($item->creation_date)),
        'owner_id' => null,
        'owner' => null,
        'manage' => $options
      );

      if ($page_pref)
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    $component = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items
    );
    $searchKeyword = $this->_getParam('search', false);
    if ($searchKeyword) {
      $component['search'] = array(
        'keyword' => $searchKeyword . '', // to string
        'count' => $paginator->getTotalItemCount(),
      );
    }

    return $component;
  }

  public function indexInit()
  {
    $this->page_id = $page_id = $this->_getParam('page_id');
    if (!$page_id) {
      return $this->redirect($this->view->url(array(), 'page_browse'));
    }

    $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);

    if (!$page) {
      return $this->redirect($this->view->url(array(), 'page_browse'));
    }

    $this->isAllowedView = Engine_Api::_()->getApi('core', 'page')->isAllowedView($page);

    if (!$this->isAllowedView) {
      $this->isAllowedPost = false;
      $this->isAllowedComment = false;
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->isAllowedPost = $this->getApi()->isAllowedPost($page);
    $this->isAllowedComment = $this->getPageApi()->isAllowedComment($page);

    $this->viewer = Engine_Api::_()->user()->getViewer();

    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexUpcomingAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return $this->redirect($this->pageObject->getHref());
    }

    if ($this->pageObject)
      Engine_Api::_()->core()->setSubject($this->pageObject);

    $paginator = $this
      ->getEventPaginator($this->pageObject->page_id, 'upcomming', $this->_getParam('page', 1));

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pageevent', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ;

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pageevent_quick', true));

    $this->add($this->component()->itemList($paginator, 'browseItemList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexPastAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return $this->redirect($this->pageObject->getHref());
    }

    if ($this->pageObject)
      Engine_Api::_()->core()->setSubject($this->pageObject);

    $paginator = $this
      ->getEventPaginator($this->pageObject->page_id, 'past', $this->_getParam('page', 1));

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pageevent', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('pageevent_quick', true))
      ->add($this->component()->itemList($paginator, 'browseItemList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexMineAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return $this->redirect($this->pageObject->getHref());
    }

    if ($this->pageObject)
      Engine_Api::_()->core()->setSubject($this->pageObject);

    $paginator = $this
      ->getEventPaginator($this->pageObject->page_id, 'user', $this->_getParam('page', 1), $this->viewer->getIdentity());

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pageevent', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ;

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pageevent_quick', true));

    $this->add($this->component()->itemList($paginator, 'manageEventList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function getEventPaginator($page_id, $show, $page = 1, $viewer_id = 0)
  {
    $table = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
    $pageevent_ids = $table->getPageeventIds($page_id);
    if (!empty($pageevent_ids)) {
      $selectEvent = $table->select()
          ->where('page_id = ?', $page_id);

      $selectEvent = $selectEvent->where('pageevent_id IN(?)', $pageevent_ids);
      if ($this->_getParam('search', false)) {
        $selectEvent->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
      }

      // Show Past Events
      if ($show == 'past'){
        $selectEvent->where('endtime < FROM_UNIXTIME(?)', time());
      }
      // Show User Events
      else if ($show == 'user' && $viewer_id)
      {
        $selectEvent->where('user_id = ?', $viewer_id);
      }
      // Show Upcoming Events
      else {
        $selectEvent->where('endtime > FROM_UNIXTIME(?)', time());
      }

      $selectEvent->order('starttime ASC');
      $paginator = Zend_Paginator::factory($selectEvent);
      $paginator->setCurrentPageNumber($page);
      return $paginator;
    }

    return  Zend_Paginator::factory(0);
  }

  public function indexCreateAction()
  {
    if (!$this->isAllowedPost)
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));

    $form = new Pageevent_Form_Form($this->pageObject);
    $form->removeElement('event_photo');
    $form->addElement('File', 'event_photo', array(
      'label' => 'Event Photo',
      'order' => 4,
    ));

    $form->removeElement('starttime');
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $start->setOrder(2);
    $form->addElement($start);

    $form->removeElement('endtime');
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $end->setOrder(3);
    $form->addElement($end);

    $form->getElement('cancel')->setAttrib('onclick', '');
    $form->removeAttrib('onsubmit');

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pageevent', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {

      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pageevent', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $values = $form->getValues();

    $oldTz = date_default_timezone_get();
    date_default_timezone_set($this->viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    if ($start > $end) {
      $this->view->error = $this->view->translate('PAGEEVENT_DATEERROR');
      return;
    }

    /**
     * @var $table Pageevent_Model_DbTable_Pageevents
     */
    $table = $this->getTable();
    $db = $table->getDefaultAdapter();

    $db->beginTransaction();
    try {
      $event = $table->createRow();
      $event->page_id = $this->pageObject->getIdentity();
      $event->user_id = $this->viewer->getIdentity();

      $event->title = $values['title'];
      $event->description = $values['description'];
      $event->location = $values['location'];
      $event->approval = $values['approval'];
      $event->invite = $values['invite'];
      $event->starttime = $values['starttime'];
      $event->endtime = $values['endtime'];
      $event->save();

      $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'likes' => 'Likes, Admins and Owner',
        'team' => 'Admins and Owner Only'
      );

      if (Engine_Api::_()->getApi('settings', 'core')->__get('page.package.enabled') && $this->view->pageObject instanceof Page_Model_Page) {
        $package = $this->view->pageObject->getPackage();

        $view_options = $package->auth_view;
        $comment_options = $package->auth_comment;
        $posting_options = $package->auth_posting;
      }

      else {
        $comment_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->viewer, 'auth_comment');
        $posting_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->viewer, 'auth_posting');
      }

      $comment_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->viewer, 'auth_comment');
      $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

      $posting_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $this->viewer, 'auth_posting');
      $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));

      $event->setPrivacy(array(
        'auth_view' => $values['privacy'],
        'auth_comment' => key($comment_options),
        'auth_posting' => key($posting_options)
      ));

      // Add Member
      $event->membership()->addMember($this->viewer)
        ->setUserApproved($this->viewer)
        ->setResourceApproved($this->viewer);

      $event->membership()
        ->getMemberInfo($this->viewer)
        ->setFromArray(array('rsvp' => 2))
        ->save();

      // Add Activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_create', null, array('is_mobile' => true));
      if ($action) {
        $api->attachActivity($action, $event);
      }

      $photo = $this->getPicupFiles('event_photo');
      // Set photo

      if (!empty($values['event_photo'])) {
        $event_photo = Engine_Api::_()->getApi('core', 'pageevent')->uploadPhoto($form->event_photo);
        $event->photo_id = $event_photo->getIdentity();
      } else if (!empty($photo)) {
        $photo = $photo[0];
        $event_photo = Engine_Api::_()->getApi('core', 'pageevent')->uploadPhoto($photo);
        $event->photo_id = $event_photo->getIdentity();
      }

      $event->save();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->commit();
    return $this->redirect($event);
  }

  public function indexEditAction()
  {
    $event = Engine_Api::_()->getItem('pageevent', $this->_getParam('event_id'));
    $user = $event->getOwner();
    $page = $event->getPage();

    $form = new Pageevent_Form_Form($page);
    $form->setAttrib('onsubmit', '');
    $form->removeElement('event_photo');
    $form->setTitle('PAGEEVENT_EDIT_TITLE');
    $form->addElement('File', 'event_photo', array(
      'label' => 'Event Photo',
      'order' => 4,
    ));
    $form->removeElement('starttime');
    $form->addElement('CalendarDateTime', 'starttime', array(
      'label' => 'Start Time',
      'allowEmpty' => false,
      'order' => 2
    ));

    $form->removeElement('endtime');
    $form->addElement('CalendarDateTime', 'endtime', array(
      'label' => 'End Time',
      'allowEmpty' => false,
      'order' => 3
    ));

    // Convert and re-populate times
    $start = strtotime($event->starttime);
    $end = strtotime($event->endtime);
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($this->viewer->timezone);
    $start = date('Y-m-d H:i:s', $start);
    $end = date('Y-m-d H:i:s', $end);
    date_default_timezone_set($oldTz);

    $form->title->setValue($event->title);
    $form->description->setValue($event->description);
    $form->starttime->setValue($start);
    $form->endtime->setValue($end);
    $form->location->setValue($event->location);
    $form->approval->setValue($event->approval);
    $form->invite->setValue($event->invite);

    $auth = Engine_Api::_()->authorization();

    $roles = array('team', 'likes', 'registered', 'everyone');
    $view_auth = 'team';

    foreach ($roles as $roleString) {
      $role = $roleString;

      if ($role === 'team') {
        $role = $page->getTeamList();
      }
      elseif ($role === 'likes') {
        $role = $page->getLikesList();
      }

      if (1 === $auth->isAllowed($event, $role, 'view')) {
        $view_auth = $roleString;
        $form->privacy->setValue($roleString);
      }

    }

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($event))
        ->add($this->component()->navigation('pageevent', true))
        ->add($this->component()->form($form))
        ->renderContent();

      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($event))
        ->add($this->component()->navigation('pageevent', true))
        ->add($this->component()->form($form))
        ->renderContent();

      return;
    }

    $values = $form->getValues();
    /**
     * @var $table Pageevent_Model_DbTable_Pageevents
     */
    $table = $this->getTable();
    $db = $table->getDefaultAdapter();

    $db->beginTransaction();
    try {
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($this->viewer->timezone);
      $start = strtotime($values['starttime']);
      $end = strtotime($values['endtime']);
      date_default_timezone_set($oldTz);
      $values['starttime'] = date('Y-m-d H:i:s', $start);
      $values['endtime'] = date('Y-m-d H:i:s', $end);

      $event->title = $values['title'];
      $event->description = $values['description'];
      $event->location = $values['location'];
      $event->approval = $values['approval'];
      $event->invite = $values['invite'];
      $event->starttime = $values['starttime'];
      $event->endtime = $values['endtime'];

      $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'likes' => 'Likes, Admins and Owner',
        'team' => 'Admins and Owner Only'
      );

      if (Engine_Api::_()->getApi('settings', 'core')->__get('page.package.enabled') && $this->view->pageObject instanceof Page_Model_Page) {
        /**
         * @var $page Page_Model_Package
         */
        $package = $this->view->pageObject->getPackage();

        $view_options = $package->auth_view;
        $comment_options = $package->auth_comment;
        $posting_options = $package->auth_posting;
      }

      else {
        $comment_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_comment');
        $posting_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_posting');
      }

      $comment_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_comment');
      $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

      $posting_options = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_posting');
      $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));

      $event->setPrivacy(array(
        'auth_view' => $values['privacy'],
        'auth_comment' => key($comment_options),
        'auth_posting' => key($posting_options)
      ));

      $photo = $this->getPicupFiles('event_photo');
      // Set photo
      if (!empty($values['event_photo'])) {
        if ($event->photo_id) {
          Engine_Api::_()->pageevent()->deletePhoto($event->photo_id);
          $event->photo_id = 0;
        }

        $event_photo = Engine_Api::_()->getApi('core', 'pageevent')->uploadPhoto($form->event_photo);
        $event->photo_id = $event_photo->getIdentity();
      } else if (!empty($photo)) {
        if ($event->photo_id) {
          Engine_Api::_()->pageevent()->deletePhoto($event->photo_id);
          $event->photo_id = 0;
        }

        $photo = $photo[0];
        $event_photo = Engine_Api::_()->getApi()->uploadPhoto($photo);
        $event->photo_id = $event_photo->getIdentity();
      }

      $event->save();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->commit();

    return $this->redirect($event);
  }

  public function indexDeleteAction()
  {
    $event = Engine_Api::_()->getItem('pageevent', $this->_getParam('event_id'));

    $form = new Pageevent_Form_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $event->delete();

    return $this->redirect($this->view->url(array('action' => 'mine', 'page_id' => $this->page_id), 'page_event'), Zend_Registry::get('Zend_Translate')->_("PAGEEVENT_REMOVE_SUCCESS"), true);
  }

  public function indexWaitingAction()
  {
    $event_id = $this->_getParam('event_id');

    if (!$event_id || !$this->viewer) {
      return $this->redirect($this->pageObject);
    }

    $tbl = $this->getTable();

    $event = $tbl->findRow($event_id);
    if (!$event) {
      return $this->redirect($this->pageObject);
    }

    if (!$event->isOwner($this->viewer) && !$this->pageObject->isAdmin($this->viewer)) {
      return $this->redirect($event);
    }

    Engine_Api::_()->core()->clearSubject();
    Engine_Api::_()->core()->setSubject($event);

    $tbl = Engine_Api::_()->getDbTable('users', 'user');
    $eventmember_tbl = Engine_Api::_()->getDbTable('pageeventmembership', 'pageevent');
    $select = $tbl->select()
      ->setIntegrityCheck(false)
      ->from(array('u' => $tbl->info('name')), array('u.*'))
      ->join(array('em' => $eventmember_tbl->info('name')), 'em.user_id = u.user_id', array('em.user_approved'))
      ->where('em.resource_id = ?', $event->getIdentity())
      ->where('em.active = 0');

    $members = Zend_Paginator::factory($select);
    $this->add($this->component()->subjectPhoto($event))
      ->add($this->component()->itemList($members, 'waitingMembersList'))
      ->renderContent();
  }

  public function indexMemberApproveAction()
  {
    $event_id = $this->_getParam('event_id');
    $approve = $this->_getParam('approve');

    if (!$event_id) {
      return $this->redirect($this->pageObject);
    }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event) {
      return $this->redirect($this->pageObject);
    }

    if (!$this->viewer) {
      return $this->redirect($event);
    }

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $member = $event->membership()->getRow($this->viewer);

      if (!$member) {
        return $this->redirect($event);
      }

      if ($approve) {

        $event->membership()->setUserApproved($this->viewer);

        if ($member->active) {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_join', null, array('is_mobile' => true, 'link' => $event->__toString()));
          if ($action) {
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($event->getOwner(), $this->viewer, $event, 'pageevent_approved', array('link' => $event->getHref()));
        }

        $rsvp = $this->_getParam('rsvp');

        if ($rsvp !== null && in_array($rsvp, array(0, 1, 2))) {
          $member->rsvp = $rsvp;
          $member->save();
        }

      } else {
        $event->membership()->removeMember($this->viewer);
      }

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $this->viewer, $event, 'pageevent_invite');

      if ($notification) {
        $notification->mitigated = true;
        $notification->save();
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($event);
  }

  public function indexResourceApproveAction()
  {
    $event_id = $this->_getParam('event_id');
    $user_id = $this->_getParam('user_id');
    $approve = (bool)$this->_getParam('approve');

    if (!$event_id) {
      return;
    }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event) {
      return $this->redirect($this->pageObject);
    }

    if (!$this->viewer || (!$event->getPage()->isTeamMember($this->viewer) && !$this->viewer->isSelf($event->getOwner()))) {
      return $this->redirect($event);
    }
    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $user = Engine_Api::_()->user()->getUser($user_id);
      if (!$user) {
        return $this->redirect($event);
      }

      $member = $event->membership()->getRow($user);
      if (!$member) {
        return $this->redirect($this->pageObject);
      }

      if ($approve) {

        $event->membership()->setResourceApproved($user);

        if ($member->active) {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_join', null, array('is_mobile' => true, 'link' => $event->__toString()));
          if ($action) {
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($user, $this->viewer, $event, 'pageevent_accepted', array('link' => $event->getHref()));
        }

      } else {
        $event->membership()->removeMember($user);
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($event);
  }

  public function indexInviteAction()
  {
    $event_id = $this->_getParam('event_id');
    $user_ids = $this->_getParam('uids');

    if (!$event_id){ return ; }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event){
      return $this->redirect($this->pageObject->getHref());
    }

    if (!$this->viewer){
      return $this->redirect($event->getHref()) ;
    }

    $is_owner = ($event->getPage()->isTeamMember($this->viewer) || !$this->viewer->isSelf($event->getOwner()));
    $is_guest = ($event->invite && $event->membership()->isMember($this->viewer, true));

    if (!$is_owner && !$is_guest){
      return $this->redirect($event->getHref());
    }

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $select = $event->membership()->getInviteMembersSelect($this->viewer)
        ->where('u.user_id IN (?)', $user_ids);

      $friends = Engine_Api::_()->getDbTable('users', 'user')->fetchAll($select);

      foreach ($friends as $friend)
      {
        if ($event->membership()->getRow($friend)){
          continue;
        }
        $event->membership()
          ->addMember($friend)
          ->setResourceApproved($friend);

        $event->membership()
          ->getRow($friend)
          ->setFromArray(array('rsvp', 3))
          ->save();;

        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($friend, $this->viewer, $event, 'pageevent_invite', array('link' => $event->getHref()));
      }

      $this->view->result = true;
      $this->view->message = $this->view->translate('PAGEEVENT_INVITE_SUCCESS');
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->redirect($event->getHref());
  }

  public function indexRsvpAction()
  {
    $event_id = $this->_getParam('event_id');
    $rsvp = (int)$this->_getParam('rsvp');

    if ($rsvp < 0 || $rsvp > 2) {
      $rsvp = 2;
    }

    if (!$event_id) {
      return $this->redirect($this->pageObject);
    }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event) {
      return $this->redirect($this->pageObject);
    }

    if (!$this->viewer) {
      return $this->redirect($this->pageObject);
    }

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $member = $event->membership()->getRow($this->viewer);

      if (!$member) {
        $member = $event->membership()
          ->addMember($this->viewer)
          ->getRow($this->viewer);

        $event->membership()
          ->setUserApproved($this->viewer);

        if ($member->active) {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_join', null, array('is_mobile' => true, 'link' => $event->__toString()));
          if ($action) {
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($event->getOwner(), $this->viewer, $event, 'pageevent_approved', array('link' => $event->getHref()));
        }
      }

      $event->membership()
        ->setUserApproved($this->viewer);

      $member->rsvp = ($event->approval && !$member->active) ? 3 : $rsvp;
      $member->save();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($event);
  }

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pageevent');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pageevents', 'pageevent');
  }

  public function browseItemList(Core_Model_Item_Abstract $item)
  {
    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl('thumb.normal');
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
    }
    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageEvent Customizer Functions ---------------------------------
  public function manageEventList(Core_Model_Item_Abstract $item)
  {
    $options = array();

    $options[] = array(
      'label' => $this->view->translate('Edit'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $item->getPage()->getIdentity(), 'event_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_event', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete'),
      'attrs' => array(
        'href' => $this->view->url(array(
          'action' => 'delete',
          'page_id' => $item->getPage()->getIdentity(),
          'event_id' => $item->getIdentity(),
          'no_cache' => rand(0, 1000)
        ), 'page_event', true),
        'class' => 'buttonlink smoothbox icon_album_delete',
      )
    );

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl('thumb.normal');
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
    }

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl,
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageEvent Customizer Functions ---------------------------------

  public function waitingMembersList(Core_Model_Item_Abstract $item)
  {
    $options = array();

    $subject = Engine_Api::_()->core()->getSubject();

    $url = array(
      'module' => 'pageevent',
      'controller' => 'index',
      'action' => 'resource-approve',
      'page_id' => $subject->getPage()->getIdentity(),
      'event_id' => $subject->getIdentity(),
      'user_id' => $item->getIdentity(),
      'approve' => 0
    );

    if (!$item->user_approved) {
      $options[] = array(
        'label' => $this->view->translate('PAGEEVENT_INVITE_CANCEL'),
        'attrs' => array(
          'href' => $this->view->url($url, 'default', true),
        )
      );
    } else {
      $options[] = array(
        'label' => $this->view->translate('PAGEEVENT_REJECT'),
        'attrs' => array(
          'href' => $this->view->url($url, 'default', true),
        )
      );

      $url['approve'] = 1;
      $options[] = array(
        'label' => $this->view->translate('PAGEEVENT_APPROVE'),
        'attrs' => array(
          'href' => $this->view->url($url, 'default', true),
        )
      );
    }

    $customize_fields = array(
      'manage' => $options
    );

    return $customize_fields;
  }
}
