<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 07.06.12
 * Time: 14:17
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_EventController
  extends Apptouch_Controller_Action_Bridge
{
    protected $_paramsTable = null;
    protected $_ticketsTable = null;
    protected $_subTable = null;
    protected $_setEventOrder = null;
// Index Controller {
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid()) return;

    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if ($id) {
      $event = Engine_Api::_()->getItem('event', $id);
      if ($event) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
  }

  public function indexBrowseAction()
  {

    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');


    $filter = $this->_getParam('filter', 'future');
    if ($filter != 'past' && $filter != 'future') $filter = 'future';

    // Prepare data
    $values = array();

    if ($viewer->getIdentity() && @$values['view'] == 1) {
      $values['users'] = array();
      foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    if ($filter == "past") {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

    if (($user_id = $this->_getParam('user'))) {
      $values['user_id'] = $user_id;
    }


    // Get paginator
    $select = Engine_Api::_()->getItemTable('event')
      ->getEventSelect($values);
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $items_count = 20;
    $paginator->setItemCountPerPage($items_count);
    $this->setFormat('browse')
      ->add($this->component()->itemSearch($this->getSearchForm()));

    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->_getParam('search', false)) {
      if ($canCreate)
        $this->add($this->component()->tip(
          $this->view->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'event_general') . '">', '</a>'),
          $this->view->translate('APPTOUCH_Nobody has created an event with that criteria.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('APPTOUCH_Nobody has created an event with that criteria.')
        ));
    } else {
      $title = null;
      $message = null;
      if ($canCreate) {
        $message = $this->view->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'event_general') . '">', '</a>');
        $title = $this->view->translate('Nobody has created an event yet.');
      }
      else
        $message = $this->view->translate('Nobody has created an event yet.');
      if ($filter == 'past') {
        if ($canCreate)
          $title = $this->view->translate('There are no past events yet.');
        else
          $message = $this->view->translate('There are no past events yet.');
      }
      $this->add($this->component()->tip(
        $message,
        $title
      ));

    }
    $this->renderContent();
  }

  public function indexManageAction()
  {
    // Create form
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid()) return;

    $values = array();

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('events', 'event');
    $tableName = $table->info('name');

    // Only mine
    if (@$values['view'] == 2) {
      $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity());
    }
    // All membership
    else {
      $membership = Engine_Api::_()->getDbtable('membership', 'event');
      $select = $membership->getMembershipsOfSelect($viewer);
    }

    if (!empty($values['text'])) {
      $select->where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
    }

    $select->order('starttime ASC');

    if ($this->_getParam('search', false)) {
      $select->where('`' . $tableName . '`.title LIKE ? OR `' . $tableName . '`.description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
    $this->setFormat('manage')
      ->add($this->component()->itemSearch($this->getSearchForm()));
    if ($paginator->getTotalItemCount()) {
      $this
        ->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->_getParam('search', false)) {
      $this->add($this->component()->tip(
        $this->view->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'event_general') . '">', '</a>'),
        $this->view->translate('APPTOUCH_Nobody has created an event with that criteria.')
      ));
    } else {
      $title = null;
      $message = null;
      if ($canCreate) {
        $message = $this->view->translate('Why don\'t you %1$screate one%2$s?',
          '<a href="' . $this->view->url(array('action' => 'create'), 'event_general') . '">', '</a>');
        $title = $this->view->translate('You have not joined any events yet.');
      }
      else
        $message = $this->view->translate('You have not joined any events yet.');
      $this->add($this->component()->tip(
        $message,
        $title
      ));
    }
    $this->renderContent();
  }

  public function indexCreateAction()
  {
    if (!$this->_helper->requireUser->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $parent_type = $this->_getParam('parent_type');
    $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));

    if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
      $group = Engine_Api::_()->getItem('group', $parent_id);
      if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid()) {
        return;
      }
    } else {
      $parent_type = 'user';
      $parent_id = $viewer->getIdentity();
    }

    // Create form
    //    $this->view->parent_type = $parent_type;
    $form = new Event_Form_Create(array(
      'parent_type' => $parent_type,
      'parent_id' => $parent_id
    ));
    $this->setFormat('create');

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }
    if ($parent_type == 'group') {
      $this->add($this->component()->crumb(array(
        array(
          'label' => $group->getTitle(),
          'attrs' => array(
            'href' => $group->getHref()
          )
        ),
        array(
          'label' => $this->view->translate('Events'),
          'attrs' => array(
            'data-icon' => 'arrow-d'
          ),
          'active' => true
        )
      )));
    } else {
      $this->add($this->component()->html($this->dom()->new_('h2', array(), $this->view->translate('Events'))));
    }


    // Not post/invalid
    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    // Process
    $values = $form->getValues();

    $values['user_id'] = $viewer->getIdentity();
    $values['parent_type'] = $parent_type;
    $values['parent_id'] = $parent_id;
    if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host'])) {
      $values['host'] = $group->getTitle();
    }

    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    $db = Engine_Api::_()->getDbtable('events', 'event')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create event
      $table = Engine_Api::_()->getDbtable('events', 'event');
      $event = $table->createRow();

      $event->setFromArray($values);
      $event->save();

      // Add owner as member
      $event->membership()->addMember($viewer)
        ->setUserApproved($viewer)
        ->setResourceApproved($viewer);

      // Add owner rsvp
      $event->membership()
        ->getMemberInfo($viewer)
        ->setFromArray(array('rsvp' => 2))
        ->save();

      // Add photo
      if (!empty($values['photo'])) {
        $event->setPhoto($form->photo);
      } else if ($picupFile = $this->getPicupFiles('photo')) {
        $event->setPhoto($picupFile[0]);
      }

      // Set auth
      $auth = Engine_Api::_()->authorization()->context;

      if ($values['parent_type'] == 'group') {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if (empty($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
      }

      $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

      // Add an entry for member_requested
      $auth->setAllowed($event, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      $action = $activityApi->addActivity($viewer, $event, 'event_create', null, array('is_mobile' => true));

      if ($action) {
        $activityApi->attachActivity($action, $event);
      }
      // Commit
      $db->commit();

      // Redirect
      return $this->redirect($this->view->url(array('id' => $event->getIdentity()), 'event_profile', true));
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
      $this
        ->add($this->component()->form($form))
        ->renderContent();
    }

    catch (Exception $e)
    {
      $form->addError($e->getMessage());
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      $db->rollBack();
      throw $e;
    }

  }




    public function indexTicketsAction()
    {

        $this->_paramsTable = Engine_Api::_()->getDbTable('params', 'heevent');
        $this->_ticketsTable = Engine_Api::_()->getDbTable('tickets', 'heevent');
        $this->_subTable = Engine_Api::_()->getDbTable('subscriptions', 'heevent');
        $this->_setEventOrder = Engine_Api::_()->getDbTable('subscriptions', 'heevent');
        if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid()) return;

        $id = $this->_getParam('event_id', $this->_getParam('id', null));
        if ($id) {
            $event = Engine_Api::_()->getItem('event', $id);
            if ($event) {
                Engine_Api::_()->core()->setSubject($event);
            }
        }
        $this->view->format = $this->_getParam('format', false);
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Create form
        if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid()) return;

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('event_main');


        $this->view->formFilter = $formFilter = new Event_Form_Filter_Manage();
        $defaultValues = $formFilter->getValues();

        // Populate form data
        if ($formFilter->isValid($this->_getAllParams())) {
            $this->view->formValues = $values = $formFilter->getValues();
        } else {
            $formFilter->populate($defaultValues);
            $this->view->formValues = $values = array();
        }
        $params = $this->_getAllParams();
        /**
         * @var $Ctable Heevent_Model_DbTable_Cards
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
        $Cardname = $tblCard->info('name');
        $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
        $Ename = $tblevent->info('name');

        $fetch = $tblCard->select()->from(array('c'=>$Cardname))
            ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where('c.state = ?','okay')->where('c.user_id = ?',$viewer->getIdentity());
        $this->view->active_upcoming = 1;

        $select = $tblCard->fetchAll($fetch);
        $this->view->ticketCodes = array();
        $cardArray = $select->toArray();
        $events = array();
        foreach($cardArray as $card) {
            $event = Engine_Api::_()->getItem('event', $card['event_id']);
            $this->view->ticketCodes[$card['event_id']] = $card['ticked_code'];
            $events[] = $event;
        }
        $this->view->text = $values['text'];
        $this->view->view = $values['view'];

//        $this->view->paginator =
        $paginator = Zend_Paginator::factory($events);
        $paginator->setItemCountPerPage(12);
        // });
        // Check create
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
        // Render
        $this->add($this->component()->itemList($paginator, 'ticketItemData', array('listPaginator' => true,)))
//            ->add($this->component()->paginator($paginator))
            ->renderContent();
        return;

    }
  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('led by') . ' ' . $owner->getTitle(),
        $this->view->translate(array('%s guest', '%s guests', $item->membership()->getMemberCount()), $this->view->locale()->toNumber($item->membership()->getMemberCount())) . ' &#183; ' . $this->view->locale()->toDateTime($item->starttime)
      ),
      'photo' => $item->getPhotoUrl('thumb.normal'),
      'creation_date' => null,
    );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $options = array();
    if ($viewer && $item->isOwner($viewer)) {
      $options[] = $this->getOption($item, 0);
      $options[] = $this->getOption($item, 1);
    }

    if ($viewer && !$item->membership()->isMember($viewer, null)) {
      $options[] = $this->getOption($item, 2);
    }
    if ($viewer && $item->membership()->isMember($viewer) && !$item->isOwner($viewer)) {
      $options[] = $this->getOption($item, 3);
    }
    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate(array('%s guest', '%s guests', $item->membership()->getMemberCount()), $this->view->locale()->toNumber($item->membership()->getMemberCount())) . ' &#183; ' . $this->view->locale()->toDateTime($item->starttime)
      ),
      'creation_date' => null,
      'owner_id' => null,
      'owner' => null,
      'manage' => $options
    );
    return $customize_fields;
  }

//  } Index Controller {


//  Profile Controller {
  public function profileInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject() &&
      ($id = $this->_getParam('id'))
    ) {
      $subject = Engine_Api::_()->getItem('event', $id);
      if ($subject && $subject->getIdentity()) {
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $subject,
      Engine_Api::_()->user()->getViewer(),
      'view'
    );

  }

  public function profileIndexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid()) {
      return;
    }
    // Check block
    if ($viewer->isBlockedBy($subject)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Increment view count
    if (!$subject->getOwner()->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }
    $fors = new Event_Form_Rsvp();
    //    $this->printArr($fors->render($this->view));

  $this->setFormat('profile');
    if (Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode()) {
      $this->addPageInfo('fields', $this->widgetProfileInfo($subject) . '' . $this->widgetProfileRsvp($subject)
      );
    } else {
      $this
          ->add($this->component()->heEventCover(), 0)
          ->add($this->component()->html($this->widgetProfileInfo($subject) . ''), 4)
          ->add($this->component()->html($this->widgetProfileRsvp($subject) . ''), 3);
    }
    $this->renderContent();
  }
  private function eventProfileMember()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'event' ) {
      throw new Event_Model_Exception('Whoops, not a event!');
    }

    if( !$viewer->getIdentity() ) {
      return false;
    }
    $cg = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-type' => 'horizontal', 'data-mini' => true, 'style' => 'text-align: center', 'class' => 'switcher'), '');

    $row = $subject->membership()->getRow($viewer);

    // Not yet associated at all
    if( null === $row ) {
      if( $subject->membership()->isResourceApprovalRequired() ) {
        $cg->append($this->dom()->new_('a', array(
          'data-icon' => 'edit',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
                      'controller' => 'member',
                      'action' => 'request',
                      'event_id' => $subject->getIdentity(),
                    ), 'event_extended', true),

        ), $this->view->translate('Request Invite')));
        return $cg;
      } else {
        $cg->append($this->dom()->new_('a', array(
          'data-icon' => 'plus',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
                      'controller' => 'member',
                      'action' => 'join',
                      'event_id' => $subject->getIdentity(),
                    ), 'event_extended', true),

        ), $this->view->translate('Join Event')));

        return $cg;
      }
    }

    // Full member
    // @todo consider owner
    else if( $row->active ) {
      if( !$subject->isOwner($viewer) ) {
        $cg->append($this->dom()->new_('a', array(
          'data-icon' => 'signout',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
                      'controller' => 'member',
                      'action' => 'leave',
                      'event_id' => $subject->getIdentity(),
                    ), 'event_extended', true),

        ), $this->view->translate('Leave Event')));
        return $cg;
      } else {
        return false;
        /*
        return array(
          'label' => 'Delete Event',
          'icon' => 'application/modules/Event/externals/images/delete.png',
          'class' => 'smoothbox',
          'route' => 'event_specific',
          'params' => array(
            'action' => 'delete',
            'event_id' => $subject->getIdentity()
          ),
        );
       */
      }
    } else if( !$row->resource_approved && $row->user_approved ) {
      return array(
        'label' => 'Cancel Invite Request',
        'icon' => 'application/modules/Event/externals/images/member/cancel.png',
        'class' => 'smoothbox',
        'route' => 'event_extended',
        'params' => array(
          'controller' => 'member',
          'action' => 'cancel',
          'event_id' => $subject->getIdentity()
        ),
      );
    } else if( !$row->user_approved && $row->resource_approved ) {
      return array(
        array(
          'label' => 'Accept Event Invite',
          'icon' => 'application/modules/Event/externals/images/member/accept.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'accept',
            'event_id' => $subject->getIdentity()
          ),
        ), array(
          'label' => 'Ignore Event Invite',
          'icon' => 'application/modules/Event/externals/images/member/reject.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'reject',
            'event_id' => $subject->getIdentity()
          ),
        )
      );
    }

    else
    {
      throw new Event_Model_Exception('An error has occurred.');
    }


    return false;
  }

  private function widgetProfileInfo($subject)
  {
      $collapsible = 'collapsible';
    $viewer = Engine_Api::_()->user()->getViewer();
    $eventDetails = $this->dom()->new_('div', array('data-role' => "{$collapsible}", 'data-content-theme' => 'd', 'data-mini' => 'true', 'data-inset' => 'false'), '', array($this->dom()->new_('h3', array(), $this->view->translate('Event Details'))));
    $detailsContent = $this->dom()->new_('p', array(), '');
    $eventDetails->append($detailsContent);
    if (!empty($subject->description)) {
      $detailsContent
        ->append($this->dom()->new_('span', array(), nl2br($subject->description)));
    }

    $startDateObject = new Zend_Date(strtotime($subject->starttime));
    $endDateObject = new Zend_Date(strtotime($subject->endtime));
    if ($viewer && $viewer->getIdentity()) {
      $tz = $viewer->timezone;
      $startDateObject->setTimezone($tz);
      $endDateObject->setTimezone($tz);
    }
    $tbody = $this->dom()->new_('tbody');

    if ($subject->starttime == $subject->endtime) {
      $tbody->append($this->dom()->new_('tr', array(), '', array(
        $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Date')),
        $this->dom()->new_('td', array(), $this->view->locale()->toDate($startDateObject))
      )));
      $tbody->append($this->dom()->new_('tr', array(), '', array(
        $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Time')),
        $this->dom()->new_('td', array(), $this->view->locale()->toTime($startDateObject))
      )));
    } elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')) {
      $tbody->append($this->dom()->new_('tr', array(), '', array(
        $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Date')),
        $this->dom()->new_('td', array(), $this->view->locale()->toDate($startDateObject))
      )));
      $tbody->append($this->dom()->new_('tr', array(), '', array(
        $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Time')),
        $this->dom()->new_('td', array(), $this->view->locale()->toTime($startDateObject) . '-' . $this->view->locale()->toTime($endDateObject))
      )));
    } else {
      $tbody->append($this->dom()->new_('catpion', array(), '<strong>' . $this->view->translate('%1$s at %2$s',
        $this->view->locale()->toDate($startDateObject),
        $this->view->locale()->toTime($startDateObject)
      ) . '-' . $this->view->translate('%1$s at %2$s',
        $this->view->locale()->toDate($endDateObject),
        $this->view->locale()->toTime($endDateObject)
      ) . '</strong>'));

    }
    if (!empty($subject->location)) {
      $tbody->append($this->dom()->new_('tr', array(), '', array(
        $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Where')),
        $this->dom()->new_('td', array(), $this->view->htmlLink('http://maps.google.com/?q=' . urlencode($subject->location), $subject->location, array('data-role' => 'button', 'data-icon' => 'map', 'target' => 'blank')))
      )));
    }
    if (!empty($subject->host)) {
      if ($subject->host != $subject->getParent()->getTitle()) {
        $tbody->append($this->dom()->new_('tr', array(), '', array(
          $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Host')),
          $this->dom()->new_('td', array(), $subject->host)
        )));
      } else {
        $tbody->append($this->dom()->new_('tr', array(), '', array(
          $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Led by')),
          $this->dom()->new_('td', array(), $subject->getParent()->__toString())
        )));
      }
    }
    if (!empty($subject->category_id)) {
      $tbody->append($this->dom()->new_('tr', array(), '', array(
        $this->dom()->new_('th', array('scope' => 'row'), $this->view->translate('Category')),
        $this->dom()->new_('td', array(), $this->view->htmlLink(array(
          'route' => 'event_general',
          'action' => 'browse',
          'category_id' => $subject->category_id,
        ), $this->view->translate((string)$subject->categoryName())))
      )));
    }
    $detailsContent
      ->append($this->dom()->new_('table', array(), '', array(
      $tbody
    )));
    $detailsContent->append($this->dom()->new_('ul', array('data-role' => 'listview', 'data-inset' => true), '', array(
      $this->dom()->new_('li', array('data-role' => 'list-divider'), $this->view->translate('RSVPs'), array(
        $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->locale()->toNumber($subject->membership()->getMemberCount()))
      )),
      $this->dom()->new_('li', array(), $this->view->translate('attending'), array(
        $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->locale()->toNumber($subject->getAttendingCount()))
      )),
      $this->dom()->new_('li', array(), $this->view->translate('maybe attending'), array(
        $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->locale()->toNumber($subject->getMaybeCount()))
      )),
      $this->dom()->new_('li', array(), $this->view->translate('not attending'), array(
        $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->locale()->toNumber($subject->getNotAttendingCount()))
      )),
      $this->dom()->new_('li', array(), $this->view->translate('awaiting reply'), array(
        $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->locale()->toNumber($subject->getAwaitingReplyCount()))
      )),
    )));

    return $eventDetails;
  }

  private function widgetProfileRsvp($subject)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be a member
    if (!$subject->membership()->isMember($viewer, true)) {
      return;
    }

    $row = $subject->membership()->getRow($viewer);
    if (!$row) {
      return;
    }
      $modules = Engine_Api::_()->getDbTable('modules', 'core');
      if ($modules->isModuleEnabled('heevent')) {



      }
      $collapsible = 'collapsible';
    $rsvp = $row->rsvp;
    //    $this->printArr($rsvp);
    $memberRsvpStatus = $this->view->translate('Your RSVPs');
      $modules = Engine_Api::_()->getDbTable('modules', 'core');


      if ($modules->isModuleEnabled('heevent') && $subject->getPrice()>=0) {
        if(!$subject->getCouponsCount() && !Engine_Api::_()->apptouch()->isApp()){
            $yes = $this->dom()->new_('input', array('onclick'=>'javascript:void(0)','type' => 'button', 'name' => 'yes', 'id' => 'yes', 'value' => 'Tickets not found'));
            $eventRsvp = $this->dom()->new_('div', array('data-role' => "{$collapsible}", 'data-content-theme' => 'd', 'data-mini' => 'true', 'data-inset' => 'false'), '', array($this->dom()->new_('h3', array(), $memberRsvpStatus)));
            $rsvpContent = $this->dom()->new_('p', array(), '');

            $rsvpContent->append($this->dom()->new_('form', array(
                'action' =>'',
                'method' => '',
                'data-role' => 'controlgroup',
                'data-mini' => tdrue
            ), '', array(
                $yes,
                $this->dom()->new_('label', array('for' => 'yes'), $this->view->translate('Tickets is empty')),


            )));
            $eventRsvp->append($rsvpContent);
            return $eventRsvp;
        }elseif(Engine_Api::_()->apptouch()->isApp()){
            $yes = $this->dom()->new_('input', array('onclick'=>'javascript:void(0)','type' => 'button', 'name' => 'yes', 'id' => 'yes', 'value' => 'Not allowed'));
            $eventRsvp = $this->dom()->new_('div', array('data-role' => "{$collapsible}", 'data-content-theme' => 'd', 'data-mini' => 'true', 'data-inset' => 'false'), '', array($this->dom()->new_('h3', array(), $memberRsvpStatus)));
            $rsvpContent = $this->dom()->new_('p', array(), '');

            $rsvpContent->append($this->dom()->new_('form', array(
                'action' =>'',
                'method' => '',
                'data-role' => 'controlgroup',
                'data-mini' => tdrue
            ), '', array(
                $yes,
                $this->dom()->new_('label', array('for' => 'yes'), $this->view->translate('Not allowed')),


            )));
            $eventRsvp->append($rsvpContent);
            return $eventRsvp;
        }else{
            $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
            $yes = $this->dom()->new_('input', array('type' => 'submit', 'name' => 'yes', 'id' => 'yes', 'value' => 'Buy '.$subject->getPrice().'  '.$currency));
            $eventRsvp = $this->dom()->new_('div', array('data-role' => "{$collapsible}", 'data-content-theme' => 'd', 'data-mini' => 'true', 'data-inset' => 'false'), '', array($this->dom()->new_('h3', array(), $memberRsvpStatus)));
            $rsvpContent = $this->dom()->new_('p', array(), '');

            $rsvpContent->append($this->dom()->new_('form', array(
                'action' => $this->view->url(array('module' => 'heevent', 'controller' => 'index', 'action' => 'buy', 'event_id' => $subject->getIdentity()), 'default', true),
                'method' => 'post',
                'data-role' => 'controlgroup',
                'data-mini' => tdrue
            ), '', array(
                $yes,
                //$this->dom()->new_('label', array('for' => 'yes'), $this->view->translate('Buy ticket')),


            )));
            $eventRsvp->append($rsvpContent);
            return $eventRsvp;
          }

      }
    $attending = $this->dom()->new_('input', array('onclick' => "$(this.form).trigger('submit')", 'type' => 'radio', 'name' => 'rsvp', 'id' => 'rsvp_options_2', 'value' => 2));
    $maybeAttending = $this->dom()->new_('input', array('onclick' => "$(this.form).trigger('submit')", 'type' => 'radio', 'name' => 'rsvp', 'id' => 'rsvp_options_1', 'value' => 1));
    $notAttending = $this->dom()->new_('input', array('onclick' => "$(this.form).trigger('submit')", 'type' => 'radio', 'name' => 'rsvp', 'id' => 'rsvp_options_0', 'value' => 0));
    if ($rsvp == 2) {
      $memberRsvpStatus .= ' (' . $this->view->translate('Attending') . ')';
      $attending->attr('checked', true);
    }
    if ($rsvp == 1) {
      $memberRsvpStatus .= ' (' . $this->view->translate('Maybe Attending') . ')';
      $maybeAttending->attr('checked', true);
    }
    if ($rsvp == 0) {
        $memberRsvpStatus .= ' (' . $this->view->translate('Not Attending') . ')';
        $notAttending->attr('checked', true);
    }

    $eventRsvp = $this->dom()->new_('div', array('data-role' => "{$collapsible}", 'data-content-theme' => 'd', 'data-mini' => 'true', 'data-inset' => 'false'), '', array($this->dom()->new_('h3', array(), $memberRsvpStatus)));
    $rsvpContent = $this->dom()->new_('p', array(), '');

    $rsvpContent->append($this->dom()->new_('form', array(
      'action' => $this->view->url(array('module' => 'event', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $subject->getGuid()), 'default', true),
      'method' => 'post',
      'data-role' => 'controlgroup',
      'data-mini' => true
    ), '', array(
      $attending,
      $this->dom()->new_('label', array('for' => 'rsvp_options_2'), $this->view->translate('Attending')),
      $maybeAttending,
      $this->dom()->new_('label', array('for' => 'rsvp_options_1'), $this->view->translate('Maybe Attending')),
      $notAttending,
      $this->dom()->new_('label', array('for' => 'rsvp_options_0'), $this->view->translate('Not Attending')),
      $this->dom()->new_('input', array('type' => 'hidden', 'name' => 'event_id', 'value' => $subject->getIdentity()))
    )));
    $eventRsvp->append($rsvpContent);
    return $eventRsvp;
  }

