<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 07.06.12
 * Time: 19:18
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PollController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // Get subject
    $poll = null;
    if (null !== ($pollIdentity = $this->_getParam('poll_id'))) {
      $poll = Engine_Api::_()->getItem('poll', $pollIdentity);
      if (null !== $poll) {
        Engine_Api::_()->core()->setSubject($poll);
      }
    }

    // Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    // only show polls if authorized
    $resource = ($poll ? $poll : 'poll');
    $viewer = ($viewer && $viewer->getIdentity() ? $viewer : null);
    if (!$this->_helper->requireAuth()->setAuthParams($resource, $viewer, 'view')->isValid()) {
      return;
    }

  }

  public function indexBrowseAction()
  {
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');

    // Get form

    // Process form
    $values = $this->_getAllParams();
    // deleted some of code
    $values['search'] = null;

    if (@$values['show'] == 2 && $viewer->getIdentity()) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);

    // Make paginator
    $currentPageNumber = $this->_getParam('page', 1);
    $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perPage', 10);
    $table = Engine_Api::_()->getItemTable('poll');
    $sName = $table->info('name');

    $select = $table
           ->getPollSelect($values);
     if ($this->_getParam('search', false)) {
       $select->where('`' . $sName . '`.title LIKE ? OR `' . $sName . '`.description LIKE ?', '%' . $this->_getParam('search') . '%');
     }
     $paginator = Zend_Paginator::factory($select);
    $paginator
      ->setItemCountPerPage($itemCountPerPage)
      ->setCurrentPageNumber($currentPageNumber);
    $this->setFormat('browse')
      ->add($this->component()->itemSearch($this->getSearchForm()));

    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->_getParam('search', false)) {
      $this->add($this->component()->tip(
        $this->view->translate('APPTOUCH_There are no polls with that criteria.')
      ));
    } else {
      if($canCreate)
        $this->add($this->component()->tip(
          $this->view->translate('Why don\'t you %1$screate one%2$s?',
                    '<a href="'.$this->view->url(array('action' => 'create'), 'poll_general').'">', '</a>'),
          $this->view->translate('There are no polls yet.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('There are no polls yet.')
        ));

    }
    $this->renderContent();
  }

  public function indexManageAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid()) {
      return;
    }

    $owner = Engine_Api::_()->user()->getViewer();
    $values = $this->_getAllParams(); // todo $this->_getAllParams() is it safe?
    $values['user_id'] = $owner->getIdentity();
    $values['search'] = null;

    $currentPageNumber = $this->_getParam('page', 1);
    $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perPage', 10);

    $table = Engine_Api::_()->getItemTable('poll');
    $sName = $table->info('name');

    $select = $table
           ->getPollSelect($values);
     if ($search = $this->_getParam('search', false)) {
       $select->where('`' . $sName . '`.title LIKE ? OR `' . $sName . '`.description LIKE ?', '%' . $this->_getParam('search') . '%');
     }

     $paginator = Zend_Paginator::factory($select);
    $paginator
      ->setItemCountPerPage($itemCountPerPage)
      ->setCurrentPageNumber($currentPageNumber);

    $this->setFormat('manage')
      ->add($this->component()->itemSearch($this->getSearchForm()));

    $canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');

    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($search) {
      $this->add($this->component()->tip(
        $this->view->translate('APPTOUCH_There are no polls with that criteria.')
      ));
    } else {
      if($canCreate)
        $this->add($this->component()->tip(
          $this->view->translate('Why don\'t you %1$screate one%2$s?',
                    '<a href="'.$this->view->url(array('action' => 'create'), 'poll_general').'">', '</a>'),
          $this->view->translate('There are no polls yet.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('There are no polls yet.')
        ));

    }
    $this->renderContent();
  }

  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'photo' => $owner->getPhotoUrl('thumb.normal'),
      'counter' => strtoupper($this->view->translate(array('%s vote', '%s votes', $item->vote_count), $this->view->locale()->toNumber($item->vote_count))),
    );

    if($item->closed)
      $customize_fields['attrsLi'] = array(
                'data-icon' => 'lock'
              );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $options = array();

    $options[] = $this->getOption($item, 0);
    if (!$item->closed) {
      $options[] = $this->getOption($item, 1);
    } else {
      $options[] = $this->getOption($item, 2);
    }
    $options[] = $this->getOption($item, 3);

    $owner = $item->getOwner();

    $customize_fields = array(
      'descriptions' => null,
      'photo' => $owner->getPhotoUrl('thumb.normal'),
      'owner_id' => null,
      'owner' => null,
      'counter' => strtoupper($this->view->translate(array('%s vote', '%s votes', $item->vote_count), $this->view->locale()->toNumber($item->vote_count))),
      'manage' => $options
    );

    if($item->closed){
      $customize_fields['descriptions'] = array($this->view->translate('APPTOUCH_Closed'));
      $customize_fields['attrsLi'] = array(
        'data-theme' => 'd'
      );
    }

    return $customize_fields;
  }

  public function pollInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // Get subject
    $poll = null;
    if( null !== ($pollIdentity = $this->_getParam('poll_id')) ) {
      $poll = Engine_Api::_()->getItem('poll', $pollIdentity);
      if( null !== $poll ) {
        Engine_Api::_()->core()->setSubject($poll);
      }
    }

    // Get viewer
    $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    // only show polls if authorized
    $resource = ( $poll ? $poll : 'poll' );
    $viewer = ( $viewer && $viewer->getIdentity() ? $viewer : null );
    if( !$this->_helper->requireAuth()->setAuthParams($resource, $viewer, 'view')->isValid() ) {
      return;
    }
  }

  public function pollCloseAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $poll = Engine_Api::_()->getItem('poll', $this->_getParam('poll_id'));
    if( !Engine_Api::_()->core()->hasSubject('poll') ) {
      Engine_Api::_()->core()->setSubject($poll);
    }