// Tabs
  public function tabGuests($active = null)
  {
    // Get params
    $page = $this->_getParam('page', 1);
    $search = $this->_getParam('search');
    $waiting = $this->_getParam('waiting', false);

    // Prepare data
    $this->event = $event = Engine_Api::_()->core()->getSubject();

    $members = null;
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() && $event->isOwner($viewer)) {
      $waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
      if ($waiting) {
        $members = $waitingMembers;
      }
    }

    if (!$members) {
      $select = $event->membership()->getMembersObjectSelect();
      if ($search) {
        $select->where('displayname LIKE ?', '%' . $search . '%');
      }
      $members = Zend_Paginator::factory($select);
    }

    $paginator = $members;

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', $page));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0 && '' == $search) {
      return;
    }
    if ($active) {
      $this
        ->add($this->component()->itemSearch($this->getSearchForm()), 6)
        ->add($this->component()->itemList($paginator, 'guestListCustomizer'), 7);
    }
    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

//  Guest List customizer {
  public function guestListCustomizer(Core_Model_Item_Abstract $item)
  {

    if (!empty($item->resource_id)) {
      $memberInfo = $item;
      $item = $this->item('user', $memberInfo->user_id);
    } else {
      $memberInfo = $this->event->membership()->getMemberInfo($item);
    }

    if ($memberInfo->rsvp == 0)
      $rsvp = $this->view->translate('Not Attending');
    elseif ($memberInfo->rsvp == 1)
      $rsvp = $this->view->translate('Maybe Attending');
    elseif ($memberInfo->rsvp == 2)
      $rsvp = $this->view->translate('Attending');
    else
      $rsvp = $this->view->translate('Awaiting Reply');

    $customize_fields = array(
      'title' => $item->getTitle() . ($this->event->getParent()->getGuid() == ($item->getGuid()) ? $this->view->translate('(%s)', ($memberInfo->title ? $memberInfo->title : $this->view->translate('owner'))) : ''),
      'descriptions' => array(
        $rsvp
      ),
      'creation_date' => null
    );
    if ($this->event->isOwner($this->view->viewer())) {
      $manage = array();
      if (!$this->event->isOwner($item) && $memberInfo->active == true) {
        $manage[] = array(
          'label' => $this->view->translate('Remove Request'),
          'attrs' => array(
            'href' => $this->view->url(array('controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $item->getIdentity()), 'event_extended', true),
            'data-rel' => 'dialog'
          )
        );
      }
      if ($memberInfo->active == false && $memberInfo->resource_approved == false) {
        $manage[] = array(
          'label' => $this->view->translate('Approve Member'),
          'attrs' => array(
            'href' => $this->view->url(array('controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $item->getIdentity()), 'event_extended', true),
            'data-rel' => 'dialog'
          )
        );
        $manage[] = array(
          'label' => $this->view->translate('Reject Member'),
          'attrs' => array(
            'href' => $this->view->url(array('controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $item->getIdentity()), 'event_extended', true),
            'data-rel' => 'dialog'
          )
        );
      }

      if ($memberInfo->active == false && $memberInfo->resource_approved == true) {
        $manage[] = array(
          'label' => $this->view->translate('Remove Member'),
          'attrs' => array(
            'href' => $this->view->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $item->getIdentity()), 'event_extended', true),
            'data-rel' => 'dialog'
          )
        );
      }
    }
    $customize_fields['manage'] = $manage;
    return $customize_fields;
  }

//  } Guest List customizer
  public function tabPhotos($active = null)
  {
    // Get paginator
    $subject = Engine_Api::_()->core()->getSubject('event');
    $album = $subject->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $canUpload = $subject->authorization()->isAllowed(null, 'photo'); // todo upload

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show and cannot upload
    if ($paginator->getTotalItemCount() <= 0 && !$canUpload) {
      return;
    }

    if ($active) {
      $html = '<br /><div data-role="controlgroup" data-type="horizontal" style="text-align: center">';
      $html .= $this->view->htmlLink(array(
        'route' => 'event_extended',
        'controller' => 'photo',
        'action' => 'list',
        'subject' => $this->view->subject()->getGuid(),
      ), $this->view->translate('View All Photos'), array(
        'data-role' => 'button',
        'data-icon' => 'grid'
      ));
      if ($canUpload) {
        $html .= $this->view->htmlLink(array(
          'route' => 'event_extended',
          'controller' => 'photo',
          'action' => 'upload',
          'subject' => $this->view->subject()->getGuid(),
        ), $this->view->translate('Upload Photos'), array(
          'data-role' => 'button',
          'data-icon' => 'photo'
        ));
      }
      $html .= '</div>';
      $this
        ->add($this->component()->html($html), 8);
      if ($paginator->getTotalItemCount()) {
        $this
          ->add($this->component()->gallery($paginator), 9);
      }
    }
    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabDiscussions($active = null)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject('event');

    // Get paginator
    $table = Engine_Api::_()->getItemTable('event_topic');
    $select = $table->select()
      ->where('event_id = ?', $subject->getIdentity())
      ->order('sticky DESC')
      ->order('modified_date DESC');
    ;
    //    $this->view->paginator =
    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $can_post = $this->_helper->requireAuth->setAuthParams(null, null, 'comment')->checkRequire();
    if ($active && $this->view->viewer()->getIdentity()) {
      $this
        ->add($this->component()->html($this->view->htmlLink(array(
        'route' => 'event_extended',
        'controller' => 'topic',
        'action' => 'create',
        'subject' => $this->view->subject()->getGuid(),
      ), $this->view->translate('Post New Topic'), array(
        'data-icon' => 'plus',
        'data-role' => 'button'
      ))), 7)
        ->add($this->component()->itemSearch(new Apptouch_Form_Search()), 6)
        ->add($this->component()->itemList($paginator), 8);
    }


    // Do not render if nothing to show and not viewer
    if ($paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity()) {
      return;
    }
    return array(
      'showContent' => 0,
      'response' => $paginator
    );
  }

// Tabs
//  } Profile Controller


//  Event Controller {
  public function eventInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if ($id) {
      $event = Engine_Api::_()->getItem('event', $id);
      if ($event) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
  }

  public function eventEditAction()
  {
    $event_id = $this->getRequest()->getParam('event_id');
    $event = Engine_Api::_()->getItem('event', $event_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!($this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() || $event->isOwner($viewer))) {
      return;
    }

    // Create form
    $event = Engine_Api::_()->core()->getSubject();
    $form = new Event_Form_Edit(array('parent_type' => $event->parent_type, 'parent_id' => $event->parent_id));

    $this->setFormat('create');

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }

    if (!$this->getRequest()->isPost()) {
      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;

      if ($event->parent_type == 'group') {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      foreach ($roles as $role) {
        if (isset($form->auth_view->options[$role]) && $auth->isAllowed($event, $role, 'view')) {
          $form->auth_view->setValue($role);
        }
        if (isset($form->auth_comment->options[$role]) && $auth->isAllowed($event, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
        if (isset($form->auth_photo->options[$role]) && $auth->isAllowed($event, $role, 'photo')) {
          $form->auth_photo->setValue($role);
        }
      }
      $form->auth_invite->setValue($auth->isAllowed($event, 'member', 'invite'));
      $form->populate($event->toArray());

      // Convert and re-populate times
      $start = strtotime($event->starttime);
      $end = strtotime($event->endtime);
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $start = date('Y-m-d H:i:s', $start);
      $end = date('Y-m-d H:i:s', $end);
      date_default_timezone_set($oldTz);

      $form->populate(array(
        'starttime' => $start,
        'endtime' => $end,
      ));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    // Process
    $values = $form->getValues();

    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    // Check parent
    if (!isset($values['host']) && $event->parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
      $group = Engine_Api::_()->getItem('group', $event->parent_id);
      $values['host'] = $group->getTitle();
    }

    // Process
    $db = Engine_Api::_()->getItemTable('event')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set event info
      $event->setFromArray($values);
      $event->save();
      $picupFiles = $this->getPicupFiles('photo');
      if (empty($picupFiles) && !empty($values['photo'])) {
        $event->setPhoto($form->photo);
      }
      elseif (isset($picupFiles[0]) && $picupFiles[0])
        $event->setPhoto($picupFiles[0]);


      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      if ($event->parent_type == 'group') {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
      }

      $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

      // Commit
      $db->commit();
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      throw $e;
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
      return $this->add($this->component()->form($form))
        ->renderContent();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($event) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    if ($this->_getParam('ref') === 'profile') {
      $this->redirect($event);
    } else {
      $this->redirect($this->view->url(array('action' => 'manage'), 'event_general', true));
    }
  }

  public function eventInviteAction()
  {

    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event')->isValid()) return;
    // @todo auth

    // Prepare data
    $viewer = Engine_Api::_()->user()->getViewer();
    $event = Engine_Api::_()->core()->getSubject();
    $friends = $viewer->membership()->getMembers();

    // Prepare form
    $form = new Event_Form_Invite();

    $count = 0;
    foreach ($friends as $friend)
    {
      if ($event->membership()->isMember($friend, null)) continue;
      $form->users->addMultiOption($friend->getIdentity(), $friend->getTitle());
      $count++;
    }
    //    $this->view->count = $count;
    // Not posting
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = $event->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $usersIds = $form->getValue('users');

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      foreach ($friends as $friend)
      {
        if (!in_array($friend->getIdentity(), $usersIds)) {
          continue;
        }

        $event->membership()->addMember($friend)
          ->setResourceApproved($friend);

        $notifyApi->addNotification($friend, $viewer, $event, 'event_invite');
      }


      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Members invited'));
  }


  public function eventDeleteAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $event = Engine_Api::_()->getItem('event', $this->getRequest()->getParam('event_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'delete')->isValid()) return;

    // Make form
    $form = new Event_Form_Delete();

    if (!$event) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Event doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      //      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $event->delete();
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(
      $this->view->url(array('action' => 'manage'), 'event_general', true),
      $this->view->translate('The selected event has been deleted.'),
      true
    );
  }

//  } Event Controller


//  Member Controller {
  public function memberInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (0 !== ($event_id = (int)$this->_getParam('event_id')) &&
      null !== ($event = Engine_Api::_()->getItem('event', $event_id))
    ) {
      Engine_Api::_()->core()->setSubject($event);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('event');
    /*
   $this->_helper->requireAuth()->setAuthParams(
     null,
     null,
     null
     //'edit'
   );
    *
    */
  }

  public function memberJoinAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Check resource approval
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->membership()->isResourceApprovalRequired()) {
      $row = $subject->membership()->getReceiver()
        ->select()
        ->where('resource_id = ?', $subject->getIdentity())
        ->where('user_id = ?', $viewer->getIdentity())
        ->query()
        ->fetch(Zend_Db::FETCH_ASSOC, 0);
      ;
      if (empty($row)) {
        // has not yet requested an invite
        return $this->redirect($this->view->url(array('action' => 'request')));
      } elseif ($row['user_approved'] && !$row['resource_approved']) {
        // has requested an invite; show cancel invite page
        return $this->redirect($this->view->url(array('action' => 'cancel')));
      }
    }

    // Make form
    $form = new Event_Form_Member_Join();
    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $membership_status = $subject->membership()->getRow($viewer)->active;

        $subject->membership()
          ->addMember($viewer)
          ->setUserApproved($viewer);

        $row = $subject->membership()
          ->getRow($viewer);

        $row->rsvp = $form->getValue('rsvp');
        $row->save();

        // Add activity if membership status was not valid from before
        if (!$membership_status) {
          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activityApi->addActivity($viewer, $subject, 'event_join', null, array('is_mobile' => true));
        }

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect($subject, $this->view->translate('Event joined'));
    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

  public function memberRequestAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Make form
    $this->view->form = $form = new Event_Form_Member_Request();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->addMember($viewer)->setUserApproved($viewer);

        // Add notification
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'event_approve');

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      return $this->redirect(
        'refresh',
        Zend_Registry::get('Zend_Translate')->_('Your invite request has been sent.'),
        true
      );
    }
  }

  public function memberCancelAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Make form
    $form = new Event_Form_Member_Cancel();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $user_id = $this->_getParam('user_id');
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject->authorization()->isAllowed($viewer, 'invite') &&
        $user_id != $viewer->getIdentity() &&
        $user_id
      ) {
        return;
      }

      if ($user_id) {
        $user = Engine_Api::_()->getItem('user', $user_id);
        if (!$user) {
          return;
        }
      } else {
        $user = $viewer;
      }

      $subject = Engine_Api::_()->core()->getSubject('event');
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try
      {
        $subject->membership()->removeMember($user);

        // Remove the notification?
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
          $subject->getOwner(), $subject, 'event_approve');
        if ($notification) {
          $notification->delete();
        }

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      return $this->redirect(
        $subject,
        Zend_Registry::get('Zend_Translate')->_('Your invite request has been cancelled.'),
        true
      );
    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

  public function memberLeaveAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->isOwner($viewer)) return;

    // Make form
    $form = new Event_Form_Member_Leave();

    $this->add($this->component()->form($form))
      ->renderContent();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->removeMember($viewer);
        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect('refresh', $this->view->translate('Event left'));

    }
  }

  public function memberAcceptAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event')->isValid()) return;

    // Make form
    $form = new Event_Form_Member_Join();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process form
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $membership_status = $subject->membership()->getRow($viewer)->active;

      $subject->membership()->setUserApproved($viewer);

      $row = $subject->membership()
        ->getRow($viewer);

      $row->rsvp = $form->getValue('rsvp');
      $row->save();

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $viewer, $subject, 'event_invite');
      if ($notification) {
        $notification->mitigated = true;
        $notification->save();
      }

      // Add activity
      if (!$membership_status) {
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $subject, 'event_join', null, array('is_mobile' => true));
      }
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the event %s');
    $message = sprintf($message, $subject->__toString());

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Event invite accepted'), true);
  }

  public function memberRejectAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event')->isValid()) return;

    // Make form
    $form = new Event_Form_Member_Reject();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process form
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $viewer, $subject, 'event_invite');
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

    $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the event %s');
    $message = sprintf($message, $subject->__toString());
    $this->view->message = $message;

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Event invite rejected'), true);
  }

  public function memberRemoveAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    $event = Engine_Api::_()->core()->getSubject();

    if (!$event->membership()->isMember($user)) {
      throw new Event_Model_Exception('Cannot remove a non-member');
    }

    // Make form
    $form = new Event_Form_Member_Remove();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = $event->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        // Remove membership
        $event->membership()->removeMember($user);

        // Remove the notification?
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
          $event->getOwner(), $event, 'event_approve');
        if ($notification) {
          $notification->delete();
        }

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Event member removed.'));
    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

  public function memberInviteAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event')->isValid()) return;
    // @todo auth

    // Prepare data
    $viewer = Engine_Api::_()->user()->getViewer();
    //    $this->view->event =
    $event = Engine_Api::_()->core()->getSubject();

    // Prepare friends
    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsIds = $friendsTable->select()
      ->from($friendsTable, 'user_id')
      ->where('resource_id = ?', $viewer->getIdentity())
      ->where('active = ?', true)
      ->limit(100)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);
    if (!empty($friendsIds)) {
      $friends = Engine_Api::_()->getItemTable('user')->find($friendsIds);
    } else {
      $friends = array();
    }
    //    $this->view->friends = $friends;

    // Prepare form
    $form = new Event_Form_Invite();

    $count = 0;
    foreach ($friends as $friend) {
      if ($event->membership()->isMember($friend, null)) {
        continue;
      }
      $form->users->addMultiOption($friend->getIdentity(), $friend->getTitle());
      $count++;
    }
    //    $this->view->count = $count;

    // Not posting
    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      if ($count)
        $this->add($this->component()->form($form));
      else
        $this->add($this->component()->html($this->view->translate('You have no friends you can invite.')))
          ->add($this->component()->html('<a href="" data-role="button" data-rel="back">' . $this->view->translate('Close') . '</a>'));
      return $this->renderContent();
    }


    // Process
    $table = $event->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $usersIds = $form->getValue('users');

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      foreach ($friends as $friend)
      {
        if (!in_array($friend->getIdentity(), $usersIds)) {
          continue;
        }

        $event->membership()->addMember($friend)
          ->setResourceApproved($friend);

        $notifyApi->addNotification($friend, $viewer, $event, 'event_invite');
      }


      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
    $this->redirect($event, $this->view->translate('Members invited'));
  }

  public function memberApproveAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event')->isValid()) return;

    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    // Make form
    $form = new Event_Form_Member_Approve();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->setResourceApproved($user);

        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'event_accepted');

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Event request approved'));
    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