//    $this->view->poll = $poll;

    // Check auth
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams($poll, $viewer, 'edit')->isValid() ) {
      return;
    }

    // @todo convert this to post only

    $table = $poll->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $poll->closed = (bool) $this->_getParam('closed');
      $poll->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    if( !($returnUrl = $this->_getParam('return_url')) ) {
      return $this->redirect($this->view->url(array('action' => 'manage', 'nocache' => rand(0, 1000)), 'poll_general', true));
    } else {
      return $this->redirect($returnUrl);
    }
  }

  public function pollDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $poll = Engine_Api::_()->getItem('poll', $this->getRequest()->getParam('poll_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($poll, null, 'delete')->isValid()) return;


    $form = new Poll_Form_Delete();

    if( !$poll ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Poll doesn't exist or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $poll->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $poll->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your poll has been deleted.');
    return $this->redirect($this->view->url(array('action' => 'manage', 'nocache' => rand(0, 1000)), 'poll_general', true));
  }

  public function pollEditAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }

    // Get navigation
//    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
//      ->getNavigation('poll_main');

    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
    $poll = Engine_Api::_()->core()->getSubject('poll');

    // Get form
    $form = new Poll_Form_Edit();
    $form->removeElement('title');
    $form->removeElement('description');
    $form->removeElement('options');

    // Prepare privacy
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    // Populate form with current settings
    $form->search->setValue($poll->search);
    foreach( $roles as $role ) {
      if( 1 === $auth->isAllowed($poll, $role, 'view') ) {
        $form->auth_view->setValue($role);
      }
      if( 1 === $auth->isAllowed($poll, $role, 'comment') ) {
        $form->auth_comment->setValue($role);
      }
    }

    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      // CREATE AUTH STUFF HERE
      if( empty($values['auth_view']) ) {
        $values['auth_view'] = array('everyone');
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = array('everyone');
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
      }

      $poll->search = (bool) $values['search'];
      $poll->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($poll) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('action' => 'manage', 'nocache' => rand(0, 1000)), 'poll_general', true));
  }

  public function pollViewAction()
  {
    // Check auth
    if( !$this->_helper->requireSubject('poll')->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }

    $poll = Engine_Api::_()->core()->getSubject('poll');
    $this->setFormat('view')
      ->poll($poll)
      ->renderContent();

  }

  protected function poll($poll)
  {
    $owner = $poll->getOwner();
    $viewer = Engine_Api::_()->user()->getViewer();
    $pollOptions = $poll->getOptions();
    $hasVoted = $poll->viewerVoted();
//    $showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.showpiechart', false);
    $canVote = $poll->authorization()->isAllowed(null, 'vote');
    $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);

    if( !$owner->isSelf($viewer) ) {
      $poll->view_count++;
      $poll->save();
    }
    $options = array();
    $answers = array();
    foreach( $pollOptions as $i => $option ){
      $pct = $poll->vote_count ? floor(100*($option->votes/$poll->vote_count)) : 0;
      if (!$pct)
        $pct = 1;
      $options[$option->poll_option_id] = $option->poll_option;
      $answers[] = $this->dom()->new_('li', array(), '', array(
        $this->dom()->new_('span', array('class' => 'chart-item poll_answer poll-answer-' .(($i%8)+1), 'style' => 'width: ' . $pct . '%'), ' '),
        $this->dom()->new_('div', array('class' => 'answer-body'), $option->poll_option . '(' . $this->view->translate('%1$s%%', $this->view->locale()->toNumber($option->votes ? $pct : 0)) . ')'),
        $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->translate(array('%1$s vote', '%1$s votes', $option->votes), $this->view->locale()->toNumber($option->votes)))
      ));
    }
    $this->add($this->component()->html($this->dom()->new_('h3', array(), $poll->getTitle())));
    $this->add($this->component()->html($this->dom()->new_('p', array(), $poll->description)));
    if($canVote && (!$hasVoted || $canChangeVote) && !$poll->closed ){
      $form = new Engine_Form();
      $form->setAction($this->view->url(array('module' => 'poll', 'controller' => 'poll', 'action' => 'vote', 'poll_id' => $poll->getIdentity()), 'default', true))
        ->addAttribs(array(
        'id' => 'poll_form_' . $poll->getIdentity(),
        'class' => 'poll_form'
      ));
      if($hasVoted)
        $form->setAttrib('style', 'display: none');
      $form->addElement('Radio', 'option_id', array(
        'multiOptions' => $options,
        'value' => $hasVoted
      ))
        ->addElement('Button', 'submit', array(
        'type' => 'submit',
        'label' => 'Send'
      ));
      $this->add($this->component()->form($form));
    }

    $results = $this->dom()->new_('ul', array('class' => 'poll_view_single', 'data-role' => 'listview', 'data-inset' => true, 'data-mini' => 'true'), '',
      $answers
    );
    if(!$hasVoted){
      $results->attr('style', 'display: none');
    }
    $btnGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal'), '', array(
      $this->dom()->new_('a', array('data-role' => 'button', 'data-icon' => $hasVoted ? 'question' : 'check', 'class' => $hasVoted ? 'aqSwitcher showQ' : 'aqSwitcher showA'), $hasVoted ? $this->view->translate('Show Questions') : $this->view->translate('Show Results')),
    ));

    if( $viewer && $viewer->getIdentity() ) {
      $btnGroup->append($this->dom()->new_('a', array('data-role' => 'button', 'data-rel' => 'dialog', 'data-icon' => 'chat', 'href' => $this->view->url(array(
        'module'=>'activity',
        'controller'=>'index',
        'action'=>'share',
        'type'=>'poll',
        'id' => $poll->getIdentity(),
      ), 'default', true)
      ), $this->view->translate("Share")));

      $btnGroup->append($this->dom()->new_('a', array('data-role' => 'button', 'data-rel' => 'dialog', 'data-icon' => 'flag', 'href' => $this->view->url(array(
        'module'=>'core',
        'controller'=>'report',
        'action'=>'create',
        'subject' => $poll->getGuid(),
      ), 'default', true)
      ), $this->view->translate("Report")));
    }

    $this->lang(array(
      'Show Questions',
      'Show Results'
    ));

    $this->add($this->component()->html($results))
      ->add($this->component()->html($btnGroup));

    return $this;
  }

  public function pollVoteAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'vote')->isValid() ) {
      return;
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $option_id = $this->_getParam('option_id');
    $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);

    $poll = Engine_Api::_()->core()->getSubject('poll');
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$poll ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll does not seem to exist anymore.');
      return;
    }

    if( $poll->closed ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll is closed.');
      return;
    }

    if( $poll->hasVoted($viewer) && !$canChangeVote ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You have already voted on this poll, and are not permitted to change your vote.');
      return;
    }

    $db = Engine_Api::_()->getDbtable('polls', 'poll')->getAdapter();
    $db->beginTransaction();
    try {
      $poll->vote($viewer, $option_id);

      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      $this->view->success = false;
      throw $e;
    }

    $this->view->success = true;
    $pollOptions = array();
    $this->redirect($this->view->url(array(
          'module' => 'poll',
          'controller' => 'poll',
          'action' => 'view',
          'user_id' => $poll->user_id,
          'poll_id' => $poll->poll_id,
          'slug' => $poll->getSlug(),
          'nocache' => rand(0, 1000),
        ), 'default', true));
  }

}