//  } Member Controller


//  Photo Controller {
  public function photoInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('event_photo', $photo_id))
      ) {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if (0 !== ($event_id = (int)$this->_getParam('event_id')) &&
        null !== ($event = Engine_Api::_()->getItem('event', $event_id))
      ) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'photo-upload',
      'photo-edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'photo-list' => 'event',
      'photo-upload' => 'event',
      'photo-view' => 'event_photo',
      'photo-edit' => 'event_photo',
    ));
  }

  public function photoListAction()
  {
    $event = Engine_Api::_()->core()->getSubject();
    $album = $event->getSingletonAlbum();

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid()) {
      return;
    }
    $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $canUpload = $event->authorization()->isAllowed(null, 'photo');

    if ($canUpload)
      $this->add($this->component()->html($this->dom()->new_('a', array(
        'data-role' => 'button',
        'data-icon' => 'photo',
        'href' => $this->view->url(array('controller' => 'photo', 'action' => 'upload', 'subject' => $this->view->subject()->getGuid()), 'event_extended', true),
      ), $this->view->translate('Upload Photos'))));
    $this
      ->add($this->component()->crumb(array(
      array(
        'label' => $event->getTitle(),
        'attrs' => array(
          'href' => $event->getHref()
        )
      ),
      array(
        'label' => $this->view->translate('Photos'),
        'attrs' => array(
          'data-icon' => 'arrow-d'
        ),
        'active' => true
      )
    )))
      ->add($this->component()->gallery($paginator))
      ->renderContent();

  }

  public function photoViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->event = $event = $photo->getEvent();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid()) {
      return;
    }

    if (!$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity()) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
  }

  public function photoUploadAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();
    $album = $event->getSingletonAlbum();

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'photo')->isValid()) {
      return;
    }
    $form = new Event_Form_Photo_Upload();
    $form->removeElement('file');
    $form->addElement('File', 'file', array(
      'label' => 'APPTOUCH_Upload Photos',
      'order' => 0,
      'isArray' => true
    ));
    $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('event_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $photoTable = Engine_Api::_()->getItemTable('event_photo');
      $photodb = $photoTable->getAdapter();
      $photodb->beginTransaction();
      $picupFiles = $this->getPicupFiles('file');

      if (empty($picupFiles))
        $photos = $form->file->getFileName();
      else
        $photos = $picupFiles;
      $count = 0;
      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $event, 'event_photo_upload', null, array('is_mobile' => true, 'count' => count($values['file'])));

      // Do other stuff
      if (is_array($photos))
        foreach ($photos as $photoPath) {
          $photo = $photoTable->createRow();
          $photo->setFromArray(array(
            // We can set them now since only one album is allowed
            'collection_id' => $album->getIdentity(),
            'album_id' => $album->getIdentity(),

            'event_id' => $event->getIdentity(),
            'user_id' => $viewer->getIdentity(),
          ));
          $photo->setPhoto($photoPath);
          $photo->save();
          if ($action instanceof Activity_Model_Action && $count < 8) {
            $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
          }
          $count++;

        } else {
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
          // We can set them now since only one album is allowed
          'collection_id' => $album->getIdentity(),
          'album_id' => $album->getIdentity(),

          'event_id' => $event->getIdentity(),
          'user_id' => $viewer->getIdentity(),
        ));
        $photo->setPhoto($photos);
        $photo->save();
      }
      $photodb->commit();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $this->redirect($event->getHref(array('tab' => 'photos')));
  }

  public function uploadPhotoAction()
  {
    $event = Engine_Api::_()->getItem('event', $this->_getParam('event_id'));

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'photo')->isValid()) {
      return;
    }

    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      //      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    // @todo check auth
    //$event

    $values = $this->getRequest()->getPost();
    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $event->getSingletonAlbum();

      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'event_id' => $event->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      $photoTable = Engine_Api::_()->getItemTable('event_photo');
      $photo = $photoTable->createRow();
      $photo->setFromArray($params);
      $photo->save();

      $photo->setPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->getIdentity();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      // throw $e;
      return;
    }
  }

  public function photoEditAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();

    if (!$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid()) {
      return;
    }

    $form = new Event_Form_Photo_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Changes saved'));
  }

  public function photoDeleteAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $event = $photo->getParent('event');

    if (!$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid()) {
      return;
    }

    $form = new Event_Form_Photo_Delete();

    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($event, Zend_Registry::get('Zend_Translate')->_('Photo deleted'));
  }

//  } Photo Controller


//  Post Controller {
  public function postInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (Engine_Api::_()->core()->hasSubject()) return;

    if (0 !== ($post_id = (int)$this->_getParam('post_id')) &&
      null !== ($post = Engine_Api::_()->getItem('event_post', $post_id))
    ) {
      Engine_Api::_()->core()->setSubject($post);
    }

    $this->_helper->requireUser->addActionRequires(array(
      'post-edit',
      'post-delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'post-edit' => 'event_post',
      'post-delete' => 'event_post',
    ));
  }

  public function postEditAction()
  {
    $post = Engine_Api::_()->core()->getSubject('event_post');
    $event = $post->getParent('event');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event->isOwner($viewer) && !$post->isOwner($viewer)) {
      if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) {
        return;
      }
    }

    $form = new Event_Form_Post_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate($post->toArray());
      $form->body->setValue(html_entity_decode($post->body));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $post->setFromArray($form->getValues());
      $post->modified_date = date('Y-m-d H:i:s');
      $post->body = htmlspecialchars($post->body, ENT_NOQUOTES, 'UTF-8');
      $post->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
    // Try to get topic
    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));

  }

  public function postDeleteAction()
  {
    $post = Engine_Api::_()->core()->getSubject('event_post');
    $event = $post->getParent('event');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event->isOwner($viewer) && !$post->isOwner($viewer)) {
      if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) {
        return;
      }
    }

    $form = new Event_Form_Post_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {

      $topic_id = $post->topic_id;
      $post->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Try to get topic
    $topic = Engine_Api::_()->getItem('event_topic', $topic_id);
    $href = (null === $topic ? $event->getHref() : $topic->getHref());
    return $this->redirect($href, Zend_Registry::get('Zend_Translate')->_('Post deleted.'));

  }

//  } Post Controller


//  } Topic Controller
  public function topicInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (Engine_Api::_()->core()->hasSubject()) return;

    /*
   if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
       null !== ($post = Engine_Api::_()->getItem('event_post', $post_id)) )
   {
     Engine_Api::_()->core()->setSubject($post);
   }

   else */
    if (0 !== ($topic_id = (int)$this->_getParam('topic_id')) &&
      null !== ($topic = Engine_Api::_()->getItem('event_topic', $topic_id))
    ) {
      Engine_Api::_()->core()->setSubject($topic);
    }

    else if (0 !== ($event_id = (int)$this->_getParam('event_id')) &&
      null !== ($event = Engine_Api::_()->getItem('event', $event_id))
    ) {
      Engine_Api::_()->core()->setSubject($event);
    }

    $this->_helper->requireUser->addActionRequires(array(
      'topic-close', 'topic-create', 'topic-delete', 'topic-post', 'topic-rename', 'topic-reply', 'topic-sticky', 'topic-watch',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'topic-close' => 'event_topic',
      'topic-create' => 'event',
      'topic-delete' => 'event_topic',
      'topic-index' => 'event',
      'topic-post' => 'event_topic',
      'topic-rename' => 'event_topic',
      'topic-reply' => 'event_topic',
      'topic-sticky' => 'event_topic',
      'topic-view' => 'event_topic',
      'topic-watch' => 'event_topic',
    ));
  }

  public function topicIndexAction()
  {
    if (!$this->_helper->requireSubject('event')->isValid()) return;
    //if( !$this->_helper->requireAuth()->setAuthParams()->isValid() ) return;

    $this->view->event = $event = Engine_Api::_()->core()->getSubject();

    $table = Engine_Api::_()->getDbtable('topics', 'event');
    $select = $table->select()
      ->where('event_id = ?', $event->getIdentity())
      ->order('sticky DESC')
      ->order('modified_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $can_post = $this->_helper->requireAuth->setAuthParams(null, null, 'comment')->checkRequire();
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  public function topicViewAction()
  {
    if (!$this->_helper->requireSubject('event_topic')->isValid()) return;
    //if( !$this->_helper->requireAuth()->setAuthParams()->isValid() ) return;

    $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->topic = $topic = Engine_Api::_()->core()->getSubject();
    $this->event = $event = $topic->getParentEvent();

    $this->canEdit = $canEdit = $event->authorization()->isAllowed($viewer, 'edit');
    $this->canPost = $canPost = $event->authorization()->isAllowed($viewer, 'comment');
    $this->canAdminEdit = Engine_Api::_()->authorization()->isAllowed($event, null, 'edit');

    if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
      $isWatching = $topicWatchesTable
        ->select()
        ->from($topicWatchesTable->info('name'), 'watch')
        ->where('resource_id = ?', $event->getIdentity())
        ->where('topic_id = ?', $topic->getIdentity())
        ->where('user_id = ?', $viewer->getIdentity())
        ->limit(1)
        ->query()
        ->fetchColumn(0);
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool)$isWatching;
      }
    }
    $this->isWatching = $isWatching;

    // @todo implement scan to post
    $this->post_id = $post_id = (int)$this->_getParam('post');

    $table = Engine_Api::_()->getDbtable('posts', 'event');
    $select = $table->select()
      ->where('event_id = ?', $event->getIdentity())
      ->where('topic_id = ?', $topic->getIdentity())
      ->order('creation_date ASC');

    $paginator = $paginator = Zend_Paginator::factory($select);

    // Skip to page of specified post
    if (0 !== ($post_id = (int)$this->_getParam('post_id')) &&
      null !== ($post = Engine_Api::_()->getItem('event_post', $post_id))
    ) {
      $icpp = $paginator->getItemCountPerPage();
      $page = ceil(($post->getPostIndex() + 1) / $icpp);
      $paginator->setCurrentPageNumber($page);
    }

    // Use specified page
    else if (0 !== ($page = (int)$this->_getParam('page'))) {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    if ($canPost && !$topic->closed) {
      $form = new Event_Form_Post_Create();
      $form->setAction($topic->getHref(array('action' => 'post')));
      $form->populate(array(
        'topic_id' => $topic->getIdentity(),
        'ref' => $topic->getHref(),
        'watch' => (false === $isWatching ? '0' : '1'),
      ));
    }

    $options = array();
    if ($canPost && !$topic->closed)
      $options[] = array(
        'label' => $this->view->translate('Post Reply'),
        'attrs' => array(
          'href' => $this->topic->getHref(array('action' => 'post')),
          'data-icon' => 'chat'
        )
      );

    if ($viewer->getIdentity()) {
      if (!$isWatching)
        $options[] = array(
          'label' => $this->view->translate('Watch Topic'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'watch', 'watch' => '1')),
            'data-icon' => 'wifi'
          )
        );
      else
        $options[] = array(
          'label' => $this->view->translate('Stop Watching Topic'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'watch', 'watch' => '0')),
            'data-icon' => 'wifi'
          )
        );
    }

    if ($this->canEdit || $this->canDelete) {
      if ($this->canEdit) {
        if (!$topic->sticky)
          $options[] = array(
            'label' => $this->view->translate('Make Sticky'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'sticky', 'sticky' => '1')),
              'data-icon' => 'page'
            )
          );
        else
          $options[] = array(
            'label' => $this->view->translate('Remove Sticky'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'sticky', 'sticky' => '0')),
              'data-icon' => 'delete'
            )
          );
        if (!$topic->closed)
          $options[] = array(
            'label' => $this->view->translate('Close'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'close', 'close' => '1')),
              'data-icon' => 'lock'
            )
          );
        else
          $options[] = array(
            'label' => $this->view->translate('Open'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'close', 'close' => '0')),
              'data-icon' => 'wifi'
            )
          );

        $options[] = array(
          'label' => $this->view->translate('Rename'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'rename')),
            'data-icon' => 'edit',
            'data-rel' => 'dialog'
          )
        );
        $options[] = array(
          'label' => $this->view->translate('Delete'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'delete')),
            'data-icon' => 'delete',
            'data-rel' => 'dialog'
          )
        );
      }
    }
    $links = array(
      array(
        'label' => $event->getTitle(),
        'attrs' => array(
          'href' => $event->getHref()
        )
      ),
      array(
        'label' => $this->view->translate("Discussions"),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'topic',
            'action' => 'index',
            'event_id' => $this->event->getIdentity()), 'event_extended', true),
          'class' => 'ui-btn-active',
          'data-icon' => 'arrow-d'
        )
      )
    );

    $this
      ->add($this->component()->crumb($links))
      ->add($this->component()->discussion($topic, $paginator, array(
      'options' => $options,
      'postForm' => isset($form) ? $form : null
    ), 'topicPostCustomize'))
      ->add($this->component()->paginator($paginator))
      ->renderContent();

  }

  public function topicPostCustomize($post)
  {
    $postFormat = array();
    $options = array();
    if ($this->canPost && !$this->topic->closed)
      $options[] = array(
        'label' => $this->view->translate('Quote'),
        'attrs' => array(
          'href' => $this->view->url(array(
            'controller' => 'topic',
            'action' => 'post',
            'event_id' => $this->event->getIdentity(),
            'topic_id' => $this->view->subject()->getIdentity(),
            'quote_id' => $post->getIdentity(),
          ), 'event_extended'),
          'data-icon' => 'chat'
        )
      );
    if ($post->user_id ==
      $this->view->viewer()->getIdentity() ||
      $this->event->getOwner()->getIdentity() == $this->view->viewer()->getIdentity() ||
      $this->canAdminEdit
    ) {

      $options[] = array(
        'label' => $this->view->translate('Edit'),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity()), 'event_extended'),
          'data-icon' => 'edit'
        )
      );

      $options[] = array(
        'label' => $this->view->translate('Delete'),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity()), 'event_extended'),
          'data-icon' => 'delete',
          'data-rel' => 'dialog'
        )
      );
    }

    $postFormat['options'] = $options;
    $postFormat['owner'] = array();

    $user = $this->view->item('user', $post->user_id);
    $isOwner = false;
    $isMember = false;
    if ($this->event->isOwner($user)) {
      $isOwner = true;
      $isMember = true;
    } else if ($this->event->membership()->isMember($user)) {
      $isMember = true;
    }

    $postFormat['owner']['postCount'] = $isOwner ? $this->view->translate('Host') : ($isMember ? $this->view->translate('Member') : false);

    return $postFormat;
  }

  public function topicCreateAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) return;

    $event = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // Make form
    $form = new Event_Form_Topic_Create();
    $links = array(
      array(
        'label' => $event->getTitle(),
        'attrs' => array(
          'href' => $event->getHref()
        )
      ),
      array(
        'label' => $this->view->translate("Discussions"),
        'attrs' => array(
          'class' => 'ui-btn-active',
          'data-icon' => 'arrow-d'
        )
      ),
    );
    $this
      ->add($this->component()->crumb($links));

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['event_id'] = $event->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'event');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
    $postTable = Engine_Api::_()->getDbtable('posts', 'event');

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create topic
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->save();

      // Create post
      $values['topic_id'] = $topic->topic_id;

      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      // Create topic watch
      $topicWatchesTable->insert(array(
        'resource_id' => $event->getIdentity(),
        'topic_id' => $topic->getIdentity(),
        'user_id' => $viewer->getIdentity(),
        'watch' => (bool)$values['watch'],
      ));

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $topic, 'event_topic_create', null, array('is_mobile' => true));
      if ($action) {
        $action->attach($topic);
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect to the post
    return $this->redirect($post);
  }

  public function topicPostAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('event_topic')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) return;

    $topic = Engine_Api::_()->core()->getSubject();
    $event = $topic->getParentEvent();

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This has been closed for posting.');
      return;
    }

    // Make form
    $form = new Event_Form_Post_Create();
    $form->removeAttrib('action');
    $quoteText = '';
    $quote_id = $this->getRequest()->getParam('quote_id');
    if (!empty($quote_id)) {
      $quote = Engine_Api::_()->getItem('event_post', $quote_id);
      if ($quote->user_id == 0) {
        $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
      } else {
        $owner_name = $quote->getOwner()->__toString();
      }
      $quoteText = "<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />";
      $this->add($this->component()->html($quoteText));
      //      $form->body->setValue("<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />");
    }

    $links = array(
      array(
        'label' => $event->getTitle(),
        'attrs' => array(
          'href' => $event->getHref()
        )
      ),
      array(
        'label' => $this->view->translate("Discussions"),
        'attrs' => array(
          'href' => $event->getHref(array('tab' => 'discussions'))
        )
      ),
      array(
        'label' => $topic->getTitle(),
        'attrs' => array(
          'href' => $topic->getHref(),
          'class' => 'ui-btn-active',
          'data-icon' => 'arrow-d'
        )
      ),
    );
    $this
      ->add($this->component()->crumb($links));

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $topicOwner = $topic->getOwner();
    $isOwnTopic = $viewer->isSelf($topicOwner);

    $postTable = Engine_Api::_()->getDbtable('posts', 'event');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
    $userTable = Engine_Api::_()->getItemTable('user');
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

    $values = $form->getValues();
    $values['body'] = $quoteText . $values['body'];
    $values['user_id'] = $viewer->getIdentity();
    $values['event_id'] = $event->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool)$values['watch'];
    $isWatching = $topicWatchesTable
      ->select()
      ->from($topicWatchesTable->info('name'), 'watch')
      ->where('resource_id = ?', $event->getIdentity())
      ->where('topic_id = ?', $topic->getIdentity())
      ->where('user_id = ?', $viewer->getIdentity())
      ->limit(1)
      ->query()
      ->fetchColumn(0);

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create post
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      // Watch
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
          'resource_id' => $event->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool)$watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
          'watch' => (bool)$watch,
        ), array(
          'resource_id = ?' => $event->getIdentity(),
          'topic_id = ?' => $topic->getIdentity(),
          'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      // Activity
      $action = $activityApi->addActivity($viewer, $topic, 'event_topic_reply', null);
      if ($action) {
        $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
      }

      // Notifications
      $notifyUserIds = $topicWatchesTable->select()
        ->from($topicWatchesTable->info('name'), 'user_id')
        ->where('resource_id = ?', $event->getIdentity())
        ->where('topic_id = ?', $topic->getIdentity())
        ->where('watch = ?', 1)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

      foreach ($userTable->find($notifyUserIds) as $notifyUser) {
        // Don't notify self
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }
        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'event_discussion_response';
        } else {
          $type = 'event_discussion_reply';
        }
        $notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
          'message' => $this->view->BBCode($post->body),
        ));
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect to the post
    return $this->redirect($post);
  }

  public function topicStickyAction()
  {
    $topic = Engine_Api::_()->core()->getSubject();
    $event = Engine_Api::_()->getItem('event', $topic->event_id);
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) return;

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->sticky = (null === $this->_getParam('sticky') ? !$topic->sticky : (bool)$this->_getParam('sticky'));
      $topic->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($topic);
  }

  public function topicCloseAction()
  {
    $topic = Engine_Api::_()->core()->getSubject();
    $event = Engine_Api::_()->getItem('event', $topic->event_id);
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) return;


    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->closed = (null === $this->_getParam('closed') ? !$topic->closed : (bool)$this->_getParam('closed'));
      $topic->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($topic);
  }

  public function topicRenameAction()
  {

    $topic = Engine_Api::_()->core()->getSubject();
    $event = Engine_Api::_()->getItem('event', $topic->event_id);
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) return;

    $form = new Event_Form_Topic_Rename();

    if (!$this->getRequest()->isPost()) {
      $form->title->setValue(htmlspecialchars_decode($topic->title));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $title = $form->getValue('title');

      $topic = Engine_Api::_()->core()->getSubject();
      $topic->title = htmlspecialchars($title);
      $topic->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Topic renamed.'));
  }

  public function topicDeleteAction()
  {


    $topic = Engine_Api::_()->core()->getSubject();
    $event = Engine_Api::_()->getItem('event', $topic->event_id);
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) return;

    $form = new Event_Form_Topic_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $topic = Engine_Api::_()->core()->getSubject();
      $event = $topic->getParent('event');
      $topic->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($event, Zend_Registry::get('Zend_Translate')->_('Topic deleted.'));

  }

  public function topicWatchAction()
  {
    $topic = Engine_Api::_()->core()->getSubject();
    $event = Engine_Api::_()->getItem('event', $topic->event_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid()) {
      return;
    }

    $watch = $this->_getParam('watch', true);

    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $isWatching = $topicWatchesTable
        ->select()
        ->from($topicWatchesTable->info('name'), 'watch')
        ->where('resource_id = ?', $event->getIdentity())
        ->where('topic_id = ?', $topic->getIdentity())
        ->where('user_id = ?', $viewer->getIdentity())
        ->limit(1)
        ->query()
        ->fetchColumn(0);

      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
          'resource_id' => $event->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool)$watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
          'watch' => (bool)$watch,
        ), array(
          'resource_id = ?' => $event->getIdentity(),
          'topic_id = ?' => $topic->getIdentity(),
          'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($topic);
  }

//  } Topic Controller

//  Widget Controller {
  public function widgetProfileInfoAction()
  {
    // Don't render this if not authorized
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid())
      return $this->_helper->viewRenderer->setNoRender(true);
  }

  public function widgetProfileRsvpAction()
  {

    //    $this->view->form = new Event_Form_Rsvp();
    $event = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$event->membership()->isMember($viewer, true)) {
      return $this->redirect($event);
    }
    $row = $event->membership()->getRow($viewer);
    //    $this->view->viewer_id = $viewer->getIdentity();
    if (!$row) {
      return $this->redirect($event);
    }
    if ($this->getRequest()->isPost()) {
      $option_id = $this->getRequest()->getParam('rsvp');

      $row->rsvp = $option_id;
      $this->view->rsvp = $row->rsvp;
      $row->save();
    }
    return $this->redirect($event);
  }

  public function widgetRequestEventAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }

//  } Widget Controller
    public function getHeventBuyForm(Heevent_Model_Event $event = null, $price = 0)
    {

        $ticketForm = new Heevent_Form_Ticket($event,$price);
        $ticketForm->setAction($this->view->url(array('event_id' => $event->getIdentity()), 'heevent_payment', true));
        $form = $ticketForm->render($this->view);
        $content = <<<CONTENT
                <script>
        function price_changer(d, id){
    var count = parseInt(d.value)+1
var p = parseInt($('price_heevent'+id).value);
    $$('.heticket_price_'+id+' #price_tag').set('html',p*count);
}
        </script>
       <div class="ticket_form_wrapper" >{$form}</div>
CONTENT;
        return $content;
    }
    public function  ticketItemData(Heevent_Model_Event $item){
        $customize_fields = array(
            'descriptions' => array(
                $this->view->translate('Ticket code') . ' - ' . $this->view->ticketCodes[$item['event_id']],
            ),
            'creation_date' => null,
        );
        return $customize_fields;
    }

}